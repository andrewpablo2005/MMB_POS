<?php
require_once __DIR__ . '/guard.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/addprodfunct.php";

use Classes\ProductManagement;

$productAlertManager = new ProductManagement($db);
$lowStockItems = [];
$expiryItems = [];
$showGlobalAlerts = in_array(strtolower($_SESSION['position'] ?? ''), ['owner', 'admin'], true);

if ($showGlobalAlerts) {
    $lowStockItems = $productAlertManager->getLowStockAlertItems();
    $expiryItems = $productAlertManager->getExpiryAlertItems();
}

$globalAlertItems = [];

foreach ($lowStockItems as $item) {
    $globalAlertItems[] = [
        'title' => 'Low Stock',
        'message' => htmlspecialchars($item['product_name']) . ' has only ' . ($item['quantity'] ?? 0) . ' unit(s) left.',
        'icon' => 'fas fa-exclamation-triangle',
        'bg' => '#f59e0b'
    ];
}

foreach ($expiryItems as $item) {
    $message = $item['status'] === 'Expired'
        ? htmlspecialchars($item['name']) . ' has expired. Remove it from sale immediately.'
        : htmlspecialchars($item['name']) . ' will expire in ' . $item['days_left'] . ' day(s).';

    $globalAlertItems[] = [
        'title' => $item['status'] === 'Expired' ? 'Expired Item' : 'Near Expiry',
        'message' => $message,
        'icon' => $item['status'] === 'Expired' ? 'fas fa-times-circle' : 'fas fa-clock',
        'bg' => $item['status'] === 'Expired' ? '#dc2626' : '#d97706'
    ];
}

$mmbPosition = htmlspecialchars($_SESSION['position'] ?? '');
$mmbInitial = mb_strtoupper(mb_substr($mmbPosition, 0, 1));
?>

<!-- ═══════════════════════════════════════════════════════════════
     TOPBAR — all visual styling lives in css/mmb-theme.css (v1.1+)
     ═══════════════════════════════════════════════════════════════ -->
<nav class="navbar app-topbar navbar-expand-lg sticky-top">

    <!-- Sidebar Toggle (Mobile) -->
    <button class="btn topbar-toggle d-lg-none me-1" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Brand -->
    <a class="navbar-brand app-brand" href="#">
        <span class="brand-mark"><i class="fas fa-prescription-bottle-medical"></i></span>
        <span class="brand-text">
            <span class="brand-name">MMB's Drugstore</span>
            <span class="brand-sub">Pharmacy POS</span>
        </span>
    </a>

    <!-- Right Side -->
    <div class="ms-auto d-flex align-items-center gap-2">

        <?php if (!empty($globalAlertItems)): ?>
        <button type="button" id="headerAlertBell" class="btn topbar-icon-btn position-relative d-inline-flex align-items-center justify-content-center" aria-label="Open notifications">
            <i class="fas fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger bell-badge">
                <?= count($globalAlertItems) ?>
            </span>
        </button>
        <?php endif; ?>

        <span class="topbar-divider d-none d-sm-block"></span>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button type="button" class="btn user-chip" id="userDropdownBtn" onclick="toggleDropdown()">
                <span class="user-avatar"><?= $mmbInitial ?></span>
                <span class="user-chip-name d-none d-sm-inline"><?= $mmbPosition ?></span>
                <i class="fas fa-chevron-down user-chip-chevron"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu shadow" id="userDropdownMenu" style="display: none;">
                <li class="dropdown-header">
                    Signed in as
                    <div class="user-dropdown-role"><?= $mmbPosition ?></div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="../login_logout_page/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <script>
            function toggleDropdown() {
                const menu = document.getElementById('userDropdownMenu');
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const btn = document.getElementById('userDropdownBtn');
                const menu = document.getElementById('userDropdownMenu');
                if (!btn.contains(event.target) && !menu.contains(event.target)) {
                    menu.style.display = 'none';
                }
            });
        </script>

    </div>
</nav>

