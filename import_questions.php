<?php
require 'db_connection.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if (!isset($_FILES['excel']) || $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Lỗi file tải lên']);
    exit;
}

$file = $_FILES['excel']['tmp_name'];
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$inserted = 0;
foreach ($rows as $index => $row) {
    if ($index === 0) continue; // Bỏ dòng tiêu đề

    [$topic, $question, $a, $b, $c, $d, $correct] = $row;

    if (!$question || !$a || !$b || !$c || !$d || !$correct) continue;

    $stmt = $conn->prepare("INSERT INTO questions (topic, question, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $topic, $question, $a, $b, $c, $d, strtoupper(trim($correct)));
    $stmt->execute();
    $inserted++;
}

echo json_encode(['success' => true, 'message' => "Đã nhập $inserted câu hỏi."]);
