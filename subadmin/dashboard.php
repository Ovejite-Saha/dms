<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/doc_access.php';
require_role('subadmin');

$pdo = db();
$cu = current_user();

$myDocs = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE uploaded_by = ?");
$myDocs->execute([$cu['username']]);
$myDocCount = $myDocs->fetchColumn();

$totalDocs = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

$stmt = $pdo->prepare(documents_base_query() . " WHERE d.uploaded_by = ? ORDER BY d.created_at DESC LIMIT 5");
$stmt->execute([$cu['username']]);
$recentDocs = $stmt->fetchAll();

$flash = $_SESSION['dms_flash'] ?? null;
unset($_SESSION['dms_flash']);

function type_class($ext) {
    $map = ['pdf'=>'pdf','doc'=>'word','docx'=>'word','xls'=>'excel','xlsx'=>'excel','ppt'=>'ppt','pptx'=>'ppt','jpg'=>'img','jpeg'=>'img','png'=>'img','gif'=>'img','zip'=>'zip','rar'=>'zip','txt'=>'txt','shp'=>'gis','kml'=>'gis','geojson'=>'gis','tif'=>'gis'];
    return $map[strtolower($ext ?? '')] ?? 'txt';
}
?>
<div class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2><i class="fa-solid fa-gauge"></i> Subadmin Dashboard</h2>
        <span class="text-muted">Welcome, <?= htmlspecialchars($cu['full_name']) ?></span>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" data-auto-dismiss>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i class="fa-solid fa-file-arrow-up"></i></div>
                <div class="stat-value"><?= $myDocCount ?></div>
                <div class="stat-label">My Uploads</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                <div class="stat-value"><?= $totalDocs ?></div>
                <div class="stat-label">Total Documents</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i class="fa-solid fa-folder-tree"></i></div>
                <div class="stat-value"><?= $projectCount ?></div>
                <div class="stat-label">Projects</div>
            </div>
        </div>
    </div>

    <div class="dms-card">
        <h5 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> My Recent Uploads</h5>
        <?php if (empty($recentDocs)): ?>
            <div class="empty-state"><i class="fa-solid fa-folder-open"></i><h4>No uploads yet</h4><p>Start by uploading a document.</p></div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Document</th><th>Project</th><th>Division</th><th>Upazila</th><th>Date</th><th>Type</th></tr></thead>
                <tbody>
                <?php foreach ($recentDocs as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['document_name']) ?></td>
                        <td><?= htmlspecialchars($d['project_name']) ?></td>
                        <td><?= htmlspecialchars($d['division_name']) ?></td>
                        <td><?= htmlspecialchars($d['upazila_name']) ?></td>
                        <td><?= htmlspecialchars($d['doc_date']) ?></td>
                        <td><span class="doc-type-badge type-<?= type_class($d['file_type']) ?>"><?= strtoupper($d['file_type'] ?? 'FILE') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="d-flex gap-2 mt-3">
        <a href="upload_document.php" class="btn btn-primary"><i class="fa-solid fa-upload"></i> Upload New Document</a>
        <a href="manage_documents.php" class="btn btn-outline-primary"><i class="fa-solid fa-file-lines"></i> Manage Documents</a>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
