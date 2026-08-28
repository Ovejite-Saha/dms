<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

// If already logged in, redirect to dashboard
$cu = current_user();
if ($cu) {
    $role = $cu['role'];
    header("Location: $role/dashboard.php");
    exit;
}

$base = base_url();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMS - PWD Survey Division</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/img/index.png" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/css/all.min.css">
</head>
<body>

<!-- ===== Hero Section ===== -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="hero-badge"><i class="fa-solid fa-shield-halved"></i> Secure & Organized</span>
                        <h1 class="hero-title">Wellcome To DMS</h1>
                        <p class="hero-subtitle">
                            A Digital Document Management System Of <strong>PWD Survey Division.</strong>
                        </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2">
                        <button class="btn hero-cta" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fa-solid fa-right-to-bracket"></i> Login
                        </button>
                        <a href="#features" class="btn hero-outline">What's New</a>
                    </div>
            </div>

            <!-- Right Side - Logo + Company Info -->
            <div class="col-lg-6 text-center">
                <div class="feature-card border-start border-4 border-dark shadow-sm">
                    <!-- Circular Logo -->
                    <img src="<?= $base ?>/assets/img/logo.png" 
                         alt="Company Logo" 
                         class="pwd-logo mx-auto d-block mb-4 rounded-circle shadow"
                         style="width: 180px; height: 180px; object-fit: cover; border: 5px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    
                    <h2 class="fw-bold mb-2">PWD SURVEY DIVISION</h2>
                    <hr class="my-4">
                    <p class="lead"><strong>--- SURVEYING . GEOMATICS . MAPPING ---</strong></p>
                    <p class="mb-4">DMS - Document Management System</p>
                    
                    <div class="mt-5">
                        <img src="<?= $base ?>/assets/img/doc.png" 
                             alt="Document Illustration" 
                             class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>  
        </div>
    </div>
</section>

<!-- ===== Features Section ===== -->
<section id="features" class="py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color:var(--dms-primary);">What's In Our DMS?</h2>
            <p class="text-muted">Upload and store an important document, and download it for future use with many options.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="dms-card text-center h-100 border-start border-top border-bottom border-end border-4 border-primary shadow-sm">
                    <div class="stat-card stat-blue mb-3" style="display:inline-block;width:70px;height:70px;padding:1rem;">
                        <i class="fa-solid fa-layer-group fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Store Files</h5>
                    <p class="text-muted">Store them there so they are easily accessible for future use.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dms-card text-center h-100 border-start border-top border-bottom border-end border-4 border-info shadow-sm">
                    <div class="stat-card stat-info mb-3" style="display:inline-block;width:70px;height:70px;padding:1rem;">
                        <i class="fa-solid fa-layer-group fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Organized Login</h5>
                    <p class="text-muted">Organized users for role-based permission to control login.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dms-card text-center h-100 border-start border-top border-bottom border-end border-4 border-success shadow-sm">
                    <div class="stat-card stat-green mb-3" style="display:inline-block;width:70px;height:70px;padding:1rem;">
                        <i class="fa-solid fa-search fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Search Files</h5>
                    <p class="text-muted">Can search for necessary files by document names or project names.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dms-card text-center h-100 border-start border-top border-bottom border-end border-4 border-warning shadow-sm">
                    <div class="stat-card stat-orange mb-3" style="display:inline-block;width:70px;height:70px;padding:1rem;">
                        <i class="fa-solid fa-file-arrow-down fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Smart Download</h5>
                    <p class="text-muted">Important files can be easily downloaded, which are named as the project name_document name.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Footer ===== -->
<footer class="dms-footer text-center">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> <strong>PWD Survey Division.</strong> All rights reserved | Powered By Ⓟ <a target="_blank" href="https://www.youtube.com/c/FireONBD">FireON</a></p>
    </div>
</footer>

<!-- ===== Login Modal ===== -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content login-modal-content">
            <div class="login-modal-header">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;top:1rem;right:1rem;"></button>
                <i class="fa-solid fa-lock"></i>
                <h4>Sign In</h4>
                <p class="mb-0" style="font-size:.85rem;opacity:.8;">Enter your credentials to continue</p>
            </div>
            <div class="login-modal-body">
                <div class="alert alert-danger login-alert" id="loginAlert" role="alert"></div>
                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-user"></i> Username</label>
                        <input type="text" class="form-control" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-key"></i> Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="passwordField" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePass">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2" id="loginBtn">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= $base ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>/assets/js/main.js"></script>
<script>
// Toggle password visibility
document.getElementById('togglePass').addEventListener('click', function() {
    const field = document.getElementById('passwordField');
    const icon = this.querySelector('i');
    if (field.type === 'password') { field.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
    else { field.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
});
</script>
</body>
</html>
