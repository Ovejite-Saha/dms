<?php
// ============================================================
// Logout - destroy session and redirect
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$_SESSION = [];
session_destroy();

header('Location: ' . base_url() . '/index.php');
exit;
