<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
require_once __DIR__ . "/../function/addprodfunct.php";
require_once __DIR__ . "/../conn/connection_links.php";

use Classes\ProductManagement;

$product = new ProductManagement($db);
$products = $product->getAllProducts();
$categories = $product->getCategories();
$unitMeasurements = $product->getUnitMeasurements();

$pmUserRole = strtolower($_SESSION['position'] ?? 'staff');
$pmIsManager = in_array($pmUserRole, ['owner', 'admin']);

if (isset($_GET['deleteProduct'])) {
    // CSRF protection: deletes are only accepted with a valid per-session token
    $deleteToken = (string)($_GET['t'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $deleteToken)) {
        echo "<script>mmbNotify({type:'warning', title:'Invalid or expired delete request'}); setTimeout(function(){ window.location.href='dashboard.php?tab=product'; }, 1800);</script>";
        exit;
    }

    $id = (int) $_GET['deleteProduct'];

    if ($product->deleteProduct($id)) {
        echo "<script>mmbNotify({type:'success', title:'Product deleted successfully'}); setTimeout(function(){ window.location.href = 'dashboard.php?tab=product'; }, 1600);</script>";
        exit;
    } else {
        echo "<script>mmbNotify({type:'danger', title:'Delete failed', message:" . json_encode($product->getResponse()) . "});</script>";
    }
}

// UPDATE
if ($product->updateProduct()) {
    echo "<script>mmbNotify({type:'success', title:'Product updated successfully'}); setTimeout(function(){ window.location.href = 'dashboard.php?tab=product'; }, 1600);</script>";
    exit;
}

if ($product->addProduct()) {
    echo "<script>mmbNotify({type:'success', title:'Product added successfully'}); setTimeout(function(){ window.location.href = 'dashboard.php?tab=product'; }, 1600);</script>";
    exit;
} else {
    if (!empty($_POST) && isset($_POST['addProduct'])) {
        echo "<script>mmbNotify({type:'danger', title:'Could not add product', message:" . json_encode($product->getResponse()) . "});</script>";
    }
}
?>
<div class="card">
    <div class="card-body p-4">

        <!-- PAGE HEADER -->
        <div class="page-head">
            <div>
                <h4>Product Management</h4>
                <p class="page-sub">Manage the medicine catalog — brands, generics, strengths and pricing.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i>Add Item
                </button>
            </div>
        </div>

        <div class="table-responsive"><table class="table table-striped table-hover align-middle w-100 myTable">
            <thead class="table-dark">
                <tr>
                    <th data-priority="6">ID</th>
                    <th data-priority="1">Branded</th>
                    <th data-priority="2">Generic</th>
                    <th data-priority="5">Strength</th>
                    <th data-priority="7">Dosage Form</th>
                    <th data-priority="8">Strength Qty</th>
                    <th data-priority="4">Category</th>
                    <th data-priority="3">Product Code</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty(trim((string)($prod['imageproduct'] ?? '')))): ?>
                                    <span class="mmb-thumb mmb-thumb--md">
                                        <img src="../img/<?= htmlspecialchars($prod['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                                    </span>
                                <?php else: ?>
                                    <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                <?php endif; ?>
                                <span><?= htmlspecialchars((string)($prod['branded_name']), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars((string)($prod['generic_name']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)($prod['strength'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string)($prod['measurement_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= !empty(trim($prod['dosage_form'] ?? '')) ? htmlspecialchars(trim($prod['dosage_form'])) : 'N/A' ?></td>
                        <td>
                            <?php
                                $qty = $prod['strength_per_quantity'] ?? null;
                                $unit = trim($prod['strength_per_quantity_unit'] ?? '');
                                if (isset($qty) && $qty !== '' && $qty !== null && (float)$qty > 0) {
                                    echo htmlspecialchars((string) $qty) . ($unit ? ' ' . htmlspecialchars($unit) : '');
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                        <td><?= htmlspecialchars((string)($prod['category_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="mmb-code-chip" data-code="<?= htmlspecialchars((string)($prod['barcode']), ENT_QUOTES, 'UTF-8') ?>" data-name="<?= htmlspecialchars(trim(($prod['branded_name'] ?? '') . ' ' . ($prod['generic_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)($prod['barcode']), ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <!-- BARCODE (issue #4: preview + download the real Code 128 label) -->
                            <button class="btn btn-outline-secondary btn-sm" data-barcode="<?= htmlspecialchars((string)($prod['barcode']), ENT_QUOTES, 'UTF-8') ?>" data-product="<?= htmlspecialchars(trim(($prod['branded_name'] ?? '') . ' ' . ($prod['generic_name'] ?? '') . ' ' . ($prod['strength'] ?? '') . ' ' . ($prod['measurement_name'] ?? '') . ' ' . trim($prod['dosage_form'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" aria-label="Barcode preview">
                                <i class="fas fa-barcode"></i>
                            </button>
                            <!-- VIEW -->
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#viewProduct<?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?>">
                                View
                            </button>
                            <!-- EDIT -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProduct<?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?>">
                                Edit
                            </button>
                            <!-- DELETE -->
                            <a href="?deleteProduct=<?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?>&t=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-danger"
                                data-mmb-confirm="Delete this product? This cannot be undone." data-mmb-ok="Yes, delete">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php include 'updateproductmodal.php'; ?>
<?php include 'viewproductmodal.php'; ?>
<?php include 'addproductmodal.php'; ?>
<!-- usersmanagement.js is loaded globally via conn/connection_links.php (broken duplicate reference removed) -->
<!-- ═══ BARCODE PREVIEW MODAL (issue #4) — real Code 128 label with
     preview + PNG download + print, for sticking on packaging ═══ -->
<div class="modal fade" id="mmbBarcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mmbBarcodeModalTitle"><i class="fas fa-barcode me-2 text-danger"></i>Product barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mmbBarcodeCanvasHolder" class="border rounded p-3 bg-white text-center"></div>
                <div class="text-center mt-2">
                    <span class="badge text-bg-light border" id="mmbBarcodeCodeText" style="font-family: 'Courier New', monospace; letter-spacing: 1px;"></span>
                </div>
                <p class="text-muted small mt-3 mb-0">
                    Code 128 barcode generated from the product code. Download the PNG and print it as a label for the product packaging.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary" data-mmb-barcode-print>
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <button type="button" class="btn btn-primary" data-mmb-barcode-download>
                    <i class="fas fa-download me-1"></i> Download PNG
                </button>
            </div>
            <div id="mmbBarcodePrintHolder" style="display:none;"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-barcode]');
        if (!btn) return;
        mmbShowBarcodeModal(btn.getAttribute('data-barcode'), btn.getAttribute('data-product'));
    });
</script>
