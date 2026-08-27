<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
 foreach ($users as $u): ?>

<!-- View User Modal -->
<div class="modal fade" id="view<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>" tabindex="-1">
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
                        <?= htmlspecialchars((string)($u['username']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>First Name:</strong><br>
                        <?= htmlspecialchars((string)($u['firstname']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Middle Name:</strong><br>
                        <?= htmlspecialchars((string)($u['middlename']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Last Name:</strong><br>
                        <?= htmlspecialchars((string)($u['lastname']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Age:</strong><br>
                        <?= htmlspecialchars((string)($u['age']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Position:</strong><br>
                        <?= htmlspecialchars((string)($u['position']), ENT_QUOTES, 'UTF-8') ?>
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
                        <?= htmlspecialchars((string)($u['email']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-6 mb-2">
                        <strong>Contact Number:</strong><br>
                        <?= htmlspecialchars((string)($u['contactnumber']), ENT_QUOTES, 'UTF-8') ?>
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
                        <?= htmlspecialchars((string)($u['street']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Barangay:</strong><br>
                        <?= htmlspecialchars((string)($u['barangay']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>City:</strong><br>
                        <?= htmlspecialchars((string)($u['city']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Province:</strong><br>
                        <?= htmlspecialchars((string)($u['province']), ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <div class="col-md-4 mb-2">
                        <strong>Country:</strong><br>
                        <?= htmlspecialchars((string)($u['country']), ENT_QUOTES, 'UTF-8') ?>
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
                        <span class="text-muted">(hidden — stored hashed)</span>
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