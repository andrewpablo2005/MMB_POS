# MMB POS — Pharmacy Point-of-Sale System

A web-based Point-of-Sale and inventory system for a small pharmacy, built with **PHP 8 + MySQL (MariaDB) + Bootstrap 5**. Designed for XAMPP-style local deployment.

---

## 1. Features

| Module | What it does |
|---|---|
| **POS (wePOS-style)** | Barcode scanning, product search, cart, cash/card payment, on-screen receipt + printing (jsPDF) |
| **PhilHealth-style statutory discounts** | Senior Citizen / PWD 20% discount + 12% VAT exemption, with the legal weekly caps (₱125 discount / ₱2,500 purchase per customer per week) |
| **Senior/PWD verification** | First visit: cashier completes an **in-app physical ID inspection checklist** (no external website redirect). Repeat visits auto-verified from the local registry. Every verification records who verified and when. |
| **Manager price override** | Owner/Admin username+password approval per item, with reason + audit log (`override_log`) |
| **Void authorization** | Cart-line removal / cart clear requires a 7-digit manager Void PIN (stored **hashed**) |
| **Returns / refunds** | Partial or full returns per transaction, restock to the original inventory batch, refund receipt |
| **Inventory (FEFO)** | Batch-level stock with expiry dates, auto FEFO deduction at checkout, batch disposal, low-stock and expiry alerts |
| **Register closing (Z-report)** | End-of-shift system-vs-counted cash reconciliation with variance |
| **Reports** | Daily / monthly / yearly sales, sales detail (filter by date/month/year + cashier), top products, expired products, real revenue (net of discounts/VAT exemption/refunds) |
| **User management** | Owner/Admin/Staff roles, account approval workflow, per-account Void PIN, account enable/disable |
| **Dashboard** | KPI cards + Chart.js sales charts |

---

## 2. Requirements

- XAMPP (Apache + MySQL/MariaDB + PHP 8.x) — or any Apache + PHP 8 + MySQL stack
- `mod_rewrite` enabled (for the clean-URL `.htaccess` rules)
- PHP extensions: `pdo_mysql`, `openssl` (for SMTP mail, optional)

---

## 3. Installation (XAMPP)

1. Copy this repository into `C:\xampp\htdocs\MMBPOS\`
   (any folder name works — the app auto-detects its URL path, see §7).
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open <http://localhost/phpmyadmin> and create a database named **`mmbpos`**.
4. Import **`mmbpos.sql`** (root of this repo) into the `mmbpos` database.
5. Make sure the DB credentials in `conn/database.php` match your MySQL user
   (default XAMPP: user `root`, empty password).
6. Browse to <http://localhost/MMBPOS/> → the login page loads.

### Default demo accounts (from the seed data)

| Username | Role | Void PIN |
|---|---|---|
| `andrew_owner` | Owner | `1234567` |
| `andrew_admin` | Admin | `1234567` |
| `andrew_staff` | Staff | `1234567` |

> **Change these immediately in a real deployment.** Login passwords are stored bcrypt-hashed; Void PINs are stored bcrypt-hashed as well (they can no longer be viewed after being set — only replaced).

### Optional: email notifications

Account approval/rejection emails use PHPMailer via Gmail SMTP. Configure these environment variables (e.g. in Apache config `SetEnv` or in your vhost) — **credentials are never stored in the repo**:

```
MMBPOS_SMTP_USER=your-address@gmail.com
MMBPOS_SMTP_PASS=your-16-char-app-password
MMBPOS_SMTP_FROM=your-address@gmail.com   # optional
```

If not set, emails are silently skipped — everything else works.

---

## 4. Folder structure

```
MMBPOS/
├── adminpage/dashboard.php      # Admin dashboard (role: admin)
├── ownerpage/dashboard.php      # Owner dashboard (role: owner)
├── staffpos/dashboard.php       # Staff dashboard → POS (role: staff)
├── reusablepage/                # Page fragments INCLUDED by the dashboards.
│   │                            #   ❗ Direct URL access is blocked (.htaccess + guard.php)
│   ├── guard.php                #   Session + role guard included by every fragment
│   ├── salespos.php             #   POS screen
│   ├── productmanagement.php    #   Product CRUD (soft-delete preserves sales history)
│   ├── inventorymanagement.php  #   Batches, suppliers, disposals
│   ├── reports.php              #   Sales reports
│   ├── usermanagement.php       #   Account CRUD (owner only)
│   ├── systemsettings.php       #   Self-service account settings
│   └── ...
├── function/                    # Business logic + JSON API endpoints
│   ├── workingpos.php           #   Product class + transaction engine (server-side pricing)
│   ├── process_transaction.php  #   POST /checkout endpoint
│   ├── process_return.php       #   Returns engine (row-locked, batch-aware restock)
│   ├── verify_void_pin.php      #   Void PIN check (hashed, rate-limited, session-gated)
│   ├── verify_override_pin.php  #   Manager override credential check
│   ├── verify_customer_id.php   #   Senior/PWD registry lookup
│   ├── save_customer_id.php     #   First-time Senior/PWD verification (records verifier)
│   └── ...
├── conn/                        # PDO connection (utf8mb4, Manila timezone)
├── js/ css/ img/                # Assets (pos_wepos.js is the live POS client)
├── docs/                        # Project documents (blocked from web access)
└── mmbpos.sql                   # Full database dump (schema + demo data)
```

---

## 5. Operational flows

### 5.1 A normal sale (the core flow)

```
Login → Dashboard → POS
  1. Scan barcode / click product  (stock = sum of unexpired batch quantities)
  2. (optional) Select Senior/PWD discount → verify customer
  3. (optional) Manager override on an item (username+password + reason, logged)
  4. Pay → server re-validates EVERYTHING:
       • prices from the DB (never trusted from the browser)
       • quantities must be positive integers
       • discount / VAT exemption capped at what the rules allow
       • FEFO batch deduction inside a DB transaction (SELECT ... FOR UPDATE)
  5. Receipt printed / shown; stock deducted; transaction recorded
  6. End of shift → Close Register (system cash vs counted cash → variance)
