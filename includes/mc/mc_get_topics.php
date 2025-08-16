<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

require_once __DIR__ . '/../db_connection.php';

try {
    $sql = "SELECT DISTINCT mc_topic FROM mc_questions ORDER BY mc_topic ASC";
    $stmt = $pdo->query($sql);

    $topics = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['mc_topic'])) {
            $topics[] = $row['mc_topic'];
        }
    }

    echo json_encode($topics, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể lấy danh sách chủ đề',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
