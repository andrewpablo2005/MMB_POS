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
        ? htmlspecialchars($item['name']) . ' has already expired and needs immediate attention.'
        : htmlspecialchars($item['name']) . ' will expire in ' . $item['days_left'] . ' day(s).';

    $globalAlertItems[] = [
        'title' => $item['status'] === 'Expired' ? 'Expired Item' : 'Near Expiry',
        'message' => $message,
        'icon' => $item['status'] === 'Expired' ? 'fas fa-times-circle' : 'fas fa-clock',
        'bg' => $item['status'] === 'Expired' ? '#dc2626' : '#d97706'
    ];
}
?>

<style>
    #sidebar {
        overflow-x: hidden;
    }

    #sidebar.offcanvas-lg.offcanvas-start {
        overflow-x: hidden;
    }

    #sidebar .offcanvas-body {
        padding: 0;
        overflow-x: hidden;
    }

    #sidebar .nav-pills {
        width: 100%;
        margin: 0 !important;
        padding: 0;
    }

    #sidebar .nav-pills.me-3 {
        margin-right: 0 !important;
    }

    #sidebar .nav-pills .nav-link {
        display: block;
        width: calc(100% - 2rem);
        max-width: calc(100% - 2rem);
        min-width: 0;
        box-sizing: border-box;
        padding: 0.45rem 0.65rem;
        margin: 0.15rem 0.35rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 0.75rem;
    }

    #sidebar .nav-pills .nav-link.active,
    #sidebar .nav-pills .nav-link:hover {
        width: calc(100% - 3.1rem);
    }

    .navbar {
        z-index: 1030 !important;
    }

    .notification-scroll {
        max-height: 320px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #c7c7c7 transparent;
    }

    .notification-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .notification-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .notification-scroll::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 999px;
    }

    #headerAlertBell {
        animation: bellPulse 1.8s infinite;
        box-shadow: 0 0 0 rgba(245, 158, 11, 0.35);
    }

    @keyframes bellPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.35);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.10);
            transform: scale(1.03);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            transform: scale(1);
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3 sticky-top" style="z-index: 1030;">

    <!-- ☰ Sidebar Toggle (Mobile) -->
    <button class="btn btn-outline-success d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
        ☰
    </button>

    <!-- 🏥 Logo / System Name -->
    <a class="navbar-brand fw-bold text-success" href="#">
        MMB'S DRUGSTORE
    </a>

    <!-- Right Side -->
    <div class="ms-auto d-flex align-items-center gap-2">

        <?php if (!empty($globalAlertItems)): ?>
        <button type="button" id="headerAlertBell" class="btn btn-outline-warning position-relative d-flex align-items-center justify-content-center" aria-label="Open notifications" style="width: 42px; height: 42px; border-radius: 50%;">
            <i class="fas fa-bell text-warning"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.62rem; min-width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;">
                <?= count($globalAlertItems) ?>
            </span>
        </button>
        <?php endif; ?>

        <!-- 👤 User Dropdown -->
        <div class="dropdown">
            <button type="button" class="btn btn-success dropdown-toggle" id="userDropdownBtn" onclick="toggleDropdown()">
                <?php echo htmlspecialchars($_SESSION['position']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg" id="userDropdownMenu" style="display: none; width: 20%; border-radius: 8px; border: none;">
                <li><a class="dropdown-item text-danger d-flex align-items-center gap-2" href="../login_logout_page/logout.php" style="padding: 12px 16px 12px; font-size: 15px; font-weight: 500;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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