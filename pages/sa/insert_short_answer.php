<?php
require '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = trim($_POST['sa_topic'] ?? '');
    $question = trim($_POST['sa_question'] ?? '');
    $correct_answer = trim($_POST['sa_correct_answer'] ?? '');
    $image_url = trim($_POST['sa_image_url'] ?? '');

    if ($topic !== '' && $question !== '') {
        $stmt = $conn->prepare("INSERT INTO sa_questions (sa_topic, sa_question, sa_image_url, sa_correct_answer) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $topic, $question, $image_url, $correct_answer);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'sa_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Thiếu dữ liệu bắt buộc.']);
    }
}
?>
