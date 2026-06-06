<?php
require_once 'db.php';

$db = getDB();

$sql = "SELECT p.*,
        (SELECT COUNT(*) FROM results r WHERE r.problem_id = p.id) AS result_count
        FROM problems p ORDER BY p.created_at DESC";
$result = $db->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $db->error]);
    exit;
}

$problems = [];
while ($row = $result->fetch_assoc()) {
    $pid = (int)$row['id'];

    $cstmt = $db->prepare("SELECT id, name, coef_a, coef_b, max_val, unit FROM constraints WHERE problem_id = ? ORDER BY id ASC");
    $cstmt->bind_param("i", $pid);
    $cstmt->execute();
    $cresult = $cstmt->get_result();

    $constraints = [];
    while ($crow = $cresult->fetch_assoc()) {
        $constraints[] = $crow;
    }
    $cstmt->close();

    $row['constraints'] = $constraints;
    $problems[] = $row;
}

echo json_encode($problems);
