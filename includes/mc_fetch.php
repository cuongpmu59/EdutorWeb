<?php
require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_GET['mc_id']) || !is_numeric($_GET['mc_id'])) {
    echo json_encode(['error' => 'ID không hợp lệ']);
    exit;
}

$mc_id = (int) $_GET['mc_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Không tìm thấy']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
