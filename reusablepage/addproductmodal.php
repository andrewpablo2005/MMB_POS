<?php
require_once __DIR__ . '/guard.php';
guard_require_roles(['owner', 'admin']);
require_once __DIR__ . "/../function/addprodfunct.php";
require_once __DIR__ . "/../conn/connection_links.php";

use Classes\ProductManagement;

$product = new ProductManagement($db);

// USE CLASS FUNCTIONS
$categories = $product->getCategories();
$servingUnits = $product->getUnitMeasurements();
$unitMeasurements = $product->getPackageUnitMeasurements();
$dosageForms = $product->getDosageForms();

?>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg add-product-dialog">
        <div class="modal-content add-product-modal">

            <form method="POST" enctype="multipart/form-data" onsubmit="return validateAddProductForm(event)">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="modal-head-icon"><i class="fas fa-pills"></i></span> Add New
                        Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Add Product" title="Close Add Product"></button>
                </div>

                <div class="modal-body add-product-body">

                    <!-- BASIC INFORMATION SECTION -->
                    <div class="add-product-section-head">
                        <div>
                            <span class="add-product-section-kicker">01</span>
                            <h6>Product identity</h6>
                        </div>
                        <span>Names and classification</span>
                    </div>

                    <div class="add-product-row">
                        <div class="add-product-field">
                            <label for="branded_name" class="form-label">Brand Name (if applicable)</label>
                            <input type="text" id="branded_name" name="branded_name" class="form-control"
                                placeholder="e.g., Tylenol">

                                <div class="form-text text-muted mt-1">
                                    Enter the brand printed on the package, such as Coca-Cola or Tylenol. Leave blank if there is no brand.
                            </div>
                        </div>
                        
                        <div class="add-product-field">
                            <label for="generic_name" class="form-label">Generic/Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="generic_name" name="generic_name" class="form-control"
                                placeholder="e.g., Paracetamol or Potato Chips" required>
                            <div class="form-text text-muted mt-1">
                                Enter the name customers will recognize, such as Paracetamol, Potato Chips, or Bottled Water.
                            </div>
                        </div>
                    </div>

                    <div class="add-product-row add-product-row--single">
                        <div class="add-product-field">
                            <label for="category_id" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">— select category —</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int) ($cat['id'] ?? 0) ?>"
                                        data-senior="<?= (int) ($cat['senior_discount'] ?? 0) ?>"
                                        data-pwd="<?= (int) ($cat['pwd_discount'] ?? 0) ?>"
                                        data-vat="<?= (int) ($cat['has_vat'] ?? 0) ?>"><?= htmlspecialchars($cat['category_name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                            </select>
                            <div id="categoryRuleNote" class="form-text text-muted mt-1">
                                Select a category to see whether senior/PWD discounts apply.
                            </div>
                        </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div>
                            <span class="add-product-section-kicker">02</span>
                            <h6>Product details</h6>
                        </div>
                        <span>Size, amount, and product form</span>
                    </div>

                    <div class="add-product-row">
                        <div class="add-product-field">
                            <label for="strength" class="form-label">Amount per Serving <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" id="strength" name="strength" class="form-control"
                                    placeholder="e.g., 250" min="0" required>
                            </div>
                            <div class="form-text text-muted">Use this for a measured serving or portion, such as 250 mg, 30 g, or 1 piece.
                            </div>
                        </div>
                        <div class="add-product-field">
                            <label for="unit_measurement" class="form-label">Serving Unit <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select id="unit_measurement" name="unit_measurement" class="form-select" required>
                                    <option value="">— none —</option>
                                    <?php foreach ($servingUnits as $unit): ?>
                                        <option value="<?= (int) ($unit['id'] ?? 0) ?>" data-measurement-id="<?= (int) ($unit['id'] ?? 0) ?>"><?= htmlspecialchars($unit['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-primary" data-open-measurement-modal
                                    data-measurement-target="unit_measurement" aria-label="Add serving unit" title="Add serving unit" data-bs-toggle="tooltip">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-delete-measurement="unit_measurement"
                                    aria-label="Delete serving unit" title="Delete selected serving unit" data-bs-toggle="tooltip">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted mt-1">
                                Choose the unit for the serving amount, such as mg, g, mL, or pcs.
                            </div>
                        </div>
                    </div>

                    <div id="dosageFormContainer" class="add-product-row add-product-row--single">
                        <div class="add-product-field">
                            <label for="dosage_form_id" class="form-label">Product Form <span class="text-muted">(if applicable)</span></label>
                            <input type="hidden" id="dosage_form" name="dosage_form" value="">
                            <div class="input-group">
                                <select id="dosage_form_id" name="dosage_form_id" class="form-select">
                                <option value="">— none —</option>
                                <?php foreach ($dosageForms as $form): ?>
                                    <option value="<?= (int) ($form['id'] ?? 0) ?>"
                                        data-name="<?= htmlspecialchars($form['name'] ?? '') ?>"><?= htmlspecialchars($form['name'] ?? '') ?></option>
                                <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-primary" data-open-dosage-form-modal
                                    aria-label="Add product form" title="Add product form" data-bs-toggle="tooltip">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-delete-dosage-form
                                    aria-label="Delete product form" title="Delete selected product form" data-bs-toggle="tooltip">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted mt-1">
                                Select a form such as tablet, syrup, bag, bottle, or box, or leave blank when it does not apply.
                            </div>
                        </div>
                    </div>

                    <div id="strengthQuantityFields" class="add-product-row">
                        <div class="add-product-field">
                            <label for="strength_per_quantity" class="form-label">Package Size<span
                                class="text-muted">(if applicable)</span></label>
                            <input type="number" id="strength_per_quantity" name="strength_per_quantity" step="1" min="1"
                                inputmode="numeric" class="form-control" placeholder="e.g., 500">
                            <div class="form-text text-muted">Total contents of one package — whole numbers only, e.g., 100, 50, or 10.
                            </div>
                        </div>
                        <div class="add-product-field">
                            <label for="strength_per_quantity_unit" class="form-label">Unit<span
                                class="text-muted">(if applicable)</span></label>
                            <div class="input-group">
                                <select id="strength_per_quantity_unit" name="strength_per_quantity_unit" class="form-select">
                                    <option value="">— none —</option>
                                    <?php foreach ($unitMeasurements as $unit): ?>
                                        <option value="<?= htmlspecialchars($unit['name'] ?? '') ?>" data-measurement-id="<?= (int) ($unit['id'] ?? 0) ?>"><?= htmlspecialchars($unit['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-primary" data-open-measurement-modal
                                    data-measurement-target="strength_per_quantity_unit" aria-label="Add package unit" title="Add package unit" data-bs-toggle="tooltip">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-danger" data-delete-measurement="strength_per_quantity_unit"
                                    aria-label="Delete package unit" title="Delete selected package unit" data-bs-toggle="tooltip">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted mt-1">
                                Choose the unit for the package contents, such as mL, g, kg, or pcs.
                            </div>
                        </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div>
                            <span class="add-product-section-kicker">03</span>
                            <h6>Product code</h6>
                        </div>
                        <span>Barcode and scanning</span>
                    </div>

                    <div class="add-product-row">
                        <div class="add-product-field add-product-field--full">
                            <label for="barcode" class="form-label">Product Code</label>
                            <div class="input-group">
                                <input type="text" id="barcode" name="barcode" class="form-control"
                                    placeholder="e.g., 123456789012">
                                <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()" title="Generate barcode" aria-label="Generate barcode" data-bs-toggle="tooltip">
                                    <i class="fas fa-barcode"></i> Auto
                                </button>
                            </div>
                            <div id="barcodePreviewWrap" class="border rounded mt-2 p-2 bg-light d-none">
                                <canvas id="barcodePreviewCanvas"
                                    style="width:100%; max-width:440px; display:block; margin:0 auto;"></canvas>
                            </div>
                            <div class="form-text text-muted mt-1">
                                This code renders a scannable Code 128 barcode — preview it here and download the label
                                PNG from the product list.
                            </div>
                        </div>
                    </div>

                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div>
                            <span class="add-product-section-kicker">04</span>
                            <h6>Opening stock</h6>
                        </div>
                        <span>Optional inventory batch</span>
                    </div>

                    <div class="add-product-row add-product-row--single">
                        <div class="add-product-field add-product-field--full">
                            <label class="form-label">Do you want to add a batch and quantity now?</label>
                            <div class="btn-group w-100" role="group" aria-label="Add batch prompt">
                                <button type="button" class="btn btn-outline-primary"
                                    data-batch-option="yes" title="Add a batch now" data-bs-toggle="tooltip">Yes</button>
                                <button type="button" class="btn btn-secondary active"
                                    data-batch-option="no" title="Skip adding a batch" data-bs-toggle="tooltip">No</button>
                            </div>
                            <input type="hidden" name="add_batch_prompt" id="add_batch_prompt" value="no">
                        </div>
                    </div>

                    <div id="batchPromptFields" class="add-product-batch-panel" style="display:none;">
                        <div class="add-product-row">
                            <div class="add-product-field">
                                <label for="batch_number" class="form-label">Batch No</label>
                                <input type="text" id="batch_number" class="form-control"
                                    placeholder="Automatically generated" readonly>
                                <div class="form-text text-muted mt-1">
                                    The system will automatically assign the next batch number for this item.
                                </div>
                            </div>
                            <div class="add-product-field">
                                <label for="batch_supplier_search" class="form-label">Supplier (optional)</label>
                                <input type="text" id="batch_supplier_search" class="form-control"
                                    list="batch_supplier_list" placeholder="Select supplier or leave blank"
                                    autocomplete="off">
                                <input type="hidden" id="batch_supplier_id" name="supplier_id" value="">
                                <datalist id="batch_supplier_list">
                                    <?php
                                    try {
                                        $stmt = $db->prepare("SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");
                                        $stmt->execute();
                                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $supplier):
                                            ?>
                                            <option value="<?= htmlspecialchars($supplier['supplier_name'] ?? '') ?>"
                                                data-id="<?= (int) ($supplier['id'] ?? 0) ?>">
                                                <?php
                                        endforeach;
                                    } catch (Exception $e) {
                                    }
                                    ?>
                                </datalist>
                                <div class="form-text text-muted mt-1">
                                    Select the supplier for this inventory batch, if known.
                                </div>
                            </div>
                        </div>

                        <div class="add-product-row add-product-row--three">
                            <div class="add-product-field">
                                <label for="batch_quantity" class="form-label">Quantity Received</label>
                                <input type="number" id="batch_quantity" name="received_quantity" class="form-control"
                                    min="1" placeholder="e.g. 10">
                                <div class="form-text text-muted mt-1">
                                    Enter the number of packages received into stock.
                                </div>
                            </div>
                            <div class="add-product-field">
                                <label for="batch_purchase_cost" class="form-label">Purchase Cost</label>
                                <input type="number" id="batch_purchase_cost" name="purchase_cost" class="form-control"
                                    step="0.01" min="0" placeholder="e.g. 50.00">
                                <div class="form-text text-muted mt-1">
                                    Enter the purchase cost for one package or selling unit.
                                </div>
                            </div>
                            <div class="add-product-field">
                                <label for="batch_sale_price" class="form-label">Sale Price</label>
                                <input type="number" id="batch_sale_price" name="sale_price" class="form-control"
                                    step="0.01" min="0" placeholder="e.g. 75.00">
                                <div class="form-text text-muted mt-1">
                                    This price is calculated from purchase cost and markup.
                                </div>
                            </div>
                        </div>

                        <div class="add-product-row">
                            <div class="add-product-field">
                                <label for="batch_markup" class="form-label">Markup %</label>
                                <input type="number" id="batch_markup" name="markup" class="form-control" step="0.01"
                                    min="0" value="5" placeholder="e.g. 20">
                                <div class="form-text text-muted mt-1">
                                    Enter the percentage added to the purchase cost.
                                </div>
                            </div>
                            <div class="add-product-field">
                                <label for="batch_expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" id="batch_expiry_date" name="expiry_date" class="form-control">
                                <div class="form-text text-muted mt-1">
                                    Enter the product expiry date when applicable.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IMAGE SECTION -->
                    <div class="add-product-section-head add-product-section-head--spaced">
                        <div>
                            <span class="add-product-section-kicker">05</span>
                            <h6>Product image</h6>
                        </div>
                        <span>Catalog display image</span>
                    </div>
                    <div class="add-product-row add-product-row--single">
                        <div class="add-product-field add-product-field--full">
                            <label for="product_image_input" class="form-label">Upload Image</label>
                            <input type="file" id="product_image_input" name="product_image" class="form-control"
                                accept="image/*" onchange="previewImage(event)">
                            <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Recommended:
                                500x500px, JPG/PNG, max 5MB</small>
                        </div>
                    </div>

                    <!-- IMAGE PREVIEW -->
                    <div id="image_preview_container" style="display: none;">
                        <div class="text-center">
                            <img id="image_preview" src="" alt="Image Preview"
                                style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background-color: #f8f9fa;">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cancel and close" data-bs-toggle="tooltip">Cancel</button>
                    <button type="submit" name="addProduct" class="btn btn-primary" title="Save product" data-bs-toggle="tooltip">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ADD MEASUREMENT MODAL -->
<div class="modal fade" id="addMeasurementModal" tabindex="-1" aria-labelledby="addMeasurementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addMeasurementForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMeasurementModalLabel">Add Measurement Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Add Measurement" title="Close Add Measurement"></button>
                </div>
                <div class="modal-body">
                    <label for="measurement_name" class="form-label">Measurement Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="measurement_name" name="measurement_name"
                        placeholder="e.g., mg, mL, bottle" required autocomplete="off">
                    <div class="form-text">Add a unit that can be used for serving amounts and package sizes.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cancel and close" data-bs-toggle="tooltip">Cancel</button>
                    <button type="submit" class="btn btn-primary" title="Add measurement unit" data-bs-toggle="tooltip">
                        <i class="fas fa-plus me-1"></i>Add Measurement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD PRODUCT FORM MODAL -->
<div class="modal fade" id="addDosageFormModal" tabindex="-1" aria-labelledby="addDosageFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addDosageFormForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDosageFormModalLabel">Add Product Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Add Product Form" title="Close Add Product Form"></button>
                </div>
                <div class="modal-body">
                    <label for="dosage_form_name" class="form-label">Product Form <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="dosage_form_name" name="dosage_form_name"
                        placeholder="e.g., tablet, syrup, bottle" required autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cancel and close" data-bs-toggle="tooltip">Cancel</button>
                    <button type="submit" class="btn btn-primary" title="Add product form" data-bs-toggle="tooltip">
                        <i class="fas fa-plus me-1"></i>Add Product Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/auto_generatebarcode.js?v=3"></script>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image_preview');
        const previewContainer = document.getElementById('image_preview_container');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    }

    function updateCategoryRuleNote(optionEl) {
        const note = document.getElementById('categoryRuleNote');
        if (!optionEl || !note) return;

        const senior = optionEl.getAttribute('data-senior') === '1';
        const pwd = optionEl.getAttribute('data-pwd') === '1';
        const vat = optionEl.getAttribute('data-vat') === '1';

        const discountText = senior && pwd
            ? 'Senior/PWD discount: Yes for both'
            : senior
                ? 'Senior/PWD discount: Senior only'
                : pwd
                    ? 'Senior/PWD discount: PWD only'
                    : 'Senior/PWD discount: No';

        note.innerHTML = `${discountText} • VAT: ${vat ? 'Yes' : 'No'}`;
    }

    function bindSearchableList({ inputId, valueId, listId, onSelect }) {
        const inputEl = document.getElementById(inputId);
        const valueEl = document.getElementById(valueId);
        const listEl = document.getElementById(listId);

        if (!inputEl || !valueEl || !listEl) {
            return;
        }

        const syncFromInput = () => {
            const query = inputEl.value.trim();
            const optionEl = Array.from(listEl.options).find((option) => option.value.trim() === query);

            if (optionEl) {
                valueEl.value = optionEl.getAttribute('data-id') || '';
                if (onSelect) {
                    onSelect(optionEl);
                }
            } else {
                valueEl.value = '';
                if (onSelect) {
                    onSelect(null);
                }
            }
        };

        inputEl.addEventListener('input', syncFromInput);
        inputEl.addEventListener('change', syncFromInput);
    }

    function normalizeCategoryName(name) {
        return String(name || '').trim().toLowerCase();
    }

    function toggleDosageFormVisibility(categoryName) {
        const dosageFormContainer = document.getElementById('dosageFormContainer');

        if (dosageFormContainer) {
            dosageFormContainer.style.display = 'grid';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
            bootstrap.Tooltip.getOrCreateInstance(element);
        });

        const measurementModal = document.getElementById('addMeasurementModal');
        const measurementForm = document.getElementById('addMeasurementForm');
        const measurementInput = document.getElementById('measurement_name');
        let measurementTargetId = '';
        let returnToProductModal = false;

        const closeMeasurementAndReturn = () => {
            if (!measurementModal) return;
            bootstrap.Modal.getOrCreateInstance(measurementModal).hide();
        };

        document.querySelectorAll('[data-open-measurement-modal]').forEach((button) => {
            button.addEventListener('click', function () {
                measurementTargetId = this.dataset.measurementTarget || '';
                returnToProductModal = true;
                const productModal = document.getElementById('addProductModal');
                if (productModal) {
                    bootstrap.Modal.getOrCreateInstance(productModal).hide();
                }
                if (measurementModal) {
                    bootstrap.Modal.getOrCreateInstance(measurementModal).show();
                }
                window.setTimeout(() => measurementInput?.focus(), 250);
            });
        });

        if (measurementForm && measurementModal) {
            measurementForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const name = measurementInput.value.trim();
                const submitButton = measurementForm.querySelector('button[type="submit"]');
                if (!name || !measurementTargetId) {
                    mmbNotify({ type: 'warning', title: 'Measurement name required', message: 'Enter a measurement before saving.' });
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

                const formData = new FormData();
                formData.append('measurement_name', name);
                const measurementEndpoint = measurementTargetId === 'unit_measurement'
                    ? '../function/add_serving_unit_ajax.php'
                    : '../function/add_measurement_ajax.php';
                fetch(measurementEndpoint, { method: 'POST', body: formData })
                    .then((response) => response.json().then((data) => ({ status: response.status, data })))
                    .then(({ status, data }) => {
                        if (status !== 200 || (!data.success && !data.existing_id)) {
                            throw new Error(data.message || 'Unable to save measurement.');
                        }

                        const target = document.getElementById(measurementTargetId);
                        const id = data.id || data.existing_id;
                        const savedName = data.name || data.existing_name || name;
                        if (target && !Array.from(target.options).some((option) => option.value === String(id) || option.value === savedName)) {
                            const newOption = new Option(savedName, target.id === 'unit_measurement' ? id : savedName);
                            newOption.dataset.measurementId = String(id);
                            target.add(newOption);
                        }
                        if (target) {
                            target.value = target.id === 'unit_measurement' ? String(id) : savedName;
                            const selectedOption = target.options[target.selectedIndex];
                            if (selectedOption) selectedOption.dataset.measurementId = String(id);
                        }

                        mmbNotify({
                            type: data.success ? 'success' : 'warning',
                            title: data.success ? 'Measurement added' : 'Measurement already exists',
                            message: data.message || 'The measurement is ready to use.'
                        });
                        measurementForm.reset();
                        closeMeasurementAndReturn();
                    })
                    .catch((error) => {
                        mmbNotify({ type: 'danger', title: 'Could not add measurement', message: error.message });
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-plus me-1"></i>Add Measurement';
                    });
            });

            measurementModal.addEventListener('hidden.bs.modal', function () {
                measurementForm.reset();
                measurementTargetId = '';
                if (returnToProductModal) {
                    const productModal = document.getElementById('addProductModal');
                    if (productModal) {
                        bootstrap.Modal.getOrCreateInstance(productModal).show();
                    }
                    returnToProductModal = false;
                }
            });
        }

        const dosageFormModal = document.getElementById('addDosageFormModal');
        const dosageFormForm = document.getElementById('addDosageFormForm');
        const dosageFormInput = document.getElementById('dosage_form_name');
        const dosageFormSelect = document.getElementById('dosage_form_id');
        let returnToProductModalAfterDosageForm = false;

        document.querySelectorAll('[data-open-dosage-form-modal]').forEach((button) => {
            button.addEventListener('click', function () {
                returnToProductModalAfterDosageForm = true;
                const productModal = document.getElementById('addProductModal');
                if (productModal) {
                    bootstrap.Modal.getOrCreateInstance(productModal).hide();
                }
                bootstrap.Modal.getOrCreateInstance(dosageFormModal).show();
                window.setTimeout(() => dosageFormInput?.focus(), 250);
            });
        });

        if (dosageFormForm && dosageFormModal && dosageFormSelect) {
            dosageFormForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const name = dosageFormInput.value.trim();
                const submitButton = dosageFormForm.querySelector('button[type="submit"]');
                if (!name) {
                    mmbNotify({ type: 'warning', title: 'Product form required', message: 'Enter a product form before saving.' });
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
                const formData = new FormData();
                formData.append('form_name', name);

                fetch('../function/add_dosage_form_ajax.php', { method: 'POST', body: formData })
                    .then((response) => response.json().then((data) => ({ status: response.status, data })))
                    .then(({ status, data }) => {
                        if (status !== 200 || (!data.success && !data.existing_id)) {
                            throw new Error(data.message || 'Unable to save product form.');
                        }

                        const id = data.id || data.existing_id;
                        const savedName = data.name || data.existing_name || name;
                        let option = Array.from(dosageFormSelect.options).find((item) => item.value === String(id));
                        if (!option) {
                            option = new Option(savedName, id);
                            dosageFormSelect.add(option);
                        }
                        option.textContent = savedName;
                        option.setAttribute('data-name', savedName);
                        dosageFormSelect.value = String(id);
                        dosageFormValue.value = savedName;
                        mmbNotify({
                            type: data.success ? 'success' : 'warning',
                            title: data.success ? 'Product form added' : 'Product form already exists',
                            message: data.message || 'The product form is ready to use.'
                        });
                        dosageFormForm.reset();
                        bootstrap.Modal.getOrCreateInstance(dosageFormModal).hide();
                    })
                    .catch((error) => {
                        mmbNotify({ type: 'danger', title: 'Could not add product form', message: error.message });
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-plus me-1"></i>Add Product Form';
                    });
            });

            dosageFormModal.addEventListener('hidden.bs.modal', function () {
                dosageFormForm.reset();
                if (returnToProductModalAfterDosageForm) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('addProductModal')).show();
                    returnToProductModalAfterDosageForm = false;
                }
            });
        }

        document.addEventListener('click', function (event) {
            const measurementDeleteButton = event.target.closest('[data-delete-measurement]');
            const dosageDeleteButton = event.target.closest('[data-delete-dosage-form]');
            if (!measurementDeleteButton && !dosageDeleteButton) return;

            const selectId = measurementDeleteButton
                ? measurementDeleteButton.dataset.deleteMeasurement
                : 'dosage_form_id';
            const select = document.getElementById(selectId);
            const option = select?.options[select.selectedIndex];
            const id = measurementDeleteButton
                ? option?.dataset.measurementId
                : option?.value;
            const name = option?.textContent.trim();
            if (!select || !id || !name) {
                mmbNotify({ type: 'warning', title: 'Select an item first' });
                return;
            }
            if (!window.confirm(`Delete "${name}"? This cannot be undone.`)) return;

            const formData = new FormData();
            formData.append('id', id);
            const endpoint = measurementDeleteButton
                ? (selectId === 'unit_measurement'
                    ? '../function/delete_serving_unit_ajax.php'
                    : '../function/delete_measurement_ajax.php')
                : '../function/delete_dosage_form_ajax.php';
            const button = measurementDeleteButton || dosageDeleteButton;
            button.disabled = true;
            fetch(endpoint, { method: 'POST', body: formData })
                .then((response) => response.json().then((data) => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200 || !data.success) {
                        throw new Error(data.message || 'Unable to delete the selected item.');
                    }
                    option.remove();
                    select.value = '';
                    if (selectId === 'dosage_form_id') {
                        dosageFormValue.value = '';
                    }
                    mmbNotify({ type: 'success', title: 'Deleted successfully', message: data.message });
                })
                .catch((error) => {
                    mmbNotify({ type: 'danger', title: 'Could not delete item', message: error.message });
                })
                .finally(() => {
                    button.disabled = false;
                });
        });

        const categorySelect = document.getElementById('category_id');
        if (categorySelect) {
            categorySelect.addEventListener('change', function () {
                const optionEl = this.options[this.selectedIndex];
                if (this.value) {
                    updateCategoryRuleNote(optionEl);
                    toggleDosageFormVisibility(optionEl.textContent || '');
                } else {
                    toggleDosageFormVisibility('');
                }
            });
        }

        const dosageFormValue = document.getElementById('dosage_form');
        const dosageFormId = document.getElementById('dosage_form_id');
        const strengthQuantityFields = document.getElementById('strengthQuantityFields');
        const toggleDosageAndStrengthFields = () => {
            const dosageFormContainer = document.getElementById('dosageFormContainer');
            if (dosageFormContainer) {
                dosageFormContainer.style.display = 'grid';
            }

            if (strengthQuantityFields) {
                strengthQuantityFields.style.display = 'grid';
            }
        };

        if (dosageFormValue && dosageFormId) {
            const syncDosageForm = function () {
                const selectedOption = dosageFormId.options[dosageFormId.selectedIndex];
                dosageFormValue.value = this.value ? (selectedOption.getAttribute('data-name') || selectedOption.textContent.trim()) : '';
                toggleDosageAndStrengthFields();
            };
            dosageFormId.addEventListener('change', syncDosageForm);
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', toggleDosageAndStrengthFields);
        }

        toggleDosageAndStrengthFields();

        bindSearchableList({
            inputId: 'batch_supplier_search',
            valueId: 'batch_supplier_id',
            listId: 'batch_supplier_list'
        });

        // ── ISSUE #6 (1): Package Size accepts whole numbers >= 1 only ──
        // Type filters strip illegal characters as they are typed.
        const spqInput = document.getElementById('strength_per_quantity');
        if (spqInput) {
            spqInput.addEventListener('input', function () {
                const cleaned = this.value.replace(/[^0-9]/g, '');
                if (this.value !== cleaned) this.value = cleaned;
            });
        }
        const strengthInputEl = document.getElementById('strength');
        if (strengthInputEl) {
            strengthInputEl.addEventListener('input', function () {
                const cleaned = this.value.replace(/-/g, '');
                if (this.value !== cleaned) this.value = cleaned;
            });
        }

        const batchPromptInput = document.getElementById('add_batch_prompt');
        const batchFields = document.getElementById('batchPromptFields');
        const batchButtons = document.querySelectorAll('[data-batch-option]');
        const productAddModal = document.getElementById('addProductModal');
        const purchaseCostInput = productAddModal ? productAddModal.querySelector('#batch_purchase_cost') : null;
        const markupInput = productAddModal ? productAddModal.querySelector('#batch_markup') : null;
        const salePriceInput = productAddModal ? productAddModal.querySelector('#batch_sale_price') : null;

        const updateBatchSalePrice = () => {
            if (!purchaseCostInput || !markupInput || !salePriceInput) {
                return;
            }

            const purchaseCost = parseFloat(purchaseCostInput.value);
            const markup = parseFloat(markupInput.value);

            if (!Number.isFinite(purchaseCost) || purchaseCost < 0) {
                salePriceInput.value = '';
                return;
            }

            const safeMarkup = Number.isFinite(markup) && markup >= 0 ? markup : 0;
            salePriceInput.value = (purchaseCost * (1 + safeMarkup / 100)).toFixed(2);
        };

        if (purchaseCostInput && markupInput && salePriceInput) {
            if (!markupInput.value) {
                markupInput.value = '5';
            }
            purchaseCostInput.oninput = updateBatchSalePrice;
            markupInput.oninput = updateBatchSalePrice;
        }

        const clearBatchFields = () => {
            ['batch_number', 'batch_supplier_id', 'batch_quantity', 'batch_purchase_cost', 'batch_sale_price', 'batch_markup', 'batch_expiry_date'].forEach((fieldId) => {
                const field = document.getElementById(fieldId);
                if (field) field.value = '';
            });
            if (markupInput) markupInput.value = '5';
        };

        const toggleBatchFields = (option) => {
            const isYes = option === 'yes';
            if (batchPromptInput) batchPromptInput.value = option;
            if (batchFields) batchFields.style.display = isYes ? 'block' : 'none';

            batchButtons.forEach((button) => {
                const active = button.dataset.batchOption === option;
                button.classList.toggle('active', active);
                button.classList.toggle('btn-primary', active && isYes);
                button.classList.toggle('btn-outline-primary', !active && isYes);
                button.classList.toggle('btn-secondary', active && !isYes);
                button.classList.toggle('btn-outline-secondary', !active && !isYes);
            });

            if (!isYes) {
                clearBatchFields();
            }
        };

        toggleBatchFields('no');

        batchButtons.forEach((button) => {
            button.addEventListener('click', function () {
                toggleBatchFields(this.dataset.batchOption);
            });
        });
    });

    // ADD FORM VALIDATION BEFORE SUBMIT
    function validateAddProductForm(event) {
        const categoryValue = document.getElementById('category_id');
        const brandedName = document.getElementById('branded_name');
        const genericName = document.getElementById('generic_name');
        const strength = document.getElementById('strength');
        const barcodeInput = document.getElementById('barcode');
        const imageInput = document.getElementById('product_image_input');
        const addBatchPrompt = document.getElementById('add_batch_prompt');
        const receivedQuantity = document.getElementById('batch_quantity');

        let errors = [];

        if (!genericName.value.trim()) errors.push('Generic Name is required');
        if (!categoryValue.value) errors.push('Category is required - Please select a category');
        if (!strength || strength.value === '') errors.push('Amount per Serving is required');
        if (strength && strength.value !== '' && Number(strength.value) < 0) {
            errors.push('Amount per Serving cannot be negative');
        }
        const servingUnit = document.getElementById('unit_measurement');
        if (!servingUnit || !servingUnit.value) errors.push('Serving Unit is required');

        // ISSUE #6 (1): Package Size — whole number >= 1, no negatives/decimals
        const spqField = document.getElementById('strength_per_quantity');
        if (spqField && spqField.value !== '') {
            const spqNum = Number(spqField.value);
            if (!Number.isFinite(spqNum) || !Number.isInteger(spqNum) || spqNum < 1) {
                errors.push('Package Size must be a whole number of at least 1 (no decimals or negatives)');
            }
        }
        if (addBatchPrompt && addBatchPrompt.value === 'yes') {
            if (receivedQuantity && (!receivedQuantity.value || Number(receivedQuantity.value) <= 0)) {
                errors.push('Quantity Received must be greater than zero when adding a batch');
            }
        }

        if (errors.length > 0) {
            mmbAlert({
                type: 'warning',
                title: 'Form Validation Error',
                message: errors.join('\n'),
                okLabel: 'Fix it'
            });
            event.preventDefault();
            return false;
        }

        return true;
    }

    // ── LIVE BARCODE PREVIEW (issue #4) ──────────────────────────
    // Renders a real Code 128 barcode from the product code as the
    // user types / clicks Auto. Full-size preview + PNG download live
    // in the product list (barcode button per row).
    document.addEventListener('DOMContentLoaded', function () {
        var barcodeInput = document.getElementById('barcode');
        var previewWrap = document.getElementById('barcodePreviewWrap');
        var previewCanvas = document.getElementById('barcodePreviewCanvas');
        if (!barcodeInput || !previewWrap || !previewCanvas || !window.mmbBarcodePreview) return;

        var previewTimer = null;
        function renderPreview() {
            var code = barcodeInput.value.trim();
            if (code.length < 4) {
                previewWrap.classList.add('d-none');
                return;
            }
            var label = (document.getElementById('branded_name').value.trim() + ' ' +
                document.getElementById('generic_name').value.trim()).trim();
            var drawn = mmbBarcodePreview(previewCanvas, code, { label: label, moduleWidth: 2, barHeight: 54 });
            previewWrap.classList.toggle('d-none', !drawn);
        }

        barcodeInput.addEventListener('input', function () {
            clearTimeout(previewTimer);
            previewTimer = setTimeout(renderPreview, 250);
        });
        renderPreview();
    });
</script>