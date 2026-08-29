<?php
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$pdo = db();
$users      = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$ministries = $pdo->query("SELECT id, name FROM ministries ORDER BY name")->fetchAll();
$divisions  = $pdo->query("SELECT id, name FROM divisions ORDER BY name")->fetchAll();
$districts  = $pdo->query("SELECT id, name, division_id FROM districts ORDER BY name")->fetchAll();
$upazilas   = $pdo->query("SELECT id, name, district_id FROM upazilas ORDER BY name")->fetchAll();
$projects   = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);

function access_label($type, $ids_json, $lookups) {
    if ($type === 'all' || empty($type)) {
        return '<span class="badge bg-success">All Documents</span>';
    }
    $ids = is_string($ids_json) ? json_decode($ids_json, true) : $ids_json;
    if (!is_array($ids) || empty($ids)) {
        return '<span class="badge bg-secondary">' . htmlspecialchars(ucfirst($type)) . '</span>';
    }
    $names = [];
    $map   = $lookups[$type] ?? [];
    foreach ($ids as $id) {
        if (isset($map[$id])) $names[] = $map[$id];
    }
    $text = implode(', ', array_slice($names, 0, 3));
    if (count($names) > 3) $text .= ' +' . (count($names) - 3);
    return '<span class="badge bg-info text-dark">' . htmlspecialchars(ucfirst($type)) . ': ' . htmlspecialchars($text) . '</span>';
}

$minMap  = []; foreach ($ministries as $d) $minMap[$d['id']]  = $d['name'];
$divMap  = []; foreach ($divisions  as $d) $divMap[$d['id']]  = $d['name'];
$distMap = []; foreach ($districts  as $d) $distMap[$d['id']] = $d['name'];
$upaMap  = []; foreach ($upazilas   as $u) $upaMap[$u['id']]  = $u['name'];
$projMap = []; foreach ($projects   as $p) $projMap[$p['id']] = $p['name'];

