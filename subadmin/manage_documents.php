<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/doc_access.php';
require_role(['admin','subadmin']);

$pdo = db();
$ministries = $pdo->query("SELECT id, name FROM ministries ORDER BY name")->fetchAll();
$divisions  = $pdo->query("SELECT id, name FROM divisions ORDER BY name")->fetchAll();
$projects   = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll();

$search     = trim($_GET['search'] ?? '');
$minFilter  = $_GET['ministry_id'] ?? '';
$divFilter  = $_GET['division_id'] ?? '';
$distFilter = $_GET['district_id'] ?? '';
$upaFilter  = $_GET['upazila_id'] ?? '';
$projFilter = $_GET['project_id'] ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 16;

if ($divFilter !== '') {
    $stmt = $pdo->prepare("SELECT id, name FROM districts WHERE division_id = ? ORDER BY name");
    $stmt->execute([$divFilter]);
    $filterDistricts = $stmt->fetchAll();
} else {
    $filterDistricts = $pdo->query("SELECT id, name FROM districts ORDER BY name")->fetchAll();
}

if ($distFilter !== '') {
    $stmt = $pdo->prepare("SELECT id, name FROM upazilas WHERE district_id = ? ORDER BY name");
    $stmt->execute([$distFilter]);
    $filterUpazilas = $stmt->fetchAll();
} else {
    $filterUpazilas = $pdo->query("SELECT id, name FROM upazilas ORDER BY name")->fetchAll();
}

$sql = documents_base_query() . " WHERE 1=1";
$params = [];
if ($search !== '') {
    $sql .= " AND (d.document_name LIKE ? OR p.name LIKE ? OR m.name LIKE ? OR u.name LIKE ?)";
    $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]);
}
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
$sql .= " ORDER BY d.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allDocuments = $stmt->fetchAll();

