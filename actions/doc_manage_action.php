<?php
// ============================================================
// Document Manage Action - Edit / Delete
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role(['admin', 'subadmin']);

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'edit':
        $id               = (int)($_POST['id'] ?? 0);
        $ministry_id      = (int)($_POST['ministry_id'] ?? 0);
        $division_id      = (int)($_POST['division_id'] ?? 0);
        $district_id      = (int)($_POST['district_id'] ?? 0);
        $upazila_id       = (int)($_POST['upazila_id'] ?? 0);
        $project_id       = (int)($_POST['project_id'] ?? 0);
        $building_name    = trim($_POST['building_name'] ?? '');
        $document_type_id = (int)($_POST['document_type_id'] ?? 0);
        $doc_date         = $_POST['doc_date'] ?? date('Y-m-d');

        if (!$ministry_id || !$division_id || !$district_id || !$upazila_id || !$project_id || $building_name === '' || !$document_type_id) {
            redirect_doc('All fields are required.', 'danger');
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

        $stmt = $pdo->prepare("
            UPDATE documents SET
                document_name = ?, ministry_id = ?, division_id = ?, district_id = ?,
                upazila_id = ?, project_id = ?, building_name = ?, document_type_id = ?, doc_date = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $document_name, $ministry_id, $division_id, $district_id,
            $upazila_id, $project_id, $building_name, $document_type_id, $doc_date, $id
        ]);
        redirect_doc('Document updated successfully.', 'success');
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
        if ($doc) {
            $filePath = UPLOAD_DIR . $doc['file_path'];
            if (file_exists($filePath)) unlink($filePath);
            $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
            $stmt->execute([$id]);
            redirect_doc('Document deleted successfully.', 'success');
        }
        redirect_doc('Document not found.', 'danger');
        break;

    default:
        redirect_doc('Invalid action.', 'danger');
}

function redirect_doc($msg, $type) {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    $role = current_role();
    $base = base_url();
    $page = ($role === 'admin') ? '/admin/manage_documents.php' : '/subadmin/manage_documents.php';
    header("Location: $base$page");
    exit;
}
