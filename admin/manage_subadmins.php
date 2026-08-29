<?php
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$pdo = db();
$subadmins = $pdo->query("SELECT * FROM subadmins ORDER BY created_at DESC")->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);
?>
<div class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="fa-solid fa-user-gear"></i> Manage Subadmins</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus"></i> Add Subadmin</button>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="dms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>#</th><th>Username</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($subadmins as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><i class="fa-solid fa-user-gear text-success"></i> <?= htmlspecialchars($s['username']) ?></td>
                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($s['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                            <a href="<?= base_url() ?>/actions/admin_user_action.php?action=delete&id=<?= $s['id'] ?>&role=subadmin" class="btn btn-sm btn-outline-danger btn-confirm" data-confirm="Delete this subadmin?"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $s['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Edit Subadmin</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <input type="hidden" name="role" value="subadmin">
                                        <div class="mb-3"><label class="form-label">Full Name</label><input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($s['full_name']) ?>" required></div>
                                        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($s['email']) ?>" required></div>
                                        <div class="mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($s['phone'] ?? '') ?>"></div>
                                        <div class="mb-3"><label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label><input type="password" class="form-control" name="new_password"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($subadmins)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No subadmins found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subadmin Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus"></i> Add Subadmin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="role" value="subadmin">
                    <div class="mb-3"><label class="form-label">Username *</label><input type="text" class="form-control" name="username" required></div>
                    <div class="mb-3"><label class="form-label">Password *</label><input type="password" class="form-control" name="password" required></div>
                    <div class="mb-3"><label class="form-label">Full Name *</label><input type="text" class="form-control" name="full_name" required></div>
                    <div class="mb-3"><label class="form-label">Email *</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Subadmin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
