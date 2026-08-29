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
        echo "<script>alert('Invalid or expired delete request.'); window.location.href='dashboard.php?tab=product';</script>";
        exit;
    }

    $id = (int) $_GET['deleteProduct'];

    if ($product->deleteProduct($id)) {
        echo "<script>alert('Product deleted successfully'); window.location.href = 'dashboard.php?tab=product';</script>";
        exit;
    } else {
        echo "<script>alert(" . json_encode($product->getResponse()) . ");</script>";
    }
}

// UPDATE
if ($product->updateProduct()) {
    echo "<script>alert('Updated successfully'); window.location.href = 'dashboard.php?tab=product';</script>";
    exit;
}

if ($product->addProduct()) {
    echo "<script>alert('Product added successfully'); window.location.href = 'dashboard.php?tab=product';</script>";
    exit;
} else {
    if (!empty($_POST) && isset($_POST['addProduct'])) {
        echo "<script>alert(" . json_encode($product->getResponse()) . ");</script>";
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
                    <th>ID</th>
                    <th>Branded</th>
                    <th>Generic</th>
                    <th>Strength</th>
                    <th>Dosage Form</th>
                    <th>Strength Qty</th>
                    <th>Category</th>
                    <th>Barcode</th>
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
                        <td><?= htmlspecialchars((string)($prod['barcode']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
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
                                onclick="return confirm('Delete this product?')">
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