<?php
require_once __DIR__ . '/guard.php';
guard_require_roles(['admin']);
?>
<?php include __DIR__ . '/usermanagement.php'; ?>