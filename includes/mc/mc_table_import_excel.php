<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../includes/db_connection.php'; // sửa đường dẫn DB

// Lấy dữ liệu JSON raw
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$rows = $input['rows'] ?? [];

if (!$rows || !is_array($rows)) {
    echo json_encode(['status'=>'error','message'=>'Dữ liệu Excel không hợp lệ']);
    exit;
}

$inserted = 0;
$errors = [];

$stmt = $conn->prepare("INSERT INTO mc_questions
(mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($rows as $i => $row) {
    if (empty($row['mc_topic']) || empty($row['mc_question'])) {
        $errors[] = "Dòng ".($i+2)." thiếu topic hoặc question";
        continue;
    }

    $correct = strtoupper(trim($row['mc_correct_answer'] ?? ''));
    if (!in_array($correct, ['A','B','C','D'])) {
        $errors[] = "Dòng ".($i+2)." đáp án không hợp lệ (phải A/B/C/D)";
        continue;
    }

    $stmt->bind_param(
        'ssssssss',
        $row['mc_topic'],
        $row['mc_question'],
        $row['mc_answer1'],
        $row['mc_answer2'],
        $row['mc_answer3'],
        $row['mc_answer4'],
        $correct,
        $row['mc_image_url']
    );

    if($stmt->execute()) $inserted++;
    else $errors[] = "Dòng ".($i+2)." lỗi: ".$stmt->error;
}

$stmt->close();
echo json_encode(['status'=>'success','count'=>$inserted,'errors'=>$errors]);