$totalDocuments = count($allDocuments);
$totalPages     = max(1, (int)ceil($totalDocuments / $perPage));
$page           = min($page, $totalPages);
$offset         = ($page - 1) * $perPage;
$documents      = array_slice($allDocuments, $offset, $perPage);

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
    <h2><i class="fa-solid fa-file-lines"></i> Manage Documents</h2>
    <a href="<?= base_url() ?>/subadmin/upload_document.php" class="btn btn-primary rounded-pill">
      <i class="fa-solid fa-upload"></i> Upload Document
    </a>
  </div>

  <?php if ($flash): ?>
  <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

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

      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="Search document, project..." value="<?= htmlspecialchars($search) ?>"
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

  <?php if (empty($documents)): ?>
    <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No documents found</h4></div>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($documents as $d): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">   <!-- 4 columns -->
        <div class="doc-card">
          <div class="doc-icon"><img src="<?= base_url() ?>/<?= file_type_icon($d['file_path']) ?>" alt="icon" width="40"></div>
          <div class="doc-name"><?= htmlspecialchars($d['document_name']) ?></div>
          <div class="doc-meta small">
            <span class="doc-type-badge type-<?= type_class($d['file_type']) ?>"><?= file_type_label($d['file_path']) ?></span><br>
            <strong>Ministry:</strong> <?= htmlspecialchars($d['ministry_name']) ?><br>
            <strong>Division:</strong> <?= htmlspecialchars($d['division_name']) ?><br>
            <strong>District:</strong> <?= htmlspecialchars($d['district_name']) ?><br>
            <strong>Upazila:</strong> <?= htmlspecialchars($d['upazila_name']) ?><br>
            <strong>Project:</strong> <?= htmlspecialchars($d['project_name']) ?><br>
            Date: <?= htmlspecialchars($d['doc_date']) ?> | By: <?= htmlspecialchars($d['uploaded_by']) ?>
          </div>
          <div class="mt-2 d-flex gap-1 flex-wrap">
            <a href="<?= base_url() ?>/assets/uploads/<?= rawurlencode($d['file_path']) ?>" download
               class="btn btn-sm btn-success rounded-pill"><i class="fa-solid fa-download"></i></a>
            <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editDoc<?= $d['id'] ?>">
              <i class="fa-solid fa-pen"></i>
            </button>
            <a href="<?= base_url() ?>/actions/doc_manage_action.php?action=delete&id=<?= $d['id'] ?>"
               class="btn btn-sm btn-outline-danger rounded-pill btn-confirm" data-confirm="Delete this document?">
              <i class="fa-solid fa-trash"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Edit Modal (unchanged) -->
      <div class="modal fade" id="editDoc<?= $d['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <form action="<?= base_url() ?>/actions/doc_manage_action.php" method="post" class="edit-doc-form" data-id="<?= $d['id'] ?>">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Ministry *</label>
                    <select name="ministry_id" class="form-select ministry-sel" required>
                      <?php foreach ($ministries as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= $d['ministry_id']==$m['id']?'selected':'' ?>><?= htmlspecialchars($m['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Division *</label>
                    <select name="division_id" class="form-select division-sel" required>
                      <?php foreach ($divisions as $dv): ?>
                        <option value="<?= $dv['id'] ?>" <?= $d['division_id']==$dv['id']?'selected':'' ?>><?= htmlspecialchars($dv['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">District *</label>
                    <select name="district_id" class="form-select district-sel" required data-selected="<?= $d['district_id'] ?>">
                      <option value="<?= $d['district_id'] ?>"><?= htmlspecialchars($d['district_name']) ?></option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Upazila *</label>
                    <select name="upazila_id" class="form-select upazila-sel" required data-selected="<?= $d['upazila_id'] ?>">
                      <option value="<?= $d['upazila_id'] ?>"><?= htmlspecialchars($d['upazila_name']) ?></option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Project *</label>
                    <select name="project_id" class="form-select" required>
                      <?php foreach ($projects as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $d['project_id']==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Document Name *</label>
                    <input type="text" name="document_name" class="form-control" value="<?= htmlspecialchars($d['document_name']) ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="doc_date" class="form-control" value="<?= htmlspecialchars($d['doc_date']) ?>">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill">Save</button>
              </div>
            </form>
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

<script>
(function(){
  const ajaxBase = '<?= $ajaxBase ?>';
  document.querySelectorAll('.edit-doc-form').forEach(function(form){
    const divSel  = form.querySelector('.division-sel');
    const distSel = form.querySelector('.district-sel');
    const upaSel  = form.querySelector('.upazila-sel');
    const selDist = distSel.getAttribute('data-selected');
    const selUpa  = upaSel.getAttribute('data-selected');

    function loadDistricts(divId, selected) {
      distSel.innerHTML = '<option value="">Loading...</option>';
      upaSel.innerHTML  = '<option value="">-- Select Upazila --</option>';
      fetch(ajaxBase+'?action=districts&division_id='+divId).then(r=>r.json()).then(data=>{
        distSel.innerHTML = '<option value="">-- Select District --</option>';
        data.forEach(i=>{
          const o=document.createElement('option'); o.value=i.id; o.textContent=i.name;
          if (String(i.id)===String(selected)) o.selected=true;
          distSel.appendChild(o);
        });
        if (selected) loadUpazilas(selected, selUpa);
      });
    }
    function loadUpazilas(distId, selected) {
      upaSel.innerHTML = '<option value="">Loading...</option>';
      fetch(ajaxBase+'?action=upazilas&district_id='+distId).then(r=>r.json()).then(data=>{
        upaSel.innerHTML = '<option value="">-- Select Upazila --</option>';
        data.forEach(i=>{
          const o=document.createElement('option'); o.value=i.id; o.textContent=i.name;
          if (String(i.id)===String(selected)) o.selected=true;
          upaSel.appendChild(o);
        });
      });
    }
    divSel.addEventListener('change', function(){ loadDistricts(this.value, null); });
    distSel.addEventListener('change', function(){ loadUpazilas(this.value, null); });
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
<?php require_once __DIR__ . '/../includes/footer.php'; ?>