<?php
require_once __DIR__ . '/../includes/header.php';
require_role(['admin', 'subadmin']);

$pdo = db();

$search     = trim($_GET['search'] ?? '');
$distFilter = $_GET['district_id'] ?? '';
$upaFilter  = $_GET['upazila_id'] ?? '';
$levelFilter= $_GET['level_type'] ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 16;

$districts = $pdo->query("SELECT id, name FROM districts ORDER BY name")->fetchAll();

if ($distFilter !== '') {
    $stmt = $pdo->prepare("SELECT id, name FROM upazilas WHERE district_id = ? ORDER BY name");
    $stmt->execute([$distFilter]);
    $filterUpazilas = $stmt->fetchAll();
} else {
    $filterUpazilas = [];
}

$sql = "
  SELECT b.*, 
         d.name AS district_name,
         u.name AS upazila_name
  FROM pwd_benchmark b
  JOIN districts d ON d.id = b.district_id
  LEFT JOIN upazilas u ON u.id = b.upazila_id
  WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (d.name LIKE ? OR u.name LIKE ? OR b.file_path LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($distFilter !== '') {
    $sql .= " AND b.district_id = ?";
    $params[] = $distFilter;
}
if ($upaFilter !== '') {
    $sql .= " AND b.upazila_id = ?";
    $params[] = $upaFilter;
}
if ($levelFilter !== '') {
    $sql .= " AND b.level_type = ?";
    $params[] = $levelFilter;
}

$sql .= " ORDER BY b.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$all = $stmt->fetchAll();

$total = count($all);
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$items = array_slice($all, $offset, $perPage);

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);

$ajaxBase = base_url() . '/actions/ajax_hierarchy.php';
?>
<div class="container">
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="fa-solid fa-map-location-dot"></i> Manage PWD Benchmark Data</h2>
    <a href="<?= base_url() ?>/<?= current_role() === 'admin' ? 'admin' : 'subadmin' ?>/upload_bmdata.php" class="btn btn-primary rounded-pill">
      <i class="fa-solid fa-upload"></i> Upload Benchmark
    </a>
  </div>

  <?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Filters -->
<form method="get" class="mb-4" id="filterForm">
  <div class="search-bar d-flex flex-nowrap align-items-center gap-2" style="overflow-x:auto;">
    
    <select name="district_id" id="filter_district" class="form-select form-select-sm" style="min-width:100px;">
      <option value="">All Districts</option>
      <?php foreach ($districts as $d): ?>
        <option value="<?= $d['id'] ?>" <?= (string)$distFilter === (string)$d['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($d['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="upazila_id" id="filter_upazila" class="form-select form-select-sm" style="min-width:100px;">
      <option value="">All Upazilas</option>
      <?php foreach ($filterUpazilas as $u): ?>
        <option value="<?= $u['id'] ?>" <?= (string)$upaFilter === (string)$u['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="level_type" class="form-select form-select-sm" style="min-width:100px;">
      <option value="">All Levels</option>
      <option value="district" <?= $levelFilter === 'district' ? 'selected' : '' ?>>District</option>
      <option value="upazila"  <?= $levelFilter === 'upazila'  ? 'selected' : '' ?>>Upazila</option>
    </select>

    <input type="text" name="search" class="form-control form-control-sm"
           placeholder="Search district / upazila..." 
           value="<?= htmlspecialchars($search) ?>"
           style="min-width:220px; flex:1;">

    <!-- Slightly bigger button + icon & text stay on same line -->
    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1" style="white-space: nowrap;">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>Search</span>
    </button>

    <a href="?" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
  </div>
</form>

<script>
(function(){
  var ajaxBase = '<?= $ajaxBase ?>';
  var dist = document.getElementById('filter_district');
  var upa  = document.getElementById('filter_upazila');
  dist.addEventListener('change', function(){
    upa.innerHTML = '<option value="">All Upazilas</option>';
    if (!this.value) return;
    fetch(ajaxBase + '?action=upazilas&district_id=' + this.value)
      .then(function(r){ return r.json(); })
      .then(function(data){
        data.forEach(function(i){
          var o = document.createElement('option');
          o.value = i.id;
          o.textContent = i.name;
          upa.appendChild(o);
        });
      });
  });
})();
</script>

  <?php if (empty($items)): ?>
    <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No Benchmark data found</h4></div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($items as $b): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="doc-card">
          <div class="doc-icon">
            <img src="<?= base_url() ?>/assets/icons/pdf.svg" alt="PDF" width="40">
          </div>
          <div class="doc-name">
            <?= htmlspecialchars($b['district_name']) ?>
            <?php if ($b['level_type'] === 'upazila'): ?>
              <br><small class="text-muted"><?= htmlspecialchars($b['upazila_name']) ?></small>
            <?php endif; ?>
          </div>
          <div class="doc-meta small">
            <span class="badge bg-<?= $b['level_type'] === 'district' ? 'primary' : 'info' ?>">
              <?= ucfirst($b['level_type']) ?>
            </span><br>
            File: <?= htmlspecialchars($b['file_path']) ?><br>
            By: <?= htmlspecialchars($b['uploaded_by']) ?><br>
            <?= date('d M Y', strtotime($b['created_at'])) ?>
          </div>
          <div class="mt-2 d-flex gap-1 flex-wrap">
            <a href="<?= base_url() ?>/assets/bmdata/<?= rawurlencode($b['file_path']) ?>" download
               class="btn btn-sm btn-success rounded-pill"><i class="fa-solid fa-download"></i></a>

            <button class="btn btn-sm btn-outline-primary rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#editBm<?= $b['id'] ?>">
              <i class="fa-solid fa-pen"></i>
            </button>

            <a href="<?= base_url() ?>/actions/bm_manage_action.php?action=delete&id=<?= $b['id'] ?>"
               class="btn btn-sm btn-outline-danger rounded-pill btn-confirm"
               data-confirm="Delete this Benchmark data?">
              <i class="fa-solid fa-trash"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="editBm<?= $b['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="<?= base_url() ?>/actions/bm_manage_action.php" method="post" enctype="multipart/form-data">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Replace Benchmark PDF</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <p class="mb-2">
                  <strong><?= htmlspecialchars($b['district_name']) ?></strong>
                  <?php if ($b['level_type'] === 'upazila'): ?>
                    → <?= htmlspecialchars($b['upazila_name']) ?>
                  <?php endif; ?>
                </p>
                <label class="form-label">New PDF (optional – leave empty to keep current)</label>
                <input type="file" name="bm_file" class="form-control" accept=".pdf">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&district_id=<?= urlencode($distFilter) ?>&upazila_id=<?= urlencode($upaFilter) ?>&level_type=<?= urlencode($levelFilter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
