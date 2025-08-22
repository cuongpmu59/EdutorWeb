<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../includes/db_connection.php';

$input = json_decode(file_get_contents('php://input'), true);
$rows = $input['rows'] ?? [];

if (!is_array($rows) || empty($rows)) {
    echo json_encode(['status'=>'error','message'=>'Dữ liệu Excel không hợp lệ']);
    exit;
}

$inserted = 0;
$errors = [];

$stmt = $conn->prepare("INSERT INTO mc_questions
(mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($rows as $i => $row) {
    $topic   = trim($row['mc_topic'] ?? '');
    $question= trim($row['mc_question'] ?? '');
    $a       = trim($row['mc_answer1'] ?? '');
    $b       = trim($row['mc_answer2'] ?? '');
    $c       = trim($row['mc_answer3'] ?? '');
    $d       = trim($row['mc_answer4'] ?? '');
    $correct = strtoupper(trim($row['mc_correct_answer'] ?? ''));
    $image   = trim($row['mc_image_url'] ?? '');

    if (!$topic || !$question) {
        $errors[] = "Dòng ".($i+2)." thiếu topic hoặc question";
        continue;
    }
    if (!$a || !$b || !$c || !$d) {
        $errors[] = "Dòng ".($i+2)." thiếu đáp án A/B/C/D";
        continue;
    }
    if (!in_array($correct, ['A','B','C','D'])) {
        $errors[] = "Dòng ".($i+2)." đáp án không hợp lệ (A/B/C/D)";
        continue;
    }

    $stmt->bind_param('ssssssss', $topic, $question, $a, $b, $c, $d, $correct, $image);
    if ($stmt->execute()) $inserted++;
    else $errors[] = "Dòng ".($i+2)." lỗi: ".$stmt->error;
}

$stmt->close();
echo json_encode(['status'=>'success','count'=>$inserted,'errors'=>$errors]);
