<?php
// mc_table_import_excel.php
header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức không hợp lệ'
    ]);
    exit;
}

// Kết nối DB
require_once __DIR__ . '/../../includes/db_connection.php'; // chỉnh lại đường dẫn nếu khác

// Nhận dữ liệu JSON từ JS
$rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : [];

if (!$rows || !is_array($rows)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu Excel không hợp lệ'
    ]);
    exit;
}

$inserted = 0;
$errors = [];

// Chuẩn bị câu lệnh INSERT
$stmt = $conn->prepare("
    INSERT INTO mc_questions 
    (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

foreach ($rows as $index => $row) {
    try {
        // Map dữ liệu từ Excel
        $topic   = trim($row['mc_topic'] ?? '');
        $question= trim($row['mc_question'] ?? '');
        $a1      = trim($row['mc_answer1'] ?? '');
        $a2      = trim($row['mc_answer2'] ?? '');
        $a3      = trim($row['mc_answer3'] ?? '');
        $a4      = trim($row['mc_answer4'] ?? '');
        $correct = trim($row['mc_correct_answer'] ?? '');
        $image   = trim($row['mc_image_url'] ?? '');

        // Bỏ qua dòng không có câu hỏi
        if ($question === '') {
            $errors[] = "Dòng " . ($index+2) . " thiếu mc_question";
            continue;
        }

        $stmt->bind_param(
            "ssssssss",
            $topic, $question, $a1, $a2, $a3, $a4, $correct, $image
        );
        $stmt->execute();
        $inserted++;

    } catch (Exception $e) {
        $errors[] = "Dòng " . ($index+2) . " lỗi: " . $e->getMessage();
    }
}

$stmt->close();
$conn->close();

// Trả về kết quả JSON
echo json_encode([
    'status' => 'success',
    'count'  => $inserted,
    'errors' => $errors
]);
