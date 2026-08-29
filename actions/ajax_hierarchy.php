<?php
// ============================================================
// AJAX: Cascading hierarchy data
// ?action=districts&division_id=X
// ?action=upazilas&district_id=X
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_login();
header('Content-Type: application/json');

$pdo    = db();
$action = $_GET['action'] ?? '';

if ($action === 'districts') {
    $division_id = (int)($_GET['division_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT id, name FROM districts WHERE division_id = ? ORDER BY name");
    $stmt->execute([$division_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'upazilas') {
    $district_id = (int)($_GET['district_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT id, name FROM upazilas WHERE district_id = ? ORDER BY name");
    $stmt->execute([$district_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'all') {
    // Return full tree for advanced UIs if needed
    $ministries = $pdo->query("SELECT id, name FROM ministries ORDER BY name")->fetchAll();
    $divisions  = $pdo->query("SELECT id, name FROM divisions ORDER BY name")->fetchAll();
    $projects   = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll();
    echo json_encode([
        'ministries' => $ministries,
        'divisions'  => $divisions,
        'projects'   => $projects,
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
