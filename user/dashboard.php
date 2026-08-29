<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/doc_access.php';
require_role('user');

$pdo = db();
$cu  = current_user();

$search     = trim($_GET['search'] ?? '');
$minFilter  = $_GET['ministry_id'] ?? '';
$divFilter  = $_GET['division_id'] ?? '';
$distFilter = $_GET['district_id'] ?? '';
$upaFilter  = $_GET['upazila_id'] ?? '';
$projFilter = $_GET['project_id'] ?? '';
$typeFilter = $_GET['document_type_id'] ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 16;

$ministries = $pdo->query("SELECT id, name FROM ministries ORDER BY name")->fetchAll();
$divisions  = $pdo->query("SELECT id, name FROM divisions ORDER BY name")->fetchAll();
$projects   = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll();
try {
    $documentTypes = $pdo->query("SELECT id, name FROM document_types ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    $documentTypes = [];
}

// Load all districts & upazilas for access label
$allDistricts = $pdo->query("SELECT id, name FROM districts ORDER BY name")->fetchAll();
$allUpazilas  = $pdo->query("SELECT id, name FROM upazilas ORDER BY name")->fetchAll();

if ($divFilter !== '') {
    $stmt = $pdo->prepare("SELECT id, name FROM districts WHERE division_id = ? ORDER BY name");
    $stmt->execute([$divFilter]);
    $filterDistricts = $stmt->fetchAll();
} else {
    $filterDistricts = $allDistricts;
}

if ($distFilter !== '') {
    $stmt = $pdo->prepare("SELECT id, name FROM upazilas WHERE district_id = ? ORDER BY name");
    $stmt->execute([$distFilter]);
    $filterUpazilas = $stmt->fetchAll();
} else {
    $filterUpazilas = $allUpazilas;
}

[$where, $params] = user_document_filter($pdo, $cu);
$sql = documents_base_query() . " WHERE $where";

if ($minFilter !== '') {
    $sql .= " AND d.ministry_id = ?";
    $params[] = $minFilter;
}
if ($divFilter !== '') {
    $sql .= " AND d.division_id = ?";
    $params[] = $divFilter;
}
if ($distFilter !== '') {
    $sql .= " AND d.district_id = ?";
    $params[] = $distFilter;
}
if ($upaFilter !== '') {
    $sql .= " AND d.upazila_id = ?";
    $params[] = $upaFilter;
}
if ($projFilter !== '') {
    $sql .= " AND d.project_id = ?";
    $params[] = $projFilter;
}
if ($typeFilter !== '') {
    $sql .= " AND d.document_type_id = ?";
    $params[] = $typeFilter;
}
if ($search !== '') {
    $sql .= " AND (d.document_name LIKE ? OR d.building_name LIKE ? OR p.name LIKE ? OR m.name LIKE ? OR u.name LIKE ? OR dt.name LIKE ? OR COALESCE(dtype.name, d.document_name) LIKE ?)";
    $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%","%$search%","%$search%","%$search%"]);
}

$sql .= " ORDER BY d.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allDocuments = $stmt->fetchAll();
$allBenchmarks = $stmt->fetchAll();

$totalDocuments = count($allDocuments);
$totalBenchmarks = count($allBenchmarks);
$totalPages     = max(1, (int)ceil($totalDocuments / $perPage));
$page           = min($page, $totalPages);
$offset         = ($page - 1) * $perPage;
$documents      = array_slice($allDocuments, $offset, $perPage);
$docCount       = $totalDocuments;
$bmCount       = $totalBenchmarks;

// ========== Access Label Logic ==========
$accessType = $cu['access_type'] ?? 'all';
$accessIds  = $cu['access_ids'] ?? null;

$minMap  = []; foreach ($ministries as $m) $minMap[$m['id']]  = $m['name'];
$divMap  = []; foreach ($divisions  as $d) $divMap[$d['id']]  = $d['name'];
$distMap = []; foreach ($allDistricts as $d) $distMap[$d['id']] = $d['name'];
$upaMap  = []; foreach ($allUpazilas  as $u) $upaMap[$u['id']]  = $u['name'];
$projMap = []; foreach ($projects as $p) $projMap[$p['id']] = $p['name'];

$lookups = [
    'ministrie' => $minMap,
    'division'  => $divMap,
    'district'  => $distMap,
    'upazila'   => $upaMap,
    'project'   => $projMap,
];

function user_access_label($type, $ids_json, $lookups) {
    if ($type === 'all' || empty($type)) {
        return '<i class="fa-solid fa-globe"></i> Access: All Documents';
    }

    $ids = is_string($ids_json) ? json_decode($ids_json, true) : $ids_json;
    if (!is_array($ids) || empty($ids)) {
        return '<i class="fa-solid fa-lock"></i> Access: ' . htmlspecialchars(ucfirst($type));
    }

    $map   = $lookups[$type] ?? [];
    $names = [];
    foreach ($ids as $id) {
        if (isset($map[$id])) {
            $names[] = $map[$id];
        }
    }

    $label = ucfirst($type);
    $text  = implode(', ', array_slice($names, 0, 4)); // show max 4 names
    if (count($names) > 4) {
        $text .= ' +' . (count($names) - 4);
    }

    return '<i class="fa-solid fa-lock"></i> Access: ' . htmlspecialchars($label) . ': ' . htmlspecialchars($text);
}

function type_class($ext) {
    $ext = strtolower($ext ?? '');
    if (in_array($ext, ['pdf'])) return 'pdf';
    if (in_array($ext, ['doc','docx'])) return 'doc';
    if (in_array($ext, ['xls','xlsx'])) return 'xls';
    if (in_array($ext, ['ppt','pptx'])) return 'ppt';
    if (in_array($ext, ['jpg','jpeg','png','gif'])) return 'img';
    if (in_array($ext, ['zip','rar','7z'])) return 'zip';
    if (in_array($ext, ['shp','geojson','kml','kmz','gpkg','tif','tiff','gpx'])) return 'gis';
    return 'file';
}

function page_url($pageNum) {
    $q = $_GET;
    $q['page'] = $pageNum;
    return '?' . http_build_query($q);
}

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);
$ajaxBase = base_url() . '/actions/ajax_hierarchy.php';
?>
<div class="container">
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="fa-solid fa-gauge"></i> User Dashboard</h2>
    <span class="text-muted">Welcome, <strong><?= htmlspecialchars($cu['full_name']) ?></strong></span>
  </div>

  <?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Access Badge (shows real names) -->
  <div class="mb-3">
    <?php if ($accessType === 'all'): ?>
      <span class="badge bg-success p-2">
        <i class="fa-solid fa-globe"></i> Access: All Documents
      </span>
    <?php else: ?>
      <span class="badge bg-warning text-dark p-2">
        <?= user_access_label($accessType, $accessIds, $lookups) ?>
      </span>
    <?php endif; ?>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="stat-card stat-blue">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-value"><?= $docCount ?></div>
        <div class="stat-label">Available Documents</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-card stat-bm">
        <div class="stat-icon"><i class="fa-solid fa-landmark"></i></div>
        <div class="stat-value"><?= $bmCount ?></div>
        <div class="stat-label">Available Benchmarks</div>
      </div>
    </div>
  </div>

  <!-- Search Bar -->
  <form method="get" class="mb-4" id="filterForm">
    <div class="search-bar d-flex flex-nowrap align-items-center gap-2" style="overflow-x:auto; padding-bottom:4px;">
      <select name="ministry_id" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Ministries</option>
        <?php foreach ($ministries as $m): ?>
          <option value="<?= $m['id'] ?>" <?= (string)$minFilter===(string)$m['id']?'selected':'' ?>><?= htmlspecialchars($m['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="division_id" id="filter_division" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Divisions</option>
        <?php foreach ($divisions as $d): ?>
          <option value="<?= $d['id'] ?>" <?= (string)$divFilter===(string)$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="district_id" id="filter_district" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Districts</option>
        <?php foreach ($filterDistricts as $d): ?>
          <option value="<?= $d['id'] ?>" <?= (string)$distFilter===(string)$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="upazila_id" id="filter_upazila" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Upazilas</option>
        <?php foreach ($filterUpazilas as $u): ?>
          <option value="<?= $u['id'] ?>" <?= (string)$upaFilter===(string)$u['id']?'selected':'' ?>><?= htmlspecialchars($u['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="project_id" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Projects</option>
        <?php foreach ($projects as $p): ?>
          <option value="<?= $p['id'] ?>" <?= (string)$projFilter===(string)$p['id']?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="document_type_id" class="form-select form-select-sm" style="min-width:150px;max-width:170px;">
        <option value="">All Doc Types</option>
        <?php foreach ($documentTypes as $t): ?>
          <option value="<?= $t['id'] ?>" <?= (string)$typeFilter===(string)$t['id']?'selected':'' ?>><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="Search type, building, project..." value="<?= htmlspecialchars($search) ?>"
             style="min-width:180px;max-width:220px;flex:1 1 auto;">

      <button type="submit" class="btn btn-primary btn-sm rounded-pill text-nowrap px-3">
        <i class="fa-solid fa-magnifying-glass"></i> Search
      </button>
      <a href="?" class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap px-3">Reset</a>
    </div>
  </form>

  <script>
  (function(){
    var ajaxBase = '<?= $ajaxBase ?>';
    var div  = document.getElementById('filter_division');
    var dist = document.getElementById('filter_district');
    var upa  = document.getElementById('filter_upazila');
    div.addEventListener('change', function(){
      dist.innerHTML = '<option value="">All Districts</option>';
      upa.innerHTML  = '<option value="">All Upazilas</option>';
      if (!this.value) return;
      fetch(ajaxBase+'?action=districts&division_id='+this.value).then(r=>r.json()).then(function(data){
        data.forEach(function(i){ var o=document.createElement('option'); o.value=i.id; o.textContent=i.name; dist.appendChild(o); });
      });
    });
    dist.addEventListener('change', function(){
      upa.innerHTML = '<option value="">All Upazilas</option>';
      if (!this.value) return;
      fetch(ajaxBase+'?action=upazilas&district_id='+this.value).then(r=>r.json()).then(function(data){
        data.forEach(function(i){ var o=document.createElement('option'); o.value=i.id; o.textContent=i.name; upa.appendChild(o); });
      });
    });
  })();
  </script>
  <script>
(function() {
    // Detect if the page was refreshed (F5 / Ctrl+R / browser refresh button)
    let isRefresh = false;

    if (performance.getEntriesByType) {
        const nav = performance.getEntriesByType('navigation')[0];
        if (nav && nav.type === 'reload') {
            isRefresh = true;
        }
    } else if (performance.navigation) {
        // Old browsers
        if (performance.navigation.type === 1) {
            isRefresh = true;
        }
    }

    if (isRefresh) {
        // Redirect to clean URL (remove all filters)
        window.location.href = window.location.pathname;
    }
})();
</script>
<script>
(function() {
    function isPageRefresh() {
        if (performance.getEntriesByType) {
            const nav = performance.getEntriesByType('navigation')[0];
            return nav && nav.type === 'reload';
        }
        return performance.navigation && performance.navigation.type === 1;
    }

    if (isPageRefresh()) {
        // Go to clean page (no query parameters)
        window.location.replace(window.location.pathname);
    }
})();
</script>

  <?php if (empty($documents)): ?>
    <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No documents available for your access level</h4></div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($documents as $d): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="doc-card">
          <div class="doc-icon"><img src="<?= base_url() ?>/<?= file_type_icon($d['file_path']) ?>" alt="icon" width="40"></div>
          <div class="doc-name"><?= htmlspecialchars($d['document_type_name'] ?? $d['document_name']) ?></div>
          <div class="doc-meta small">
            <span class="doc-type-badge type-<?= type_class($d['file_type']) ?>"><?= file_type_label($d['file_path']) ?></span><br>
            <strong>Building:</strong> <?= htmlspecialchars($d['building_name'] ?? '—') ?><br>
            <strong>Ministry:</strong> <?= htmlspecialchars($d['ministry_name']) ?><br>
            <strong>Division:</strong> <?= htmlspecialchars($d['division_name']) ?><br>
            <strong>District:</strong> <?= htmlspecialchars($d['district_name']) ?><br>
            <strong>Upazila:</strong> <?= htmlspecialchars($d['upazila_name']) ?><br>
            <strong>Project:</strong> <?= htmlspecialchars($d['project_name']) ?><br>
            Date: <?= htmlspecialchars($d['doc_date']) ?>
          </div>
          <div class="mt-2">
            <a href="<?= base_url() ?>/assets/uploads/<?= rawurlencode($d['file_path']) ?>" download
               class="btn btn-sm btn-success rounded-pill">
              <i class="fa-solid fa-download"></i> Download
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link rounded-pill me-1" href="<?= $page <= 1 ? '#' : page_url($page-1) ?>">« Prev</a>
      </li>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link rounded-pill mx-1" href="<?= page_url($i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link rounded-pill ms-1" href="<?= $page >= $totalPages ? '#' : page_url($page+1) ?>">Next »</a>
      </li>
    </ul>
  </nav>
  <div class="text-center text-muted small mt-1">
    Showing <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalDocuments) ?> of <?= $totalDocuments ?> documents
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
