<?php
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$pdo = db();
$tab = $_GET['tab'] ?? 'ministry';
$allowed = ['ministry','division','district','upazila','project'];
if (!in_array($tab, $allowed, true)) $tab = 'ministry';

$ministries = $pdo->query("SELECT * FROM ministries ORDER BY name")->fetchAll();
$divisions  = $pdo->query("SELECT * FROM divisions ORDER BY name")->fetchAll();
$districts  = $pdo->query("SELECT d.*, dv.name AS division_name FROM districts d JOIN divisions dv ON dv.id=d.division_id ORDER BY dv.name, d.name")->fetchAll();
$upazilas   = $pdo->query("SELECT u.*, dt.name AS district_name FROM upazilas u JOIN districts dt ON dt.id=u.district_id ORDER BY dt.name, u.name")->fetchAll();
$projects   = $pdo->query("SELECT * FROM projects ORDER BY name")->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);
$actionUrl = base_url() . '/actions/admin_class_action.php';
?>
<div class="container">
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="fa-solid fa-sitemap"></i> Manage Hierarchy</h2>
  </div>

  <?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <ul class="nav nav-tabs mb-3">
    <?php
    $tabs = ['ministry'=>'Ministries','division'=>'Divisions','district'=>'Districts','upazila'=>'Upazilas','project'=>'Projects'];
    foreach ($tabs as $k=>$label): ?>
      <li class="nav-item">
        <a class="nav-link <?= $tab===$k?'active':'' ?>" href="?tab=<?= $k ?>"><?= $label ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <!-- ADD FORM -->
  <div class="dms-card mb-4 p-3">
    <h5 class="mb-3">Add <?= htmlspecialchars($tabs[$tab]) ?></h5>
    <form method="post" action="<?= $actionUrl ?>" class="row g-2 align-items-end">
      <input type="hidden" name="action" value="create">
      <input type="hidden" name="type" value="<?= $tab ?>">
      <?php if ($tab === 'district'): ?>
        <div class="col-md-4">
          <label class="form-label">Division</label>
          <select name="parent_id" class="form-select" required>
            <option value="">-- Select Division --</option>
            <?php foreach ($divisions as $d): ?>
              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php elseif ($tab === 'upazila'): ?>
        <div class="col-md-4">
          <label class="form-label">District</label>
          <select name="parent_id" class="form-select" required>
            <option value="">-- Select District --</option>
            <?php foreach ($districts as $d): ?>
              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['division_name'] . ' / ' . $d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required placeholder="Enter name">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-plus"></i> Add</button>
      </div>
    </form>
  </div>

  <!-- LIST -->
  <div class="dms-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <?php if ($tab==='district'): ?><th>Division</th><?php endif; ?>
            <?php if ($tab==='upazila'): ?><th>District</th><?php endif; ?>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $rows = match($tab) {
            'ministry' => $ministries,
            'division' => $divisions,
            'district' => $districts,
            'upazila'  => $upazilas,
            'project'  => $projects,
        };
        foreach ($rows as $i => $r):
        ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <?php if ($tab==='district'): ?><td><?= htmlspecialchars($r['division_name']) ?></td><?php endif; ?>
            <?php if ($tab==='upazila'): ?><td><?= htmlspecialchars($r['district_name']) ?></td><?php endif; ?>
            <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit<?= $tab.$r['id'] ?>"><i class="fa-solid fa-pen"></i></button>
              <a href="<?= $actionUrl ?>?action=delete&type=<?= $tab ?>&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger btn-confirm" data-confirm="Delete this item?"><i class="fa-solid fa-trash"></i></a>
            </td>
          </tr>

          <div class="modal fade" id="edit<?= $tab.$r['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="post" action="<?= $actionUrl ?>">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit <?= htmlspecialchars($tabs[$tab]) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="type" value="<?= $tab ?>">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <?php if ($tab === 'district'): ?>
                      <div class="mb-3">
                        <label class="form-label">Division</label>
                        <select name="parent_id" class="form-select">
                          <?php foreach ($divisions as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($r['division_id']??0)==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    <?php elseif ($tab === 'upazila'): ?>
                      <div class="mb-3">
                        <label class="form-label">District</label>
                        <select name="parent_id" class="form-select">
                          <?php foreach ($districts as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($r['district_id']??0)==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    <?php endif; ?>
                    <div class="mb-3">
                      <label class="form-label">Name</label>
                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($r['name']) ?>" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="5" class="text-center text-muted">No records yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
