<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
require_once __DIR__ . "/../function/addprodfunct.php";
require_once __DIR__ . "/../conn/connection_links.php";

use Classes\ProductManagement;

$product = new ProductManagement($db);
$products = $product->getAllProducts();
$categories = $product->getCategories();
$servingUnits = $product->getUnitMeasurements();
$unitMeasurements = $product->getPackageUnitMeasurements();

usort($products, static function (array $first, array $second): int {
    return ((int) ($first['id'] ?? 0)) <=> ((int) ($second['id'] ?? 0));
});

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
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#categorySettingsModal">
                    Category Settings
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    Add Item
                </button>
            </div>
        </div>

        <div class="table-responsive"><table id="productManagementTable" class="table table-striped table-hover align-middle w-100 myTable">
            <thead class="table-dark">
                <tr>
                    <th data-priority="6">ID</th>
                    <th data-priority="2">Image</th>
                    <th data-priority="3">Branded</th>
                    <th data-priority="4">Generic</th>
                    <th data-priority="5">Serving</th>
                    <th data-priority="6">Package Size</th>
                    <th data-priority="7">Category</th>
                    <th data-priority="1">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($products as $prod): ?>
                    <?php
                        $brandName = trim((string) ($prod['branded_name'] ?? ''));
                        $itemName = trim((string) ($prod['generic_name'] ?? ''));
                        $strength = trim((string) ($prod['strength'] ?? ''));
                        $measurementName = trim((string) ($prod['measurement_name'] ?? ''));
                        $servingSize = $strength !== ''
                            ? $strength . ($measurementName !== '' ? ' ' . $measurementName : '')
                            : 'N/A';
                        $quantity = $prod['strength_per_quantity'] ?? null;
                        $quantityUnit = trim((string) ($prod['strength_per_quantity_unit'] ?? ''));
                        $packageQuantity = isset($quantity) && $quantity !== '' && (float) $quantity > 0
                            ? (string) $quantity . ($quantityUnit !== '' ? ' ' . $quantityUnit : '')
                            : 'N/A';
                        $categoryName = trim((string) ($prod['category_name'] ?? ''));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (!empty(trim((string)($prod['imageproduct'] ?? '')))): ?>
                                <span class="mmb-thumb mmb-thumb--md">
                                    <img src="../img/<?= htmlspecialchars($prod['imageproduct'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)($prod['generic_name'] ?? 'Product'), ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                </span>
                            <?php else: ?>
                                <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($brandName !== '' ? $brandName : 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($itemName !== '' ? $itemName : 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($servingSize, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($packageQuantity, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($categoryName !== '' ? $categoryName : 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                       
                        <td>
                            <!-- BARCODE (issue #4: preview + download the real Code 128 label) -->
                            <button class="btn btn-outline-secondary btn-sm mmb-barcode-btn" data-barcode="<?= htmlspecialchars((string)($prod['barcode']), ENT_QUOTES, 'UTF-8') ?>" data-product="<?= htmlspecialchars(trim(($prod['branded_name'] ?? '') . ' ' . ($prod['generic_name'] ?? '') . ' ' . ($prod['strength'] ?? '') . ' ' . ($prod['measurement_name'] ?? '') . ' ' . trim($prod['dosage_form'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" aria-label="Barcode preview">
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
                            <a href="?tab=product&amp;deleteProduct=<?= htmlspecialchars((string)($prod['id']), ENT_QUOTES, 'UTF-8') ?>&amp;t=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-danger"
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
<!-- CATEGORY SETTINGS MODAL -->
<div class="modal fade" id="categorySettingsModal" tabindex="-1" aria-labelledby="categorySettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categorySettingsModalLabel">
                    Category Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Edit category names and the discount/VAT rules used when selling products.</p>
                <button type="button" class="btn btn-primary mb-3" data-open-add-category>
                    Add Category
                </button>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-center">VAT</th>
                                <th class="text-center">Senior</th>
                                <th class="text-center">PWD</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr data-category-row="<?= (int) ($category['id'] ?? 0) ?>">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" data-category-name
                                            value="<?= htmlspecialchars($category['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                    </td>
                                    <?php foreach (['has_vat' => 'VAT', 'senior_discount' => 'Senior', 'pwd_discount' => 'PWD'] as $field => $label): ?>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input" data-category-field="<?= $field ?>"
                                                <?= !empty($category[$field]) ? 'checked' : '' ?> aria-label="<?= $label ?>">
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary" data-save-category>
                                            Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-category>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addCategoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="newCategoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-3" id="newCategoryName" required autocomplete="off">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="newCategoryVat">
                        <label class="form-check-label" for="newCategoryVat">VAT applicable</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="newCategorySenior">
                        <label class="form-check-label" for="newCategorySenior">Senior discount</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="newCategoryPwd">
                        <label class="form-check-label" for="newCategoryPwd">PWD discount</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- usersmanagement.js is loaded globally via conn/connection_links.php (broken duplicate reference removed) -->
<!-- ═══ BARCODE PREVIEW MODAL (issue #4) — real Code 128 label with
     preview + PNG download + print, for sticking on packaging ═══ -->
<div class="modal fade" id="mmbBarcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mmbBarcodeModalTitle">Product barcode</h5>
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
                    Print
                </button>
                <button type="button" class="btn btn-primary" data-mmb-barcode-download>
                    Download PNG
                </button>
            </div>
            <div id="mmbBarcodePrintHolder" style="display:none;"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (e) {
        // Scoped to .mmb-barcode-btn buttons ONLY. The previous selector
        // [data-barcode] also matched POS product cards (they carry a
        // data-barcode attribute), so adding an item to the cart on the
        // owner/admin shell opened the barcode preview modal and darkened
        // the whole screen (GitHub issue #5 item 2). Staff pages never
        // include this script's markup, which is why only owner/admin saw it.
        var btn = e.target.closest('.mmb-barcode-btn');
        if (!btn) return;
        mmbShowBarcodeModal(btn.getAttribute('data-barcode'), btn.getAttribute('data-product'));
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySettingsModal = document.getElementById('categorySettingsModal');
        const addCategoryModal = document.getElementById('addCategoryModal');
        const addCategoryForm = document.getElementById('addCategoryForm');
        const categoryNameInput = document.getElementById('newCategoryName');
        if (!addCategoryForm || !addCategoryModal || !categorySettingsModal) return;

        document.querySelector('[data-open-add-category]')?.addEventListener('click', function () {
            bootstrap.Modal.getOrCreateInstance(categorySettingsModal).hide();
            categorySettingsModal.addEventListener('hidden.bs.modal', function openAddCategory() {
                categorySettingsModal.removeEventListener('hidden.bs.modal', openAddCategory);
                bootstrap.Modal.getOrCreateInstance(addCategoryModal).show();
                categoryNameInput?.focus();
            });
        });

        addCategoryForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const name = categoryNameInput.value.trim();
            if (!name) {
                mmbNotify({ type: 'warning', title: 'Category name required' });
                categoryNameInput.focus();
                return;
            }

            const submitButton = addCategoryForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
            const formData = new FormData();
            formData.append('category_name', name);
            formData.append('has_vat', document.getElementById('newCategoryVat').checked ? '1' : '0');
            formData.append('senior_discount', document.getElementById('newCategorySenior').checked ? '1' : '0');
            formData.append('pwd_discount', document.getElementById('newCategoryPwd').checked ? '1' : '0');

            fetch('../function/add_category_ajax.php', { method: 'POST', body: formData })
                .then((response) => response.json().then((data) => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200 || !data.success) {
                        throw new Error(data.message || 'Unable to add category.');
                    }
                    const categoryTableBody = document.querySelector('#categorySettingsModal tbody');
                    const newRow = document.createElement('tr');
                    newRow.dataset.categoryRow = String(data.id);
                    newRow.innerHTML = `
                        <td><input type="text" class="form-control form-control-sm" data-category-name required></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="has_vat" aria-label="VAT"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="senior_discount" aria-label="Senior"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="pwd_discount" aria-label="PWD"></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-primary" data-save-category>Save</button>
                            <button type="button" class="btn btn-sm btn-danger" data-delete-category>Delete</button>
                        </td>`;
                    newRow.querySelector('[data-category-name]').value = name;
                    newRow.querySelector('[data-category-field="has_vat"]').checked = formData.get('has_vat') === '1';
                    newRow.querySelector('[data-category-field="senior_discount"]').checked = formData.get('senior_discount') === '1';
                    newRow.querySelector('[data-category-field="pwd_discount"]').checked = formData.get('pwd_discount') === '1';
                    categoryTableBody?.appendChild(newRow);
                    mmbNotify({ type: 'success', title: 'Category added', message: data.message });
                    addCategoryForm.reset();
                    bootstrap.Modal.getOrCreateInstance(addCategoryModal).hide();
                })
                .catch((error) => {
                    mmbNotify({ type: 'danger', title: 'Could not add category', message: error.message });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Add Category';
                });
        });

        addCategoryModal.addEventListener('hidden.bs.modal', function () {
            bootstrap.Modal.getOrCreateInstance(categorySettingsModal).show();
        });
    });
</script>
<script>
    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('[data-delete-category]');
        if (!deleteButton) return;

        const row = deleteButton.closest('[data-category-row]');
        const name = row.querySelector('[data-category-name]').value.trim();
        if (!window.confirm(`Delete category "${name}"? This cannot be undone.`)) return;

        deleteButton.disabled = true;
        const formData = new FormData();
        formData.append('category_id', row.dataset.categoryRow);

        fetch('../function/delete_category_ajax.php', { method: 'POST', body: formData })
            .then((response) => response.json().then((data) => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (status !== 200 || !data.success) {
                    throw new Error(data.message || 'Unable to delete category.');
                }
                row.remove();
                mmbNotify({ type: 'success', title: 'Category deleted', message: data.message });
            })
            .catch((error) => {
                mmbNotify({ type: 'danger', title: 'Could not delete category', message: error.message });
                deleteButton.disabled = false;
            });
    });
</script>
<script>
    document.addEventListener('click', function (event) {
        const saveButton = event.target.closest('[data-save-category]');
        if (!saveButton) return;

        const row = saveButton.closest('[data-category-row]');
        const nameInput = row.querySelector('[data-category-name]');
        const name = nameInput.value.trim();
        if (!name) {
            mmbNotify({ type: 'warning', title: 'Category name required' });
            nameInput.focus();
            return;
        }

        const originalText = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
        const formData = new FormData();
        formData.append('category_id', row.dataset.categoryRow);
        formData.append('category_name', name);
        row.querySelectorAll('[data-category-field]').forEach((field) => {
            formData.append(field.dataset.categoryField, field.checked ? '1' : '0');
        });

        fetch('../function/update_category_ajax.php', { method: 'POST', body: formData })
            .then((response) => response.json().then((data) => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (status !== 200 || !data.success) {
                    throw new Error(data.message || 'Unable to update category.');
                }
                mmbNotify({ type: 'success', title: 'Category updated', message: data.message });
            })
            .catch((error) => {
                mmbNotify({ type: 'danger', title: 'Could not update category', message: error.message });
            })
            .finally(() => {
                saveButton.disabled = false;
                saveButton.innerHTML = originalText;
            });
    });
</script>
