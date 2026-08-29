<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/doc_access.php';
require_role('admin');

$pdo = db();

$adminCount    = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
$subadminCount = $pdo->query("SELECT COUNT(*) FROM subadmins")->fetchColumn();
$userCount     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$docCount      = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$ministryCount = $pdo->query("SELECT COUNT(*) FROM ministries")->fetchColumn();
$divisionCount = $pdo->query("SELECT COUNT(*) FROM divisions")->fetchColumn();
$districtsCount = $pdo->query("SELECT COUNT(*) FROM districts")->fetchColumn();
$upazilasCount = $pdo->query("SELECT COUNT(*) FROM upazilas")->fetchColumn();
$projectCount  = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

$recentDocs = $pdo->query(documents_base_query() . " ORDER BY d.created_at DESC LIMIT 5")->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);

function type_class($ext) {
    $map = ['pdf'=>'pdf','doc'=>'word','docx'=>'word','xls'=>'excel','xlsx'=>'excel','ppt'=>'ppt','pptx'=>'ppt','jpg'=>'img','jpeg'=>'img','png'=>'img','gif'=>'img','zip'=>'zip','rar'=>'zip','txt'=>'txt','shp'=>'gis','kml'=>'gis','geojson'=>'gis','tif'=>'gis'];
    return $map[strtolower($ext ?? '')] ?? 'txt';
}
?>
<div class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="fa-solid fa-gauge"></i> Admin Dashboard</h2>
        <span class="text-muted">Welcome, <?= htmlspecialchars($cu['full_name']) ?></span>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i class="fa-solid fa-user-tie"></i></div>
                <div class="stat-value"><?= $adminCount ?></div>
                <div class="stat-label">Admins</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i class="fa-solid fa-user-gear"></i></div>
                <div class="stat-value"><?= $subadminCount ?></div>
                <div class="stat-label">Subadmins</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-value"><?= $userCount ?></div>
                <div class="stat-label">Users</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card stat-red">
                <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                <div class="stat-value"><?= $docCount ?></div>
                <div class="stat-label">Documents</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dms-card">
                <h5 class="card-title"><i class="fa-solid fa-sitemap"></i> Hierarchy</h5>
                <p class="mb-1">Ministries: <strong><?= $ministryCount ?></strong></p>
                <p class="mb-1">Divisions: <strong><?= $divisionCount ?></strong></p>
                <p class="mb-1">Districts: <strong><?= $districtsCount ?></strong></p>
                <p class="mb-1">Upazilas: <strong><?= $upazilasCount ?></strong></p>
                <p class="mb-2">Projects: <strong><?= $projectCount ?></strong></p>
                <a href="manage_classes.php" class="btn btn-sm btn-primary">Manage Hierarchy</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="dms-card">
                <h5 class="card-title"><i class="fa-solid fa-chart-simple"></i> Quick Actions</h5>
                <h3></h3>
                <div class="d-flex flex-wrap gap-4">
                    <a href="manage_users.php" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-user-plus"></i> Add User</a>
                    <a href="manage_subadmins.php" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-user-plus"></i> Add Subadmin</a>
                    <a href="manage_classes.php" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-plus"></i> Hierarchy</a>
                    <a href="manage_documents.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-file-lines"></i> Documents</a>
                    <a href="manage_bmdata.php" class="btn btn-outline-dark btn-sm"><i class="fa-solid fa-landmark"></i> PWD Benchmark</a>
                    <a href="<?= base_url() ?>/subadmin/upload_document.php" class="btn btn-outline-info btn-sm"><i class="fa-solid fa-upload"></i> Upload</a>
                </div>
            </div>
        </div>
    </div>

    <div class="dms-card">
        <h5 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Documents</h5>
        <?php if (empty($recentDocs)): ?>
            <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No documents yet</h4></div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Document</th><th>Project</th><th>Division</th><th>Upazila</th><th>Date</th><th>Type</th><th>By</th></tr></thead>
                <tbody>
                <?php foreach ($recentDocs as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['document_name']) ?></td>
                        <td><?= htmlspecialchars($d['project_name']) ?></td>
                        <td><?= htmlspecialchars($d['division_name']) ?></td>
                        <td><?= htmlspecialchars($d['upazila_name']) ?></td>
                        <td><?= htmlspecialchars($d['doc_date']) ?></td>
                        <td><span class="doc-type-badge type-<?= type_class($d['file_type']) ?>"><?= strtoupper($d['file_type'] ?? 'FILE') ?></span></td>
                        <td><?= htmlspecialchars($d['uploaded_by']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
