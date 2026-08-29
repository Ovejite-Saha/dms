<?php
require_once __DIR__ . '/../includes/header.php';
require_role(['user']);

$pdo = db();

$search     = trim($_GET['search'] ?? '');
$distFilter = $_GET['district_id'] ?? '';
$levelFilter= $_GET['level_type'] ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 16;

$districts = $pdo->query("SELECT id, name FROM districts ORDER BY name")->fetchAll();

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
    $sql .= " AND (d.name LIKE ? OR u.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($distFilter !== '') {
    $sql .= " AND b.district_id = ?";
    $params[] = $distFilter;
}
if ($levelFilter !== '') {
    $sql .= " AND b.level_type = ?";
    $params[] = $levelFilter;
}

$sql .= " ORDER BY d.name, u.name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$all = $stmt->fetchAll();

$total = count($all);
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$items = array_slice($all, ($page-1)*$perPage, $perPage);
?>
<div class="container">
  <div class="page-header">
    <h2><i class="fa-solid fa-map-location-dot"></i> PWD Benchmark Data</h2>
  </div>

  <!-- Filters -->
<form method="get" class="mb-4">
  <div class="search-bar d-flex flex-nowrap align-items-center gap-2" style="overflow-x:auto;">
    
    <select name="district_id" class="form-select form-select-sm" style="min-width:100px;">
      <option value="">All Districts</option>
      <?php foreach ($districts as $d): ?>
        <option value="<?= $d['id'] ?>" <?= (string)$distFilter === (string)$d['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($d['name']) ?>
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

  <?php if (empty($items)): ?>
    <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No Benchmark data available</h4></div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($items as $b): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="doc-card">
          <div class="doc-icon"><img src="<?= base_url() ?>/assets/icons/pdf.svg" width="40"></div>
          <div class="doc-name">
            <?= htmlspecialchars($b['district_name']) ?>
            <?php if ($b['level_type'] === 'upazila'): ?>
              <br><small class="text-muted"><?= htmlspecialchars($b['upazila_name']) ?></small>
            <?php endif; ?>
          </div>
          <div class="doc-meta small">
            <span class="badge bg-<?= $b['level_type'] === 'district' ? 'primary' : 'info' ?>">
              <?= ucfirst($b['level_type']) ?>
            </span>
          </div>
          <div class="mt-2">
            <a href="<?= base_url() ?>/assets/bmdata/<?= rawurlencode($b['file_path']) ?>" download
               class="btn btn-sm btn-success rounded-pill w-100">
              <i class="fa-solid fa-download"></i> Download PDF
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($totalPages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&district_id=<?= urlencode($distFilter) ?>&level_type=<?= urlencode($levelFilter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>