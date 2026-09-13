<?php
// ============================================================
// Document Upload Action
// Fields: Ministry, Division, District, Upazila, Project,
//         Building Name, Document Type
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role(['admin', 'subadmin']);

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request.');
}

$ministry_id       = (int)($_POST['ministry_id'] ?? 0);
$division_id       = (int)($_POST['division_id'] ?? 0);
$district_id       = (int)($_POST['district_id'] ?? 0);
$upazila_id        = (int)($_POST['upazila_id'] ?? 0);
$project_id        = (int)($_POST['project_id'] ?? 0);
$building_name     = trim($_POST['building_name'] ?? '');
$document_type_id  = (int)($_POST['document_type_id'] ?? 0);
$doc_date          = $_POST['doc_date'] ?? date('Y-m-d');

if (!$ministry_id || !$division_id || !$district_id || !$upazila_id || !$project_id || $building_name === '' || !$document_type_id) {
    redirect_doc('All fields (Ministry, Division, District, Upazila, Project, Building Name, Document Type) are required.', 'danger');
}

$stmt = $pdo->prepare("SELECT id FROM districts WHERE id = ? AND division_id = ?");
$stmt->execute([$district_id, $division_id]);
if (!$stmt->fetch()) {
    redirect_doc('Invalid District for selected Division.', 'danger');
}
$stmt = $pdo->prepare("SELECT id FROM upazilas WHERE id = ? AND district_id = ?");
$stmt->execute([$upazila_id, $district_id]);
if (!$stmt->fetch()) {
    redirect_doc('Invalid Upazila for selected District.', 'danger');
}

$stmt = $pdo->prepare("SELECT id, name FROM document_types WHERE id = ?");
$stmt->execute([$document_type_id]);
$docType = $stmt->fetch();
if (!$docType) {
    redirect_doc('Invalid Document Type.', 'danger');
}
$document_name = $docType['name'];

$allowed = [
    'pdf','doc','docx','xls','xlsx','ppt','pptx',
    'jpg','jpeg','png','gif','txt','zip',
    'shp','shx','dbf','prj','sbn','sbx','cpg','qix',
    'geojson','json','kml','kmz','gpx','gpkg','tif','tiff','geotiff',
    'asc','dem','tab','mif','mid','gml','dxf',
    'dwg'   // ← added
];

if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
    redirect_doc('Please select a file to upload.', 'danger');
}

$file     = $_FILES['document_file'];
$origName = $file['name'];
$ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed, true)) {
    redirect_doc('File type not allowed. Supported: PDF, Office, Images, TXT, ZIP, and GIS formats.', 'danger');
}

$projName = $pdo->prepare("SELECT name FROM projects WHERE id = ?");
$projName->execute([$project_id]);
$project_name = $projName->fetchColumn() ?: 'project';

$safeProject  = preg_replace('/[^A-Za-z0-9_-]/', '_', $project_name);
$safeBuilding = preg_replace('/[^A-Za-z0-9_-]/', '_', $building_name);
$safeType     = preg_replace('/[^A-Za-z0-9_-]/', '_', $document_name);
$newFileName  = $safeProject . '_' . $safeBuilding . '_' . $safeType . '.' . $ext;

$uploadPath = UPLOAD_DIR . $newFileName;
$counter = 1;
while (file_exists($uploadPath)) {
    $newFileName  = $safeProject . '_' . $safeBuilding . '_' . $safeType . '_' . $counter . '.' . $ext;
    $uploadPath   = UPLOAD_DIR . $newFileName;
    $counter++;
}

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    redirect_doc('Failed to upload file. Check folder permissions.', 'danger');
}

$cu = current_user();

$stmt = $pdo->prepare("
    INSERT INTO documents
        (document_name, ministry_id, division_id, district_id, upazila_id, project_id,
         building_name, document_type_id, doc_date, file_path, file_type, uploaded_by, uploader_role)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
");
$stmt->execute([
    $document_name, $ministry_id, $division_id, $district_id,
    $upazila_id, $project_id, $building_name, $document_type_id,
    $doc_date, $newFileName, $ext,
    $cu['username'], $cu['role'],
]);

redirect_doc('Document uploaded successfully.', 'success');

function redirect_doc($msg, $type) {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    $role = current_role();
    $base = base_url();
    $page = ($role === 'admin') ? '/admin/manage_documents.php' : '/subadmin/manage_documents.php';
    header("Location: $base$page");
    exit;
}
