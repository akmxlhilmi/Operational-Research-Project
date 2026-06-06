<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST method required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? (int)$data['id'] : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Problem id is required']);
    exit;
}

$db = getDB();

$stmt = $db->prepare("DELETE FROM problems WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Problem not found']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete: ' . $stmt->error]);
}
$stmt->close();
