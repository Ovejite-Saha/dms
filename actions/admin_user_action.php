<?php
// ============================================================
// Admin User Action - Create / Edit / Delete admins, subadmins & users
// User access_type: all | ministrie | division | district | upazila | project
// access_ids: JSON array of selected IDs
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$role   = $_POST['role'] ?? $_GET['role'] ?? 'user';

function manage_page_for_role($role) {
    if ($role === 'admin')    return '/admin/manage_admins.php';
    if ($role === 'subadmin') return '/admin/manage_subadmins.php';
    return '/admin/manage_users.php';
}

function redirect_with_msg($msg, $type, $role = 'user') {
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    header('Location: ' . base_url() . manage_page_for_role($role));
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
        $username    = trim($_POST['username'] ?? '');
        $password    = $_POST['password'] ?? '';
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $access_type = $_POST['access_type'] ?? 'all';
        $access_ids  = parse_access_ids();

        if ($username === '' || $password === '' || $full_name === '' || $email === '') {
            redirect_with_msg('All required fields must be filled.', 'danger', $role);
        }

        $allowed_access = ['all', 'ministrie', 'division', 'district', 'upazila', 'project'];
        if (!in_array($access_type, $allowed_access, true)) $access_type = 'all';
        if ($access_type === 'all') $access_ids = null;

        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE username=? OR email=?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'admin');

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO admins (username,password,full_name,email,phone) VALUES (?,?,?,?,?)")
                ->execute([$username, $hash, $full_name, $email, $phone]);
            redirect_with_msg('Admin created successfully.', 'success', 'admin');
        }

        if ($role === 'subadmin') {
            $stmt = $pdo->prepare("SELECT id FROM subadmins WHERE username=? OR email=?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'subadmin');

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO subadmins (username,password,full_name,email,phone) VALUES (?,?,?,?,?)")
                ->execute([$username, $hash, $full_name, $email, $phone]);
            redirect_with_msg('Subadmin created successfully.', 'success', 'subadmin');
        }

        // user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'user');

        if ($access_type !== 'all' && empty($access_ids)) {
            redirect_with_msg('Please select at least one item for the chosen access level.', 'danger', 'user');
        }

        $hash     = password_hash($password, PASSWORD_DEFAULT);
        $ids_json = $access_ids ? json_encode($access_ids) : null;
        $pdo->prepare("INSERT INTO users (username,password,full_name,email,phone,access_type,access_ids) VALUES (?,?,?,?,?,?,?)")
            ->execute([$username, $hash, $full_name, $email, $phone, $access_type, $ids_json]);
        redirect_with_msg('User created successfully.', 'success', 'user');
        break;

    case 'edit':
        $id          = (int)($_POST['id'] ?? 0);
        $username    = trim($_POST['username'] ?? '');
        $password    = $_POST['password'] ?? $_POST['new_password'] ?? '';
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $access_type = $_POST['access_type'] ?? 'all';
        $access_ids  = parse_access_ids();

        if (!$id || $full_name === '' || $email === '') {
            redirect_with_msg('Required fields missing.', 'danger', $role);
        }

        // Username is required for admin/user edit forms; keep existing if blank (e.g. older subadmin form)
        if ($username === '' && in_array($role, ['admin', 'subadmin'], true)) {
            $table = $role === 'admin' ? 'admins' : 'subadmins';
            $stmt = $pdo->prepare("SELECT username FROM {$table} WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            $username = $row['username'] ?? '';
        }

        if ($username === '') {
            redirect_with_msg('Username is required.', 'danger', $role);
        }

        $allowed_access = ['all', 'ministrie', 'division', 'district', 'upazila', 'project'];
        if (!in_array($access_type, $allowed_access, true)) $access_type = 'all';
        if ($access_type === 'all') $access_ids = null;

        if ($role === 'admin') {
            // Unique check excluding current record
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE (username=? OR email=?) AND id<>?");
            $stmt->execute([$username, $email, $id]);
            if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'admin');

            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE admins SET username=?,password=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $hash, $full_name, $email, $phone, $id]);
            } else {
                $pdo->prepare("UPDATE admins SET username=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $full_name, $email, $phone, $id]);
            }

            // Keep session in sync if editing self
            $cu = current_user();
            if ($cu && ($cu['role'] ?? '') === 'admin' && (int)$cu['id'] === $id) {
                $_SESSION['dms_user']['username']  = $username;
                $_SESSION['dms_user']['full_name'] = $full_name;
                $_SESSION['dms_user']['email']     = $email;
                $_SESSION['dms_user']['phone']     = $phone;
            }

            redirect_with_msg('Admin updated.', 'success', 'admin');
        }

        if ($role === 'subadmin') {
            $stmt = $pdo->prepare("SELECT id FROM subadmins WHERE (username=? OR email=?) AND id<>?");
            $stmt->execute([$username, $email, $id]);
            if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'subadmin');

            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE subadmins SET username=?,password=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $hash, $full_name, $email, $phone, $id]);
            } else {
                $pdo->prepare("UPDATE subadmins SET username=?,full_name=?,email=?,phone=? WHERE id=?")
                    ->execute([$username, $full_name, $email, $phone, $id]);
            }
            redirect_with_msg('Subadmin updated.', 'success', 'subadmin');
        }

        // user
        if ($access_type !== 'all' && empty($access_ids)) {
            redirect_with_msg('Please select at least one item for the chosen access level.', 'danger', 'user');
        }
        $ids_json = $access_ids ? json_encode($access_ids) : null;

        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username=? OR email=?) AND id<>?");
        $stmt->execute([$username, $email, $id]);
        if ($stmt->fetch()) redirect_with_msg('Username or email already exists.', 'danger', 'user');

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET username=?,password=?,full_name=?,email=?,phone=?,access_type=?,access_ids=? WHERE id=?")
                ->execute([$username, $hash, $full_name, $email, $phone, $access_type, $ids_json, $id]);
        } else {
            $pdo->prepare("UPDATE users SET username=?,full_name=?,email=?,phone=?,access_type=?,access_ids=? WHERE id=?")
                ->execute([$username, $full_name, $email, $phone, $access_type, $ids_json, $id]);
        }
        redirect_with_msg('User updated.', 'success', 'user');
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            redirect_with_msg('Invalid ID.', 'danger', $role);
        }

        if ($role === 'admin') {
            $cu = current_user();
            if ($cu && ($cu['role'] ?? '') === 'admin' && (int)$cu['id'] === $id) {
                redirect_with_msg('You cannot delete your own admin account.', 'danger', 'admin');
            }

            $count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
            if ($count <= 1) {
                redirect_with_msg('Cannot delete the last remaining admin.', 'danger', 'admin');
            }

            $pdo->prepare("DELETE FROM admins WHERE id=?")->execute([$id]);
            redirect_with_msg('Admin deleted.', 'success', 'admin');
        }

        if ($role === 'subadmin') {
            $pdo->prepare("DELETE FROM subadmins WHERE id=?")->execute([$id]);
            redirect_with_msg('Subadmin deleted.', 'success', 'subadmin');
        }

        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        redirect_with_msg('User deleted.', 'success', 'user');
        break;

    default:
        redirect_with_msg('Invalid action.', 'danger', $role);
}