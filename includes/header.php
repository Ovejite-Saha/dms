<?php
// ============================================================
// Global header - HTML head, navbar, sidebar
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/file_helper.php';

$base = base_url();
$cu = current_user();
$crole = current_role();

// Determine active nav item
$currentFile = basename($_SERVER['SCRIPT_NAME']);
function isActive($file) {
    global $currentFile;
    return $currentFile === $file ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - DMS' : 'DMS - PWD Survey Division' ?></title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/img/index.png" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/fontawesome/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark dms-navbar fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $base ?>/index.php">
            <i class="fa-solid fa-folder-tree"></i>
            <span>DMS - PWD Survey Division</span>
            <small class="badge bg-light text-dark ms-2 text-capitalize"><?= $crole ?? '' ?></small>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <?php if ($crole === 'admin'): ?>
                <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="<?= $base ?>/admin/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_admins.php') ?>" href="<?= $base ?>/admin/manage_admins.php"><i class="fa-solid fa-user-shield"></i> Admins</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_subadmins.php') ?>" href="<?= $base ?>/admin/manage_subadmins.php"><i class="fa-solid fa-user-gear"></i> Subadmins</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_users.php') ?>" href="<?= $base ?>/admin/manage_users.php"><i class="fa-solid fa-users"></i> Users</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_classes.php') ?>" href="<?= $base ?>/admin/manage_classes.php"><i class="fa-solid fa-sitemap"></i> Hierarchy</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_bmdata.php') ?>" href="<?= $base ?>/admin/manage_bmdata.php"><i class="fa-solid fa-landmark"></i> PWD Benchmark</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_documents.php') ?>" href="<?= $base ?>/admin/manage_documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a></li>
                <?php elseif ($crole === 'subadmin'): ?>
                <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="<?= $base ?>/subadmin/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('upload_document.php') ?>" href="<?= $base ?>/subadmin/upload_document.php"><i class="fa-solid fa-upload"></i> Upload</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_documents.php') ?>" href="<?= $base ?>/subadmin/manage_documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('manage_bmdata.php') ?>" href="<?= $base ?>/subadmin/manage_bmdata.php"><i class="fa-solid fa-landmark"></i> PWD Benchmark</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('profile.php') ?>" href="<?= $base ?>/subadmin/profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                <?php elseif ($crole === 'user'): ?>
                <li class="nav-item"><a class="nav-link <?= isActive('dashboard.php') ?>" href="<?= $base ?>/user/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('bmdata.php') ?>" href="<?= $base ?>/user/bmdata.php"><i class="fa-solid fa-landmark"></i> PWD Benchmark</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('profile.php') ?>" href="<?= $base ?>/user/profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($cu): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                        <i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($cu['full_name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($crole === 'admin'): ?>
                        <li><a class="dropdown-item" href="<?= $base ?>/admin/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                        <?php elseif ($crole === 'subadmin'): ?>
                        <li><a class="dropdown-item" href="<?= $base ?>/subadmin/profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                        <?php elseif ($crole === 'user'): ?>
                        <li><a class="dropdown-item" href="<?= $base ?>/user/profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= $base ?>/actions/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="dms-body">
