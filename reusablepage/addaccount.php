<!-- Modal -->
<div class="modal fade" id="adduser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Account Information Section -->
                    <h6 class="mb-3 text-secondary fw-bold">Account Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input id="username" name="username" class="form-control" placeholder="e.g., john_doe" minlength="6" maxlength="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                            <select id="position" name="position" class="form-control" required>
                                <option value="">Select Position</option>
                                <option>Admin</option>
                                <option>Owner</option>
                                <option selected>Staff</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input id="password" type="password" name="password" class="form-control" placeholder="8-16 characters" minlength="8" maxlength="16" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Personal Information Section. -->
                    <h6 class="mb-3 text-secondary fw-bold">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input id="firstname" name="firstname" class="form-control" placeholder="e.g., John" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middlename" class="form-label">Middle Name <span class="text-danger">*</span></label>
                            <input id="middlename" name="middlename" class="form-control" placeholder="e.g., Matthew" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input id="lastname" name="lastname" class="form-control" placeholder="e.g., Doe" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                            <input id="age" type="number" name="age" class="form-control" placeholder="e.g., 28" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input id="email" type="email" name="email" class="form-control" placeholder="e.g., john@example.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contactnumber" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input id="contactnumber" name="contactnumber" class="form-control" placeholder="e.g., 09123456789" minlength="11" maxlength="11" required>
                    </div>

                    <hr class="my-4">

                    <!-- Address Information Section -->
                    <h6 class="mb-3 text-secondary fw-bold">Address Information</h6>
                    <div class="mb-3">
                        <label for="street" class="form-label">Street <span class="text-danger">*</span></label>
                        <input id="street" name="street" class="form-control" placeholder="e.g., 123 Main Street" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                            <input id="barangay" name="barangay" class="form-control" placeholder="e.g., Niyugan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input id="city" name="city" class="form-control" placeholder="e.g., Jaen" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                            <input id="province" name="province" class="form-control" placeholder="e.g., Nueva Ecija" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input id="country" name="country" class="form-control" placeholder="e.g., Philippines" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Security Section -->
                    <h6 class="mb-3 text-secondary fw-bold">Security PIN</h6>
                    <div class="mb-3">
                        <label for="void_password" class="form-label">Void PIN (7 digits) <span class="text-danger">*</span></label>
                        <input id="void_password" name="void_password" class="form-control" minlength="7" maxlength="7" placeholder="e.g., 1234567">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="addUser" class="btn btn-primary">Save User</button>
                </div>
            </form>

        </div>
    </div>
</div>