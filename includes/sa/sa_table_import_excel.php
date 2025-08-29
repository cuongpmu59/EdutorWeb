<?php
// sa_table_import_excel.php
header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}

// Kết nối CSDL
require_once __DIR__ . '/../../includes/env/db_connection.php'; // đường dẫn tới file PDO

// Nhận dữ liệu JSON
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!is_array($data) || empty($data)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu không hợp lệ hoặc rỗng.'
    ]);
    exit;
}

$inserted = 0;
$errors = [];

foreach ($data as $rowIndex => $row) {
    // Kiểm tra cột bắt buộc
    if (empty($row['sa_topic']) || empty($row['sa_question'])) {
        $errors[] = "Dòng " . ($rowIndex + 1) . " thiếu dữ liệu bắt buộc.";
        continue;
    }

    // Lấy dữ liệu, tránh lỗi SQL injection
    $sa_topic          = mysqli_real_escape_string($conn, trim($row['sa_topic']));
    $sa_question       = mysqli_real_escape_string($conn, trim($row['sa_question']));
    $sa_answer         = mysqli_real_escape_string($conn, trim($row['sa_answer'] ?? ''));
    $sa_image_url      = mysqli_real_escape_string($conn, trim($row['sa_image_url'] ?? ''));

    // Câu lệnh INSERT
    $sql = "
        INSERT INTO short_answer (
            sa_topic, sa_question, sa_answer, sa_image_url
        ) VALUES (
            '$sa_topic', '$sa_question', '$sa_answer', '$sa_image_url'
        )
    ";

    if (mysqli_query($conn, $sql)) {
        $inserted++;
    } else {
        $errors[] = "Lỗi dòng " . ($rowIndex + 1) . ": " . mysqli_error($conn);
    }
}

echo json_encode([
    'status' => 'success',
    'inserted' => $inserted,
    'errors' => $errors
]);
