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

// Đọc raw JSON từ body (fetch gửi application/json)
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'status' => 'error',
        'message' => 'JSON không hợp lệ: ' . json_last_error_msg()
    ]);
    exit;
}

if (!is_array($data) || empty($data)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu trống hoặc sai định dạng.'
    ]);
    exit;
}

// Mapping cột Excel → DB
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

// Hàm ánh xạ giá trị từ Excel
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

try {
    // Dùng prepared statement để an toàn
    $stmt = $conn->prepare("
        INSERT INTO multiple_choice (
            mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4,
            mc_correct_answer, mc_image_url
        ) VALUES (?,?,?,?,?,?,?,?)
    ");

    foreach ($data as $rowIndex => $row) {
        $mc_topic          = mapValue($row, $mapping['mc_topic']);
        $mc_question       = mapValue($row, $mapping['mc_question']);
        $mc_answer1        = mapValue($row, $mapping['mc_answer1']);
        $mc_answer2        = mapValue($row, $mapping['mc_answer2']);
        $mc_answer3        = mapValue($row, $mapping['mc_answer3']);
        $mc_answer4        = mapValue($row, $mapping['mc_answer4']);
        $mc_correct_answer = mapValue($row, $mapping['mc_correct_answer']);
        $mc_image_url      = mapValue($row, $mapping['mc_image_url']);

        if (empty($mc_topic) || empty($mc_question)) {
            $errors[] = "❌ Dòng " . ($rowIndex + 1) . " thiếu topic hoặc question.";
            continue;
        }

        $stmt->bind_param(
            "ssssssss",
            $mc_topic, $mc_question,
            $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4,
            $mc_correct_answer, $mc_image_url
        );

        if ($stmt->execute()) {
            $inserted++;
        } else {
            $errors[] = "⚠️ Lỗi dòng " . ($rowIndex + 1) . ": " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode([
        'status'   => 'success',
        'inserted' => $inserted,
        'errors'   => $errors
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Exception: ' . $e->getMessage()
    ]);
    exit;
}
