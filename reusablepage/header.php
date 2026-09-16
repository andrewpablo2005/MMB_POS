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
    $lowStockBatches = [];
    foreach (($item['batches'] ?? []) as $batch) {
        $batchLabel = !empty($batch['batch_number'])
            ? 'Batch ' . $batch['batch_number']
            : 'Batch #' . (int) $batch['id'];
        $lowStockBatches[] = [
            'name' => $batchLabel,
            'quantity' => (int) $batch['current_quantity'],
            'href' => 'dashboard.php?tab=inventory&alert_product_id=' . (int) $item['id'] . '&alert_batch_id=' . (int) $batch['id'] . '&alert_type=low-stock'
        ];
    }

    $globalAlertItems[] = [
        'type' => count($lowStockBatches) > 1 ? 'batch-group' : 'single',
        'title' => 'Low Stock',
        'message' => htmlspecialchars($item['product_name']) . ' has only ' . ($item['quantity'] ?? 0) . ' unit(s) left.',
        'icon' => 'fas fa-exclamation-triangle',
        'bg' => '#dc2626',
        'href' => 'dashboard.php?tab=inventory&alert_product_id=' . (int)$item['id'] . '&alert_type=low-stock',
        'batches' => $lowStockBatches
    ];
}

$expiryGroups = [];
foreach ($expiryItems as $item) {
    $groupKey = (int) $item['product_id'] . ':' . $item['status'];
    if (!isset($expiryGroups[$groupKey])) {
        $expiryGroups[$groupKey] = [
            'type' => 'batch-group',
            'title' => $item['status'] === 'Expired' ? 'Expired Item' : 'Near Expiry',
            'product_name' => $item['product_name'] ?? $item['name'],
            'status' => $item['status'],
            'icon' => $item['status'] === 'Expired' ? 'fas fa-exclamation-triangle' : 'fas fa-clock',
            'bg' => $item['status'] === 'Expired' ? '#f59e0b' : '#2563eb',
            'batches' => []
        ];
    }

    $expiryGroups[$groupKey]['batches'][] = $item;
}

foreach ($expiryGroups as $group) {
    $firstBatch = $group['batches'][0];
    $batchCount = count($group['batches']);
    $message = htmlspecialchars($group['product_name']) . ' has ' . $batchCount . ' warning batch' . ($batchCount === 1 ? '' : 'es') . '.';

    $globalAlertItems[] = [
        'type' => 'batch-group',
        'title' => $group['title'],
        'message' => $message,
        'icon' => $group['icon'],
        'bg' => $group['bg'],
        'href' => 'dashboard.php?tab=inventory&alert_product_id=' . (int)$firstBatch['product_id'] . '&alert_batch_id=' . (int)($firstBatch['batch_id'] ?? 0) . '&alert_type=expiry',
        'batches' => $group['batches']
    ];
}

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

        <!-- ISSUE #6 (6): the account dropdown was redundant — a clean
             logout button replaces it (no more "O" avatar) -->
        <a class="btn topbar-logout-btn" href="../login_logout_page/logout.php"
           data-mmb-confirm="Log out of your account?" data-mmb-ok="Yes, log out">
            <i class="fas fa-sign-out-alt"></i>
            <span class="d-none d-sm-inline">Logout</span>
        </a>

    </div>
</nav>

<?php if (!empty($globalAlertItems)): ?>
<div id="globalAlertWidget" class="mmb-alert-widget" data-alert-signature="<?= htmlspecialchars(hash('sha256', json_encode($globalAlertItems)), ENT_QUOTES, 'UTF-8') ?>" style="z-index: 1085;">
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
                <?php foreach ($globalAlertItems as $alertIndex => $alert): ?>
                    <div class="alert-item border-bottom bg-white">
                    <a href="<?= htmlspecialchars($alert['href'], ENT_QUOTES, 'UTF-8') ?>" class="d-flex align-items-start gap-2 px-3 py-3 text-decoration-none">
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
                    </a>
                    <?php if (($alert['type'] ?? '') === 'batch-group' && count($alert['batches']) > 1): ?>
                        <button type="button" class="alert-group-toggle btn btn-link text-decoration-none px-3 pb-2 pt-0"
                                data-alert-group="alert-group-<?= $alertIndex ?>" aria-expanded="false">
                            +<?= count($alert['batches']) - 1 ?> more batch<?= count($alert['batches']) - 1 === 1 ? '' : 'es' ?>
                        </button>
                        <div id="alert-group-<?= $alertIndex ?>" class="alert-group-details px-3 pb-2" hidden>
                            <?php foreach ($alert['batches'] as $batch): ?>
                                <a href="<?= htmlspecialchars($batch['href'] ?? ('dashboard.php?tab=inventory&alert_product_id=' . (int)$batch['product_id'] . '&alert_batch_id=' . (int)($batch['batch_id'] ?? 0) . '&alert_type=expiry'), ENT_QUOTES, 'UTF-8') ?>"
                                   class="d-block text-muted text-decoration-none py-1" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($batch['name']) ?>
                                    <?php if (isset($batch['quantity'])): ?>
                                        (<?= (int)$batch['quantity'] ?> unit<?= (int)$batch['quantity'] === 1 ? '' : 's' ?> left)
                                    <?php elseif ($batch['status'] === 'Expired'): ?>
                                        (expired)
                                    <?php else: ?>
                                        (<?= (int)$batch['days_left'] ?> day<?= (int)$batch['days_left'] === 1 ? '' : 's' ?> left)
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
        const alertStorageKey = 'mmbDismissedGlobalAlertSignature';
        const alertSignature = alertWidget ? alertWidget.dataset.alertSignature : '';

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

        document.querySelectorAll('.alert-group-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const details = document.getElementById(button.dataset.alertGroup);
                if (!details) return;
                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!isExpanded));
                details.hidden = isExpanded;
                button.textContent = isExpanded
                    ? button.textContent.replace('−', '+')
                    : button.textContent.replace('+', '−');
            });
        });

        if (alertClose) {
            alertClose.addEventListener('click', function () {
                if (alertWidget) {
                    try {
                        localStorage.setItem(alertStorageKey, alertSignature);
                    } catch (error) {
                    }
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

        try {
            if (alertWidget && localStorage.getItem(alertStorageKey) === alertSignature) {
                alertWidget.style.display = 'none';
            }
        } catch (error) {
        }

        updateAlertToggleUI();
    });
</script>
<?php endif; ?>
