<?php
// ============================================================
// Session verification & role-based access control
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user() {
    if (empty($_SESSION['dms_user'])) return null;
    return $_SESSION['dms_user'];
}

function current_role() {
    $u = current_user();
    return $u ? $u['role'] : null;
}

/**
 * Correct path to the login page (index.php in project root)
 * From admin/ user/ subadmin/ actions/  →  ../index.php
 * From root                            →  index.php
 */
function login_url($query = '') {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

    // Are we inside a sub-folder?
    $inSubdir = preg_match('#/(admin|subadmin|user|actions)/#', $script);

    $url = $inSubdir ? '../index.php' : 'index.php';

    if ($query !== '') {
        $url .= '?' . ltrim($query, '?&');
    }
    return $url;
}

function require_login() {
    if (!current_user()) {
        header('Location: ' . login_url());
        exit;
    }
}

function require_role($roles) {
    require_login();
    if (!in_array(current_role(), (array)$roles, true)) {
        http_response_code(403);
        die('Access denied. You do not have permission to view this page.');
    }
}

function url_path($path) {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $base . '/' . ltrim($path, '/');
}

function base_url() {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    // If inside admin / subadmin / user / actions → go up one level
    if (preg_match('#/(admin|subadmin|user|actions)$#', $scriptDir)) {
        return rtrim(dirname($scriptDir), '/');
    }
    return rtrim($scriptDir, '/') ?: '';
}

// -------------------------------------------------------
// Session timeout – 15 minutes
// -------------------------------------------------------
$timeout = 15 * 60; // 15 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {

    // Destroy session completely
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();

    // Redirect to the real login page
    header('Location: ' . login_url('timeout=1'));
    exit;
}

// Update last activity only when still logged in
if (current_user()) {
    $_SESSION['last_activity'] = time();
}