#!/usr/bin/env python3
"""
MMB POS auto-deploy: GitHub Actions -> InfinityFree FTP (passive mode).

Strategy:
  1. Read the last deployed commit SHA from the FTP marker file (.deploy-sha).
     Fallback: the 'before' SHA of the push event; if neither is usable,
     do a full sync of every tracked file.
  2. git diff <base>..HEAD -> upload added/modified files, delete removed files.
     .github/, .git*, README.md, mmbpos.sql and docs/ are never deployed.
  3. Size-verify every upload; retry with reconnect on failure.
  4. Only after a fully successful run, write the new SHA to the marker.
     A failed run leaves the marker untouched, so the next run self-heals
     by re-deploying the same diff plus whatever came after it.

Exit code 0 = deployed (or nothing to deploy), 1 = failure (run shows red X).
"""
import ftplib
import os
import subprocess
import sys
import time

HOST = os.environ.get("IF_FTP_HOST", "").strip()
USER = os.environ.get("IF_FTP_USER", "").strip()
PASS = os.environ.get("IF_FTP_PASS", "")
WS = os.environ.get("GITHUB_WORKSPACE", os.getcwd())
EVENT_BEFORE = (os.environ.get("EVENT_BEFORE") or "").strip()
FULL_SYNC = (os.environ.get("FULL_SYNC") or "").strip().lower() in ("1", "true", "yes")
ROOT_DIR = "htdocs"
MARKER = ".deploy-sha"

EXCLUDE_TOP = {".git", ".github", "docs"}          # whole directories, never deployed
EXCLUDE_FILES = {".gitignore", ".gitattributes", ".gitmodules", "README.md", "mmbpos.sql"}


def excluded(path: str) -> bool:
    if path.split("/", 1)[0] in EXCLUDE_TOP:
        return True
    return path in EXCLUDE_FILES


def git(*args):
    r = subprocess.run(["git", "-c", "core.quotepath=off", "-C", WS, *args],
                       capture_output=True, text=True)
    if r.returncode != 0:
        raise RuntimeError(f"git {' '.join(args)}: {r.stderr.strip()}")
    return r.stdout.rstrip("\n")


def commit_exists(sha: str) -> bool:
    if not sha or any(c not in "0123456789abcdef" for c in sha.lower()):
        return False
    return subprocess.run(["git", "-C", WS, "cat-file", "-e", sha + "^{commit}"],
                          capture_output=True).returncode == 0


class Deployer:
    def __init__(self):
        self.ftp = None

    def connect(self, quiet=False):
        last = None
        for attempt in range(5):
            try:
                f = ftplib.FTP()
                f.connect(HOST, timeout=45)
                f.login(USER, PASS)
                f.set_pasv(True)
                f.cwd(ROOT_DIR)
                self.ftp = f
                return
            except Exception as e:
                last = e
                time.sleep(4 * (attempt + 1))
        raise RuntimeError(f"FTP connect failed: {last}")

    def read_marker(self) -> str | None:
        try:
            buf = []
            self.ftp.retrlines(f"RETR {MARKER}", buf.append)
            sha = "".join(buf).strip().lower()
            return sha if len(sha) == 40 else None
        except Exception:
            return None

    def write_marker(self, sha: str):
        tmp = "/tmp/deploy_sha_marker"
        with open(tmp, "w") as fh:
            fh.write(sha + "\n")
        with open(tmp, "rb") as fh:
            self.ftp.storbinary(f"STOR {MARKER}", fh)

    def ensure_dir(self, rel_dir: str):
        cur = ""
        for part in rel_dir.split("/"):
            cur = f"{cur}/{part}" if cur else part
            try:
                self.ftp.mkd(cur)
            except ftplib.error_perm:
                pass  # already exists

    def upload(self, rel: str) -> bool:
        """STOR once, then poll SIZE with backoff before re-uploading.

        Why: the hosting FTP backend can serve a STALE size for a freshly
        stored file (SIZE != uploaded bytes for a short while after the
        server already ack'd the transfer). Re-uploading instantly (the old
        behavior) just hits the same stale value 4 times in a row and fails
        the run even though the file landed correctly. So: check size
        immediately (happy path, no delay), then re-check with growing
        waits, and only re-upload if it still mismatches.
        """
        local = os.path.join(WS, rel)
        size = os.path.getsize(local)
        for attempt in range(3):
            try:
                parent = "/".join(rel.split("/")[:-1])
                if parent:
                    self.ensure_dir(parent)
                with open(local, "rb") as fh:
                    self.ftp.storbinary(f"STOR {rel}", fh, blocksize=65536)
            except Exception as e:
                print(f"    transfer error on {rel} ({e.__class__.__name__}), reconnecting")
                time.sleep(3)
                try:
                    self.ftp.quit()
                except Exception:
                    pass
                self.connect()
                continue
            for wait_s in (0, 1, 2, 4, 8):
                if wait_s:
                    time.sleep(wait_s)
                try:
                    got = self.ftp.size(rel)
                except Exception:
                    got = None
                if got == size:
                    return True
                print(f"    size check on {rel}: expected {size}, got {got}"
                      + (f", waiting {wait_s}s" if wait_s else ""))
            print(f"    re-uploading {rel} (attempt {attempt + 2}/3)")
        return False

    def verify_sizes(self, rels) -> list:
        """Final pass: re-check sizes of all uploaded files once the dust settled."""
        bad = []
        for rel in rels:
            local = os.path.join(WS, rel)
            try:
                size = os.path.getsize(local)
            except OSError:
                continue
            try:
                if self.ftp.size(rel) != size:
                    bad.append(rel)
            except Exception:
                try:
                    self.ftp.quit()
                except Exception:
                    pass
                self.connect()
                try:
                    if self.ftp.size(rel) != size:
                        bad.append(rel)
                except Exception:
                    bad.append(rel)
        return bad

    def delete(self, rel: str) -> bool:
        try:
            self.ftp.delete(rel)
            return True
        except ftplib.error_perm:
            return True  # already gone
        except Exception:
            return False


