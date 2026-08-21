<?php
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
    $id = (int) $_GET['deleteProduct'];

    if ($product->deleteProduct($id)) {
        echo "<script>alert('Product deleted successfully'); window.location.href = window.location.href = 'dashboard.php?tab=product';</script>";
        exit;
    } else {
        echo "<script>alert('" . $product->getResponse() . "');</script>";
    }
}

// UPDATE
if ($product->updateProduct()) {
    echo "<script>alert('Updated successfully'); window.location.href = window.location.href = 'dashboard.php?tab=product';</script>";
    exit;
}

if ($product->addProduct()) {
    echo "<script>alert('Product added successfully'); window.location.href = window.location.href = 'dashboard.php?tab=product';</script>";
    exit;
} else {
    if (!empty($_POST) && isset($_POST['addProduct'])) {
        echo "<script>alert('" . $product->getResponse() . "');</script>";
    }
}
?>
<link rel="stylesheet" href="../css/button.css">
<div class="card shadow-sm">
    <div class="card-body">

        <!-- ADD PRODUCT BUTTON -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Product Management</h4>
            <div class="d-flex gap-2">
                <?php if ($pmIsManager): ?>

                <?php endif; ?>
                <button class="button" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <span class="button__text">Add Item</span>
                    <span class="button__icon"><svg xmlns="http://www.w3.org/2000" width="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor"
                            height="24" fill="none" class="svg">
                            <line y2="19" y1="5" x2="12" x1="12"></line>
                            <line y2="12" y1="12" x2="19" x1="5"></line>
                        </svg></span>
                </button>
            </div>
        </div>

        <table class="table table-striped table-hover align-middle w-100 myTable">
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
                        <td><?= $prod['id'] ?></td>
                        <td><?= $prod['branded_name'] ?></td>
                        <td><?= $prod['generic_name'] ?></td>
                        <td><?= $prod['strength'] ?? 'N/A' ?> <?= $prod['measurement_name'] ?? 'N/A' ?></td>
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
                        <td><?= $prod['category_name'] ?? 'N/A' ?></td>
                        <td><?= $prod['barcode'] ?></td>
                        <td>
                            <!-- VIEW -->
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#viewProduct<?= $prod['id'] ?>">
                                View
                            </button>
                            <!-- EDIT -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProduct<?= $prod['id'] ?>">
                                Edit
                            </button>
                            <!-- DELETE -->
                            <a href="?deleteProduct=<?= $prod['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this product?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'updateproductmodal.php'; ?>
<?php include 'viewproductmodal.php'; ?>
<?php include 'addproductmodal.php'; ?>
<script src="js/usermanagement.js"></script>