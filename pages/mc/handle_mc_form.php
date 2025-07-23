<?php
// pages/mc/handle_mc_form.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic    = trim($_POST['topic'] ?? '');
    $question = trim($_POST['question'] ?? '');
    $answer1  = trim($_POST['answer1'] ?? '');
    $answer2  = trim($_POST['answer2'] ?? '');
    $answer3  = trim($_POST['answer3'] ?? '');
    $answer4  = trim($_POST['answer4'] ?? '');
    $correct  = $_POST['answer'] ?? '';
    $image_url = '';

    // Xử lý ảnh nếu có
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $filepath = $uploadDir . $filename;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']);

        if (in_array($mime, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $image_url = '../../uploads/' . $filename;
            }
        }
    } elseif (!empty($_POST['existing_image'])) {
        $image_url = $_POST['existing_image'];
    }

    try {
        if (!empty($_POST['mc_id'])) {
            $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? WHERE mc_id=?");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, (int)$_POST['mc_id']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url]);
        }
    } catch (PDOException $e) {
        die("Lỗi khi ghi dữ liệu: " . $e->getMessage());
    }

    header('Location: mc_form.php');
    exit;
}
