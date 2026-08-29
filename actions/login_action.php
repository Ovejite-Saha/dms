<?php
// ============================================================
// Login Action - handles popup login for all 3 roles
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter username and password.']);
    exit;
}

$pdo = db();
$role = null;
$user = null;

$stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if ($user) $role = 'admin';

if (!$user) {
    $stmt = $pdo->prepare('SELECT * FROM subadmins WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user) $role = 'subadmin';
}

if (!$user) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user) $role = 'user';
}

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

$access_ids = $user['access_ids'] ?? null;
if (is_string($access_ids)) {
    $decoded = json_decode($access_ids, true);
    $access_ids = is_array($decoded) ? $decoded : null;
}

$_SESSION['dms_user'] = [
    'id'          => $user['id'],
    'username'    => $user['username'],
    'full_name'   => $user['full_name'],
    'email'       => $user['email'],
    'phone'       => $user['phone'] ?? '',
    'role'        => $role,
    'access_type' => $user['access_type'] ?? 'all',
    'access_ids'  => $access_ids,
];

$base = base_url();
echo json_encode([
    'success'  => true,
    'redirect' => $base . '/' . $role . '/dashboard.php',
]);
