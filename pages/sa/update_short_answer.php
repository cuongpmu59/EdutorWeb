<?php
require '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['sa_id'] ?? 0);
    $topic = trim($_POST['sa_topic'] ?? '');
    $question = trim($_POST['sa_question'] ?? '');
    $correct_answer = trim($_POST['sa_correct_answer'] ?? '');
    $image_url = trim($_POST['sa_image_url'] ?? '');

    if ($id > 0 && $topic !== '' && $question !== '') {
        $stmt = $conn->prepare("UPDATE sa_questions SET sa_topic = ?, sa_question = ?, sa_image_url = ?, sa_correct_answer = ? WHERE sa_id = ?");
        $stmt->bind_param("ssssi", $topic, $question, $image_url, $correct_answer, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ.']);
    }
}
?>
