<?php
require_once __DIR__ . '/../includes/header.php';
require_role('user');

$pdo = db();
$cu = current_user();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$cu['id']]);
$profile = $stmt->fetch();

$accessType = $profile['access_type'] ?? 'all';
$accessLabels = [
    'all'      => 'All Documents',
    'division' => 'Selected Division(s)',
    'district' => 'Selected District(s)',
    'upazila'  => 'Selected Upazila(s)',
    'project'  => 'Selected Project(s)',
];

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

    <div class="dms-card mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-card stat-blue" style="width:70px;height:70px;padding:1rem;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-user fa-2x"></i>
            </div>
            <div>
                <h4 class="mb-1"><?= htmlspecialchars($profile['full_name']) ?></h4>
                <p class="text-muted mb-1"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($profile['email']) ?></p>
                <p class="text-muted mb-1"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($profile['phone'] ?? '—') ?></p>
                <p class="mb-0">
                    <?php if ($accessType === 'all'): ?>
                        <span class="badge bg-success">Access: All Documents</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Access: <?= htmlspecialchars($accessLabels[$accessType] ?? $accessType) ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
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
