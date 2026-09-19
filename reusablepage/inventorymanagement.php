<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
require_once __DIR__ . "/../function/addprodfunct.php";

use Classes\ProductManagement;

$inventoryManager = new ProductManagement($db);
$inventoryBatches = $inventoryManager->getAllInventoryBatches();
$inventoryBatchSequence = $inventoryManager->getAllInventoryBatchNumbersForSequence();
$disposedBatches = $inventoryManager->getDisposedBatches();
$noStockBatches = $inventoryManager->getNoStockBatches();
$returnedProducts = $inventoryManager->getReturnedProducts();
$products = $inventoryManager->getAllProducts();
$requestedInventoryTab = $_GET['inventory_tab'] ?? 'current';
$validInventoryTabs = ['current', 'disposed', 'no-stock', 'returned'];
if (!in_array($requestedInventoryTab, $validInventoryTabs, true)) {
    $requestedInventoryTab = 'current';
}

usort($inventoryBatches, static function (array $first, array $second): int {
    return ((int) ($first['id'] ?? 0)) <=> ((int) ($second['id'] ?? 0));
});

$inventoryNotification = null;
if ($inventoryManager->addInventoryBatch()) {
    echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php?tab=inventory&success=1'; }, 10);</script>";
    exit;
} elseif (isset($_POST['addInventoryBatch'])) {
    $inventoryNotification = [
        'type' => 'danger',
        'title' => 'Add batch failed',
        'message' => $inventoryManager->getResponse() ?: 'Unable to add stock batch.'
    ];
}

if ($inventoryManager->disposeInventoryBatch()) {
    echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php?tab=inventory&inventory_tab=disposed&success=disposed'; }, 10);</script>";
    exit;
} elseif (isset($_POST['disposeInventoryBatch'])) {
    $inventoryNotification = [
        'type' => 'danger',
        'title' => 'Dispose batch failed',
        'message' => $inventoryManager->getResponse() ?: 'Unable to dispose inventory batch.'
    ];
}

