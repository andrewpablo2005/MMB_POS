<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
 foreach ($products as $prod): ?>
    <div class="modal fade" id="viewProduct<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="viewProductLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable mmb-view-modal-dialog">
            <div class="modal-content mmb-view-modal">
                <div class="modal-header mmb-view-header">
                    <h5 class="modal-title" id="viewProductLabel<?= $prod['id'] ?>"><span class="modal-head-icon"><i class="fas fa-capsules"></i></span> Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mmb-view-body">
                    <div class="mmb-view-identity">
                        <div class="mmb-view-image-wrap">
                            <?php if (!empty($prod['imageproduct'])): ?>
                                <img src="../img/<?= htmlspecialchars($prod['imageproduct']) ?>" alt="<?= htmlspecialchars($prod['generic_name'] ?? 'Product') ?>" class="mmb-view-image">
                            <?php else: ?>
                                <div class="mmb-view-image-placeholder"><i class="fas fa-box-open"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="mmb-view-identity-copy">
                            <span class="mmb-view-eyebrow">Product overview</span>
                            <h4><?= htmlspecialchars($prod['generic_name'] ?? 'Unnamed product') ?></h4>
                            <p><?= !empty(trim($prod['branded_name'] ?? '')) ? htmlspecialchars(trim($prod['branded_name'])) : 'No brand' ?> · <?= htmlspecialchars($prod['category_name'] ?? 'Uncategorized') ?></p>
                            <?php if (!empty(trim((string)($prod['barcode'] ?? '')))): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary mmb-barcode-btn" data-barcode="<?= htmlspecialchars((string)$prod['barcode'], ENT_QUOTES, 'UTF-8') ?>" data-product="<?= htmlspecialchars(trim(($prod['branded_name'] ?? '') . ' ' . ($prod['generic_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
                                    <i class="fas fa-barcode"></i> View barcode
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <section class="mmb-view-section">
                        <h6 class="mmb-view-section-title">Product information</h6>
                        <div class="mmb-view-grid">
                        <div class="mmb-view-item">
                            <strong>Brand Name</strong>
                            <span><?= !empty(trim($prod['branded_name'] ?? '')) ? htmlspecialchars(trim($prod['branded_name'])) : 'No brand' ?></span>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Product Name</strong>
                            <span><?= htmlspecialchars($prod['generic_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Amount per Serving</strong>
                            <span><?= !empty(trim((string) ($prod['strength'] ?? ''))) ? htmlspecialchars((string) $prod['strength']) . (!empty(trim($prod['measurement_name'] ?? '')) ? ' ' . htmlspecialchars(trim($prod['measurement_name'])) : '') : 'N/A' ?></span>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Product Form</strong>
                            <span><?= !empty(trim($prod['dosage_form'] ?? '')) ? htmlspecialchars(trim($prod['dosage_form'])) : 'N/A' ?></span>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Total Volume / Quantity per Package</strong>
                            <?php
                                $qty = $prod['strength_per_quantity'] ?? null;
                                $unit = trim($prod['strength_per_quantity_unit'] ?? '');
                                if (isset($qty) && $qty !== '' && $qty !== null && (float)$qty > 0) {
                                    echo htmlspecialchars((string) $qty) . ($unit ? ' ' . htmlspecialchars($unit) : '');
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Category</strong>
                            <span><?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="mmb-view-item">
                            <strong>Product Code</strong>
                            <span><?= htmlspecialchars($prod['barcode'] ?? 'N/A') ?></span>
                        </div>
                        </div>
                    </section>

                    <section class="mmb-view-section mmb-view-section--spaced">
                        <h6 class="mmb-view-section-title">Price and stock</h6>
                        <div class="mmb-view-metrics">
                        <div class="mmb-view-metric">
                            <strong>Price</strong>
                            <span>₱ <?= number_format((float) ($prod['display_price'] ?? $prod['total_price'] ?? 0), 2) ?></span>
                        </div>
                        <div class="mmb-view-metric">
                            <strong>Original Stock</strong>
                            <span><?= htmlspecialchars((string) ($prod['received_quantity'] ?? 0)) ?></span>
                        </div>
                        <div class="mmb-view-metric">
                            <strong>Current Stock</strong>
                            <span><?= htmlspecialchars((string) ($prod['current_quantity'] ?? 0)) ?></span>
                        </div>
                        <div class="mmb-view-metric">
                            <strong>Net Price</strong>
                            <span>₱ <?= number_format((float) ($prod['net_price'] ?? 0), 2) ?></span>
                        </div>
                        <div class="mmb-view-metric">
                            <strong>Sale Price</strong>
                            <span>₱ <?= number_format((float) ($prod['total_price'] ?? 0), 2) ?></span>
                        </div>
                        <div class="mmb-view-metric">
                            <strong>Expiry Date</strong>
                            <span><?= !empty($prod['expiry_date']) ? htmlspecialchars($prod['expiry_date']) : 'No expiry date' ?></span>
                        </div>
                        </div>
                    </div>

                    <section class="mmb-view-section mmb-view-section--spaced mmb-view-batch-section">
                    <h6 class="mmb-view-section-title">Batch details</h6>
                    <?php $batchDetails = $prod['inventory_batches'] ?? []; ?>
                    <?php if (!empty($batchDetails)): ?>
                        <div class="table-responsive mmb-view-table-wrap mb-3">
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
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
