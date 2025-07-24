<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

if (isset($_GET['mc_id'])) {
    // Lấy 1 câu hỏi theo mc_id
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

// Truy xuất tất cả câu hỏi cho DataTables
$sql = "SELECT mc_id, mc_topic, mc_question FROM multiple_choice ORDER BY mc_id DESC";
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $data
]);

$conn->close();
