<?php
require 'db_connection.php';
require 'dotenv.php';

header('Content-Type: application/json');

$id = null;
$topic = $_POST['topic'] ?? '';
$question = $_POST['question'] ?? '';
$answer1 = $_POST['answer1'] ?? '';
$answer2 = $_POST['answer2'] ?? '';
$answer3 = $_POST['answer3'] ?? '';
$answer4 = $_POST['answer4'] ?? '';
$correct = $_POST['correct_answer'] ?? '';
$imageUrl = $_POST['image_url'] ?? '';

try {
    $stmt = $conn->prepare("INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, topic, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$question, $answer1, $answer2, $answer3, $answer4, $correct, $topic, $imageUrl]);
    $id = $conn->lastInsertId();
    echo json_encode(['status' => 'success', 'message' => '✅ Thêm thành công!', 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => '❌ Lỗi: ' . $e->getMessage()]);
}