def build_plan(head_sha: str):
    """Returns (mode, [(action, path), ...], base_or_None)."""
    d = Deployer()
    d.connect()
    marker = d.read_marker()
    try:
        d.ftp.quit()
    except Exception:
        pass

    base = None
    if not FULL_SYNC:
        if marker and commit_exists(marker):
            base = marker
        elif commit_exists(EVENT_BEFORE) and not EVENT_BEFORE.startswith("0" * 6):
            base = EVENT_BEFORE
    mode = "incremental" if base else "full"

    if mode == "incremental":
        out = git("diff", "--no-renames", "--name-status", base, head_sha)
        plan = []
        for line in out.splitlines():
            if not line.strip():
                continue
            parts = line.split("\t")
            st, path = parts[0], parts[-1]
            if excluded(path):
                continue
            st = st[0]
            if st in ("A", "M", "C", "T"):
                plan.append(("PUT", path))
            elif st == "D":
                plan.append(("DEL", path))
    else:
        files = [p for p in git("ls-files").splitlines() if p and not excluded(p)]
        plan = [("PUT", p) for p in files]
    return mode, plan, base


def main():
    if not (HOST and USER and PASS):
        raise SystemExit("::error::FTP secrets are not configured on the repository")

    head_sha = git("rev-parse", "HEAD")
    print(f"HEAD = {head_sha}")

    mode, plan, base = build_plan(head_sha)
    print(f"Deploy mode: {mode}" + (f" (base {base})" if base else ""))

    puts = [p for a, p in plan if a == "PUT"]
    dels = [p for a, p in plan if a == "DEL"]
    if not puts and not dels:
        print("Nothing to deploy (only excluded files changed or marker already current).")
        return

    print(f"Files: {len(puts)} to upload, {len(dels)} to delete")
    for p in (puts + dels)[:60]:
        print(f"  {p}")
    if len(plan) > 60:
        print(f"  ... and {len(plan) - 60} more")

    d = Deployer()
    d.connect()
    print(f"Connected to {HOST} -> /{ROOT_DIR}")

    t0 = time.time()
    failed = []
    for rel in puts:
        if not d.upload(rel):
            failed.append(rel)
    for rel in dels:
        if not d.delete(rel):
            failed.append(rel)
    elapsed = time.time() - t0

    if failed:
        print("::error::Failed after retries: " + ", ".join(failed[:20]))
        raise SystemExit(1)

    # Final verification pass: the FTP backend can lag on fresh uploads, so
    # re-check every uploaded file's size once all transfers are done.
    if puts:
        print("Final verification pass...")
        bad = d.verify_sizes(puts)
        if bad:
            print("::error::Size verification failed for: " + ", ".join(bad[:20]))
            raise SystemExit(1)

    d.write_marker(head_sha)
    print(f"Deployed {len(puts)} file(s), deleted {len(dels)}, in {elapsed:.0f}s. Marker -> {head_sha[:10]}")

    # GitHub step summary
    summary = os.environ.get("GITHUB_STEP_SUMMARY")
    if summary:
        with open(summary, "a") as fh:
            fh.write(f"### Deployed to live site\n- Mode: **{mode}**\n- Base: `{base or 'full sync'}`\n")
            fh.write(f"- Uploaded: **{len(puts)}**, deleted: **{len(dels)}**, took {elapsed:.0f}s\n")
            if puts:
                fh.write("\n Uploaded files:\n" + "".join(f"- `{p}`\n" for p in puts[:40]))
    try:
        d.ftp.quit()
    except Exception:
        pass


if __name__ == "__main__":
    main()
