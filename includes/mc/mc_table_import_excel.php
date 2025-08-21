<?php
// mc_table_import_excel.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}

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

// Mapping tiêu đề Excel → cột DB
$mapping = [
    'mc_topic'          => ['mc_topic', 'topic', 'chủ đề'],
    'mc_question'       => ['mc_question', 'question', 'câu hỏi'],
    'mc_answer1'        => ['mc_answer1', 'answer1', 'đáp án 1'],
    'mc_answer2'        => ['mc_answer2', 'answer2', 'đáp án 2'],
    'mc_answer3'        => ['mc_answer3', 'answer3', 'đáp án 3'],
    'mc_answer4'        => ['mc_answer4', 'answer4', 'đáp án 4'],
    'mc_correct_answer' => ['mc_correct_answer', 'correct', 'đáp án đúng'],
    'mc_image_url'      => ['mc_image_url', 'image', 'ảnh', 'hình ảnh']
];

/**
 * Hàm tìm value theo mapping
 */
function mapValue($row, $candidates) {
    foreach ($candidates as $key) {
        foreach ($row as $colName => $val) {
            if (mb_strtolower(trim($colName)) === mb_strtolower(trim($key))) {
                return $val;
            }
        }
    }
    return ''; // Nếu không thấy thì trả về rỗng
}

$inserted = 0;
$errors = [];

foreach ($data as $rowIndex => $row) {
    // Lấy dữ liệu theo mapping
    $mc_topic          = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_topic'])));
    $mc_question       = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_question'])));
    $mc_answer1        = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_answer1'])));
    $mc_answer2        = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_answer2'])));
    $mc_answer3        = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_answer3'])));
    $mc_answer4        = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_answer4'])));
    $mc_correct_answer = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_correct_answer'])));
    $mc_image_url      = mysqli_real_escape_string($conn, trim(mapValue($row, $mapping['mc_image_url'])));

    // Bắt buộc phải có topic và question
    if (empty($mc_topic) || empty($mc_question)) {
        $errors[] = "Dòng " . ($rowIndex+1) . " thiếu dữ liệu bắt buộc (topic/question).";
        continue;
    }

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
        $errors[] = "Lỗi dòng " . ($rowIndex+1) . ": " . mysqli_error($conn);
    }
}

echo json_encode([
    'status'   => 'success',
    'inserted' => $inserted,
    'errors'   => $errors
]);
