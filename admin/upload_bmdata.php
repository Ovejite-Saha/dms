<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['admin', 'subadmin']);

$pdo = db();
$districts = $pdo->query("SELECT id, name FROM districts ORDER BY name")->fetchAll();

$page_title = 'Upload PWD Benchmark Data';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h3 class="mb-4"><i class="fas fa-map-marked-alt me-2"></i>Upload PWD Benchmark Data</h3>

  <?php if (!empty($_SESSION['dms_flash'])): $f = $_SESSION['dms_flash']; unset($_SESSION['dms_flash']); ?>
    <div class="alert alert-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['msg']) ?></div>
  <?php endif; ?>

  <form method="post" action="<?= base_url() ?>/actions/bm_upload_action.php" enctype="multipart/form-data" class="card shadow-sm p-4" id="bmForm">
    
    <!-- Level Type Radio -->
    <div class="mb-4">
      <label class="form-label fw-semibold">Benchmark Level <span class="text-danger">*</span></label>
      <div class="d-flex gap-4">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="level_type" id="level_district" value="district" checked>
          <label class="form-check-label" for="level_district">District</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="level_type" id="level_upazila" value="upazila">
          <label class="form-check-label" for="level_upazila">Upazila</label>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
        <select name="district_id" id="district_id" class="form-select" required>
          <option value="">-- Select District --</option>
          <?php foreach ($districts as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6" id="upazila_wrap" style="display:none;">
        <label class="form-label fw-semibold">Upazila <span class="text-danger">*</span></label>
        <select name="upazila_id" id="upazila_id" class="form-select">
          <option value="">-- Select Upazila --</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">PDF File <span class="text-danger">*</span></label>
        <input type="file" name="bm_file" id="bm_file" class="form-control" accept=".pdf" required>
        <div class="form-text">Only PDF files are accepted.</div>
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary" id="btnUpload">
        <i class="fas fa-cloud-upload-alt me-1"></i> Upload Benchmark Data
      </button>
      <a href="<?= base_url() ?>/<?= current_role() === 'admin' ? 'admin' : 'subadmin' ?>/manage_bmdata.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
(function() {
  const levelDistrict = document.getElementById('level_district');
  const levelUpazila  = document.getElementById('level_upazila');
  const districtSel   = document.getElementById('district_id');
  const upazilaWrap   = document.getElementById('upazila_wrap');
  const upazilaSel    = document.getElementById('upazila_id');
  const ajaxBase      = '<?= base_url() ?>/actions/ajax_hierarchy.php';

  function toggleUpazila() {
    if (levelUpazila.checked) {
      upazilaWrap.style.display = 'block';
      upazilaSel.required = true;
    } else {
      upazilaWrap.style.display = 'none';
      upazilaSel.required = false;
      upazilaSel.innerHTML = '<option value="">-- Select Upazila --</option>';
    }
  }

  levelDistrict.addEventListener('change', toggleUpazila);
  levelUpazila.addEventListener('change', toggleUpazila);

  districtSel.addEventListener('change', function() {
    upazilaSel.innerHTML = '<option value="">-- Select Upazila --</option>';
    if (!this.value || !levelUpazila.checked) return;

    fetch(ajaxBase + '?action=upazilas&district_id=' + this.value)
      .then(r => r.json())
      .then(data => {
        data.forEach(i => {
          const o = document.createElement('option');
          o.value = i.id;
          o.textContent = i.name;
          upazilaSel.appendChild(o);
        });
      });
  });
})();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>