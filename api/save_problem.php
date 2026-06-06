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

$id           = isset($data['id']) ? (int)$data['id'] : null;
$name         = trim($data['name'] ?? 'Untitled Problem');
$prodAName    = trim($data['prod_a_name'] ?? 'Product A');
$prodASale    = (float)($data['prod_a_sale'] ?? 0);
$prodACost    = (float)($data['prod_a_cost'] ?? 0);
$prodATime    = (float)($data['prod_a_time'] ?? 0);
$prodBName    = trim($data['prod_b_name'] ?? 'Product B');
$prodBSale    = (float)($data['prod_b_sale'] ?? 0);
$prodBCost    = (float)($data['prod_b_cost'] ?? 0);
$prodBTime    = (float)($data['prod_b_time'] ?? 0);
$budget       = (float)($data['budget'] ?? 0);
$budgetPeriod = trim($data['budget_period'] ?? 'week');
$workHours    = (float)($data['work_hours'] ?? 0);
$constraints  = $data['constraints'] ?? [];

if ($name === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Problem name is required']);
    exit;
}

$db->begin_transaction();
try {
    if ($id) {
        $stmt = $db->prepare(
            "UPDATE problems SET name=?, prod_a_name=?, prod_a_sale=?, prod_a_cost=?, prod_a_time=?,
             prod_b_name=?, prod_b_sale=?, prod_b_cost=?, prod_b_time=?,
             budget=?, budget_period=?, work_hours=? WHERE id=?"
        );
        $stmt->bind_param("ssddd sddd dsdi",
            $name, $prodAName, $prodASale, $prodACost, $prodATime,
            $prodBName, $prodBSale, $prodBCost, $prodBTime,
            $budget, $budgetPeriod, $workHours, $id
        );
        $stmt->execute();
        $stmt->close();

        $delStmt = $db->prepare("DELETE FROM constraints WHERE problem_id = ?");
        $delStmt->bind_param("i", $id);
        $delStmt->execute();
        $delStmt->close();
    } else {
        $stmt = $db->prepare(
            "INSERT INTO problems (name, prod_a_name, prod_a_sale, prod_a_cost, prod_a_time,
             prod_b_name, prod_b_sale, prod_b_cost, prod_b_time,
             budget, budget_period, work_hours)
             VALUES (?, ?, ?, ?, ?,  ?, ?, ?, ?,  ?, ?, ?)"
        );
        $stmt->bind_param("ssddd sddd dsd",
            $name, $prodAName, $prodASale, $prodACost, $prodATime,
            $prodBName, $prodBSale, $prodBCost, $prodBTime,
            $budget, $budgetPeriod, $workHours
        );
        $stmt->execute();
        $id = $db->insert_id;
        $stmt->close();
    }

    if (count($constraints) > 0) {
        $cstmt = $db->prepare("INSERT INTO constraints (problem_id, name, coef_a, coef_b, max_val, unit) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($constraints as $c) {
            $cName  = trim($c['name'] ?? 'Constraint');
            $coefA  = (float)($c['coef_a'] ?? 0);
            $coefB  = (float)($c['coef_b'] ?? 0);
            $maxVal = (float)($c['max_val'] ?? 0);
            $unit   = trim($c['unit'] ?? 'units');
            $cstmt->bind_param("isddds", $id, $cName, $coefA, $coefB, $maxVal, $unit);
            $cstmt->execute();
        }
        $cstmt->close();
    }

    $db->commit();
    echo json_encode(['success' => true, 'id' => $id]);

} catch (Exception $e) {
    $db->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
}
