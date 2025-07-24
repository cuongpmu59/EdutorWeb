<?php
require_once '../db_connection.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Nếu có mc_id, trả về một câu hỏi
if (isset($_GET['mc_id'])) {
    $mc_id = intval($_GET['mc_id']);

    $stmt = $conn->prepare("SELECT * FROM multiple_choice WHERE mc_id = ?");
    $stmt->bind_param("i", $mc_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy câu hỏi.'
        ]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Nếu không có mc_id, có thể dùng cho các mục đích khác (nếu cần)
echo json_encode([
    'success' => false,
    'message' => 'Thiếu mc_id'
]);
$conn->close();
exit;
