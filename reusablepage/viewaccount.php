<?php foreach ($users as $u): ?>

<!-- View User Modal -->
<div class="modal fade" id="view<?= $u['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Basic Information -->
                <h6 class="mb-3 text-secondary fw-bold">
                    Basic Information
                </h6>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <strong>Username:</strong><br>
                        <?= $u['username'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>First Name:</strong><br>
                        <?= $u['firstname'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Middle Name:</strong><br>
                        <?= $u['middlename'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Last Name:</strong><br>
                        <?= $u['lastname'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Age:</strong><br>
                        <?= $u['age'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Position:</strong><br>
                        <?= $u['position'] ?>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Contact Information -->
                <h6 class="mb-3 text-secondary fw-bold">
                    Contact Information
                </h6>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Email Address:</strong><br>
                        <?= $u['email'] ?>
                    </div>

                    <div class="col-md-6 mb-2">
                        <strong>Contact Number:</strong><br>
                        <?= $u['contactnumber'] ?>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Address Information -->
                <h6 class="mb-3 text-secondary fw-bold">
                    Address Information
                </h6>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <strong>Street:</strong><br>
                        <?= $u['street'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Barangay:</strong><br>
                        <?= $u['barangay'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>City:</strong><br>
                        <?= $u['city'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Province:</strong><br>
                        <?= $u['province'] ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Country:</strong><br>
                        <?= $u['country'] ?>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Security -->
                <h6 class="mb-3 text-secondary fw-bold">
                    Security
                </h6>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <strong>Void PIN:</strong><br>
                        <?= $u['void_password'] ?>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<?php endforeach; ?>