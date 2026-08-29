<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/file_helper.php';
require_role(['admin', 'subadmin']);

$pdo = db();
$ministries = $pdo->query("SELECT id, name FROM ministries ORDER BY name")->fetchAll();
$divisions  = $pdo->query("SELECT id, name FROM divisions ORDER BY name")->fetchAll();
$projects   = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll();
try {
    $documentTypes = $pdo->query("SELECT id, name FROM document_types ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    $documentTypes = [];
}
$exts = allowed_upload_extensions();
$accept = '.' . implode(',.', $exts);

$page_title = 'Upload Document';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h3 class="mb-4"><i class="fas fa-upload me-2"></i>Upload Document</h3>

  <?php if (!empty($_SESSION['dms_flash'])): $f = $_SESSION['dms_flash']; unset($_SESSION['dms_flash']); ?>
    <div class="alert alert-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endif; ?>

  <?php if (empty($documentTypes)): ?>
    <div class="alert alert-warning">
      No document types found. Ask an admin to add Document Types under <strong>Hierarchy → Document Types</strong>.
    </div>
  <?php endif; ?>

  <form method="post" action="<?= base_url() ?>/actions/doc_upload_action.php" enctype="multipart/form-data" id="uploadForm" class="card shadow-sm p-4">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Ministry Name <span class="text-danger">*</span></label>
        <select name="ministry_id" id="ministry_id" class="form-select" required>
          <option value="">-- Select Ministry --</option>
          <?php foreach ($ministries as $m): ?>
            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Division Name <span class="text-danger">*</span></label>
        <select name="division_id" id="division_id" class="form-select" required disabled>
          <option value="">-- Select Division --</option>
          <?php foreach ($divisions as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">District Name <span class="text-danger">*</span></label>
        <select name="district_id" id="district_id" class="form-select" required disabled>
          <option value="">-- Select District --</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Upazila Name <span class="text-danger">*</span></label>
        <select name="upazila_id" id="upazila_id" class="form-select" required disabled>
          <option value="">-- Select Upazila --</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
        <select name="project_id" id="project_id" class="form-select" required disabled>
          <option value="">-- Select Project --</option>
          <?php foreach ($projects as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Building Name <span class="text-danger">*</span></label>
        <input type="text" name="building_name" id="building_name" class="form-control" required disabled placeholder="Enter building name">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
        <select name="document_type_id" id="document_type_id" class="form-select" required disabled>
          <option value="">-- Select Document Type --</option>
          <?php foreach ($documentTypes as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Document Date</label>
        <input type="date" name="doc_date" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Select File <span class="text-danger">*</span></label>
        <input type="file" name="document_file" id="document_file" class="form-control" required disabled accept="<?= htmlspecialchars($accept) ?>">
        <div class="form-text">PDF, Office, Images, TXT, ZIP, GIS (SHP, KML, GeoJSON, GeoTIFF…)</div>
      </div>
    </div>
    <div class="mt-4">
      <button type="submit" class="btn btn-primary" id="btnUpload" disabled>
        <i class="fas fa-cloud-upload-alt me-1"></i> Upload Document
      </button>
      <a href="<?= base_url() ?>/subadmin/manage_documents.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<script>
(function() {
  const ministry  = document.getElementById('ministry_id');
  const division  = document.getElementById('division_id');
  const district  = document.getElementById('district_id');
  const upazila   = document.getElementById('upazila_id');
  const project   = document.getElementById('project_id');
  const building  = document.getElementById('building_name');
  const docType   = document.getElementById('document_type_id');
  const fileInput = document.getElementById('document_file');
  const btnUpload = document.getElementById('btnUpload');
  const ajaxBase  = '<?= base_url() ?>/actions/ajax_hierarchy.php';

  function resetSelect(el, ph) { el.innerHTML = '<option value="">'+ph+'</option>'; el.disabled = true; el.value = ''; }
  function enable(el) { el.disabled = false; }
  function checkReady() {
    btnUpload.disabled = !(
      ministry.value && division.value && district.value && upazila.value &&
      project.value && building.value.trim() && docType.value && fileInput.files.length
    );
  }

  ministry.addEventListener('change', function() {
    resetSelect(district,'-- Select District --'); resetSelect(upazila,'-- Select Upazila --');
    project.disabled=true; project.value='';
    building.disabled=true; building.value='';
    docType.disabled=true; docType.value='';
    fileInput.disabled=true; fileInput.value='';
    if (this.value) enable(division); else { division.disabled=true; division.value=''; }
    checkReady();
  });
  division.addEventListener('change', function() {
    resetSelect(district,'-- Select District --'); resetSelect(upazila,'-- Select Upazila --');
    project.disabled=true; project.value='';
    building.disabled=true; building.value='';
    docType.disabled=true; docType.value='';
    fileInput.disabled=true; fileInput.value='';
    if (this.value) {
      fetch(ajaxBase+'?action=districts&division_id='+this.value).then(r=>r.json()).then(data=>{
        data.forEach(i=>{ const o=document.createElement('option'); o.value=i.id; o.textContent=i.name; district.appendChild(o); });
        enable(district);
      });
    }
    checkReady();
  });
  district.addEventListener('change', function() {
    resetSelect(upazila,'-- Select Upazila --');
    project.disabled=true; project.value='';
    building.disabled=true; building.value='';
    docType.disabled=true; docType.value='';
    fileInput.disabled=true; fileInput.value='';
    if (this.value) {
      fetch(ajaxBase+'?action=upazilas&district_id='+this.value).then(r=>r.json()).then(data=>{
        data.forEach(i=>{ const o=document.createElement('option'); o.value=i.id; o.textContent=i.name; upazila.appendChild(o); });
        enable(upazila);
      });
    }
    checkReady();
  });
  upazila.addEventListener('change', function() {
    project.disabled=true; project.value='';
    building.disabled=true; building.value='';
    docType.disabled=true; docType.value='';
    fileInput.disabled=true; fileInput.value='';
    if (this.value) enable(project);
    checkReady();
  });
  project.addEventListener('change', function() {
    building.disabled=true; building.value='';
    docType.disabled=true; docType.value='';
    fileInput.disabled=true; fileInput.value='';
    if (this.value) enable(building);
    checkReady();
  });
  building.addEventListener('input', function() {
    if (this.value.trim()) enable(docType); else { docType.disabled=true; docType.value=''; fileInput.disabled=true; fileInput.value=''; }
    checkReady();
  });
  docType.addEventListener('change', function() {
    if (this.value) enable(fileInput); else { fileInput.disabled=true; fileInput.value=''; }
    checkReady();
  });
  fileInput.addEventListener('change', checkReady);
})();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
