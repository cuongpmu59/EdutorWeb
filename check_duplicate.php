<?php
require 'db_connection.php';

$question = trim($_POST['question'] ?? '');

if ($question === '') {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE question = ?");
$stmt->execute([$question]);
$count = $stmt->fetchColumn();

echo json_encode(['exists' => $count > 0]);
?>
