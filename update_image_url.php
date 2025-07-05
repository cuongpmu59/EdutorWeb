<?php
require_once 'config.php';
require_once 'db_connection.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$imageUrl = $_POST['image_url'] ?? '';

if (!$id || !$imageUrl) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID hoặc URL ảnh']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE questions SET image = ? WHERE id = ?");
    $success = $stmt->execute([$imageUrl, $id]);

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi SQL: ' . $e->getMessage()
    ]);
}
