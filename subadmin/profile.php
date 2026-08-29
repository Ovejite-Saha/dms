<?php
require_once __DIR__ . '/../includes/header.php';
require_role('subadmin');

$pdo = db();
$cu = current_user();

// Fetch fresh data
$stmt = $pdo->prepare("SELECT * FROM subadmins WHERE id = ?");
$stmt->execute([$cu['id']]);
$profile = $stmt->fetch();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);
?>
<div class="container">
    <div class="page-header">
        <h2><i class="fa-solid fa-user"></i> My Profile</h2>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Info -->
        <div class="col-md-6">
            <div class="dms-card">
                <h5 class="card-title"><i class="fa-solid fa-id-card"></i> Edit Information</h5>
                <form action="<?= base_url() ?>/actions/profile_action.php" method="post">
                    <input type="hidden" name="action" value="update_info">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['username']) ?>" disabled>
                        <small class="text-muted">Username cannot be changed.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($profile['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save"></i> Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="dms-card">
                <h5 class="card-title"><i class="fa-solid fa-key"></i> Change Password</h5>
                <form action="<?= base_url() ?>/actions/profile_action.php" method="post">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label class="form-label">Current Password *</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password *</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password *</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="fa-solid fa-key"></i> Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
