<?php foreach ($users as $u): ?>

<!-- Edit User Modal -->
<div class="modal fade" id="edit<?= $u['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" value="<?= $u['id'] ?>">

                    <!-- Account Information -->
                    <h6 class="mb-3 text-secondary fw-bold">
                        Account Information
                    </h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input
                                name="username"
                                class="form-control"
                                value="<?= $u['username'] ?>"
                                minlength="6"
                                maxlength="30"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position</label>
                            <select name="position" class="form-control" required>
                                <option value="Admin"
                                    <?= $u['position'] == 'Admin' ? 'selected' : '' ?>>
                                    Admin
                                </option>

                                <option value="Owner"
                                    <?= $u['position'] == 'Owner' ? 'selected' : '' ?>>
                                    Owner
                                </option>

                                <option value="Staff"
                                    <?= $u['position'] == 'Staff' ? 'selected' : '' ?>>
                                    Staff
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Leave blank if unchanged"
                            minlength="8"
                            maxlength="16"
                        >
                    </div>

                    <hr class="my-4">

                    <!-- Personal Information -->
                    <h6 class="mb-3 text-secondary fw-bold">
                        Personal Information
                    </h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input
                                name="firstname"
                                class="form-control"
                                value="<?= $u['firstname'] ?>"
                                required
                            >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input
                                name="middlename"
                                class="form-control"
                                value="<?= $u['middlename'] ?>"
                                required
                            >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input
                                name="lastname"
                                class="form-control"
                                value="<?= $u['lastname'] ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Age</label>
                            <input
                                type="number"
                                name="age"
                                class="form-control"
                                value="<?= $u['age'] ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= $u['email'] ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input
                            name="contactnumber"
                            class="form-control"
                            value="<?= $u['contactnumber'] ?>"
                            minlength="11"
                            maxlength="11"
                            required
                        >
                    </div>

                    <hr class="my-4">

                    <!-- Address Information -->
                    <h6 class="mb-3 text-secondary fw-bold">
                        Address Information
                    </h6>

                    <div class="mb-3">
                        <label class="form-label">Street</label>
                        <input
                            name="street"
                            class="form-control"
                            value="<?= $u['street'] ?>"
                            required
                        >
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barangay</label>
                            <input
                                name="barangay"
                                class="form-control"
                                value="<?= $u['barangay'] ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input
                                name="city"
                                class="form-control"
                                value="<?= $u['city'] ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Province</label>
                            <input
                                name="province"
                                class="form-control"
                                value="<?= $u['province'] ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <input
                                name="country"
                                class="form-control"
                                value="<?= $u['country'] ?>"
                                required
                            >
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Security -->
                    <h6 class="mb-3 text-secondary fw-bold">
                        Security PIN
                    </h6>

                    <div class="mb-3">
                        <label class="form-label">
                            Void PIN (7 digits)
                        </label>

                        <input
                            name="void_password"
                            class="form-control"
                            value="<?= $u['void_password'] ?>"
                            minlength="7"
                            maxlength="7"
                            required
                        >
                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        name="updateUser"
                        class="btn btn-success">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php endforeach; ?>