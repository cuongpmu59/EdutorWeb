<?php
require_once '../db_connection.php'; // Đảm bảo file kết nối DB đúng đường dẫn

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection(); // Hàm từ db_connection.php

// Nếu có mc_id → lấy 1 dòng
if (isset($_GET['mc_id'])) {
    $mc_id = intval($_GET['mc_id']);
    $stmt = $pdo->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $data['mc_image_url'] = $data['mc_image'] ? "../../uploads/" . $data['mc_image'] : null;
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy câu hỏi']);
    }
    exit;
}

// Nếu không có mc_id → xuất toàn bộ (cho DataTables)
$stmt = $pdo->query("SELECT mc_id, mc_topic, mc_question FROM mc_questions ORDER BY mc_id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $rows]);
