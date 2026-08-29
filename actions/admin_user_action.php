<?php
// ============================================================
// Admin User Action - Create / Edit / Delete users & subadmins
// User access_type: all | ministrie | division | district | upazila | project
// access_ids: JSON array of selected IDs
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function redirect_with_msg($msg, $type) {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    header('Location: ' . base_url() . '/admin/manage_users.php');
    exit;
}

function parse_access_ids() {
    $ids = $_POST['access_ids'] ?? [];
    if (!is_array($ids)) $ids = [];
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    return $ids ?: null;
}

switch ($action) {

    case 'create':
        $role        = $_POST['role'] ?? 'user';
        $username    = trim($_POST['username'] ?? '');
        $password    = $_POST['password'] ?? '';
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $access_type = $_POST['access_type'] ?? 'all';
        $access_ids  = parse_access_ids();

        if ($username === '' || $password === '' || $full_name === '' || $email === '') {
            redirect_with_msg('All required fields must be filled.', 'danger');
        }

        $allowed_access = ['all', 'ministrie', 'division', 'district', 'upazila', 'project'];
        if (!in_array($access_type, $allowed_access, true)) $access_type = 'all';
        if ($access_type === 'all') $access_ids = null;

        if ($role === 'subadmin') {
            $stmt = $pdo->prepare("SELECT id FROM subadmins WHERE username=? OR email=?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger');

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO subadmins (username,password,full_name,email,phone) VALUES (?,?,?,?,?)")
                ->execute([$username, $hash, $full_name, $email, $phone]);
            redirect_with_msg('Subadmin created successfully.', 'success');
        }

        // user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger');

        if ($access_type !== 'all' && empty($access_ids)) {
            redirect_with_msg('Please select at least one item for the chosen access level.', 'danger');
        }

        $hash     = password_hash($password, PASSWORD_DEFAULT);
        $ids_json = $access_ids ? json_encode($access_ids) : null;
        $pdo->prepare("INSERT INTO users (username,password,full_name,email,phone,access_type,access_ids) VALUES (?,?,?,?,?,?,?)")
            ->execute([$username, $hash, $full_name, $email, $phone, $access_type, $ids_json]);
        redirect_with_msg('User created successfully.', 'success');
        break;

    case 'edit':
        $id          = (int)($_POST['id'] ?? 0);
        $role        = $_POST['role'] ?? 'user';
        $username    = trim($_POST['username'] ?? '');
        $password    = $_POST['password'] ?? '';
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $access_type = $_POST['access_type'] ?? 'all';
        $access_ids  = parse_access_ids();

        if (!$id || $username === '' || $full_name === '' || $email === '') {
            redirect_with_msg('Required fields missing.', 'danger');
        }

        $allowed_access = ['all', 'ministrie', 'division', 'district', 'upazila', 'project'];
        if (!in_array($access_type, $allowed_access, true)) $access_type = 'all';
        if ($access_type === 'all') $access_ids = null;

        if ($role === 'subadmin') {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE subadmins SET username=?,password=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $hash, $full_name, $email, $phone, $id]);
            } else {
                $pdo->prepare("UPDATE subadmins SET username=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $full_name, $email, $phone, $id]);
            }
            redirect_with_msg('Subadmin updated.', 'success');
        }

        // user
        if ($access_type !== 'all' && empty($access_ids)) {
            redirect_with_msg('Please select at least one item for the chosen access level.', 'danger');
        }
        $ids_json = $access_ids ? json_encode($access_ids) : null;

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET username=?,password=?,full_name=?,email=?,phone=?,access_type=?,access_ids=? WHERE id=?")
                ->execute([$username, $hash, $full_name, $email, $phone, $access_type, $ids_json, $id]);
        } else {
            $pdo->prepare("UPDATE users SET username=?,full_name=?,email=?,phone=?,access_type=?,access_ids=? WHERE id=?")
                ->execute([$username, $full_name, $email, $phone, $access_type, $ids_json, $id]);
        }
        redirect_with_msg('User updated.', 'success');
        break;

    case 'delete':
        $id   = (int)($_GET['id'] ?? 0);
        $role = $_GET['role'] ?? 'user';
        if ($role === 'subadmin') {
            $pdo->prepare("DELETE FROM subadmins WHERE id=?")->execute([$id]);
            redirect_with_msg('Subadmin deleted.', 'success');
        }
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        redirect_with_msg('User deleted.', 'success');
        break;

    default:
        redirect_with_msg('Invalid action.', 'danger');
}