<?php if (!empty($globalAlertItems)): ?>
<div id="globalAlertWidget" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1060; width: 360px; max-width: calc(100vw - 24px);">
    <div class="bg-white border rounded-4 shadow-sm overflow-hidden position-relative" style="border-color: #ebebeb;">
        <button type="button" id="globalAlertClose" class="btn btn-link position-absolute top-0 end-0 p-2 text-muted" aria-label="Close notifications" style="z-index: 2; font-size: 0.9rem; line-height: 1;">
            <i class="fas fa-times"></i>
        </button>

        <button type="button"
                id="globalAlertToggle"
                class="btn btn-light w-100 border-0 rounded-0 px-3 py-2 text-start pe-5"
                aria-expanded="true"
                aria-controls="globalAlertList"
                style="background: #fff; min-height: 60px;">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                          style="width: 30px; height: 30px; background: #fff3d7; color: #d97706; font-size: 0.9rem;">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div>
                        <div class="fw-semibold text-dark" style="font-size: 0.88rem;">Notifications</div>
                        <div class="text-muted" id="globalAlertCount" style="font-size: 0.72rem;">
                            <?= count($globalAlertItems) ?> item<?= count($globalAlertItems) > 1 ? 's' : '' ?>
                        </div>
                    </div>
                </div>
                <span class="text-muted toggle-chevron"><i class="fas fa-chevron-up" style="font-size: 0.8rem;"></i></span>
            </div>
        </button>

        <div id="globalAlertList" class="collapse show">
            <div class="bg-light border-top notification-scroll">
                <?php foreach ($globalAlertItems as $alert): ?>
                    <div class="alert-item d-flex align-items-start gap-2 px-3 py-3 border-bottom bg-white">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white"
                              style="width: 28px; height: 28px; background: <?= $alert['bg'] ?>; font-size: 0.72rem; flex-shrink: 0;">
                            <i class="<?= $alert['icon'] ?>"></i>
                        </span>

                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-dark" style="font-size: 0.78rem; line-height: 1.3;">
                                <?= htmlspecialchars($alert['title']) ?>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.7rem; line-height: 1.4;">
                                <?= $alert['message'] ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alertWidget = document.getElementById('globalAlertWidget');
        const alertToggler = document.getElementById('globalAlertToggle');
        const alertClose = document.getElementById('globalAlertClose');
        const alertList = document.getElementById('globalAlertList');
        const chevron = alertToggler ? alertToggler.querySelector('.toggle-chevron i') : null;
        const countText = document.getElementById('globalAlertCount');
        const headerBell = document.getElementById('headerAlertBell');

        function updateAlertToggleUI() {
            if (!alertToggler || !alertList || !chevron) return;
            const isOpen = alertList.classList.contains('show');
            alertToggler.setAttribute('aria-expanded', String(isOpen));
            chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            chevron.style.transition = 'transform 0.2s ease';
        }

        function showAllNotifications() {
            if (!alertWidget || !alertList) return;
            alertList.classList.add('show');
            alertWidget.style.display = 'block';
            updateAlertToggleUI();
        }

        function hideNotifications() {
            if (!alertWidget || !alertList) return;
            alertList.classList.remove('show');
            alertWidget.style.display = 'none';
            updateAlertToggleUI();
        }

        function toggleAlertWidget() {
            if (!alertWidget || !alertList) return;
            const willShow = !alertList.classList.contains('show');
            if (willShow) {
                showAllNotifications();
            } else {
                hideNotifications();
            }
        }

        if (headerBell) {
            headerBell.addEventListener('click', function (event) {
                event.preventDefault();
                toggleAlertWidget();
            });
        }

        if (alertToggler) {
            alertToggler.addEventListener('click', function (event) {
                event.preventDefault();
                toggleAlertWidget();
            });
        }

        if (alertClose) {
            alertClose.addEventListener('click', function () {
                if (alertWidget) {
                    alertWidget.style.display = 'none';
                }
            });
        }

        document.querySelectorAll('.dismiss-alert').forEach(function (button) {
            button.addEventListener('click', function () {
                const item = this.closest('.alert-item');
                if (item) {
                    item.remove();
                }

                const remaining = document.querySelectorAll('#globalAlertList .alert-item').length;
                if (countText) {
                    if (remaining > 0) {
                        countText.textContent = remaining + ' item' + (remaining > 1 ? 's' : '');
                    } else {
                        countText.textContent = '0 items';
                    }
                }

                const bellBadge = document.querySelector('#headerAlertBell .badge');
                if (bellBadge) {
                    bellBadge.textContent = remaining;
                }

                if (remaining === 0) {
                    alertList.classList.remove('show');
                    updateAlertToggleUI();
                    if (alertWidget) {
                        alertWidget.style.display = 'none';
                    }
                }
            });
        });

        updateAlertToggleUI();
    });
</script>
<?php endif; ?>
