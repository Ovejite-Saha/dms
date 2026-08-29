<?php
// ============================================================
// Profile Action - user/subadmin update own password & details
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_login();

$pdo = db();
$cu = current_user();
$role = $cu['role'];

// Only user and subadmin can self-edit (admin manages admins separately if needed)
if ($role === 'admin') {
    header('Location: ' . base_url() . '/admin/dashboard.php');
    exit;
}

$table = ($role === 'subadmin') ? 'subadmins' : 'users';
$redirect = ($role === 'subadmin') ? '/subadmin/profile.php' : '/user/profile.php';

$action = $_POST['action'] ?? '';

switch ($action) {

    // ---- Update profile info ----
    case 'update_info':
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');

        if ($full_name === '' || $email === '') {
            redirect_profile('Full name and email are required.', 'danger');
        }

        $stmt = $pdo->prepare("UPDATE $table SET full_name=?, email=?, phone=? WHERE id=?");
        $stmt->execute([$full_name, $email, $phone, $cu['id']]);

        // Update session
        $_SESSION['dms_user']['full_name'] = $full_name;
        $_SESSION['dms_user']['email']     = $email;
        $_SESSION['dms_user']['phone']     = $phone;

        redirect_profile('Profile updated successfully.', 'success');
        break;

    // ---- Change password ----
    case 'change_password':
        $old_pass = $_POST['old_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($old_pass === '' || $new_pass === '' || $confirm === '') {
            redirect_profile('All password fields are required.', 'danger');
        }

        if ($new_pass !== $confirm) {
            redirect_profile('New passwords do not match.', 'danger');
        }

        // Verify old password
        $stmt = $pdo->prepare("SELECT password FROM $table WHERE id = ?");
        $stmt->execute([$cu['id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($old_pass, $row['password'])) {
            redirect_profile('Current password is incorrect.', 'danger');
        }

        $hash = password_hash($new_pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE $table SET password=? WHERE id=?");
        $stmt->execute([$hash, $cu['id']]);

        redirect_profile('Password changed successfully.', 'success');
        break;

    default:
        redirect_profile('Invalid action.', 'danger');
}

function redirect_profile($msg, $type) {
    global $redirect;
    $_SESSION['dms_flash'] = ['msg' => $msg, 'type' => $type];
    header('Location: ' . base_url() . $redirect);
    exit;
}
