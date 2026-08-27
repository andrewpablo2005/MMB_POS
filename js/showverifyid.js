// ✅ Senior/PWD verification now happens IN-APP (physical ID checklist in
// the POS verify modal). These helpers remain only as OPTIONAL links to the
// official government registries — they are no longer a required step.
function openSenior() {
    window.open(
        "https://www.ncsc.gov.ph/registration-verification",
        "Senior Verification",
        "width=900,height=600"
    );
}

function openPWD() {
    window.open(
        "https://pwd.doh.gov.ph/tbl_pwd_id_verificationlist.php",
        "PWD Verification",
        "width=900,height=600"
    );
}
