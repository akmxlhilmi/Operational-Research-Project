<?php
require_once 'db.php';
requireAuth();

$db = getDB();
$userId = getCurrentUserId();

$sql = "SELECT p.*,
        (SELECT COUNT(*) FROM results r WHERE r.problem_id = p.id) AS result_count
        FROM problems p WHERE p.user_id = ? ORDER BY p.created_at DESC";

$stmt = $db->prepare($sql);
if (!$stmt) {
    http_response_code(200);
    echo json_encode([]);
    exit;
}
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$problems = [];
while ($row = $result->fetch_assoc()) {
    $pid = (int)$row['id'];

    $cstmt = $db->prepare("SELECT id, name, coef_a, coef_b, max_val, unit FROM constraints WHERE problem_id = ? ORDER BY id ASC");
    if ($cstmt) {
        $cstmt->bind_param("i", $pid);
        $cstmt->execute();
        $cresult = $cstmt->get_result();

        $constraints = [];
        if ($cresult) {
            while ($crow = $cresult->fetch_assoc()) {
                $constraints[] = $crow;
            }
        }
        $cstmt->close();
    }

    $row['constraints'] = $constraints ?? [];
    $problems[] = $row;
}

echo json_encode($problems);
