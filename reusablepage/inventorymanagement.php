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
            <table id="currentInventoryTable" class="table table-striped table-hover align-middle w-100">
                <thead class="table-dark">
                    <tr>
                        <th>Batch ID</th>
                        <th>Batch No.</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Barcode</th>
                        <th>Supplier Name</th>
                        <th>Original Quantity</th>
                        <th>Current Quantity</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventoryBatches)): ?>
                        <?php // DataTables renders the empty-table message. ?>
                    <?php else: ?>
                        <?php foreach ($inventoryBatches as $batch): ?>
                            <?php
                                $batchNumber = trim((string) ($batch['batch_number'] ?? ''));
                                if ($batchNumber === '') {
                                    continue;
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($batch['id']) ?></td>
                                <td><?= htmlspecialchars($batchNumber) ?></td>
                                <td><?= htmlspecialchars(trim(($batch['branded_name'] ?? '') . ' ' . ($batch['generic_name'] ?? '') . ' ' . ($batch['strength'] ?? '') . ' ' . ($batch['measurement_name'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($batch['category_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($batch['barcode'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($batch['supplier_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars((string) ($batch['received_quantity'] ?? 0)) ?></td>
                                <td><?= htmlspecialchars((string) ($batch['current_quantity'] ?? 0)) ?></td>
                                <td><?= htmlspecialchars($batch['expiry_date'] ?: 'N/A') ?></td>
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
            <table id="disposedInventoryTable" class="table table-sm table-bordered align-middle w-100">
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
                            <td class="text-center">No disposed or expired inventory found.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disposedBatches as $disposed): ?>
                            <tr>
                                <td><?= htmlspecialchars($disposed['id']) ?></td>
                                <td><?= htmlspecialchars(trim(implode(' ', array_filter([
                                    $disposed['branded_name'] ?? '',
                                    $disposed['generic_name'] ?? '',
                                    $disposed['strength'] ?? '',
                                    $disposed['measurement_name'] ?? ''
                                ], static function ($value) {
                                    return $value !== '';
                                })))) ?></td>
                                <td><?= htmlspecialchars(($disposed['quantity'] ?? 0)) ?></td>
                                <td><?= htmlspecialchars($disposed['expiry_date'] ?: 'N/A') ?></td>
                                <td><?= htmlspecialchars($disposed['reason'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($disposed['disposed_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

            </div>

            <div class="tab-pane fade" id="returned-products-pane" role="tabpanel" aria-labelledby="returned-products-tab" tabindex="0">
            <h5 class="mb-3">Returned Products</h5>
            <div class="inventory-report-toolbar d-flex flex-wrap align-items-center gap-2 mb-2" data-table-target="returnedProductsTable">
                <label class="mb-0" for="returnedProductsSearch">Search:</label>
                <input type="search" id="returnedProductsSearch" class="form-control form-control-sm inventory-search" placeholder="Search returned products..." style="max-width:260px;">
                <button type="button" class="btn btn-sm btn-secondary inventory-copy"><i class="fas fa-copy me-1"></i>Copy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary inventory-excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                <button type="button" class="btn btn-sm btn-danger inventory-pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
                <button type="button" class="btn btn-sm btn-outline-dark inventory-print"><i class="fas fa-print me-1"></i>Print</button>
            </div>
            <div class="table-responsive mb-4">
            <table id="returnedProductsTable" class="table table-sm table-bordered align-middle w-100">
                <thead class="table-secondary">
                    <tr>
                        <th>Return Item ID</th>
                        <th>Return Tx</th>
                        <th>Original Tx</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Reason</th>
                        <th>Method</th>
                        <th>Returned At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($returnedProducts)): ?>
                        <tr>
                            <td class="text-center">No returned products recorded.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returnedProducts as $return): ?>
                            <tr>
                                <td><?= htmlspecialchars($return['id']) ?></td>
                                <td><?= htmlspecialchars($return['return_transaction_id']) ?></td>
                                <td><?= htmlspecialchars($return['original_transaction_id']) ?></td>
                                <td><?= htmlspecialchars(trim(implode(' ', array_filter([
                                    $return['branded_name'] ?? '',
                                    $return['generic_name'] ?? '',
                                    $return['strength'] ?? '',
                                    $return['measurement_name'] ?? ''
                                ], static function ($value) {
                                    return $value !== '';
                                })))) ?></td>
                                <td><?= htmlspecialchars(($return['quantity'] ?? 0)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)($return['price'] ?? 0), 2)) ?></td>
                                <td><?= htmlspecialchars(number_format((float)($return['subtotal'] ?? 0), 2)) ?></td>
                                <td><?= htmlspecialchars($return['reason'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($return['refund_method'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($return['return_date'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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

<div class="modal fade" id="addBatchModal" tabindex="-1" aria-labelledby="addBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="addInventoryBatch" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBatchModalLabel">Add Stock Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="batch_supplier_id" class="form-label">Supplier (optional)</label>
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
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal" title="Add new supplier">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="batch_product_search" class="form-label">Product</label>
                        <input type="text" id="batch_product_search" class="form-control" placeholder="Search product..." list="batch_product_list" autocomplete="off" required>
                        <input type="hidden" id="batch_product_id" name="product_id">
                        <datalist id="batch_product_list">
                            <?php foreach ($products as $product): ?>
                                <option value="<?= htmlspecialchars(trim(($product['branded_name'] ?? '') . ' ' . ($product['generic_name'] ?? ''))) ?>" data-id="<?= (int)($product['id'] ?? 0) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label for="batch_number" class="form-label">Batch No</label>
                        <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="e.g. BATCH-001" required>
                    </div>
                    <div class="mb-3">
                        <label for="batch_quantity" class="form-label">Quantity Received</label>
                        <input type="number" id="batch_quantity" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="batch_purchase_cost" class="form-label">Purchase Cost per Unit (optional)</label>
                        <input type="number" id="batch_purchase_cost" name="purchase_cost" class="form-control" step="0.01" min="0" placeholder="e.g. 50.00">
                    </div>
                    <div class="mb-3">
                        <label for="batch_markup" class="form-label">Markup % (optional)</label>
                        <input type="number" id="batch_markup" name="markup" class="form-control" step="0.01" min="0" value="5" placeholder="e.g. 20">
                    </div>
                    <div class="mb-3">
                        <label for="batch_sale_price" class="form-label">Sale Price per Unit (optional)</label>
                        <input type="number" id="batch_sale_price" name="sale_price" class="form-control" step="0.01" min="0" placeholder="e.g. 75.00" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="batch_expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" id="batch_expiry_date" name="expiry_date" class="form-control">
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
                            alert(`Batch number "${batchNumber}" already exists for this product.`);
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

        supplierForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const supplierName = document.getElementById('supplier_name').value.trim();
            const contactNumber = document.getElementById('supplier_contact_number').value.trim();
            const email = document.getElementById('supplier_email').value.trim();
            const address = document.getElementById('supplier_address').value.trim();
            const submitBtn = supplierForm.querySelector('button[type="submit"]');

            if (!supplierName) {
                alert('Please enter supplier name');
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
                    alert('Supplier "' + data.supplier_name + '" added successfully!\n\nRedirecting to inventory...');
                    
                    // Store flag and supplier ID to reopen modal after refresh
                    sessionStorage.setItem('reopenAddBatchModal', 'true');
                    sessionStorage.setItem('selectedSupplierId', data.supplier_id);
                    
                    // Redirect to inventory page
                    setTimeout(() => {
                        window.location.href = 'dashboard.php?tab=inventory';
                    }, 500);
                } else if (status === 409) {
                    // Duplicate found
                    alert('Supplier Already Exists\n\n' + data.message + '\n\nRedirecting to inventory...');
                    
                    // Store flag and supplier ID to reopen modal after refresh
                    sessionStorage.setItem('reopenAddBatchModal', 'true');
                    sessionStorage.setItem('selectedSupplierId', data.existing_id);
                    
                    // Redirect to inventory page
                    setTimeout(() => {
                        window.location.href = 'dashboard.php?tab=inventory';
                    }, 500);
                } else {
                    alert('Error: ' + (data.error || 'Failed to add supplier'));
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Supplier';
                console.error('Error:', error);
                alert('Error: ' + error.message);
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
                        <input type="tel" class="form-control" id="supplier_contact_number" placeholder="e.g., +1-800-123-4567">
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
