<?php
// ============================================================
// PWD Benchmark Manage Action – Edit (replace file) / Delete
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role(['admin', 'subadmin']);

$pdo    = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'edit':
        $id = (int)($_POST['id'] ?? 0);

        $stmt = $pdo->prepare("SELECT * FROM pwd_benchmark WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            redirect_bm('Record not found.', 'danger');
        }

        // Optional new PDF
        if (isset($_FILES['bm_file']) && $_FILES['bm_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['bm_file'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                redirect_bm('Only PDF files are allowed.', 'danger');
            }

            // Delete old file
            $oldPath = BMDATA_DIR . $row['file_path'];
            if (file_exists($oldPath)) unlink($oldPath);

            // Keep the same naming convention
            $newFileName = $row['file_path'];   // reuse existing name
            $uploadPath  = BMDATA_DIR . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                redirect_bm('Failed to replace file.', 'danger');
            }

            $stmt = $pdo->prepare("UPDATE pwd_benchmark SET file_path = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newFileName, $id]);
        }

        redirect_bm('Benchmark data updated successfully.', 'success');
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT file_path FROM pwd_benchmark WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $filePath = BMDATA_DIR . $row['file_path'];
            if (file_exists($filePath)) unlink($filePath);
            $pdo->prepare("DELETE FROM pwd_benchmark WHERE id = ?")->execute([$id]);
            redirect_bm('Benchmark data deleted successfully.', 'success');
        }
        redirect_bm('Record not found.', 'danger');
        break;

    default:
        redirect_bm('Invalid action.', 'danger');
}

function redirect_bm($msg, $type) {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    $role = current_role();
    $base = base_url();
    $page = ($role === 'admin') ? '/admin/manage_bmdata.php' : '/subadmin/manage_bmdata.php';
    header("Location: $base$page");
    exit;
}