<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
require_once __DIR__ . "/../function/addprodfunct.php";

use Classes\ProductManagement;

$inventoryManager = new ProductManagement($db);
$inventoryBatches = $inventoryManager->getAllInventoryBatches();
$disposedBatches = $inventoryManager->getDisposedBatches();
$returnedProducts = $inventoryManager->getReturnedProducts();
$products = $inventoryManager->getAllProducts();

$inventoryErrorMessage = '';
if ($inventoryManager->addInventoryBatch()) {
    echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php?tab=inventory&success=1'; }, 10);</script>";
    exit;
} elseif (isset($_POST['addInventoryBatch'])) {
    $inventoryErrorMessage = $inventoryManager->getResponse() ?: 'Unable to add stock batch.';
}

if ($inventoryManager->disposeInventoryBatch()) {
    echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php?tab=inventory&success=disposed'; }, 10);</script>";
    exit;
}

$inventorySuccessMessage = '';
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $inventorySuccessMessage = 'New batch added successfully.';
} elseif (isset($_GET['success']) && $_GET['success'] === 'disposed') {
    $inventorySuccessMessage = 'Inventory batch disposed successfully.';
}
?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php if ($inventorySuccessMessage !== ''): ?>
            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div><?= htmlspecialchars($inventorySuccessMessage) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($inventoryErrorMessage !== ''): ?>
            <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div><?= htmlspecialchars($inventoryErrorMessage) ?></div>
            </div>
        <?php endif; ?>
        <div class="page-head">
            <div>
                <h4>Inventory Management</h4>
                <p class="page-sub">Manage current stock batches and review disposed or expired inventory.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disposeBatchModal">
                    <i class="fas fa-trash me-1"></i> Dispose Batch
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                    <i class="fas fa-plus me-1"></i> Add Batch
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="current-inventory-tab" data-bs-toggle="tab" data-bs-target="#current-inventory-pane" type="button" role="tab" aria-controls="current-inventory-pane" aria-selected="true">
                    <i class="fas fa-boxes-stacked me-1"></i> Current Inventory
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="disposed-inventory-tab" data-bs-toggle="tab" data-bs-target="#disposed-inventory-pane" type="button" role="tab" aria-controls="disposed-inventory-pane" aria-selected="false">
                    <i class="fas fa-trash me-1"></i> Disposed / Expired
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="returned-products-tab" data-bs-toggle="tab" data-bs-target="#returned-products-pane" type="button" role="tab" aria-controls="returned-products-pane" aria-selected="false">
                    <i class="fas fa-rotate-left me-1"></i> Returned Products
                </button>
            </li>
        </ul>

        <div class="tab-content" id="inventoryTabContent">
            <div class="tab-pane fade show active" id="current-inventory-pane" role="tabpanel" aria-labelledby="current-inventory-tab" tabindex="0">
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="currentInventoryTable">
                <label class="mb-0" for="currentInventorySearch">Search:</label>
                <input type="search" id="currentInventorySearch" class="form-control form-control-sm inventory-search" placeholder="Search current inventory..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy"><i class="fas fa-copy me-1"></i>Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print"><i class="fas fa-print me-1"></i>Print</button>
            </div>
            <div class="table-responsive mb-4">
            <table id="currentInventoryTable" class="table table-striped table-hover align-middle w-100 mmb-stack">
                <thead class="table-dark">
                    <tr>
                        <th>Batch ID</th>
                        <th>Batch No.</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Product Code</th>
                        <th>Supplier Name</th>
                        <th>Original Quantity</th>
                        <th>Current Quantity</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventoryBatches)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No inventory batches recorded yet — add one with the “Add Batch” button.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inventoryBatches as $batch): ?>
                            <?php
                                $batchNumber = trim((string) ($batch['batch_number'] ?? ''));
                                if ($batchNumber === '') {
                                    continue;
                                }
                            ?>
                            <tr>
                                <td data-label="Batch ID"><?= htmlspecialchars($batch['id']) ?></td>
                                <td data-label="Batch No."><?= htmlspecialchars($batchNumber) ?></td>
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
                                <td data-label="Category"><?= htmlspecialchars($batch['category_name'] ?? 'N/A') ?></td>
                                <td data-label="Product Code"><?= htmlspecialchars($batch['barcode'] ?? 'N/A') ?></td>
                                <td data-label="Supplier"><?= htmlspecialchars($batch['supplier_name'] ?? 'N/A') ?></td>
                                <td data-label="Original Qty"><?= htmlspecialchars((string) ($batch['received_quantity'] ?? 0)) ?></td>
                                <td data-label="Current Qty"><?= htmlspecialchars((string) ($batch['current_quantity'] ?? 0)) ?></td>
                                <td data-label="Expiry"><?= htmlspecialchars($batch['expiry_date'] ?: 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

            </div>

            <div class="tab-pane fade" id="disposed-inventory-pane" role="tabpanel" aria-labelledby="disposed-inventory-tab" tabindex="0">
            <h5 class="mb-3">Disposed / Expired Inventory</h5>
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="disposedInventoryTable">
                <label class="mb-0" for="disposedInventorySearch">Search:</label>
                <input type="search" id="disposedInventorySearch" class="form-control form-control-sm inventory-search" placeholder="Search disposed inventory..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy"><i class="fas fa-copy me-1"></i>Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print"><i class="fas fa-print me-1"></i>Print</button>
            </div>
            <div class="table-responsive">
            <table id="disposedInventoryTable" class="table table-sm table-bordered align-middle w-100 mmb-stack">
                <thead class="table-secondary">
                    <tr>
                        <th>Dispose ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Expiry</th>
                        <th>Reason</th>
                        <th>Disposed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($disposedBatches)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No disposed or expired inventory found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disposedBatches as $disposed): ?>
                            <tr>
                                <td data-label="Dispose ID"><?= htmlspecialchars($disposed['id']) ?></td>
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
                                <td data-label="Disposed At"><?= htmlspecialchars($disposed['disposed_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

            </div>

            <div class="tab-pane fade" id="returned-products-pane" role="tabpanel" aria-labelledby="returned-products-tab" tabindex="0">
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
                <button type="button" class="btn btn-sm btn-secondary returns-copy"><i class="fas fa-copy me-1"></i>Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary returns-excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-sm btn-danger returns-pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark returns-print"><i class="fas fa-print me-1"></i>Print</button>
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

            search.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    row.style.display = !query || row.innerText.toLowerCase().includes(query) ? '' : 'none';
                });
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
            <form method="POST">
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
                        <input type="text" id="batch_product_search" class="form-control" placeholder="Search product..." list="batch_product_list" autocomplete="off" required>
                        <input type="hidden" id="batch_product_id" name="product_id">
                        <datalist id="batch_product_list">
                            <?php foreach ($products as $product): ?>
                                <option value="<?= htmlspecialchars(trim(($product['branded_name'] ?? '') . ' ' . ($product['generic_name'] ?? ''))) ?>" data-id="<?= (int)($product['id'] ?? 0) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="add-product-field">
                        <label for="batch_supplier_id" class="form-label">Supplier <span class="text-muted fw-normal">(optional)</span></label>
                        <div class="d-flex gap-2">
                            <select id="batch_supplier_id" name="supplier_id" class="form-select">
                                <option value="">Select supplier or leave blank</option>
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
                                ?>
                                    <option value="<?= (int)($supplier['id'] ?? 0) ?>">
                                        <?= htmlspecialchars($supplier['supplier_name'] ?? '') ?>
                                    </option>
                                <?php 
                                    endforeach;
                                } catch (\Exception $e) {
                                    // Suppliers table may not be populated yet
                                }
                                ?>
                            </select>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div><span class="add-product-section-kicker">02</span><h6>Batch details</h6></div>
                        <span>Traceable inventory information</span>
                    </div>
                    <div class="add-product-row">
                    <div class="add-product-field">
                        <label for="batch_number" class="form-label">Batch No</label>
                        <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="e.g. BATCH-001" required>
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
                            <input type="number" id="batch_sale_price" name="sale_price" class="form-control" step="0.01" min="0" placeholder="e.g. 75.00" readonly>
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

        const updateSalePrice = () => {
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

        if (!markupInput.value) {
            markupInput.value = '5';
        }

        purchaseCostInput.oninput = updateSalePrice;
        markupInput.oninput = updateSalePrice;
        updateSalePrice();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const disposeBatchSelect = document.getElementById('dispose_inventory_id');
        const disposeQuantityInput = document.getElementById('dispose_quantity');
        const disposeQuantityHelp = document.getElementById('dispose_quantity_help');

        if (disposeBatchSelect && disposeQuantityInput) {
            disposeBatchSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const availableQuantity = parseInt(selectedOption?.dataset.currentQuantity || '0', 10);

                disposeQuantityInput.value = '';
                disposeQuantityInput.max = String(Math.max(0, availableQuantity));
                disposeQuantityInput.disabled = availableQuantity <= 0;
                disposeQuantityInput.setCustomValidity('');

                if (disposeQuantityHelp) {
                    disposeQuantityHelp.textContent = availableQuantity > 0
                        ? `Maximum quantity to dispose: ${availableQuantity}`
                        : 'This batch has no available stock.';
                }
            });

            disposeQuantityInput.addEventListener('input', function () {
                const maximum = parseInt(this.max || '0', 10);
                const enteredQuantity = parseInt(this.value || '0', 10);
                this.setCustomValidity(enteredQuantity > maximum ? `Quantity cannot exceed ${maximum}.` : '');
            });
        }

        // Dispose confirmation (issue #4 item 6) — branded dialog instead of
        // a browser confirm(); applies to the Dispose Batch modal submit.
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

                mmbConfirm({
                    title: 'Dispose inventory?',
                    message: 'You are about to dispose ' + qty + ' unit(s) of ' + batchLabel + '.\n\nDisposed stock is permanently removed and cannot be restored.',
                    okLabel: 'Yes, dispose',
                    danger: true
                }).then(function (yes) {
                    if (!yes) return;
                    disposeForm.dataset.confirmed = '1';
                    disposeForm.submit();
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

        if (productSearch && productIdInput) {
            const form = productSearch.closest('form');
            const productOptions = Array.from(document.querySelectorAll('#batch_product_list option'));

            const validateProductSelection = () => {
                const value = productSearch.value.trim();
                const matched = productOptions.find((option) => option.value.trim() === value);

                if (matched) {
                    productIdInput.value = matched.getAttribute('data-id') || '';
                    productSearch.setCustomValidity('');
                    return true;
                }

                productIdInput.value = '';
                productSearch.setCustomValidity('Please select a product from the list.');
                return false;
            };

            productSearch.addEventListener('input', function () {
                if (this.value.trim() === '') {
                    productIdInput.value = '';
                    productSearch.setCustomValidity('Please select a product from the list.');
                    return;
                }

                const matched = productOptions.find((option) => option.value.trim().toLowerCase() === this.value.trim().toLowerCase());
                if (matched) {
                    productIdInput.value = matched.getAttribute('data-id') || '';
                    productSearch.setCustomValidity('');
                } else {
                    productIdInput.value = '';
                    productSearch.setCustomValidity('Please select a product from the list.');
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
                        <i class="fas fa-plus"></i> Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="disposeBatchModal" tabindex="-1" aria-labelledby="disposeBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
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
                        <input type="number" id="dispose_quantity" name="quantity" class="form-control" min="1" required disabled>
                        <div id="dispose_quantity_help" class="form-text">Select a batch first.</div>
                    </div>
                    <div class="mb-3">
                        <label for="dispose_reason" class="form-label">Reason</label>
                        <input type="text" id="dispose_reason" name="reason" class="form-control" placeholder="e.g. Expired, Damaged" value="Expired">
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
