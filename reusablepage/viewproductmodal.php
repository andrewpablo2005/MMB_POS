<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
 foreach ($products as $prod): ?>
    <div class="modal fade" id="viewProduct<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="viewProductLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewProductLabel<?= $prod['id'] ?>"><span class="modal-head-icon"><i class="fas fa-capsules"></i></span> Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Brand Name:</strong><br>
                            <?= htmlspecialchars($prod['branded_name'] ?? 'N/A') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Generic Name:</strong><br>
                            <?= htmlspecialchars($prod['generic_name'] ?? 'N/A') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Strength:</strong><br>
                            <?= htmlspecialchars((string) ($prod['strength'] ?? 'N/A')) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Dosage Form:</strong><br>
                            <?= !empty(trim($prod['dosage_form'] ?? '')) ? htmlspecialchars(trim($prod['dosage_form'])) : 'N/A' ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Strength per Quantity:</strong><br>
                            <?php
                                $qty = $prod['strength_per_quantity'] ?? null;
                                $unit = trim($prod['strength_per_unit'] ?? '');
                                if (isset($qty) && $qty !== '' && $qty !== null && (float)$qty > 0) {
                                    echo htmlspecialchars((string) $qty) . ($unit ? ' ' . htmlspecialchars($unit) : '');
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Category:</strong><br>
                            <?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Product Code:</strong><br>
                            <?= htmlspecialchars($prod['barcode'] ?? 'N/A') ?>
                            <?php if (!empty(trim((string)($prod['barcode'] ?? '')))): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                        data-barcode="<?= htmlspecialchars((string)$prod['barcode'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-product="<?= htmlspecialchars(trim(($prod['branded_name'] ?? '') . ' ' . ($prod['generic_name'] ?? '') . ' ' . ($prod['strength'] ?? '') . ' ' . ($prod['measurement_name'] ?? '') . ' ' . trim($prod['dosage_form'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="fas fa-barcode"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Price:</strong><br>
                            ₱ <?= number_format((float) ($prod['display_price'] ?? $prod['total_price'] ?? 0), 2) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Original Stock:</strong><br>
                            <?= htmlspecialchars((string) ($prod['received_quantity'] ?? 0)) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Current Stock:</strong><br>
                            <?= htmlspecialchars((string) ($prod['current_quantity'] ?? 0)) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Net Price:</strong><br>
                            ₱ <?= number_format((float) ($prod['net_price'] ?? 0), 2) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Sale Price:</strong><br>
                            ₱ <?= number_format((float) ($prod['total_price'] ?? 0), 2) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Expiry Date:</strong><br>
                            <?= !empty($prod['expiry_date']) ? htmlspecialchars($prod['expiry_date']) : 'No expiry date' ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Image:</strong><br>
                            <?php if (!empty($prod['imageproduct'])): ?>
                                <img src="../img/<?= htmlspecialchars($prod['imageproduct']) ?>" alt="Product Image"
                                    style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-secondary">Batch Details</h6>
                    <?php $batchDetails = $prod['inventory_batches'] ?? []; ?>
                    <?php if (!empty($batchDetails)): ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Original Qty</th>
                                        <th>Current Qty</th>
                                        <th>Expiry</th>
                                        <th>Status</th>
                                        <th>Purchase Cost</th>
                                        <th>Sale Price</th>
                                        <th>Supplier Name</th>
                                        <th>Supplier Contact</th>
                                        <th>Supplier Email</th>
                                        <th>Supplier Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batchDetails as $batch): ?>
                                        <?php
                                            $batchCurrentQuantity = (int) ($batch['current_quantity'] ?? 0);
                                            $batchExpiryDate = trim((string) ($batch['expiry_date'] ?? ''));
                                            $today = new DateTime('today');
                                            $nearExpiryDate = (clone $today)->modify('+90 days');
                                            $batchStatus = [];
                                            $batchRowClass = '';

                                            if ($batchCurrentQuantity <= 0) {
                                                $batchStatus[] = '<span class="badge bg-dark">NO STOCK</span>';
                                                $batchRowClass = 'table-secondary';
                                            } elseif ($batchCurrentQuantity <= 15) {
                                                $batchStatus[] = '<span class="badge bg-warning text-dark">LOW STOCK</span>';
                                                $batchRowClass = 'table-warning';
                                            }

                                            if ($batchExpiryDate !== '') {
                                                try {
                                                    $batchExpiry = new DateTime($batchExpiryDate);
                                                    if ($batchExpiry < $today) {
                                                        $batchStatus[] = '<span class="badge bg-danger">EXPIRED</span>';
                                                        $batchRowClass = 'table-danger';
                                                    } elseif ($batchExpiry <= $nearExpiryDate) {
                                                        $batchStatus[] = '<span class="badge bg-warning text-dark">NEAR EXPIRED</span>';
                                                        if ($batchRowClass === '') {
                                                            $batchRowClass = 'table-warning';
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    $batchStatus[] = '<span class="badge bg-secondary">INVALID EXPIRY</span>';
                                                }
                                            }

                                            if (empty($batchStatus)) {
                                                $batchStatus[] = '<span class="badge badge-soft-dark">Good Stock</span>';
                                            }
                                        ?>
                                        <tr class="<?= $batchRowClass ?>">
                                            <td><?= htmlspecialchars(trim((string) ($batch['batch_number'] ?? 'N/A'))) ?></td>
                                            <td><?= htmlspecialchars((string) ($batch['received_quantity'] ?? 0)) ?></td>
                                            <td><?= htmlspecialchars((string) $batchCurrentQuantity) ?></td>
                                            <td><?= htmlspecialchars(($batch['expiry_date'] ?? 'N/A') ?: 'N/A') ?></td>
                                            <td><?= implode(' ', $batchStatus) ?></td>
                                            <td>₱ <?= number_format((float) ($batch['purchase_cost'] ?? 0), 2) ?></td>
                                            <td>₱ <?= number_format((float) ($batch['sale_price'] ?? 0), 2) ?></td>
                                            <td><?= htmlspecialchars($batch['supplier_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($batch['supplier_contact'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($batch['supplier_email'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($batch['supplier_address'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-3">No batch details available for this product.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
