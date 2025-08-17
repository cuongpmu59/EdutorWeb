<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

require_once __DIR__ . '/../db_connection.php'; // Biến kết nối PDO: $conn

try {
    // Lấy danh sách chủ đề duy nhất, loại bỏ NULL/empty, sắp xếp ASC
    $sql = "SELECT DISTINCT mc_topic 
            FROM mc_questions 
            WHERE mc_topic IS NOT NULL AND mc_topic != '' 
            ORDER BY mc_topic ASC";
    $stmt = $conn->query($sql);

    $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($topics, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể lấy danh sách chủ đề',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
