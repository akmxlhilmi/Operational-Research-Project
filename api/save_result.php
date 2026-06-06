<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST method required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$db = getDB();

$problemId     = !empty($data['problem_id']) ? (int)$data['problem_id'] : null;
$optimalProfit = (float)($data['optimal_profit'] ?? 0);
$qtyA          = (float)($data['qty_a'] ?? 0);
$qtyB          = (float)($data['qty_b'] ?? 0);

if (!$problemId) {
    http_response_code(400);
    echo json_encode(['error' => 'problem_id is required']);
    exit;
}

$check = $db->prepare("SELECT id FROM problems WHERE id = ?");
if (!$check) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}
$check->bind_param("i", $problemId);
$check->execute();
$chkResult = $check->get_result();
if ($chkResult && $chkResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Problem not found']);
    $check->close();
    exit;
}
$check->close();

$stmt = $db->prepare("INSERT INTO results (problem_id, optimal_profit, qty_a, qty_b) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}
$stmt->bind_param("iddd", $problemId, $optimalProfit, $qtyA, $qtyB);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $db->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save result: ' . $stmt->error]);
}
$stmt->close();
