<?php
require_once __DIR__ . "/../function/addprodfunct.php";
require_once __DIR__ . "/../conn/connection_links.php";

use Classes\ProductManagement;

$product = new ProductManagement($db);

// ✅ USE CLASS FUNCTIONS
$categories = $product->getCategories();
$unitMeasurements = $product->getUnitMeasurements();
$dosageForms = $product->getDosageForms();

?>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" enctype="multipart/form-data" onsubmit="return validateAddProductForm(event)">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- BASIC INFORMATION SECTION -->
                    <h6 class="mb-3 text-secondary fw-bold">Basic Information</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branded_name" class="form-label">Brand Name</label>
                            <input type="text" id="branded_name" name="branded_name" class="form-control"
                                placeholder="e.g., Tylenol">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="generic_name" class="form-label">Generic Name</label>
                            <input type="text" id="generic_name" name="generic_name" class="form-control"
                                placeholder="e.g., Paracetamol" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit_measurement_search" class="form-label">Unit of Measurement</label>
                            <input type="text" id="unit_measurement_search" class="form-control" list="unit_measurement_list" placeholder="Select Unit" autocomplete="off">
                            <input type="hidden" id="unit_measurement" name="unit_measurement" value="">
                            <datalist id="unit_measurement_list">
                                <?php foreach ($unitMeasurements as $unit): ?>
                                    <option value="<?= htmlspecialchars($unit['name'] ?? '') ?>" data-id="<?= (int) ($unit['id'] ?? 0) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="strength" class="form-label">Strength <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" id="strength" name="strength" class="form-control"
                                    placeholder="e.g., 200,500,1000" required>
                            </div>
                        </div>
                    </div>

                    <div id="dosageFormContainer" class="row" style="display:none;">
                        <div class="col-md-12 mb-3">
                            <label for="dosage_form_search" class="form-label">Dosage Form</label>
                            <input type="text" id="dosage_form_search" class="form-control" list="dosage_form_list" placeholder="Select dosage form" autocomplete="off">
                            <input type="hidden" id="dosage_form" name="dosage_form" value="">
                            <input type="hidden" id="dosage_form_id" name="dosage_form_id" value="">
                            <datalist id="dosage_form_list">
                                <?php foreach ($dosageForms as $form): ?>
                                    <option value="<?= htmlspecialchars($form['name'] ?? '') ?>" data-id="<?= (int) ($form['id'] ?? 0) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>

                    <div id="strengthQuantityFields" class="row" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="strength_per_quantity" class="form-label">Strength per Quantity</label>
                            <input type="number" id="strength_per_quantity" name="strength_per_quantity" step="0.01"
                                class="form-control" placeholder="e.g., 500">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="strength_per_quantity_unit" class="form-label">Strength Unit</label>
                            <input type="text" id="strength_per_quantity_unit" name="strength_per_quantity_unit" class="form-control"
                                placeholder="e.g., mg, g, ml">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="barcode" class="form-label">Barcode <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="barcode" name="barcode" class="form-control"
                                    placeholder="e.g., 123456789012" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()">
                                    <i class="fas fa-sync-alt"></i> Auto
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_search" class="form-label">Category <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="category_search" class="form-control" list="category_list" placeholder="Select Category" autocomplete="off" required>
                            <input type="hidden" id="category_id" name="category_id" value="">
                            <datalist id="category_list">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['category_name'] ?? '') ?>"
                                        data-id="<?= (int) ($cat['id'] ?? 0) ?>"
                                        data-senior="<?= (int) ($cat['senior_discount'] ?? 0) ?>"
                                        data-pwd="<?= (int) ($cat['pwd_discount'] ?? 0) ?>"
                                        data-vat="<?= (int) ($cat['has_vat'] ?? 0) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <div id="categoryRuleNote" class="form-text text-muted mt-1">
                                Select a category to see whether senior/PWD discounts apply.
                            </div>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Do you want to add a batch and quantity now?</label>
                        <div class="btn-group w-100" role="group" aria-label="Add batch prompt">
                            <button type="button" class="btn btn-outline-success" data-batch-option="yes">Yes</button>
                            <button type="button" class="btn btn-secondary active" data-batch-option="no">No</button>
                        </div>
                        <input type="hidden" name="add_batch_prompt" id="add_batch_prompt" value="no">
                    </div>

                    <div id="batchPromptFields" class="border rounded p-3 bg-light" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="batch_number" class="form-label">Batch No</label>
                                <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="e.g. BATCH-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="batch_supplier_search" class="form-label">Supplier (optional)</label>
                                <input type="text" id="batch_supplier_search" class="form-control" list="batch_supplier_list" placeholder="Select supplier or leave blank" autocomplete="off">
                                <input type="hidden" id="batch_supplier_id" name="supplier_id" value="">
                                <datalist id="batch_supplier_list">
                                    <?php
                                        try {
                                            $stmt = $db->prepare("SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");
                                            $stmt->execute();
                                            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $supplier):
                                    ?>
                                        <option value="<?= htmlspecialchars($supplier['supplier_name'] ?? '') ?>" data-id="<?= (int)($supplier['id'] ?? 0) ?>">
                                    <?php
                                            endforeach;
                                        } catch (Exception $e) {
                                        }
                                    ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="batch_quantity" class="form-label">Quantity Received</label>
                                <input type="number" id="batch_quantity" name="received_quantity" class="form-control" min="1" placeholder="e.g. 10">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="batch_purchase_cost" class="form-label">Purchase Cost</label>
                                <input type="number" id="batch_purchase_cost" name="purchase_cost" class="form-control" step="0.01" min="0" placeholder="e.g. 50.00">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="batch_sale_price" class="form-label">Sale Price</label>
                                <input type="number" id="batch_sale_price" name="sale_price" class="form-control" step="0.01" min="0" placeholder="e.g. 75.00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="batch_markup" class="form-label">Markup %</label>
                                <input type="number" id="batch_markup" name="markup" class="form-control" step="0.01" min="0" value="5" placeholder="e.g. 20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="batch_expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" id="batch_expiry_date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- IMAGE SECTION -->
                    <h6 class="mb-3 text-secondary fw-bold">Product Image</h6>
                    <div class="mb-3">
                        <label for="product_image_input" class="form-label">Upload Image <span
                                class="text-danger">*</span></label>
                        <input type="file" id="product_image_input" name="product_image" class="form-control"
                            accept="image/*" required onchange="previewImage(event)">
                        <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Recommended:
                            500x500px, JPG/PNG, max 5MB</small>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="addProduct" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<script src="../js/auto_generatebarcode.js"></script>

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
        const selectedCategory = normalizeCategoryName(categoryName);
        const dosageRequiredCategories = [
            'Prescription Medicines',
            'Over-the-Counter (OTC)',
            'Vitamins & Supplements',
            'Herbal Products',
            'First Aid',
            'Health & Wellness'
        ].map(normalizeCategoryName);

        const shouldShow = dosageRequiredCategories.includes(selectedCategory);

        if (dosageFormContainer) {
            dosageFormContainer.style.display = shouldShow ? 'block' : 'none';
        }

        if (!shouldShow) {
            const dosageFormSearch = document.getElementById('dosage_form_search');
            const dosageForm = document.getElementById('dosage_form');
            const dosageFormId = document.getElementById('dosage_form_id');

            if (dosageFormSearch) dosageFormSearch.value = '';
            if (dosageForm) dosageForm.value = '';
            if (dosageFormId) dosageFormId.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindSearchableList({
            inputId: 'category_search',
            valueId: 'category_id',
            listId: 'category_list',
            onSelect: (optionEl) => {
                if (optionEl) {
                    updateCategoryRuleNote(optionEl);
                    toggleDosageFormVisibility(optionEl.value || '');
                } else {
                    toggleDosageFormVisibility('');
                }
            }
        });

        bindSearchableList({
            inputId: 'unit_measurement_search',
            valueId: 'unit_measurement',
            listId: 'unit_measurement_list'
        });

        const dosageFormInput = document.getElementById('dosage_form_search');
        const dosageFormValue = document.getElementById('dosage_form');
        const dosageFormId = document.getElementById('dosage_form_id');
        const strengthQuantityFields = document.getElementById('strengthQuantityFields');
        const strengthDependentDosageForms = ['Syrup', 'Suspension', 'Solution', 'Oral Drops', 'Oral Suspension', 'Emulsion', 'Elixir'];
        const dosageRequiredCategories = [
            'Prescription Medicines',
            'Over-the-Counter (OTC)',
            'Vitamins & Supplements',
            'Herbal Products',
            'First Aid',
            'Health & Wellness'
        ];

        const toggleDosageAndStrengthFields = () => {
            const selectedCategory = normalizeCategoryName(document.getElementById('category_search')?.value || '');
            const selectedDosage = (dosageFormInput?.value || '').trim();
            const isCategoryAllowed = dosageRequiredCategories.some((category) => normalizeCategoryName(category) === selectedCategory);
            const isDosageLiquid = strengthDependentDosageForms.some((form) => form.toLowerCase() === selectedDosage.toLowerCase());

            const dosageVisible = isCategoryAllowed;
            const strengthVisible = isCategoryAllowed && isDosageLiquid;

            const dosageFormContainer = document.getElementById('dosageFormContainer');
            if (dosageFormContainer) {
                dosageFormContainer.style.display = dosageVisible ? 'block' : 'none';
            }

            if (strengthQuantityFields) {
                strengthQuantityFields.style.display = strengthVisible ? 'flex' : 'none';
            }

            if (!strengthVisible) {
                const strengthPerQuantity = document.getElementById('strength_per_quantity');
                const strengthUnit = document.getElementById('strength_per_quantity_unit');
                if (strengthPerQuantity) strengthPerQuantity.value = '';
                if (strengthUnit) strengthUnit.value = '';
            }

            if (!dosageVisible) {
                if (dosageFormInput) dosageFormInput.value = '';
                if (dosageFormValue) dosageFormValue.value = '';
                if (dosageFormId) dosageFormId.value = '';
            }
        };

        if (dosageFormInput && dosageFormValue && dosageFormId) {
            const dosageOptions = Array.from(document.querySelectorAll('#dosage_form_list option'));
            const syncDosageForm = function () {
                const match = dosageOptions.find((option) => option.value.trim() === dosageFormInput.value.trim());
                dosageFormValue.value = match ? match.value.trim() : '';
                dosageFormId.value = match ? (match.getAttribute('data-id') || '') : '';
                toggleDosageAndStrengthFields();
            };
            dosageFormInput.addEventListener('input', syncDosageForm);
            dosageFormInput.addEventListener('change', syncDosageForm);
        }

        const categorySearchInput = document.getElementById('category_search');
        if (categorySearchInput) {
            categorySearchInput.addEventListener('input', toggleDosageAndStrengthFields);
            categorySearchInput.addEventListener('change', toggleDosageAndStrengthFields);
        }

        toggleDosageAndStrengthFields();

        bindSearchableList({
            inputId: 'batch_supplier_search',
            valueId: 'batch_supplier_id',
            listId: 'batch_supplier_list'
        });

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
                button.classList.toggle('btn-success', active && isYes);
                button.classList.toggle('btn-outline-success', !active && isYes);
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

    // 🔥 ADD FORM VALIDATION BEFORE SUBMIT
    function validateAddProductForm(event) {
        const categoryValue = document.getElementById('category_id');
        const unitValue = document.getElementById('unit_measurement');
        const brandedName = document.getElementById('branded_name');
        const genericName = document.getElementById('generic_name');
        const strength = document.getElementById('strength');
        const barcodeInput = document.getElementById('barcode');
        const imageInput = document.getElementById('product_image_input');
        const addBatchPrompt = document.getElementById('add_batch_prompt');
        const batchNumber = document.getElementById('batch_number');
        const receivedQuantity = document.getElementById('batch_quantity');

        let errors = [];

        if (!brandedName.value.trim()) errors.push('Brand Name is required');
        if (!genericName.value.trim()) errors.push('Generic Name is required');
        if (!strength.value.trim()) errors.push('Strength is required');
        if (!barcodeInput.value.trim()) errors.push('Barcode is required');
        if (!imageInput.value) errors.push('Product Image is required');

        if (!categoryValue.value) errors.push('Category is required - Please select a category');
        if (!unitValue.value) errors.push('Unit of Measurement is required - Please select a unit');

        if (addBatchPrompt && addBatchPrompt.value === 'yes') {
            if (batchNumber && !batchNumber.value.trim()) {
                errors.push('Batch No is required when adding a batch');
            }
            if (receivedQuantity && (!receivedQuantity.value || Number(receivedQuantity.value) <= 0)) {
                errors.push('Quantity Received must be greater than zero when adding a batch');
            }
        }

        if (errors.length > 0) {
            alert('❌ Form Validation Error:\n\n' + errors.join('\n'));
            event.preventDefault();
            return false;
        }

        return true;
    }
</script>