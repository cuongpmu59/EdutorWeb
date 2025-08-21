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

// Lấy dữ liệu từ POST
$rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;
if (!is_array($rows) || empty($rows)) {
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

function mapValue($row, $candidates) {
    foreach ($candidates as $key) {
        foreach ($row as $colName => $val) {
            if (mb_strtolower(trim($colName)) === mb_strtolower(trim($key))) {
                return trim($val);
            }
        }
    }
    return '';
}

$inserted = 0;
$errors   = [];

$stmt = $conn->prepare("
    INSERT INTO multiple_choice (
        mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4,
        mc_correct_answer, mc_image_url
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

foreach ($rows as $i => $row) {
    $mc_topic          = mapValue($row, $mapping['mc_topic']);
    $mc_question       = mapValue($row, $mapping['mc_question']);
    $mc_answer1        = mapValue($row, $mapping['mc_answer1']);
    $mc_answer2        = mapValue($row, $mapping['mc_answer2']);
    $mc_answer3        = mapValue($row, $mapping['mc_answer3']);
    $mc_answer4        = mapValue($row, $mapping['mc_answer4']);
    $mc_correct_answer = mapValue($row, $mapping['mc_correct_answer']);
    $mc_image_url      = mapValue($row, $mapping['mc_image_url']);

    if (empty($mc_topic) || empty($mc_question)) {
        $errors[] = "Dòng " . ($i+1) . " thiếu dữ liệu bắt buộc (topic/question).";
        continue;
    }

    $stmt->bind_param(
        'ssssssss',
        $mc_topic, $mc_question,
        $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4,
        $mc_correct_answer, $mc_image_url
    );

    if ($stmt->execute()) {
        $inserted++;
    } else {
        $errors[] = "Lỗi dòng " . ($i+1) . ": " . $stmt->error;
    }
}

$stmt->close();
$conn->close();

echo json_encode([
    'status'   => 'success',
    'inserted' => $inserted,
    'errors'   => $errors,
    'message'  => "Đã nhập thành công {$inserted} dòng."
]);
