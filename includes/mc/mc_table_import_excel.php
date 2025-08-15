<?php
// mc_table_import_excel.php
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
require_once __DIR__ . '/../../includes/db_connection.php';

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
    if (empty($row['mc_topic']) || empty($row['mc_question'])) {
        $errors[] = "Dòng " . ($rowIndex + 1) . " thiếu dữ liệu bắt buộc.";
        continue;
    }

    // Lấy dữ liệu, tránh lỗi SQL injection
    $mc_topic          = mysqli_real_escape_string($conn, trim($row['mc_topic']));
    $mc_question       = mysqli_real_escape_string($conn, trim($row['mc_question']));
    $mc_answer1        = mysqli_real_escape_string($conn, trim($row['mc_answer1'] ?? ''));
    $mc_answer2        = mysqli_real_escape_string($conn, trim($row['mc_answer2'] ?? ''));
    $mc_answer3        = mysqli_real_escape_string($conn, trim($row['mc_answer3'] ?? ''));
    $mc_answer4        = mysqli_real_escape_string($conn, trim($row['mc_answer4'] ?? ''));
    $mc_correct_answer = mysqli_real_escape_string($conn, trim($row['mc_correct_answer'] ?? ''));
    $mc_image_url      = mysqli_real_escape_string($conn, trim($row['mc_image_url'] ?? ''));

    // Câu lệnh INSERT
    $sql = "
        INSERT INTO multiple_choice (
            mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4,
            mc_correct_answer, mc_image_url
        ) VALUES (
            '$mc_topic', '$mc_question', '$mc_answer1', '$mc_answer2', '$mc_answer3', '$mc_answer4',
            '$mc_correct_answer', '$mc_image_url'
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
