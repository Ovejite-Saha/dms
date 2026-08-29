<?php
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$pdo = db();
$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);

$currentAdminId = (int)($cu['id'] ?? 0);
?>
<div class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="fa-solid fa-user-tie"></i> Manage Admins</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa-solid fa-plus"></i> Add Admin</button>
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
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($admins as $i => $a): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <i class="fa-solid fa-user-tie text-primary"></i>
                            <?= htmlspecialchars($a['username']) ?>
                            <?php if ((int)$a['id'] === $currentAdminId): ?>
                                <span class="badge bg-info text-dark ms-1">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['full_name']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= htmlspecialchars($a['phone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $a['id'] ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <?php if ((int)$a['id'] !== $currentAdminId): ?>
                            <a href="<?= base_url() ?>/actions/admin_user_action.php?action=delete&id=<?= $a['id'] ?>&role=admin"
                               class="btn btn-sm btn-outline-danger btn-confirm"
                               data-confirm="Delete this admin?">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary" disabled title="You cannot delete your own account">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $a['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Edit Admin</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <input type="hidden" name="role" value="admin">
                                        <div class="mb-3">
                                            <label class="form-label">Username *</label>
                                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($a['username']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($a['full_name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($a['email']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($a['phone'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
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
                <?php if (empty($admins)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No admins found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus"></i> Add Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="role" value="admin">
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>