```

### 5.2 Senior/PWD discount verification

```
First visit:
  Select discount → enter name + ID number
    → NOT in registry → cashier inspects the PHYSICAL ID card
    → ticks all 3 checklist items (photo match / government ID / number match)
    → "Confirm ID Verified" → saved to registry with verified_by + verified_at
    → official NCSC/DOH online registry available as an OPTIONAL helper link

Repeat visit:
  Enter name + ID number → found in registry → discount applies instantly
```

### 5.3 Returns / refunds

```
POS → Return → enter Transaction Ref #
  → items + already-returned quantities listed
  → cashier picks items + quantities + restockable?
  → Manager Void PIN required
  → refund recorded (approver saved), stock restocked into the ORIGINAL batch
    (falls back to the earliest unexpired batch; fails loudly if none exists)
```

### 5.4 Account lifecycle

```
Staff pre-registration (Staff role only, enforced server-side)
  → Admin "Pending Accounts" tab (position column visible)
  → Approve → active account (email sent if SMTP configured) / Reject
Owner can additionally: create Admin/Owner accounts, edit, disable, delete
  (self-delete and deleting the last active Owner are blocked)
```

---

## 6. Security model (after the audit fixes)

- **Roles**: `Owner` > `Admin` > `Staff`. Every dashboard checks its role; every `reusablepage/*` fragment includes `guard.php` (login + role) AND the folder is `.htaccess`-denied for direct URL access.
- **All JSON endpoints require a logged-in session** (`process_transaction`, `get_transaction_details`, `verify_*`, `save/verify_customer_id`, `add_supplier_ajax`, `workingpos.php?action=getProducts`).
- **Server-authoritative money math**: prices, quantities, discounts, VAT exemptions and units-per-package are re-derived from the database at checkout. A tampered request cannot buy at ₱0.01, use negative quantities, or claim a fake discount.
- **Void PINs are bcrypt-hashed** at rest; verification is session-gated and rate-limited (5 failures → 5-minute lockout per session).
- **Returns are serialized** with `SELECT ... FOR UPDATE` on the original transaction, preventing double refunds.
- **XSS-hardened**: all admin/user/product output goes through `htmlspecialchars`; POS JS templates escape product/customer names before `innerHTML`.
- **Session hardening**: `session_regenerate_id` at login, HttpOnly + SameSite=Lax cookies (`.htaccess`), proper logout (cookie + session destroyed).
- **Secrets out of the repo**: SMTP credentials via environment variables; no credentials in docs.
- **Sales history is sacred**: deleting a product with sales history soft-hides it instead of destroying `transaction_items`.

Known limitations (documented, not dangerous for a school deployment): no CSRF tokens on regular HTML forms (GET deletes are token-protected), no HTTPS enforcement, single-store/single-register model.

---

## 7. Deploying at a domain root (or any subfolder)

The app **auto-detects its base URL path** via `conn/basepath.php` (`mmbpos_base_path()`), so the same code works at `http://localhost/MMBPOS/` (XAMPP subfolder) **and** at a domain root such as `https://mmbpos.wuaze.com/` with zero configuration. Login redirects, logout, and asset links all use the helper. If you add a new page with a redirect or a root-absolute asset path, use `mmbpos_base_path() . '/...'` instead of a hardcoded `/MMBPOS/...`.

---

## 8. Credits

Built as a capstone/project by the MMB team. UI patterns inspired by wePOS; statutory discount rules follow Philippine law (RA 9994 / RA 10754 — Senior Citizen / PWD 20% discount + VAT exemption with weekly caps).
