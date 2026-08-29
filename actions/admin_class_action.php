<?php
// ============================================================
// Admin Hierarchy Action
// Manage: Ministries, Divisions, Districts, Upazilas, Projects
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$type   = $_POST['type']   ?? $_GET['type']   ?? 'ministry';

function redirect_hier($msg, $type_flash, $tab = 'ministry') {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type_flash];
    header('Location: ' . base_url() . '/admin/manage_classes.php?tab=' . urlencode($tab));
    exit;
}

switch ($action) {

    case 'create':
        $name = trim($_POST['name'] ?? '');
        if ($name === '') redirect_hier('Name is required.', 'danger', $type);

        if ($type === 'ministry') {
            $stmt = $pdo->prepare("SELECT id FROM ministries WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) redirect_hier('Ministry already exists.', 'danger', 'ministry');
            $pdo->prepare("INSERT INTO ministries (name) VALUES (?)")->execute([$name]);
            redirect_hier('Ministry created.', 'success', 'ministry');
        }
        if ($type === 'division') {
            $stmt = $pdo->prepare("SELECT id FROM divisions WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) redirect_hier('Division already exists.', 'danger', 'division');
            $pdo->prepare("INSERT INTO divisions (name) VALUES (?)")->execute([$name]);
            redirect_hier('Division created.', 'success', 'division');
        }
        if ($type === 'district') {
            $division_id = (int)($_POST['parent_id'] ?? 0);
            if (!$division_id) redirect_hier('Division is required.', 'danger', 'district');
            $stmt = $pdo->prepare("SELECT id FROM districts WHERE name = ? AND division_id = ?");
            $stmt->execute([$name, $division_id]);
            if ($stmt->fetch()) redirect_hier('District already exists in this Division.', 'danger', 'district');
            $pdo->prepare("INSERT INTO districts (division_id, name) VALUES (?,?)")->execute([$division_id, $name]);
            redirect_hier('District created.', 'success', 'district');
        }
        if ($type === 'upazila') {
            $district_id = (int)($_POST['parent_id'] ?? 0);
            if (!$district_id) redirect_hier('District is required.', 'danger', 'upazila');
            $stmt = $pdo->prepare("SELECT id FROM upazilas WHERE name = ? AND district_id = ?");
            $stmt->execute([$name, $district_id]);
            if ($stmt->fetch()) redirect_hier('Upazila already exists in this District.', 'danger', 'upazila');
            $pdo->prepare("INSERT INTO upazilas (district_id, name) VALUES (?,?)")->execute([$district_id, $name]);
            redirect_hier('Upazila created.', 'success', 'upazila');
        }
        if ($type === 'project') {
            $stmt = $pdo->prepare("SELECT id FROM projects WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) redirect_hier('Project already exists.', 'danger', 'project');
            $pdo->prepare("INSERT INTO projects (name) VALUES (?)")->execute([$name]);
            redirect_hier('Project created.', 'success', 'project');
        }
        if ($type === 'document_type') {
            $stmt = $pdo->prepare("SELECT id FROM document_types WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) redirect_hier('Document type already exists.', 'danger', 'document_type');
            $pdo->prepare("INSERT INTO document_types (name) VALUES (?)")->execute([$name]);
            redirect_hier('Document type created.', 'success', 'document_type');
        }
        redirect_hier('Invalid type.', 'danger', 'ministry');
        break;

    case 'edit':
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || !$id) redirect_hier('Name and ID are required.', 'danger', $type);

        if ($type === 'ministry') {
            $pdo->prepare("UPDATE ministries SET name=? WHERE id=?")->execute([$name, $id]);
            redirect_hier('Ministry updated.', 'success', 'ministry');
        }
        if ($type === 'division') {
            $pdo->prepare("UPDATE divisions SET name=? WHERE id=?")->execute([$name, $id]);
            redirect_hier('Division updated.', 'success', 'division');
        }
        if ($type === 'district') {
            $division_id = (int)($_POST['parent_id'] ?? 0);
            if ($division_id) {
                $pdo->prepare("UPDATE districts SET name=?, division_id=? WHERE id=?")->execute([$name, $division_id, $id]);
            } else {
                $pdo->prepare("UPDATE districts SET name=? WHERE id=?")->execute([$name, $id]);
            }
            redirect_hier('District updated.', 'success', 'district');
        }
        if ($type === 'upazila') {
            $district_id = (int)($_POST['parent_id'] ?? 0);
            if ($district_id) {
                $pdo->prepare("UPDATE upazilas SET name=?, district_id=? WHERE id=?")->execute([$name, $district_id, $id]);
            } else {
                $pdo->prepare("UPDATE upazilas SET name=? WHERE id=?")->execute([$name, $id]);
            }
            redirect_hier('Upazila updated.', 'success', 'upazila');
        }
        if ($type === 'project') {
            $pdo->prepare("UPDATE projects SET name=? WHERE id=?")->execute([$name, $id]);
            redirect_hier('Project updated.', 'success', 'project');
        }
        if ($type === 'document_type') {
            $pdo->prepare("UPDATE document_types SET name=? WHERE id=?")->execute([$name, $id]);
            redirect_hier('Document type updated.', 'success', 'document_type');
        }
        redirect_hier('Invalid type.', 'danger', 'ministry');
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect_hier('Invalid ID.', 'danger', $type);
        try {
            if ($type === 'ministry')  { $pdo->prepare("DELETE FROM ministries WHERE id=?")->execute([$id]); redirect_hier('Ministry deleted.', 'success', 'ministry'); }
            if ($type === 'division')  { $pdo->prepare("DELETE FROM divisions WHERE id=?")->execute([$id]); redirect_hier('Division deleted.', 'success', 'division'); }
            if ($type === 'district')  { $pdo->prepare("DELETE FROM districts WHERE id=?")->execute([$id]); redirect_hier('District deleted.', 'success', 'district'); }
            if ($type === 'upazila')   { $pdo->prepare("DELETE FROM upazilas WHERE id=?")->execute([$id]); redirect_hier('Upazila deleted.', 'success', 'upazila'); }
            if ($type === 'project')        { $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]); redirect_hier('Project deleted.', 'success', 'project'); }
            if ($type === 'document_type')  { $pdo->prepare("DELETE FROM document_types WHERE id=?")->execute([$id]); redirect_hier('Document type deleted.', 'success', 'document_type'); }
        } catch (PDOException $e) {
            redirect_hier('Cannot delete: item is in use by documents or child records.', 'danger', $type);
        }
        redirect_hier('Invalid type.', 'danger', 'ministry');
        break;

    default:
        redirect_hier('Invalid action.', 'danger', 'ministry');
}
