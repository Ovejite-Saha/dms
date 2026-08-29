<?php
// ============================================================
// Document access filter for regular users
// ============================================================

/**
 * Build SQL WHERE clause + params for the current user's document visibility.
 * Admins & subadmins see everything (caller should skip this for them).
 *
 * @return array [string $whereSql, array $params]
 */
function user_document_filter(PDO $pdo, array $user): array {
    $access_type = $user['access_type'] ?? 'all';
    $access_ids  = $user['access_ids'] ?? null;

    if (is_string($access_ids)) {
        $access_ids = json_decode($access_ids, true);
    }
    if (!is_array($access_ids)) $access_ids = [];

    if ($access_type === 'all' || empty($access_ids)) {
        return ['1=1', []];
    }

    $placeholders = implode(',', array_fill(0, count($access_ids), '?'));

    switch ($access_type) {
        case 'division':
            return ["d.division_id IN ($placeholders)", $access_ids];
        case 'district':
            return ["d.district_id IN ($placeholders)", $access_ids];
        case 'upazila':
            return ["d.upazila_id IN ($placeholders)", $access_ids];
        case 'project':
            return ["d.project_id IN ($placeholders)", $access_ids];
        default:
            return ['1=1', []];
    }
}

/**
 * Common JOIN for documents with hierarchy names
 */
function documents_base_query(): string {
    return "
        SELECT d.*,
               m.name  AS ministry_name,
               dv.name AS division_name,
               dt.name AS district_name,
               u.name  AS upazila_name,
               p.name  AS project_name
        FROM documents d
        JOIN ministries m  ON m.id  = d.ministry_id
        JOIN divisions  dv ON dv.id = d.division_id
        JOIN districts  dt ON dt.id = d.district_id
        JOIN upazilas   u  ON u.id  = d.upazila_id
        JOIN projects   p  ON p.id  = d.project_id
    ";
}