$lookups = [
    'ministrie' => $minMap,
    'division'  => $divMap,
    'district'  => $distMap,
    'upazila'   => $upaMap,
    'project'   => $projMap
];
?>
<div class="container">
  <div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fa-solid fa-users"></i> Manage Users</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
      <i class="fa-solid fa-plus"></i> Add User
    </button>
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
            <th>Access</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $i => $u):
          $uids = is_string($u['access_ids'] ?? '') ? json_decode($u['access_ids'], true) : ($u['access_ids'] ?? []);
          if (!is_array($uids)) $uids = [];
        ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><i class="fa-solid fa-user text-primary"></i> <?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['full_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= access_label($u['access_type'] ?? 'all', $u['access_ids'] ?? null, $lookups) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['id'] ?>">
                <i class="fa-solid fa-pen"></i>
              </button>
              <a href="<?= base_url() ?>/actions/admin_user_action.php?action=delete&id=<?= $u['id'] ?>&role=user"
                 class="btn btn-sm btn-outline-danger btn-confirm"
                 data-confirm="Delete this user?">
                <i class="fa-solid fa-trash"></i>
              </a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="role" value="user">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($u['username']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($u['full_name']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($u['phone'] ?? '') ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">New Password <small class="text-muted">(blank = keep)</small></label>
                        <input type="password" class="form-control" name="password">
                      </div>
                      <div class="col-12">
                        <label class="form-label fw-semibold">Access Level</label>
                        <div class="d-flex flex-wrap gap-3 mb-2">
                          <?php foreach ([
                              'all'       => 'All Documents',
                              'ministrie' => 'Selected Ministrie(s)',
                              'division'  => 'Selected Division(s)',
                              'district'  => 'Selected District(s)',
                              'upazila'   => 'Selected Upazila(s)',
                              'project'   => 'Selected Project(s)'
                          ] as $val => $lbl): ?>
                            <div class="form-check">
                              <input class="form-check-input access-type-radio" type="radio"
                                     name="access_type" value="<?= $val ?>"
                                     id="at_<?= $u['id'] ?>_<?= $val ?>"
                                     <?= ($u['access_type'] ?? 'all') === $val ? 'checked' : '' ?>
                                     data-uid="<?= $u['id'] ?>">
                              <label class="form-check-label" for="at_<?= $u['id'] ?>_<?= $val ?>"><?= $lbl ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- Ministrie box -->
                        <div class="access-box border rounded p-2" id="box_ministrie_<?= $u['id'] ?>"
                             style="display:<?= ($u['access_type'] ?? '') === 'ministrie' ? 'block' : 'none' ?>;max-height:160px;overflow:auto">
                          <?php foreach ($ministries as $d): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>"
                                     <?= in_array($d['id'], $uids) && ($u['access_type'] ?? '') === 'ministrie' ? 'checked' : '' ?>>
                              <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- Division box -->
                        <div class="access-box border rounded p-2" id="box_division_<?= $u['id'] ?>"
                             style="display:<?= ($u['access_type'] ?? '') === 'division' ? 'block' : 'none' ?>;max-height:160px;overflow:auto">
                          <?php foreach ($divisions as $d): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>"
                                     <?= in_array($d['id'], $uids) && ($u['access_type'] ?? '') === 'division' ? 'checked' : '' ?>>
                              <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- District box -->
                        <div class="access-box border rounded p-2" id="box_district_<?= $u['id'] ?>"
                             style="display:<?= ($u['access_type'] ?? '') === 'district' ? 'block' : 'none' ?>;max-height:160px;overflow:auto">
                          <?php foreach ($districts as $d): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>"
                                     <?= in_array($d['id'], $uids) && ($u['access_type'] ?? '') === 'district' ? 'checked' : '' ?>>
                              <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- Upazila box -->
                        <div class="access-box border rounded p-2" id="box_upazila_<?= $u['id'] ?>"
                             style="display:<?= ($u['access_type'] ?? '') === 'upazila' ? 'block' : 'none' ?>;max-height:160px;overflow:auto">
                          <?php foreach ($upazilas as $x): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $x['id'] ?>"
                                     <?= in_array($x['id'], $uids) && ($u['access_type'] ?? '') === 'upazila' ? 'checked' : '' ?>>
                              <label class="form-check-label"><?= htmlspecialchars($x['name']) ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>

                        <!-- Project box -->
                        <div class="access-box border rounded p-2" id="box_project_<?= $u['id'] ?>"
                             style="display:<?= ($u['access_type'] ?? '') === 'project' ? 'block' : 'none' ?>;max-height:160px;overflow:auto">
                          <?php foreach ($projects as $p): ?>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $p['id'] ?>"
                                     <?= in_array($p['id'], $uids) && ($u['access_type'] ?? '') === 'project' ? 'checked' : '' ?>>
                              <label class="form-check-label"><?= htmlspecialchars($p['name']) ?></label>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
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
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url() ?>/actions/admin_user_action.php" method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="create">
          <input type="hidden" name="role" value="user">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Access Level</label>
              <div class="d-flex flex-wrap gap-3 mb-2">
                <?php foreach ([
                    'all'       => 'All Documents',
                    'ministrie' => 'Selected Ministrie(s)',
                    'division'  => 'Selected Division(s)',
                    'district'  => 'Selected District(s)',
                    'upazila'   => 'Selected Upazila(s)',
                    'project'   => 'Selected Project(s)'
                ] as $val => $lbl): ?>
                  <div class="form-check">
                    <input class="form-check-input access-type-radio" type="radio"
                           name="access_type" value="<?= $val ?>"
                           id="add_at_<?= $val ?>"
                           <?= $val === 'all' ? 'checked' : '' ?>
                           data-uid="add">
                    <label class="form-check-label" for="add_at_<?= $val ?>"><?= $lbl ?></label>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="access-box border rounded p-2" id="box_ministrie_add" style="display:none;max-height:160px;overflow:auto">
                <?php foreach ($ministries as $d): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="access-box border rounded p-2" id="box_division_add" style="display:none;max-height:160px;overflow:auto">
                <?php foreach ($divisions as $d): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="access-box border rounded p-2" id="box_district_add" style="display:none;max-height:160px;overflow:auto">
                <?php foreach ($districts as $d): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $d['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($d['name']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="access-box border rounded p-2" id="box_upazila_add" style="display:none;max-height:160px;overflow:auto">
                <?php foreach ($upazilas as $x): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $x['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($x['name']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="access-box border rounded p-2" id="box_project_add" style="display:none;max-height:160px;overflow:auto">
                <?php foreach ($projects as $p): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="access_ids[]" value="<?= $p['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($p['name']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.access-type-radio').forEach(function(radio) {
  radio.addEventListener('change', function() {
    var uid = this.getAttribute('data-uid');
    ['ministrie', 'division', 'district', 'upazila', 'project'].forEach(function(t) {
      var box = document.getElementById('box_' + t + '_' + uid);
      if (box) {
        box.style.display = (radio.value === t) ? 'block' : 'none';
        if (radio.value !== t) {
          box.querySelectorAll('input[type=checkbox]').forEach(function(c) {
            c.checked = false;
          });
        }
      }
    });
  });
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>