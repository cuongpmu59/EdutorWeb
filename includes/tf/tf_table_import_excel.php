<?php
// tf_table_import_excel.php
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
    if (empty($row['tf_topic']) || empty($row['tf_question']) || empty($row['tf_statement1'])) {
        $errors[] = "Dòng " . ($rowIndex + 1) . " thiếu dữ liệu bắt buộc.";
        continue;
    }

    // Lấy dữ liệu an toàn
    $tf_topic            = mysqli_real_escape_string($conn, trim($row['tf_topic']));
    $tf_question         = mysqli_real_escape_string($conn, trim($row['tf_question']));
    $tf_statement1       = mysqli_real_escape_string($conn, trim($row['tf_statement1'] ?? ''));
    $tf_correct_answer1  = (int)($row['tf_correct_answer1'] ?? 0);
    $tf_statement2       = mysqli_real_escape_string($conn, trim($row['tf_statement2'] ?? ''));
    $tf_correct_answer2  = (int)($row['tf_correct_answer2'] ?? 0);
    $tf_statement3       = mysqli_real_escape_string($conn, trim($row['tf_statement3'] ?? ''));
    $tf_correct_answer3  = (int)($row['tf_correct_answer3'] ?? 0);
    $tf_statement4       = mysqli_real_escape_string($conn, trim($row['tf_statement4'] ?? ''));
    $tf_correct_answer4  = (int)($row['tf_correct_answer4'] ?? 0);
    $tf_image_url        = mysqli_real_escape_string($conn, trim($row['tf_image_url'] ?? ''));

    // Câu lệnh INSERT
    $sql = "
        INSERT INTO tf_questions (
            tf_topic, tf_question,
            tf_statement1, tf_correct_answer1,
            tf_statement2, tf_correct_answer2,
            tf_statement3, tf_correct_answer3,
            tf_statement4, tf_correct_answer4,
            tf_image_url, tf_created_at
        ) VALUES (
            '$tf_topic', '$tf_question',
            '$tf_statement1', '$tf_correct_answer1',
            '$tf_statement2', '$tf_correct_answer2',
            '$tf_statement3', '$tf_correct_answer3',
            '$tf_statement4', '$tf_correct_answer4',
            '$tf_image_url', NOW()
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