$inventoryErrorMessage = $_GET['inventory_error'] ?? '';
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $inventoryNotification = [
        'type' => 'success',
        'title' => 'Batch added',
        'message' => 'New inventory batch added successfully.'
    ];
} elseif (isset($_GET['success']) && $_GET['success'] === 'disposed') {
    $inventoryNotification = [
        'type' => 'success',
        'title' => 'Batch disposed',
        'message' => 'Inventory batch disposed successfully.'
    ];
} elseif (isset($_GET['success']) && $_GET['success'] === 'no-stock') {
    $inventoryNotification = [
        'type' => 'success',
        'title' => 'Moved to No Stock',
        'message' => 'Batch moved to the No Stock table successfully.'
    ];
} elseif ($inventoryErrorMessage !== '') {
    $inventoryNotification = [
        'type' => 'danger',
        'title' => 'Move to No Stock failed',
        'message' => $inventoryErrorMessage
    ];
}
?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php if ($inventoryNotification !== null): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof mmbNotify === 'function') {
                        mmbNotify(<?= json_encode($inventoryNotification, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
                    }
                });
            </script>
        <?php endif; ?>
        <style>
            .inventory-action-column {
                width: 230px;
                min-width: 230px;
            }
            .inventory-action-stack {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                min-width: 180px;
            }
            .inventory-action-stack .btn {
                width: 100%;
                white-space: normal;
                overflow-wrap: anywhere;
                font-weight: 600;
                letter-spacing: 0.01em;
            }
            #currentInventoryTable {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed;
            }
            #currentInventoryTable col:nth-child(1) { width: 6% !important; }
            #currentInventoryTable col:nth-child(2) { width: 13% !important; }
            #currentInventoryTable col:nth-child(3) { width: 31% !important; }
            #currentInventoryTable col:nth-child(4),
            #currentInventoryTable col:nth-child(5) { width: 13% !important; }
            #currentInventoryTable col:nth-child(6) { width: 10% !important; }
            #currentInventoryTable col:nth-child(7) { width: 14% !important; }
            #currentInventoryTable th:nth-child(1),
            #currentInventoryTable td:nth-child(1) { width: 6% !important; }
            #currentInventoryTable th:nth-child(2),
            #currentInventoryTable td:nth-child(2) { width: 13% !important; }
            #currentInventoryTable th:nth-child(3),
            #currentInventoryTable td:nth-child(3) { width: 31% !important; }
            #currentInventoryTable th:nth-child(4),
            #currentInventoryTable td:nth-child(4),
            #currentInventoryTable th:nth-child(5),
            #currentInventoryTable td:nth-child(5) { width: 13% !important; }
            #currentInventoryTable th:nth-child(6),
            #currentInventoryTable td:nth-child(6) { width: 10% !important; }
            #currentInventoryTable th:nth-child(7),
            #currentInventoryTable td:nth-child(7) { width: 14% !important; }
            #currentInventoryTable th,
            #currentInventoryTable td {
                white-space: normal !important;
                overflow-wrap: anywhere;
                word-break: break-word;
            }
            #currentInventoryTable th {
                line-height: 1.25;
            }
            #currentInventoryTable td > .d-flex {
                align-items: flex-start !important;
                min-width: 0;
            }
            #currentInventoryTable td > .d-flex > span:last-child {
                min-width: 0;
                overflow-wrap: anywhere;
            }
            #currentInventoryTable .inventory-action-column,
            #currentInventoryTable .inventory-action-cell,
            #currentInventoryTable .inventory-action-stack {
                min-width: 0;
            }
        </style>
        <div class="page-head">
            <div>
                <h4>Inventory Management</h4>
                <p class="page-sub">Manage current stock batches and review disposed or expired inventory.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                    Add Batch
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $requestedInventoryTab === 'current' ? 'active' : '' ?>" id="current-inventory-tab" data-bs-toggle="tab" data-bs-target="#current-inventory-pane" type="button" role="tab" aria-controls="current-inventory-pane" aria-selected="<?= $requestedInventoryTab === 'current' ? 'true' : 'false' ?>">
                    Current Inventory
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $requestedInventoryTab === 'disposed' ? 'active' : '' ?>" id="disposed-inventory-tab" data-bs-toggle="tab" data-bs-target="#disposed-inventory-pane" type="button" role="tab" aria-controls="disposed-inventory-pane" aria-selected="<?= $requestedInventoryTab === 'disposed' ? 'true' : 'false' ?>">
                    Disposed / Expired
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $requestedInventoryTab === 'no-stock' ? 'active' : '' ?>" id="no-stock-tab" data-bs-toggle="tab" data-bs-target="#no-stock-pane" type="button" role="tab" aria-controls="no-stock-pane" aria-selected="<?= $requestedInventoryTab === 'no-stock' ? 'true' : 'false' ?>">
                    No Stock
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $requestedInventoryTab === 'returned' ? 'active' : '' ?>" id="returned-products-tab" data-bs-toggle="tab" data-bs-target="#returned-products-pane" type="button" role="tab" aria-controls="returned-products-pane" aria-selected="<?= $requestedInventoryTab === 'returned' ? 'true' : 'false' ?>">
                    Returned Products
                </button>
            </li>
        </ul>

        <div class="tab-content" id="inventoryTabContent">
            <div class="tab-pane fade <?= $requestedInventoryTab === 'current' ? 'show active' : '' ?>" id="current-inventory-pane" role="tabpanel" aria-labelledby="current-inventory-tab" tabindex="0">
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="currentInventoryTable">
                <label class="mb-0" for="currentInventorySearch">Search:</label>
                <input type="search" id="currentInventorySearch" class="form-control form-control-sm inventory-search" placeholder="Search current inventory..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy">Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel">Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf">PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print">Print</button>
            </div>
            <div class="table-responsive mmb-table-scroll mb-4">
            <table id="currentInventoryTable" class="table table-striped table-hover align-middle w-100 mmb-stack inventory-data-table">
                <thead class="table-dark">
                    <tr>
                        <th data-priority="1">ID</th>
                        <th data-priority="2">Batch No.</th>
                        <th data-priority="3">Product</th>
                        <th data-priority="4">Original Quantity</th>
                        <th data-priority="5">Current Quantity</th>
                        <th data-priority="6">Expiry</th>
                        <th data-priority="1" class="inventory-action-column text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventoryBatches)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No inventory batches recorded yet — add one with the “Add Batch” button.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inventoryBatches as $batch): ?>
                            <?php
                                $batchNumber = trim((string) ($batch['batch_number'] ?? ''));
                                $batchCurrentQuantity = (int) ($batch['current_quantity'] ?? 0);
                                $batchExpiryDate = trim((string) ($batch['expiry_date'] ?? ''));
                                $rowWarningClass = '';
                                $today = new DateTime('today');
                                $nearExpiryDate = (clone $today)->modify('+90 days');

                                if ($batchCurrentQuantity <= 0) {
                                    $rowWarningClass = 'table-secondary';
                                } elseif ($batchCurrentQuantity <= 15) {
                                    $rowWarningClass = 'table-warning';
                                }

                                if ($batchExpiryDate !== '') {
                                    try {
                                        $batchExpiry = new DateTime($batchExpiryDate);
                                        if ($batchExpiry < $today) {
                                            $rowWarningClass = 'table-danger';
                                        } elseif ($batchExpiry <= $nearExpiryDate && $rowWarningClass === '') {
                                            $rowWarningClass = 'table-warning';
                                        }
                                    } catch (Exception $e) {
                                        // Ignore invalid expiry input and allow the stock status to dictate the row color.
                                    }
                                }
                            ?>
                            <tr class="<?= $rowWarningClass ?>" data-product-id="<?= (int)($batch['product_id'] ?? 0) ?>" data-batch-id="<?= (int)($batch['id'] ?? 0) ?>">
                                <td data-label="ID"><?= (int)($batch['id'] ?? 0) ?></td>
                                <td data-label="Batch No."><?= htmlspecialchars($batchNumber !== '' ? $batchNumber : 'N/A') ?></td>
                                <td data-label="Product">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty(trim((string)($batch['imageproduct'] ?? '')))): ?>
                                            <span class="mmb-thumb mmb-thumb--md">
                                                <img src="../img/<?= htmlspecialchars($batch['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                                            </span>
                                        <?php else: ?>
                                            <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? ''))) ?></span>
                                    </div>
                                </td>
                                <td data-label="Original Qty"><?= htmlspecialchars((string) ($batch['received_quantity'] ?? 0)) ?></td>
                                <td data-label="Current Qty"><?= htmlspecialchars((string) ($batch['current_quantity'] ?? 0)) ?></td>
                                <td data-label="Expiry"><?= htmlspecialchars($batch['expiry_date'] ?: 'N/A') ?></td>
                                <td data-label="Action" class="inventory-action-cell">
                                    <div class="inventory-action-stack">
                                        <button type="button"
                                                class="btn btn-sm btn-warning inventory-move-no-stock-btn"
                                                data-batch-id="<?= (int)($batch['id'] ?? 0) ?>"
                                                data-batch-number="<?= htmlspecialchars((string)($batchNumber !== '' ? $batchNumber : 'N/A'), ENT_QUOTES, 'UTF-8') ?>"
                                                data-product-name="<?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="(function(){var select=document.getElementById('move_no_stock_inventory_id'); var batchId='<?= (int)($batch['id'] ?? 0) ?>'; var batchNumber='<?= htmlspecialchars((string)($batchNumber !== '' ? $batchNumber : 'N/A'), ENT_QUOTES, 'UTF-8') ?>'; var productName='<?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>'; if(select){ var match=null; for(var i=0;i<select.options.length;i++){var option=select.options[i]; var optionText=(option.textContent||'').trim().toLowerCase(); if(option.value === String(batchId)) { match=option; break; } if(batchNumber && productName && optionText.indexOf(batchNumber.toLowerCase()) !== -1 && optionText.indexOf(productName.toLowerCase()) !== -1) { match=option; break; } } if(match){ select.value = match.value; if(typeof select.dispatchEvent === 'function'){ select.dispatchEvent(new Event('change', { bubbles: true })); } } } var modalEl=document.getElementById('moveNoStockModal'); if(modalEl && window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getOrCreateInstance(modalEl).show(); }})();"
                                                <?= ((int)($batch['current_quantity'] ?? 0) > 0) ? 'disabled' : '' ?>>
                                            Move No Stock
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger inventory-dispose-batch-btn"
                                                data-batch-id="<?= (int)($batch['id'] ?? 0) ?>"
                                                data-batch-number="<?= htmlspecialchars((string)($batchNumber !== '' ? $batchNumber : 'N/A'), ENT_QUOTES, 'UTF-8') ?>"
                                                data-product-name="<?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="(function(){var select=document.getElementById('dispose_inventory_id'); var batchId='<?= (int)($batch['id'] ?? 0) ?>'; var batchNumber='<?= htmlspecialchars((string)($batchNumber !== '' ? $batchNumber : 'N/A'), ENT_QUOTES, 'UTF-8') ?>'; var productName='<?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>'; if(select){ var match=null; for(var i=0;i<select.options.length;i++){var option=select.options[i]; var optionText=(option.textContent||'').trim().toLowerCase(); if(option.value === String(batchId)) { match=option; break; } if(batchNumber && productName && optionText.indexOf(batchNumber.toLowerCase()) !== -1 && optionText.indexOf(productName.toLowerCase()) !== -1) { match=option; break; } } if(match){ select.value = match.value; if(typeof select.dispatchEvent === 'function'){ select.dispatchEvent(new Event('change', { bubbles: true })); } } } var modalEl=document.getElementById('disposeBatchModal'); if(modalEl && window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getOrCreateInstance(modalEl).show(); }})();">
                                            Dispose Batch
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

            </div>

            <div class="tab-pane fade <?= $requestedInventoryTab === 'disposed' ? 'show active' : '' ?>" id="disposed-inventory-pane" role="tabpanel" aria-labelledby="disposed-inventory-tab" tabindex="0">
            <h5 class="mb-3">Disposed / Expired Inventory</h5>
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="disposedInventoryTable">
                <label class="mb-0" for="disposedInventorySearch">Search:</label>
                <input type="search" id="disposedInventorySearch" class="form-control form-control-sm inventory-search" placeholder="Search disposed inventory..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy">Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel">Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf">PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print">Print</button>
            </div>
            <div class="table-responsive mmb-table-scroll">
            <table id="disposedInventoryTable" class="table table-sm table-bordered align-middle w-100 mmb-stack inventory-data-table">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Batch No.</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Expiry</th>
                        <th>Reason</th>
                        <th>Proof</th>
                        <th>Disposed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($disposedBatches)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No disposed or expired inventory found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disposedBatches as $disposed): ?>
                            <tr>
                                <td data-label="ID"><?= htmlspecialchars($disposed['id']) ?></td>
                                <td data-label="Batch No."><?= htmlspecialchars($disposed['batch_number'] ?: 'N/A') ?></td>
                                <td data-label="Product">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty(trim((string)($disposed['imageproduct'] ?? '')))): ?>
                                            <span class="mmb-thumb mmb-thumb--md">
                                                <img src="../img/<?= htmlspecialchars($disposed['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                                            </span>
                                        <?php else: ?>
                                            <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars(trim(implode(' ', array_filter([
                                            $disposed['branded_name'] ?? '',
                                            $disposed['generic_name'] ?? '',
                                            $disposed['strength'] ?? '',
                                            $disposed['measurement_name'] ?? ''
                                        ], static function ($value) {
                                            return $value !== '';
                                        })))) ?></span>
                                    </div>
                                </td>
                                <td data-label="Quantity"><?= htmlspecialchars(($disposed['quantity'] ?? 0)) ?></td>
                                <td data-label="Expiry"><?= htmlspecialchars($disposed['expiry_date'] ?: 'N/A') ?></td>
                                <td data-label="Reason"><?= htmlspecialchars($disposed['reason'] ?? 'N/A') ?></td>
                                <td data-label="Proof">
                                    <?php if (!empty($disposed['disposal_proof_filename'])): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary disposal-proof-view-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#disposalProofModal"
                                                data-proof-image="../img/disposal-proofs/<?= htmlspecialchars($disposed['disposal_proof_filename'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-proof-title="<?= htmlspecialchars(trim(($disposed['branded_name'] ?? '') . ' ' . ($disposed['generic_name'] ?? '') . ' proof'), ENT_QUOTES, 'UTF-8') ?>">
                                            View image
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Disposed At"><?= htmlspecialchars($disposed['disposed_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

            </div>

            <div class="tab-pane fade <?= $requestedInventoryTab === 'no-stock' ? 'show active' : '' ?>" id="no-stock-pane" role="tabpanel" aria-labelledby="no-stock-tab" tabindex="0">
            <h5 class="mb-3">No Stock Batches</h5>
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="noStockTable">
                <label class="mb-0" for="noStockSearch">Search:</label>
                <input type="search" id="noStockSearch" class="form-control form-control-sm inventory-search" placeholder="Search no-stock batches..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy">Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel">Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf">PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print">Print</button>
            </div>
            <div class="table-responsive mmb-table-scroll">
            <table id="noStockTable" class="table table-sm table-bordered align-middle w-100 mmb-stack inventory-data-table">
                <thead class="table-warning">
                    <tr>
                        <th>ID</th>
                        <th>Batch No.</th>
                        <th>Product</th>
                        <th>Current Qty</th>
                        <th>Received Qty</th>
                        <th>Expiry</th>
                        <th>Reason</th>
                        <th>Moved At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($noStockBatches)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No zero-stock batches have been moved here yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($noStockBatches as $batch): ?>
                            <tr>
                                <td data-label="ID"><?= htmlspecialchars($batch['id']) ?></td>
                                <td data-label="Batch No."><?= htmlspecialchars($batch['batch_number'] ?: 'N/A') ?></td>
                                <td data-label="Product">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty(trim((string)($batch['imageproduct'] ?? '')))): ?>
                                            <span class="mmb-thumb mmb-thumb--md">
                                                <img src="../img/<?= htmlspecialchars($batch['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                                            </span>
                                        <?php else: ?>
                                            <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? ''))) ?></span>
                                    </div>
                                </td>
                                <td data-label="Current Qty"><?= htmlspecialchars((string) ($batch['current_quantity'] ?? 0)) ?></td>
                                <td data-label="Received Qty"><?= htmlspecialchars((string) ($batch['received_quantity'] ?? 0)) ?></td>
                                <td data-label="Expiry"><?= htmlspecialchars($batch['expiry_date'] ?: 'N/A') ?></td>
                                <td data-label="Reason"><?= htmlspecialchars($batch['reason'] ?? 'No stock') ?></td>
                                <td data-label="Moved At"><?= htmlspecialchars($batch['moved_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
            </div>

            <div class="tab-pane fade <?= $requestedInventoryTab === 'returned' ? 'show active' : '' ?>" id="returned-products-pane" role="tabpanel" aria-labelledby="returned-products-tab" tabindex="0">
            <h5 class="mb-3">Returned Products</h5>
            <p class="text-muted small mb-3">All returns grouped per product — pick a product from the dropdown or expand a group to see each return record.</p>

            <?php
            /* Group return records per product for the accordion view */
            $returnGroups = [];
            foreach ($returnedProducts as $return) {
                $pid = (int)($return['product_id'] ?? 0);
                if (!isset($returnGroups[$pid])) {
                    $returnGroups[$pid] = ['product' => $return, 'items' => [], 'total' => 0.0, 'qty' => 0];
                }
                $returnGroups[$pid]['items'][] = $return;
                $returnGroups[$pid]['total'] += (float)($return['subtotal'] ?? 0);
                $returnGroups[$pid]['qty'] += (int)($return['quantity'] ?? 0);
            }
            ?>

            <div class="returns-toolbar d-flex flex-wrap align-items-center gap-2 mb-3">
                <label class="mb-0 fw-semibold small" for="returnsProductFilter">Product:</label>
                <select id="returnsProductFilter" class="form-select form-select-sm" style="max-width:300px;">
                    <option value="all">All products (<?= count($returnGroups) ?>)</option>
                    <?php foreach ($returnGroups as $gPid => $g): ?>
                        <?php $gName = trim(($g['product']['branded_name'] ?? '') . ' ' . ($g['product']['generic_name'] ?? '')); ?>
                        <option value="<?= $gPid ?>"><?= htmlspecialchars($gName) ?> (<?= count($g['items']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <input type="search" id="returnsSearch" class="form-control form-control-sm" placeholder="Search returns..." style="max-width:220px;">
                <button type="button" class="btn btn-sm btn-secondary returns-copy">Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary returns-excel">Excel</button>
                <button type="button" class="btn btn-sm btn-danger returns-pdf">PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark returns-print">Print</button>
            </div>

            <div id="returnsAccordion" class="mb-4">
                <?php if (empty($returnGroups)): ?>
                    <div class="empty-panel">
                        <i class="fas fa-rotate-left"></i>
                        <h6>No returned products recorded</h6>
                        <p>Customer returns will appear here, grouped by product.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($returnGroups as $gPid => $g): ?>
                        <?php
                        $gProd = $g['product'];
                        $gName = trim(($gProd['branded_name'] ?? '') . ' ' . ($gProd['generic_name'] ?? '') . ' ' . ($gProd['strength'] ?? '') . ' ' . ($gProd['measurement_name'] ?? ''));
                        $gLabel = trim(($gProd['branded_name'] ?? '') !== '' ? $gProd['branded_name'] : ($gProd['generic_name'] ?? ''));
                        ?>
                        <div class="return-group" data-product-id="<?= $gPid ?>" data-search-name="<?= htmlspecialchars(mb_strtolower($gName)) ?>">
                            <button type="button" class="return-group-head" aria-expanded="false">
                                <?php if (!empty(trim((string)($gProd['imageproduct'] ?? '')))): ?>
                                    <span class="mmb-thumb mmb-thumb--md">
                                        <img src="../img/<?= htmlspecialchars($gProd['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                                    </span>
                                <?php else: ?>
                                    <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                <?php endif; ?>
                                <span class="rg-name">
                                    <span class="rg-title"><?= htmlspecialchars($gLabel) ?></span>
                                    <span class="rg-sub"><?= count($g['items']) ?> return<?= count($g['items']) > 1 ? 's' : '' ?> · <?= $g['qty'] ?> unit<?= $g['qty'] > 1 ? 's' : '' ?> refunded</span>
                                </span>
                                <span class="rg-total">₱<?= number_format($g['total'], 2) ?></span>
                                <i class="fas fa-chevron-down rg-chevron"></i>
                            </button>
                            <div class="return-group-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless align-middle mb-0 return-detail-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Return Tx</th>
                                                <th>Original Tx</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                                <th>Reason</th>
                                                <th>Method</th>
                                                <th>Returned At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($g['items'] as $return): ?>
                                                <tr>
                                                    <td>#<?= htmlspecialchars($return['id']) ?></td>
                                                    <td>#<?= htmlspecialchars($return['return_transaction_id']) ?></td>
                                                    <td>#<?= htmlspecialchars($return['original_transaction_id']) ?></td>
                                                    <td><?= htmlspecialchars(($return['quantity'] ?? 0)) ?></td>
                                                    <td>₱<?= htmlspecialchars(number_format((float)($return['price'] ?? 0), 2)) ?></td>
                                                    <td class="fw-semibold">₱<?= htmlspecialchars(number_format((float)($return['subtotal'] ?? 0), 2)) ?></td>
                                                    <td><?= htmlspecialchars($return['reason'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($return['refund_method'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($return['return_date'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="returns-empty-filter d-none text-center text-muted py-4">
                <i class="fas fa-magnifying-glass mb-2 d-block" style="font-size:1.4rem;opacity:.4"></i>
                No products match that filter.
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    (function () {
        function getInventoryTable(toolbar) {
            return document.getElementById(toolbar.dataset.tableTarget);
        }

        function getVisibleRows(table) {
            return Array.from(table.querySelectorAll('thead tr, tbody tr')).filter(function (row) {
                return row.style.display !== 'none';
            });
        }

        function tableText(table) {
            return getVisibleRows(table).map(function (row) {
                return Array.from(row.cells).map(function (cell) {
                    return cell.innerText.trim();
                }).join('\t');
            }).join('\n');
        }

        function tableCsv(table) {
            return getVisibleRows(table).map(function (row) {
                return Array.from(row.cells).map(function (cell) {
                    return '"' + cell.innerText.trim().replace(/"/g, '""') + '"';
                }).join(',');
            }).join('\n');
        }

        function printInventoryTable(table, title) {
            const printWindow = window.open('', '_blank', 'width=1100,height=700');
            if (!printWindow) return;
            printWindow.document.write('<!doctype html><html><head><title>' + title + '</title><style>body{font-family:Arial,sans-serif;padding:20px}h2{text-align:center}table{border-collapse:collapse;width:100%}th,td{border:1px solid #999;padding:6px;text-align:left}th{background:#e5e7eb}</style></head><body><h2>' + title + '</h2>' + table.outerHTML + '</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function downloadInventoryCsv(table, filename) {
            const blob = new Blob([tableCsv(table)], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename + '.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        }

        document.querySelectorAll('.inventory-report-toolbar').forEach(function (toolbar) {
            const table = getInventoryTable(toolbar);
            const search = toolbar.querySelector('.inventory-search');
            if (!table) return;

            if (window.jQuery && $.fn.DataTable && !$.fn.DataTable.isDataTable(table)) {
                if (typeof prepareDataTableEmptyState === 'function') {
                    prepareDataTableEmptyState(table);
                }
                $(table).DataTable({
                    responsive: false,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    dom: 'ltip',
                    ordering: false,
                    language: {
                        emptyTable: 'No inventory records found.',
                        zeroRecords: 'No matching inventory records found.',
                        lengthMenu: '_MENU_ per page'
                    }
                });
            }

            search.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                if (window.jQuery && $.fn.DataTable.isDataTable(table)) {
                    $(table).DataTable().search(query).draw();
                } else {
                    table.querySelectorAll('tbody tr').forEach(function (row) {
                        row.style.display = !query || row.innerText.toLowerCase().includes(query) ? '' : 'none';
                    });
                }
            });
            toolbar.querySelector('.inventory-copy').addEventListener('click', function () {
                navigator.clipboard.writeText(tableText(table));
            });
            toolbar.querySelector('.inventory-excel').addEventListener('click', function () {
                downloadInventoryCsv(table, toolbar.dataset.tableTarget);
            });
            toolbar.querySelector('.inventory-pdf').addEventListener('click', function () {
                printInventoryTable(table, toolbar.dataset.tableTarget + ' Report');
            });
            toolbar.querySelector('.inventory-print').addEventListener('click', function () {
                printInventoryTable(table, toolbar.dataset.tableTarget + ' Report');
            });
        });

        const inventoryTabs = document.getElementById('inventoryTabs');
        const inventoryTabTargets = {
            current: 'current-inventory-tab',
            disposed: 'disposed-inventory-tab',
            'no-stock': 'no-stock-tab',
            returned: 'returned-products-tab'
        };
        const inventoryTabParams = new URLSearchParams(window.location.search);
        const requestedInventoryTab = inventoryTabParams.get('inventory_tab');

        if (inventoryTabs && window.bootstrap) {
            inventoryTabs.addEventListener('shown.bs.tab', function (event) {
                const selectedTab = Object.keys(inventoryTabTargets).find(function (key) {
                    return inventoryTabTargets[key] === event.target.id;
                });
                if (!selectedTab) return;

                const url = new URL(window.location.href);
                url.searchParams.set('inventory_tab', selectedTab);
                window.history.replaceState(null, '', url.pathname + url.search + url.hash);
            });

            const requestedTabId = inventoryTabTargets[requestedInventoryTab];
            const requestedTab = requestedTabId ? document.getElementById(requestedTabId) : null;
            if (requestedTab) {
                bootstrap.Tab.getOrCreateInstance(requestedTab).show();
            }
        }

        const alertParams = new URLSearchParams(window.location.search);
        const alertProductId = alertParams.get('alert_product_id');
        const alertBatchId = alertParams.get('alert_batch_id');
        if (alertProductId || alertBatchId) {
            const currentTab = document.getElementById('current-inventory-tab');
            if (currentTab && window.bootstrap) {
                bootstrap.Tab.getOrCreateInstance(currentTab).show();
            }

            const currentTable = document.getElementById('currentInventoryTable');
            if (currentTable && window.jQuery && $.fn.DataTable.isDataTable(currentTable)) {
                const dataTable = $(currentTable).DataTable();
                $(currentTable).on('draw.dt.alertFocus', function () {
                    const selector = alertBatchId
                        ? 'tbody tr[data-batch-id="' + CSS.escape(alertBatchId) + '"]'
                        : 'tbody tr[data-product-id="' + CSS.escape(alertProductId) + '"]';
                    const row = currentTable.querySelector(selector);
                    if (!row) return;
                    row.classList.add('mmb-alert-row-focus');
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(function () { row.classList.remove('mmb-alert-row-focus'); }, 3000);
                    $(currentTable).off('draw.dt.alertFocus');
                });
                const matchingIndexes = [];
                dataTable.rows().every(function () {
                    const row = this.node();
                    const matchesBatch = alertBatchId && row && row.dataset.batchId === alertBatchId;
                    const matchesProduct = !alertBatchId && alertProductId && row && row.dataset.productId === alertProductId;
                    if (matchesBatch || matchesProduct) {
                        matchingIndexes.push(this.index());
                    }
                });
                if (matchingIndexes.length) {
                    const firstIndex = dataTable.rows().indexes().toArray().indexOf(matchingIndexes[0]);
                    dataTable.page(Math.floor(firstIndex / dataTable.page.len())).draw(false);
                }
            }
        }
    }());
</script>

<script>
    /* ── Returned Products: grouped accordion + product dropdown filter ── */
    (function () {
        const accordion = document.getElementById('returnsAccordion');
        if (!accordion) return;

        const filterSelect = document.getElementById('returnsProductFilter');
        const searchInput  = document.getElementById('returnsSearch');
        const emptyNote    = document.querySelector('.returns-empty-filter');
        const groups       = Array.from(accordion.querySelectorAll('.return-group'));

        function visibleGroups() {
            return groups.filter(function (g) { return g.style.display !== 'none'; });
        }

        function applyFilter() {
            const pid   = filterSelect ? filterSelect.value : 'all';
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let shown = 0;

            groups.forEach(function (g) {
                const matchPid  = pid === 'all' || g.dataset.productId === pid;
                const matchText = !query || (g.dataset.searchName || '').includes(query);
                const show = matchPid && matchText;
                g.style.display = show ? '' : 'none';
                if (show) shown++;
            });

            /* picking one product auto-expands it; "all" keeps current state */
            if (pid !== 'all') {
                groups.forEach(function (g) {
                    if (g.dataset.productId === pid) setOpen(g, true);
                });
            }

            if (emptyNote) emptyNote.classList.toggle('d-none', shown > 0);
        }

        function setOpen(group, open) {
            group.classList.toggle('open', open);
            const head = group.querySelector('.return-group-head');
            if (head) head.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        accordion.addEventListener('click', function (e) {
            const head = e.target.closest('.return-group-head');
            if (!head) return;
            const group = head.closest('.return-group');
            setOpen(group, !group.classList.contains('open'));
        });

        if (filterSelect) filterSelect.addEventListener('change', applyFilter);
        if (searchInput) searchInput.addEventListener('input', applyFilter);

        /* ── exports over the visible groups ── */
        function rowsText(delimiter) {
            const lines = [['Product', 'Return Tx', 'Original Tx', 'Qty', 'Price', 'Subtotal', 'Reason', 'Method', 'Returned At'].join(delimiter)];
            visibleGroups().forEach(function (g) {
                const name = (g.querySelector('.rg-title') || {}).textContent || '';
                g.querySelectorAll('.return-detail-table tbody tr').forEach(function (tr) {
                    const cells = Array.from(tr.cells).map(function (c) { return c.innerText.trim(); });
                    const row = [name].concat(cells);
                    lines.push(delimiter === '\t' ? row.join('\t')
                        : row.map(function (v) { return '"' + v.replace(/"/g, '""') + '"'; }).join(','));
                });
            });
            return lines.join('\n');
        }

        function printReturns() {
            const w = window.open('', '_blank', 'width=1100,height=700');
            if (!w) return;
            const body = visibleGroups().map(function (g) {
                const name = (g.querySelector('.rg-title') || {}).textContent || '';
                const sub  = (g.querySelector('.rg-sub') || {}).textContent || '';
                return '<h3>' + name + ' <small>' + sub + '</small></h3>' +
                       '<table>' + g.querySelector('.return-detail-table').outerHTML + '</table>';
            }).join('<div style="page-break-after:always"></div>');
            w.document.write('<!doctype html><html><head><title>Returned Products Report</title><style>' +
                'body{font-family:Arial,sans-serif;padding:20px}h3 small{color:#666;font-weight:400}' +
                'table{border-collapse:collapse;width:100%;margin-bottom:24px}th,td{border:1px solid #999;padding:6px;text-align:left;font-size:.85rem}' +
                'th{background:#f1f5f9}</style></head><body><h2>Returned Products</h2>' + body + '</body></html>');
            w.document.close();
            w.focus();
            w.print();
        }

        const copyBtn = document.querySelector('.returns-copy');
        if (copyBtn) copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(rowsText('\t'));
        });

        const excelBtn = document.querySelector('.returns-excel');
        if (excelBtn) excelBtn.addEventListener('click', function () {
            const blob = new Blob([rowsText(',')], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'returned-products.csv';
            link.click();
            URL.revokeObjectURL(link.href);
        });

        const pdfBtn = document.querySelector('.returns-pdf');
        if (pdfBtn) pdfBtn.addEventListener('click', printReturns);

        const printBtn = document.querySelector('.returns-print');
        if (printBtn) printBtn.addEventListener('click', printReturns);
    })();
</script>

<div class="modal fade" id="addBatchModal" tabindex="-1" aria-labelledby="addBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg add-product-dialog">
        <div class="modal-content add-product-modal">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="addInventoryBatch" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBatchModalLabel">Add Stock Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body add-product-body">
                    <div class="add-product-section-head">
                        <div><span class="add-product-section-kicker">01</span><h6>Product selection</h6></div>
                        <span>Choose the item to stock</span>
                    </div>
                    
                    <div class="add-product-row">
                    <div class="add-product-field">
                        <label for="batch_product_search" class="form-label">Product</label>
                        <div class="batch-product-search-wrap">
                            <input type="text" id="batch_product_search" class="form-control" placeholder="Search product..." autocomplete="off" required>
                            <button type="button" class="batch-product-toggle" aria-label="Show product list">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div id="batch_product_dropdown" class="batch-product-dropdown" aria-live="polite">
                                <?php foreach ($products as $product): ?>
                                    <?php $productLabel = trim(($product['branded_name'] ?? '') . ' ' . ($product['generic_name'] ?? '')); ?>
                                    <button type="button" class="batch-product-option" data-id="<?= (int)($product['id'] ?? 0) ?>" data-label="<?= htmlspecialchars($productLabel, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($productLabel) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" id="batch_product_id" name="product_id">
                    </div>
                    <div class="add-product-field">
                        <label for="batch_supplier_search" class="form-label">Supplier <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="d-flex gap-2 align-items-start">
                            <div class="batch-product-search-wrap flex-grow-1">
                                <input type="text" id="batch_supplier_search" class="form-control" placeholder="Search supplier..." autocomplete="off">
                                <button type="button" class="batch-product-toggle batch-supplier-toggle" aria-label="Show supplier list">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div id="batch_supplier_dropdown" class="batch-product-dropdown" aria-live="polite">
                                    <button type="button" class="batch-product-option batch-supplier-option" data-id="" data-label="Select supplier or leave blank">
                                        Select supplier or leave blank
                                    </button>
                                    <?php 
                                    // Get suppliers from database if available
                                    try {
                                        $chdir_result = chdir(__DIR__ . '/../function');
                                        require_once __DIR__ . '/../conn/database.php';
                                        $db = Database::getConnection();
                                        $stmt = $db->prepare("SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");
                                        $stmt->execute();
                                        $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($suppliers as $supplier):
                                            $supplierName = htmlspecialchars($supplier['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <button type="button" class="batch-product-option batch-supplier-option" data-id="<?= (int)($supplier['id'] ?? 0) ?>" data-label="<?= $supplierName ?>">
                                            <?= $supplierName ?>
                                        </button>
                                    <?php 
                                        endforeach;
                                    } catch (\Exception $e) {
                                        // Suppliers table may not be populated yet
                                    }
                                    ?>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#addSupplierModal" aria-label="Add supplier" title="Add supplier">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                        <select id="batch_supplier_id" name="supplier_id" class="form-select d-none" aria-hidden="true">
                            <option value="">Select supplier or leave blank</option>
                            <?php 
                            try {
                                if (!empty($suppliers)) {
                                    foreach ($suppliers as $supplier):
                            ?>
                                <option value="<?= (int)($supplier['id'] ?? 0) ?>">
                                    <?= htmlspecialchars($supplier['supplier_name'] ?? '') ?>
                                </option>
                            <?php
                                    endforeach;
                                }
                            } catch (\Exception $e) {
                            }
                            ?>
                        </select>
                    </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div><span class="add-product-section-kicker">02</span><h6>Batch details</h6></div>
                        <span>Traceable inventory information</span>
                    </div>
                    <div class="add-product-row">
                    <div class="add-product-field">
                        <label for="batch_number" class="form-label">Batch No</label>
                        <input type="text" id="batch_number" class="form-control" data-next-batch="" placeholder="Select a product first" readonly>
                        <div class="form-text text-muted mt-1">The system will automatically assign the next batch number for this item.</div>
                    </div>
                    <div class="add-product-field">
                        <label for="batch_quantity" class="form-label">Quantity Received</label>
                        <input type="number" id="batch_quantity" name="quantity" class="form-control" min="1" required>
                    </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div><span class="add-product-section-kicker">03</span><h6>Pricing</h6></div>
                        <span>Cost, markup, and selling price</span>
                    </div>
                    <div class="add-product-row add-product-row--three">
                        <div class="add-product-field">
                            <label for="batch_purchase_cost" class="form-label">Purchase Cost per Unit (optional)</label>
                            <input type="number" id="batch_purchase_cost" name="purchase_cost" class="form-control" step="0.01" min="0" placeholder="e.g. 50.00">
                        </div>
                        <div class="add-product-field">
                            <label for="batch_markup" class="form-label">Markup % (optional)</label>
                            <input type="number" id="batch_markup" name="markup" class="form-control" step="0.01" min="0" value="5" placeholder="e.g. 20">
                        </div>
                        <div class="add-product-field">
                            <label for="batch_sale_price" class="form-label">Sale Price per Unit (optional)</label>
                            <input type="number" id="batch_sale_price" name="sale_price" class="form-control" step="0.01" min="0" placeholder="e.g. 75.00">
                        </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div><span class="add-product-section-kicker">04</span><h6>Expiry</h6></div>
                        <span>Product shelf-life information</span>
                    </div>
                    <div class="add-product-row add-product-row--single">
                    <div class="add-product-field add-product-field--full">
                        <label for="batch_expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" id="batch_expiry_date" name="expiry_date" class="form-control">
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .batch-product-search-wrap {
        position: relative;
    }

    .batch-product-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #5a6471;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .batch-product-toggle:hover {
        background: #eef3f9;
    }

    #batch_product_search {
        padding-right: 38px;
    }

    .batch-product-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 1060;
        display: none;
        max-height: 240px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #dfe6ee;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
    }

    .batch-product-dropdown.show {
        display: block;
    }

    .batch-product-option {
        display: block;
        width: 100%;
        border: none;
        background: transparent;
        text-align: left;
        padding: 10px 12px;
        font-size: 0.95rem;
        color: #1f2d3d;
    }

    .batch-product-option:hover,
    .batch-product-option.active {
        background: #edf4ff;
    }
</style>

<script>
    function attachBatchPriceCalculator(modalSelector) {
        const modal = document.querySelector(modalSelector);
        if (!modal) return;

        const purchaseCostInput = modal.querySelector('#batch_purchase_cost');
        const markupInput = modal.querySelector('#batch_markup');
        const salePriceInput = modal.querySelector('#batch_sale_price');

        if (!purchaseCostInput || !markupInput || !salePriceInput) {
            return;
        }

        let salePriceLockedToMarkup = true;

        const updateSalePrice = () => {
            if (!salePriceLockedToMarkup) {
                return;
            }

            const purchaseCost = parseFloat(purchaseCostInput.value);
            const markup = parseFloat(markupInput.value);

            if (!Number.isFinite(purchaseCost) || purchaseCost < 0) {
                salePriceInput.value = '';
                return;
            }

            const safeMarkup = Number.isFinite(markup) && markup >= 0 ? markup : 0;
            const computedSalePrice = purchaseCost * (1 + safeMarkup / 100);
            salePriceInput.value = computedSalePrice.toFixed(2);
        };

        salePriceInput.addEventListener('input', function () {
            salePriceLockedToMarkup = false;
        });

        salePriceInput.addEventListener('focus', function () {
            if (!this.value) {
                salePriceLockedToMarkup = true;
            }
        });

        if (!markupInput.value) {
            markupInput.value = '5';
        }

        purchaseCostInput.oninput = function () {
            if (salePriceLockedToMarkup) {
                updateSalePrice();
            }
        };

        markupInput.oninput = function () {
            if (salePriceLockedToMarkup) {
                updateSalePrice();
            }
        };

        salePriceInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                salePriceLockedToMarkup = false;
            }
        });

        updateSalePrice();
    }

    document.addEventListener('DOMContentLoaded', function () {
        function findMatchingInventoryOption(select, batchId, batchNumber, productName) {
            if (!select) return null;

            const batchIdValue = batchId ? String(batchId) : '';
            const normalizedBatchNumber = (batchNumber || '').trim().toLowerCase();
            const normalizedProductName = (productName || '').trim().toLowerCase();

            for (const option of select.options) {
                const optionValue = (option.value || '').toString();
                const optionText = (option.textContent || '').trim().toLowerCase();

                if (batchIdValue && optionValue === batchIdValue) {
                    return option;
                }

                const hasBatchMatch = normalizedBatchNumber && optionText.includes(normalizedBatchNumber);
                const hasProductMatch = normalizedProductName && optionText.includes(normalizedProductName);
                if (hasBatchMatch && hasProductMatch) {
                    return option;
                }
            }

            return null;
        }

        // Inline onclick handlers are used for inventory row action buttons to ensure the
        // exact batch selection and modal opening happen reliably in the browser.

        const disposeBatchSelect = document.getElementById('dispose_inventory_id');
        const disposeQuantityInput = document.getElementById('dispose_quantity');
        const disposeQuantityHelp = document.getElementById('dispose_quantity_help');

        if (disposeBatchSelect && disposeQuantityInput) {
            disposeBatchSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const availableQuantity = parseInt(selectedOption?.dataset.currentQuantity || '0', 10);
                const safeQuantity = Number.isFinite(availableQuantity) ? Math.max(0, availableQuantity) : 0;

                disposeQuantityInput.value = String(safeQuantity);
                disposeQuantityInput.max = String(safeQuantity);
                disposeQuantityInput.disabled = false;
                disposeQuantityInput.setCustomValidity('');

                if (disposeQuantityHelp) {
                    disposeQuantityHelp.textContent = safeQuantity > 0
                        ? `Maximum quantity to dispose: ${safeQuantity}`
                        : 'This batch has no available stock. You can still record a 0-unit disposal for this empty batch.';
                }
            });

            disposeQuantityInput.addEventListener('input', function () {
                const maximum = parseInt(this.max || '0', 10);
                const enteredQuantity = parseInt(this.value || '0', 10);
                const safeMax = Number.isFinite(maximum) ? maximum : 0;
                this.setCustomValidity(enteredQuantity > safeMax ? `Quantity cannot exceed ${safeMax}.` : '');
            });
        }

        // Dispose confirmation — manual archive action for expired or unusable stock.
        const disposeForm = document.querySelector('#disposeBatchModal form');
        if (disposeForm) {
            disposeForm.addEventListener('submit', function (event) {
                if (disposeForm.dataset.confirmed === '1') return;
                event.preventDefault();

                const select = document.getElementById('dispose_inventory_id');
                const qtyInput = document.getElementById('dispose_quantity');
                const qty = qtyInput ? qtyInput.value : '?';
                const batchLabel = (select && select.selectedIndex > 0)
                    ? select.options[select.selectedIndex].text
                    : 'the selected batch';
                const reasonInput = document.getElementById('dispose_reason');
                const reason = reasonInput && reasonInput.value.trim() ? reasonInput.value.trim() : 'Disposed';

                mmbConfirm({
                    title: 'Move expired or unusable batch to disposed history?',
                    message: 'You are manually moving ' + batchLabel + ' into the Disposed / Expired archive.\n\nQuantity to record: ' + qty + ' unit(s).\nReason: ' + reason + '.\n\nThis batch will be archived and removed from active inventory. This action is permanent and should only be used for expired, damaged, or unusable stock.',
                    okLabel: 'Yes, move to disposed',
                    danger: true
                }).then(function (yes) {
                    if (!yes) return;
                    disposeForm.dataset.confirmed = '1';
                    disposeForm.submit();
                });
            });
        }

        const noStockForm = document.querySelector('#moveNoStockModal form');
        if (noStockForm) {
            noStockForm.addEventListener('submit', function (event) {
                if (noStockForm.dataset.confirmed === '1') return;
                event.preventDefault();

                const select = document.getElementById('move_no_stock_inventory_id');
                const batchLabel = (select && select.selectedIndex > 0)
                    ? select.options[select.selectedIndex].text
                    : 'the selected batch';

                mmbConfirm({
                    title: 'Move empty batch to No Stock history?',
                    message: 'You are manually archiving ' + batchLabel + ' as a zero-stock batch.\n\nThis batch is empty and will be moved to No Stock history. It will no longer appear in active inventory and should only be used when the product has no remaining stock.',
                    okLabel: 'Yes, move to No Stock',
                    danger: false
                }).then(function (yes) {
                    if (!yes) return;
                    noStockForm.dataset.confirmed = '1';
                    noStockForm.submit();
                });
            });
        }

        // Check if we need to reopen the Add Batch modal after refresh
        if (sessionStorage.getItem('reopenAddBatchModal') === 'true') {
            sessionStorage.removeItem('reopenAddBatchModal');
            const selectedSupplierId = sessionStorage.getItem('selectedSupplierId');
            if (selectedSupplierId) {
                sessionStorage.removeItem('selectedSupplierId');
            }

            setTimeout(() => {
                const addBatchModal = document.getElementById('addBatchModal');
                if (addBatchModal) {
                    const modal = new bootstrap.Modal(addBatchModal);
                    modal.show();
                    
                    // Auto-select the supplier if available
                    if (selectedSupplierId) {
                        const supplierSelect = document.getElementById('batch_supplier_id');
                        if (supplierSelect) {
                            supplierSelect.value = selectedSupplierId;
                        }
                    }
                }
            }, 300);
        }

        attachBatchPriceCalculator('#addBatchModal');

        const productSearch = document.getElementById('batch_product_search');
        const productIdInput = document.getElementById('batch_product_id');
        const batchNumberInput = document.getElementById('batch_number');
        const productDropdown = document.getElementById('batch_product_dropdown');
        const productToggle = document.querySelector('.batch-product-toggle');
        const supplierSearch = document.getElementById('batch_supplier_search');
        const supplierIdSelect = document.getElementById('batch_supplier_id');
        const supplierDropdown = document.getElementById('batch_supplier_dropdown');
        const supplierToggle = document.querySelector('.batch-supplier-toggle');
        const inventoryBatches = <?= json_encode($inventoryBatches, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const inventoryBatchSequence = <?= json_encode($inventoryBatchSequence, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

        if (supplierSearch && supplierIdSelect && supplierDropdown) {
            const supplierOptions = Array.from(supplierDropdown.querySelectorAll('.batch-supplier-option'));

            const applySupplierDropdownState = () => {
                const query = (supplierSearch.value || '').trim().toLowerCase();
                let visibleCount = 0;

                supplierOptions.forEach((option) => {
                    const label = (option.dataset.label || '').trim().toLowerCase();
                    const showOption = !query || label.includes(query);
                    option.style.display = showOption ? 'block' : 'none';
                    if (showOption) visibleCount++;
                });

                if (visibleCount === 0 && supplierSearch.value.trim() !== '') {
                    supplierDropdown.classList.add('show');
                }
            };

            const setSelectedSupplier = (selectedOption) => {
                if (!selectedOption) {
                    supplierSearch.value = '';
                    supplierIdSelect.value = '';
                    return;
                }

                const supplierId = selectedOption.dataset.id || '';
                const supplierLabel = selectedOption.dataset.label || selectedOption.textContent.trim();
                supplierSearch.value = supplierLabel === 'Select supplier or leave blank' ? '' : supplierLabel;
                supplierIdSelect.value = supplierId;
                supplierDropdown.classList.remove('show');
                supplierOptions.forEach((option) => option.classList.toggle('active', option === selectedOption));
            };

            supplierSearch.addEventListener('focus', function () {
                applySupplierDropdownState();
                supplierDropdown.classList.add('show');
            });

            supplierSearch.addEventListener('input', function () {
                applySupplierDropdownState();
                supplierDropdown.classList.add('show');

                if (this.value.trim() === '') {
                    supplierIdSelect.value = '';
                    return;
                }

                const matched = supplierOptions.find((option) => (option.dataset.label || '').trim().toLowerCase() === this.value.trim().toLowerCase());
                if (matched) {
                    setSelectedSupplier(matched);
                } else {
                    supplierIdSelect.value = '';
                }
            });

            supplierOptions.forEach((option) => {
                option.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    setSelectedSupplier(option);
                });
            });

            if (supplierToggle) {
                supplierToggle.addEventListener('click', function () {
                    const isVisible = supplierDropdown.classList.contains('show');
                    supplierDropdown.classList.toggle('show', !isVisible);
                    if (!isVisible) supplierSearch.focus();
                });
            }

            document.addEventListener('click', function (event) {
                const inputWrap = supplierSearch.closest('.batch-product-search-wrap');
                if (!inputWrap || !inputWrap.contains(event.target)) {
                    supplierDropdown.classList.remove('show');
                }
            });
        }

        const showNextBatchNumber = (productId) => {
            if (!batchNumberInput) return;

            const selectedProductId = Number(productId || 0);
            if (selectedProductId <= 0) {
                batchNumberInput.value = '';
                batchNumberInput.placeholder = 'Select a product first';
                return;
            }

            let highestBatchNumber = 0;
            inventoryBatchSequence.forEach((batch) => {
                if (Number(batch.product_id) !== selectedProductId) return;

                const match = String(batch.batch_number || '').trim().match(/^batch-(\d+)$/i);
                if (match) {
                    highestBatchNumber = Math.max(highestBatchNumber, Number(match[1]));
                }
            });

            batchNumberInput.value = `Batch-${highestBatchNumber + 1}`;
            batchNumberInput.placeholder = 'Automatically generated';
        };

        const applyProductDropdownState = () => {
            if (!productDropdown) return;
            const query = (productSearch?.value || '').trim().toLowerCase();
            const productOptions = Array.from(productDropdown.querySelectorAll('.batch-product-option'));
            let visibleCount = 0;

            productOptions.forEach((option) => {
                const label = (option.dataset.label || '').trim().toLowerCase();
                const showOption = !query || label.includes(query);
                option.style.display = showOption ? 'block' : 'none';
                if (showOption) visibleCount++;
            });

            if (visibleCount === 0 && productSearch && productSearch.value.trim() !== '') {
                productDropdown.classList.add('show');
            }
        };

        if (productSearch && productIdInput && productDropdown) {
            const form = productSearch.closest('form');
            const productOptions = Array.from(productDropdown.querySelectorAll('.batch-product-option'));

            const setSelectedProduct = (selectedOption) => {
                if (!selectedOption) return;

                const productId = selectedOption.dataset.id || '';
                const productLabel = selectedOption.dataset.label || selectedOption.textContent.trim();

                productSearch.value = productLabel;
                productIdInput.value = productId;
                productSearch.setCustomValidity('');
                showNextBatchNumber(productId);
                productDropdown.classList.remove('show');
                productOptions.forEach((option) => option.classList.toggle('active', option === selectedOption));
            };

            const validateProductSelection = () => {
                const value = productSearch.value.trim();
                const matched = productOptions.find((option) => (option.dataset.label || '').trim().toLowerCase() === value.toLowerCase());

                if (matched) {
                    setSelectedProduct(matched);
                    return true;
                }

                productIdInput.value = '';
                showNextBatchNumber('');
                productSearch.setCustomValidity('Please select a product from the list.');
                return false;
            };

            productSearch.addEventListener('focus', function () {
                applyProductDropdownState();
                productDropdown.classList.add('show');
            });

            productSearch.addEventListener('input', function () {
                applyProductDropdownState();
                productDropdown.classList.add('show');

                if (this.value.trim() === '') {
                    productIdInput.value = '';
                    showNextBatchNumber('');
                    productSearch.setCustomValidity('Please select a product from the list.');
                    return;
                }

                const matched = productOptions.find((option) => (option.dataset.label || '').trim().toLowerCase() === this.value.trim().toLowerCase());
                if (matched) {
                    setSelectedProduct(matched);
                } else {
                    productIdInput.value = '';
                    showNextBatchNumber('');
                    productSearch.setCustomValidity('Please select a product from the list.');
                }
            });

            productOptions.forEach((option) => {
                option.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    setSelectedProduct(option);
                });
            });

            if (productToggle) {
                productToggle.addEventListener('click', function () {
                    const isVisible = productDropdown.classList.contains('show');
                    productDropdown.classList.toggle('show', !isVisible);
                    if (!isVisible) {
                        productSearch.focus();
                    }
                });
            }

            document.addEventListener('click', function (event) {
                const inputWrap = productSearch.closest('.batch-product-search-wrap');
                if (!inputWrap || !inputWrap.contains(event.target)) {
                    productDropdown.classList.remove('show');
                }
            });

            productSearch.addEventListener('change', validateProductSelection);

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!validateProductSelection()) {
                        event.preventDefault();
                        productSearch.reportValidity();
                        return;
                    }

                    // Check for duplicate batch number in same product
                    const productId = parseInt(document.getElementById('batch_product_id').value);
                    const batchNumber = document.getElementById('batch_number').value.trim();
                        
                    if (productId > 0 && batchNumber !== '') {
                        const existingBatch = <?php echo json_encode($inventoryBatches); ?>.find(batch => 
                            parseInt(batch.product_id) === productId && 
                            batch.batch_number.trim().toLowerCase() === batchNumber.toLowerCase()
                        );

                        if (existingBatch) {
                            event.preventDefault();
                            mmbNotify({
                                type: 'warning',
                                title: 'Duplicate batch number',
                                message: 'Batch number "' + batchNumber + '" already exists for this product.'
                            });
                            document.getElementById('batch_number').focus();
                        }
                    }
                });
            }
        }
    });

    // ADD SUPPLIER FORM HANDLER
    document.addEventListener('DOMContentLoaded', function () {
        const supplierForm = document.getElementById('addSupplierForm');
        const supplierModal = document.getElementById('addSupplierModal');

        if (!supplierForm || !supplierModal) return;

        // Contact number: digits only (optional leading +) — issue #4 item 5
        const contactInput = document.getElementById('supplier_contact_number');
        if (contactInput) {
            contactInput.addEventListener('input', function () {
                var raw = this.value.replace(/[^0-9+]/g, '');
                var digits = raw.replace(/\+/g, '');
                this.value = (raw.charAt(0) === '+' ? '+' : '') + digits;
            });
        }

        supplierForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const supplierName = document.getElementById('supplier_name').value.trim();
            const contactNumber = contactInput ? contactInput.value.trim() : '';
            const email = document.getElementById('supplier_email').value.trim();
            const address = document.getElementById('supplier_address').value.trim();
            const submitBtn = supplierForm.querySelector('button[type="submit"]');

            if (!supplierName) {
                mmbNotify({ type: 'warning', title: 'Supplier name required', message: 'Please enter the supplier name before saving.' });
                return;
            }
            if (contactNumber && !/^\+?[0-9]{7,15}$/.test(contactNumber)) {
                mmbNotify({ type: 'warning', title: 'Invalid contact number', message: 'Contact numbers accept digits only (7-15 digits, optional leading +).' });
                contactInput.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding...';

            const formData = new FormData();
            formData.append('supplier_name', supplierName);
            formData.append('contact_number', contactNumber);
            formData.append('email', email);
            formData.append('address', address);

            fetch('../function/add_supplier_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Invalid response format');
                }
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(({ status, data }) => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Supplier';

                if (status === 200) {
                    mmbNotify({
                        type: 'success',
                        title: 'Supplier added',
                        message: '"' + data.supplier_name + '" saved. Reopening Add Batch…',
                        duration: 3000
                    });

                    // Store flag and supplier ID to reopen modal after refresh
                    sessionStorage.setItem('reopenAddBatchModal', 'true');
                    sessionStorage.setItem('selectedSupplierId', data.supplier_id);

                    // Redirect to inventory page
                    setTimeout(function () {
                        window.location.href = 'dashboard.php?tab=inventory';
                    }, 1200);
                } else if (status === 409) {
                    // Duplicate found
                    mmbNotify({
                        type: 'warning',
                        title: 'Supplier already exists',
                        message: data.message || 'A supplier with this name is already on file.',
                        duration: 3000
                    });

                    // Store flag and supplier ID to reopen modal after refresh
                    sessionStorage.setItem('reopenAddBatchModal', 'true');
                    sessionStorage.setItem('selectedSupplierId', data.existing_id);

                    // Redirect to inventory page
                    setTimeout(function () {
                        window.location.href = 'dashboard.php?tab=inventory';
                    }, 1200);
                } else {
                    mmbNotify({ type: 'danger', title: 'Could not add supplier', message: data.error || 'Failed to add supplier' });
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Supplier';
                console.error('Error:', error);
                mmbNotify({ type: 'danger', title: 'Network error', message: error.message });
            });
        });
    });
</script>

<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addSupplierForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="supplier_name" placeholder="e.g., ABC Pharma Ltd" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_contact_number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="supplier_contact_number" inputmode="numeric"
                               pattern="\+?[0-9]{7,15}" placeholder="e.g., 09181234567" autocomplete="tel">
                        <div class="form-text">Digits only (an optional leading + is allowed).</div>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="supplier_email" placeholder="e.g., contact@supplier.com">
                    </div>
                    <div class="mb-3">
                        <label for="supplier_address" class="form-label">Address</label>
                        <textarea class="form-control" id="supplier_address" rows="3" placeholder="Enter supplier address"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="moveNoStockModal" tabindex="-1" aria-labelledby="moveNoStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="../function/move_no_stock.php">
                <input type="hidden" name="moveNoStockBatch" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveNoStockModalLabel">Move Zero-Stock Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="move_no_stock_inventory_id" class="form-label">Batch with no stock</label>
                        <select id="move_no_stock_inventory_id" name="inventory_id" class="form-select" required>
                            <option value="">Select batch</option>
                            <?php foreach ($inventoryBatches as $batch): ?>
                                <?php if ((int)($batch['current_quantity'] ?? 0) <= 0): ?>
                                    <option value="<?= (int)($batch['id'] ?? 0) ?>">
                                        <?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? ''))) ?> - Qty: 0 - Batch: <?= htmlspecialchars($batch['batch_number'] ?: 'N/A') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-warning small mb-0">
                        Manual archive action: this selected empty batch will be moved to the No Stock history and will no longer appear in active inventory.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Move to No Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="disposeBatchModal" tabindex="-1" aria-labelledby="disposeBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="disposeInventoryBatch" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="disposeBatchModalLabel">Dispose Inventory Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dispose_inventory_id" class="form-label">Batch</label>
                        <select id="dispose_inventory_id" name="inventory_id" class="form-select" required>
                            <option value="">Select batch</option>
                            <?php foreach ($inventoryBatches as $batch): ?>
                                <option value="<?= (int)($batch['id'] ?? 0) ?>" data-current-quantity="<?= (int)($batch['current_quantity'] ?? 0) ?>">
                                    <?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? ''))) ?> - Qty: <?= (int)($batch['current_quantity'] ?? 0) ?> - Batch: <?= htmlspecialchars($batch['batch_number'] ?: 'N/A') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dispose_quantity" class="form-label">Quantity to Dispose</label>
                        <input type="number" id="dispose_quantity" name="quantity" class="form-control" min="0" step="1" value="0" required>
                        <div id="dispose_quantity_help" class="form-text">Select a batch first.</div>
                    </div>
                    <div class="mb-3">
                        <label for="dispose_reason" class="form-label">Reason</label>
                        <input type="text" id="dispose_reason" name="reason" class="form-control" placeholder="e.g. Expired, Damaged" value="Expired" required>
                    </div>
                    <div class="mb-3">
                        <label for="disposal_proof" class="form-label">Proof Image</label>
                        <input type="file" id="disposal_proof" name="disposal_proof" class="form-control" accept="image/jpeg,image/png" required>
                        <div class="form-text">Upload a JPG or PNG photo showing why this product must be disposed. Maximum 5 MB.</div>
                    </div>
                    <div class="alert alert-danger small mb-0">
                        Manual archive action: this batch will be moved to Disposed / Expired history. Use this only for expired, damaged, or otherwise unusable stock.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Dispose</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="disposalProofModal" tabindex="-1" aria-labelledby="disposalProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disposalProofModalLabel">Disposal proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="disposalProofImage" src="" alt="Disposal proof" class="img-fluid rounded" style="max-height:70vh; object-fit:contain;">
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const proofModal = document.getElementById('disposalProofModal');
        const proofImage = document.getElementById('disposalProofImage');
        const proofTitle = document.getElementById('disposalProofModalLabel');
        if (!proofModal || !proofImage || !proofTitle) return;

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.disposal-proof-view-btn');
            if (!button) return;

            proofImage.src = button.getAttribute('data-proof-image') || '';
            proofImage.alt = button.getAttribute('data-proof-title') || 'Disposal proof';
            proofTitle.textContent = button.getAttribute('data-proof-title') || 'Disposal proof';
        });

        proofModal.addEventListener('hidden.bs.modal', function () {
            proofImage.removeAttribute('src');
        });
    }());
</script>
