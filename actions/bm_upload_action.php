<?php
// ============================================================
// PWD Benchmark Upload Action
// PDF only | District or Upazila | No duplicates
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role(['admin', 'subadmin']);

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request.');
}

$level_type  = $_POST['level_type'] ?? 'district';          // district | upazila
$district_id = (int)($_POST['district_id'] ?? 0);
$upazila_id  = (int)($_POST['upazila_id'] ?? 0);

if (!in_array($level_type, ['district', 'upazila'], true) || !$district_id) {
    redirect_bm('District is required.', 'danger');
}

if ($level_type === 'upazila' && !$upazila_id) {
    redirect_bm('Please select an Upazila.', 'danger');
}

// Validate hierarchy
$stmt = $pdo->prepare("SELECT name FROM districts WHERE id = ?");
$stmt->execute([$district_id]);
$district = $stmt->fetch();
if (!$district) {
    redirect_bm('Invalid District.', 'danger');
}

$upazila_name = '';
if ($level_type === 'upazila') {
    $stmt = $pdo->prepare("SELECT name FROM upazilas WHERE id = ? AND district_id = ?");
    $stmt->execute([$upazila_id, $district_id]);
    $upazila = $stmt->fetch();
    if (!$upazila) {
        redirect_bm('Invalid Upazila for selected District.', 'danger');
    }
    $upazila_name = $upazila['name'];
}

// Check for existing record (no re-upload allowed)
if ($level_type === 'district') {
    $stmt = $pdo->prepare("SELECT id FROM pwd_benchmark WHERE level_type = 'district' AND district_id = ?");
    $stmt->execute([$district_id]);
} else {
    $stmt = $pdo->prepare("SELECT id FROM pwd_benchmark WHERE level_type = 'upazila' AND upazila_id = ?");
    $stmt->execute([$upazila_id]);
}
if ($stmt->fetch()) {
    redirect_bm('Benchmark data already exists for this location. Please use Edit or Delete.', 'danger');
}

// File validation – PDF only
if (!isset($_FILES['bm_file']) || $_FILES['bm_file']['error'] !== UPLOAD_ERR_OK) {
    redirect_bm('Please select a PDF file.', 'danger');
}

$file = $_FILES['bm_file'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    redirect_bm('Only PDF files are allowed for Benchmark data.', 'danger');
}

// Build safe filename
$safeDist = preg_replace('/[^A-Za-z0-9_-]/', '_', $district['name']);
if ($level_type === 'district') {
    $newFileName = $safeDist . '_bmdata.pdf';
} else {
    $safeUpa = preg_replace('/[^A-Za-z0-9_-]/', '_', $upazila_name);
    $newFileName = $safeDist . '_' . $safeUpa . '_bmdata.pdf';
}

if (!is_dir(BMDATA_DIR)) {
    mkdir(BMDATA_DIR, 0775, true);
}

$uploadPath = BMDATA_DIR . $newFileName;
if (file_exists($uploadPath)) {
    // Extremely rare, but just in case
    $newFileName = pathinfo($newFileName, PATHINFO_FILENAME) . '_' . time() . '.pdf';
    $uploadPath  = BMDATA_DIR . $newFileName;
}

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    redirect_bm('Failed to upload file. Check folder permissions on assets/bmdata/.', 'danger');
}

$cu = current_user();

$stmt = $pdo->prepare("
    INSERT INTO pwd_benchmark
        (level_type, district_id, upazila_id, file_path, file_type, uploaded_by, uploader_role)
    VALUES (?, ?, ?, ?, 'pdf', ?, ?)
");
$stmt->execute([
    $level_type,
    $district_id,
    $level_type === 'upazila' ? $upazila_id : null,
    $newFileName,
    $cu['username'],
    $cu['role']
]);

redirect_bm('PWD Benchmark data uploaded successfully.', 'success');

function redirect_bm($msg, $type) {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    $role = current_role();
    $base = base_url();
    $page = ($role === 'admin') ? '/admin/manage_bmdata.php' : '/subadmin/manage_bmdata.php';
    header("Location: $base$page");
    exit;
}