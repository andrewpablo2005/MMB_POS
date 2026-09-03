<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
$dosageForms = $product->getDosageForms(); ?>
<?php foreach ($products as $prod): ?>
    <div class="modal fade" id="editProduct<?= $prod['id'] ?>" tabindex="-1" aria-labelledby="updateproductLabel<?= $prod['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg add-product-dialog">
            <div class="modal-content add-product-modal">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateproductLabel<?= $prod['id'] ?>"><span class="modal-head-icon"><i class="fas fa-pen-to-square"></i></span> Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body add-product-body">
                        <input type="hidden" name="id" value="<?= $prod['id'] ?? '' ?>">
                        <input type="hidden" name="old_image" value="<?= $prod['imageproduct'] ?? '' ?>">
                        <input type="hidden" name="updateProduct" value="1">

                        <div class="add-product-section-head">
                            <div><span class="add-product-section-kicker">01</span><h6>Product identity</h6></div>
                            <span>Names and classification</span>
                        </div>

                        <div class="add-product-row">
                            <div class="add-product-field">
                                <label for="edit_branded_name_<?= $prod['id'] ?>" class="form-label">Brand Name</label>
                                <input type="text" id="edit_branded_name_<?= $prod['id'] ?>" name="branded_name" class="form-control"
                                    value="<?= htmlspecialchars($prod['branded_name'] ?? '') ?>" placeholder="e.g., Tylenol">
                            </div>
                            <div class="add-product-field">
                                <label for="edit_generic_name_<?= $prod['id'] ?>" class="form-label">Generic Name</label>
                                <input type="text" id="edit_generic_name_<?= $prod['id'] ?>" name="generic_name" class="form-control"
                                    value="<?= htmlspecialchars($prod['generic_name'] ?? '') ?>" placeholder="e.g., Paracetamol" required>
                            </div>
                        </div>

                        <div class="add-product-section-head add-product-section-head--spaced">
                            <div><span class="add-product-section-kicker">02</span><h6>Dosage details</h6></div>
                            <span>Strength and presentation</span>
                        </div>

                        <div class="add-product-row">
                            <div class="add-product-field">
                                <label for="edit_unit_measurement_search_<?= $prod['id'] ?>" class="form-label">Unit of Measurement</label>
                                <input type="text" id="edit_unit_measurement_search_<?= $prod['id'] ?>" class="form-control" list="edit_unit_measurement_list_<?= $prod['id'] ?>" placeholder="Select Unit" autocomplete="off"
                                    value="<?= htmlspecialchars($prod['measurement_name'] ?? '') ?>">
                                <input type="hidden" id="edit_unit_measurement_<?= $prod['id'] ?>" name="unit_measurement" value="<?= (int) ($prod['measurement_id'] ?? 0) ?>">
                                <datalist id="edit_unit_measurement_list_<?= $prod['id'] ?>">
                                    <?php foreach ($unitMeasurements as $unit): ?>
                                        <option value="<?= htmlspecialchars($unit['name'] ?? '') ?>" data-id="<?= (int) ($unit['id'] ?? 0) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="add-product-field">
                                <label for="edit_strength_<?= $prod['id'] ?>" class="form-label">Strength <span class="text-danger">*</span></label>
                                <input type="number" id="edit_strength_<?= $prod['id'] ?>" name="strength" class="form-control"
                                    placeholder="e.g., 200,500,1000" value="<?= htmlspecialchars((string) ($prod['strength'] ?? '')) ?>" required>
                            </div>
                        </div>

                        <div id="edit_dosageFormContainer_<?= $prod['id'] ?>" class="add-product-row add-product-row--single" style="display:none;">
                            <div class="add-product-field add-product-field--full">
                                <label for="edit_dosage_form_search_<?= $prod['id'] ?>" class="form-label">Dosage Form</label>
                                <input type="text" id="edit_dosage_form_search_<?= $prod['id'] ?>" class="form-control" list="edit_dosage_form_list_<?= $prod['id'] ?>" placeholder="Select dosage form" autocomplete="off"
                                    value="<?= htmlspecialchars($prod['dosage_form'] ?? '') ?>">
                                <input type="hidden" id="edit_dosage_form_<?= $prod['id'] ?>" name="dosage_form" value="<?= htmlspecialchars($prod['dosage_form'] ?? '') ?>">
                                <input type="hidden" id="edit_dosage_form_id_<?= $prod['id'] ?>" name="dosage_form_id" value="<?= (int) ($prod['dosage_form_id'] ?? 0) ?>">
                                <datalist id="edit_dosage_form_list_<?= $prod['id'] ?>">
                                    <?php foreach ($dosageForms as $form): ?>
                                        <option value="<?= htmlspecialchars($form['name'] ?? '') ?>" data-id="<?= (int) ($form['id'] ?? 0) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div id="edit_strengthQuantityFields_<?= $prod['id'] ?>" class="add-product-row" style="display:none;">
                            <div class="add-product-field">
                                <label for="edit_strength_per_quantity_<?= $prod['id'] ?>" class="form-label">Strength per Quantity</label>
                                <input type="number" id="edit_strength_per_quantity_<?= $prod['id'] ?>" name="strength_per_quantity" step="0.01" class="form-control"
                                    value="<?= htmlspecialchars((string) ($prod['strength_per_quantity'] ?? '')) ?>" placeholder="e.g., 500">
                            </div>
                            <div class="add-product-field">
                                <label for="edit_strength_per_unit_<?= $prod['id'] ?>" class="form-label">Strength Unit</label>
                                <input type="text" id="edit_strength_per_unit_<?= $prod['id'] ?>" name="strength_per_quantity_unit" class="form-control"
                                    value="<?= htmlspecialchars($prod['strength_per_quantity_unit'] ?? '') ?>" placeholder="e.g., mg, g, ml">
                            </div>
                        </div>

                        <div class="add-product-section-head add-product-section-head--spaced">
                            <div><span class="add-product-section-kicker">03</span><h6>Classification and code</h6></div>
                            <span>Category and scanning</span>
                        </div>

                        <div class="add-product-row">
                            <div class="add-product-field">
                                <label for="edit_category_search_<?= $prod['id'] ?>" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" id="edit_category_search_<?= $prod['id'] ?>" class="form-control" list="edit_category_list_<?= $prod['id'] ?>" placeholder="Select Category" autocomplete="off"
                                    value="<?= htmlspecialchars($prod['category_name'] ?? '') ?>" required>
                                <input type="hidden" id="edit_category_id_<?= $prod['id'] ?>" name="category_id" value="<?= (int) ($prod['category_id'] ?? 0) ?>">
                                <datalist id="edit_category_list_<?= $prod['id'] ?>">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['category_name'] ?? '') ?>"
                                            data-id="<?= (int) ($cat['id'] ?? 0) ?>"
                                            data-senior="<?= (int) ($cat['senior_discount'] ?? 0) ?>"
                                            data-pwd="<?= (int) ($cat['pwd_discount'] ?? 0) ?>"
                                            data-vat="<?= (int) ($cat['has_vat'] ?? 0) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <div id="edit_categoryRuleNote_<?= $prod['id'] ?>" class="form-text text-muted mt-1">
                                    This is controlled by the selected category.
                                </div>
                            </div>
                            <div class="add-product-field">
                                <label for="edit_barcode_<?= $prod['id'] ?>" class="form-label">Product Code <span class="text-danger">*</span></label>
                                <input type="text" id="edit_barcode_<?= $prod['id'] ?>" name="barcode" class="form-control"
                                    value="<?= htmlspecialchars($prod['barcode'] ?? '') ?>" placeholder="e.g., 123456789012" required>
                            </div>
                        </div>

                        <div class="add-product-section-head add-product-section-head--spaced">
                            <div><span class="add-product-section-kicker">04</span><h6>Product image</h6></div>
                            <span>Catalog display image</span>
                        </div>

                        <?php if (!empty($prod['imageproduct'])): ?>
                            <div class="add-product-row add-product-row--single">
                                <div class="add-product-field add-product-field--full">
                                <label class="form-label">Current Image</label>
                                <div class="text-center">
                                    <img src="../img/<?= htmlspecialchars($prod['imageproduct']) ?>" alt="Current Product Image"
                                        style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background-color: #f8f9fa;">
                                </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="add-product-row add-product-row--single">
                        <div class="add-product-field add-product-field--full">
                            <label for="edit_product_image_<?= $prod['id'] ?>" class="form-label">Replace Image (Optional)</label>
                            <input type="file" id="edit_product_image_<?= $prod['id'] ?>" name="product_image" class="form-control" accept="image/*"
                                onchange="previewUpdateImage(event, <?= $prod['id'] ?>)">
                            <small class="text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Recommended: 500x500px, JPG/PNG, max 5MB</small>
                        </div>
                        </div>

                        <div id="image_preview_container_<?= $prod['id'] ?>" style="display: none;">
                            <div class="text-center">
                                <img id="image_preview_<?= $prod['id'] ?>" src="" alt="New Image Preview"
                                    style="max-width: 100%; max-height: 250px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background-color: #f8f9fa;">
                                <small class="text-body-secondary d-block mt-2"><i class="fas fa-circle-check me-1 text-primary"></i>New image selected</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="updateProduct" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    function normalizeCategoryName(name) {
        return String(name || '').trim().toLowerCase();
    }

    function updateCategoryRuleNoteById(noteId, optionEl) {
        const note = document.getElementById(noteId);
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

        if (!inputEl || !valueEl || !listEl) return;

        const syncFromInput = () => {
            const query = inputEl.value.trim();
            const optionEl = Array.from(listEl.options).find((option) => option.value.trim() === query);

            if (optionEl) {
                valueEl.value = optionEl.getAttribute('data-id') || '';
                if (onSelect) onSelect(optionEl);
            } else {
                valueEl.value = '';
                if (onSelect) onSelect(null);
            }
        };

        inputEl.addEventListener('input', syncFromInput);
        inputEl.addEventListener('change', syncFromInput);
    }

    function toggleEditDosageFormVisibility(productId) {
        const categoryInput = document.getElementById('edit_category_search_' + productId);
        const categoryList = document.getElementById('edit_category_list_' + productId);
        const dosageInput = document.getElementById('edit_dosage_form_search_' + productId);
        const dosageContainer = document.getElementById('edit_dosageFormContainer_' + productId);
        const dosageFields = document.getElementById('edit_strengthQuantityFields_' + productId);
        const dosageValue = document.getElementById('edit_dosage_form_' + productId);
        const dosageId = document.getElementById('edit_dosage_form_id_' + productId);

        if (!categoryInput || !dosageInput) return;

        const categoryName = categoryInput.value.trim();
        const match = categoryList ? Array.from(categoryList.options).find((option) => option.value.trim() === categoryName) : null;
        const selectedCategory = match ? (match.value || categoryName) : categoryName;
        const selectedDosage = (dosageInput.value || '').trim();

        const dosageRequiredCategories = [
            'Prescription Medicines',
            'Over-the-Counter (OTC)',
            'Vitamins & Supplements',
            'Herbal Products',
            'First Aid',
            'Health & Wellness'
        ].map(normalizeCategoryName);

        const strengthDependentDosageForms = ['Syrup', 'Suspension', 'Solution', 'Oral Drops', 'Oral Suspension', 'Emulsion', 'Elixir'];
        const isCategoryAllowed = dosageRequiredCategories.includes(normalizeCategoryName(selectedCategory));
        const isDosageLiquid = strengthDependentDosageForms.some((form) => form.toLowerCase() === selectedDosage.toLowerCase());

        if (dosageContainer) dosageContainer.style.display = isCategoryAllowed ? 'block' : 'none';
        if (dosageFields) dosageFields.style.display = (isCategoryAllowed && isDosageLiquid) ? 'flex' : 'none';

        if (!isCategoryAllowed) {
            dosageInput.value = '';
            if (dosageValue) dosageValue.value = '';
            if (dosageId) dosageId.value = '';
        }

        if (isCategoryAllowed && !isDosageLiquid) {
            const qty = document.getElementById('edit_strength_per_quantity_' + productId);
            const unit = document.getElementById('edit_strength_per_unit_' + productId);
            if (qty) qty.value = '';
            if (unit) unit.value = '';
        }
    }

    function previewUpdateImage(event, productId) {
        const file = event.target.files[0];
        const preview = document.getElementById('image_preview_' + productId);
        const previewContainer = document.getElementById('image_preview_container_' + productId);

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

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[id^="edit_category_search_"]').forEach(function (categoryInput) {
            const productId = categoryInput.id.replace('edit_category_search_', '');
            const listId = 'edit_category_list_' + productId;
            const noteId = 'edit_categoryRuleNote_' + productId;
            const valueId = 'edit_category_id_' + productId;

            bindSearchableList({
                inputId: 'edit_category_search_' + productId,
                valueId: valueId,
                listId: listId,
                onSelect: (optionEl) => {
                    if (optionEl) updateCategoryRuleNoteById(noteId, optionEl);
                    toggleEditDosageFormVisibility(productId);
                }
            });

            const listEl = document.getElementById(listId);
            const existingOption = listEl ? Array.from(listEl.options).find((option) => option.value.trim() === categoryInput.value.trim()) : null;
            if (existingOption) {
                updateCategoryRuleNoteById(noteId, existingOption);
            }

            categoryInput.addEventListener('input', function () { toggleEditDosageFormVisibility(productId); });
            categoryInput.addEventListener('change', function () { toggleEditDosageFormVisibility(productId); });
            toggleEditDosageFormVisibility(productId);
        });

        document.querySelectorAll('[id^="edit_dosage_form_search_"]').forEach(function (dosageInput) {
            const productId = dosageInput.id.replace('edit_dosage_form_search_', '');
            const listId = 'edit_dosage_form_list_' + productId;
            const valueId = 'edit_dosage_form_' + productId;
            const idValueId = 'edit_dosage_form_id_' + productId;

            const syncDosage = () => {
                const listEl = document.getElementById(listId);
                const valueEl = document.getElementById(valueId);
                const idValueEl = document.getElementById(idValueId);
                const option = listEl ? Array.from(listEl.options).find((opt) => opt.value.trim() === dosageInput.value.trim()) : null;

                if (valueEl) valueEl.value = option ? option.value.trim() : dosageInput.value.trim();
                if (idValueEl) idValueEl.value = option ? (option.getAttribute('data-id') || '') : '';
                toggleEditDosageFormVisibility(productId);
            };

            dosageInput.addEventListener('input', syncDosage);
            dosageInput.addEventListener('change', syncDosage);
            syncDosage();
        });

        document.querySelectorAll('[data-batch-option]').forEach(function (button) {
            button.addEventListener('click', function () {
                const form = button.closest('form');
                const productIdInput = form ? form.querySelector('input[name="id"]') : null;
                const productId = productIdInput ? productIdInput.value : '';
                const hiddenInput = form ? form.querySelector('input[name="add_batch_prompt"]') : null;
                const fields = productId ? document.getElementById('edit_batchPromptFields_' + productId) : null;
                const isYes = this.dataset.batchOption === 'yes';

                if (hiddenInput) hiddenInput.value = isYes ? 'yes' : 'no';
                if (fields) fields.style.display = isYes ? 'block' : 'none';

                const yesButton = form ? form.querySelector('[data-batch-option="yes"]') : null;
                const noButton = form ? form.querySelector('[data-batch-option="no"]') : null;

                if (yesButton) {
                    yesButton.classList.toggle('btn-primary', isYes);
                    yesButton.classList.toggle('btn-outline-primary', !isYes);
                }
                if (noButton) {
                    noButton.classList.toggle('btn-secondary', !isYes);
                    noButton.classList.toggle('btn-outline-secondary', isYes);
                }
            });
        });

        document.querySelectorAll('[id^="edit_batch_purchase_cost_"]').forEach(function (purchaseInput) {
            const productId = purchaseInput.id.replace('edit_batch_purchase_cost_', '');
            const markupInput = document.getElementById('edit_batch_markup_' + productId);
            const saleInput = document.getElementById('edit_batch_sale_price_' + productId);

            const updateSalePrice = () => {
                if (!saleInput) return;
                const purchaseCost = parseFloat(purchaseInput.value || 0);
                const markup = parseFloat(markupInput ? (markupInput.value || 0) : 0);

                if (!Number.isFinite(purchaseCost) || purchaseCost < 0) {
                    saleInput.value = '';
                    return;
                }

                const safeMarkup = Number.isFinite(markup) && markup >= 0 ? markup : 0;
                saleInput.value = (purchaseCost * (1 + safeMarkup / 100)).toFixed(2);
            };

            purchaseInput.addEventListener('input', updateSalePrice);
            if (markupInput) markupInput.addEventListener('input', updateSalePrice);
        });
    });
</script>
