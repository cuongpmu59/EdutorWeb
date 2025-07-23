<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kiểm tra đúng form loại "mc_question"
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'mc_question') {

        // Lấy dữ liệu từ POST
        $topic    = trim($_POST['topic'] ?? '');
        $question = trim($_POST['question'] ?? '');
        $answer1  = trim($_POST['answer1'] ?? '');
        $answer2  = trim($_POST['answer2'] ?? '');
        $answer3  = trim($_POST['answer3'] ?? '');
        $answer4  = trim($_POST['answer4'] ?? '');
        $correct  = $_POST['answer'] ?? '';
        $image_url = '';

        // Xử lý upload ảnh nếu có
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
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
                // Cập nhật câu hỏi
                $stmt = $conn->prepare("UPDATE mc_questions 
                    SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? 
                    WHERE mc_id=?");
                $stmt->execute([
                    $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, (int)$_POST['mc_id']
                ]);
            } else {
                // Thêm mới câu hỏi
                $stmt = $conn->prepare("INSERT INTO mc_questions 
                    (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url
                ]);
            }
        } catch (PDOException $e) {
            // Lỗi DB, chỉ hiển thị trong dev
            die("Lỗi CSDL: " . $e->getMessage());
        }

        // Quay lại form sau khi lưu
        header('Location: ../pages/mc/mc_form.php');
        exit;
    }
}
