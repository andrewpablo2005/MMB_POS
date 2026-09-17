<!-- CATEGORY SETTINGS MODAL -->
<div class="modal fade" id="categorySettingsModal" tabindex="-1" aria-labelledby="categorySettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categorySettingsModalLabel">Category Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Edit category names and the discount/VAT rules used when selling products.</p>
                <button type="button" class="btn btn-primary mb-3" data-open-add-category>Add Category</button>
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
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-category>Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-success" data-save-all-categories>Save Changes</button>
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
                    <div class="form-check"><input type="checkbox" class="form-check-input" id="newCategoryVat"><label class="form-check-label" for="newCategoryVat">VAT applicable</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" id="newCategorySenior"><label class="form-check-label" for="newCategorySenior">Senior discount</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" id="newCategoryPwd"><label class="form-check-label" for="newCategoryPwd">PWD discount</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    if (status !== 200 || !data.success) throw new Error(data.message || 'Unable to add category.');
                    const categoryTableBody = document.querySelector('#categorySettingsModal tbody');
                    const newRow = document.createElement('tr');
                    newRow.dataset.categoryRow = String(data.id);
                    newRow.innerHTML = `<td><input type="text" class="form-control form-control-sm" data-category-name required></td><td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="has_vat" aria-label="VAT"></td><td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="senior_discount" aria-label="Senior"></td><td class="text-center"><input type="checkbox" class="form-check-input" data-category-field="pwd_discount" aria-label="PWD"></td><td class="text-end"><button type="button" class="btn btn-sm btn-danger" data-delete-category>Delete</button></td>`;
                    newRow.querySelector('[data-category-name]').value = name;
                    newRow.querySelector('[data-category-field="has_vat"]').checked = formData.get('has_vat') === '1';
                    newRow.querySelector('[data-category-field="senior_discount"]').checked = formData.get('senior_discount') === '1';
                    newRow.querySelector('[data-category-field="pwd_discount"]').checked = formData.get('pwd_discount') === '1';
                    categoryTableBody?.appendChild(newRow);
                    mmbNotify({ type: 'success', title: 'Category added', message: data.message });
                    addCategoryForm.reset();
                    bootstrap.Modal.getOrCreateInstance(addCategoryModal).hide();
                })
                .catch((error) => mmbNotify({ type: 'danger', title: 'Could not add category', message: error.message }))
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Add Category';
                });
        });

        addCategoryModal.addEventListener('hidden.bs.modal', function () {
            bootstrap.Modal.getOrCreateInstance(categorySettingsModal).show();
        });
    });

    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('[data-delete-category]');
        if (deleteButton) {
            const row = deleteButton.closest('[data-category-row]');
            const name = row.querySelector('[data-category-name]').value.trim();
            if (!window.confirm(`Delete category "${name}"? This cannot be undone.`)) return;
            deleteButton.disabled = true;
            const formData = new FormData();
            formData.append('category_id', row.dataset.categoryRow);
            fetch('../function/delete_category_ajax.php', { method: 'POST', body: formData })
                .then((response) => response.json().then((data) => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200 || !data.success) throw new Error(data.message || 'Unable to delete category.');
                    row.remove();
                    mmbNotify({ type: 'success', title: 'Category deleted', message: data.message });
                })
                .catch((error) => {
                    mmbNotify({ type: 'danger', title: 'Could not delete category', message: error.message });
                    deleteButton.disabled = false;
                });
            return;
        }

        const saveAllButton = event.target.closest('[data-save-all-categories]');
        if (!saveAllButton) return;
        const rows = Array.from(document.querySelectorAll('#categorySettingsModal [data-category-row]'));
        const updates = [];
        for (const row of rows) {
            const nameInput = row.querySelector('[data-category-name]');
            const name = nameInput.value.trim();
            if (!name) {
                mmbNotify({ type: 'warning', title: 'Category name required' });
                nameInput.focus();
                return;
            }
            const formData = new FormData();
            formData.append('category_id', row.dataset.categoryRow);
            formData.append('category_name', name);
            row.querySelectorAll('[data-category-field]').forEach((field) => formData.append(field.dataset.categoryField, field.checked ? '1' : '0'));
            updates.push(fetch('../function/update_category_ajax.php', { method: 'POST', body: formData })
                .then((response) => response.json().then((data) => ({ status: response.status, data })))
                .then(({ status, data }) => {
                    if (status !== 200 || !data.success) throw new Error(data.message || 'Unable to update category.');
                }));
        }
        const originalText = saveAllButton.innerHTML;
        saveAllButton.disabled = true;
        saveAllButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
        Promise.all(updates)
            .then(() => mmbNotify({ type: 'success', title: 'Categories updated', message: 'All category settings were saved successfully.' }))
            .catch((error) => mmbNotify({ type: 'danger', title: 'Could not update categories', message: error.message }))
            .finally(() => {
                saveAllButton.disabled = false;
                saveAllButton.innerHTML = originalText;
            });
    });
</script>
