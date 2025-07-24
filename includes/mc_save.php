<?php
require_once __DIR__ . '/../../includes/db_connection.php';

// Hàm xử lý ảnh minh họa
function handleImageUpload($inputName = 'image', $existingImage = '') {
    $uploadDir = __DIR__ . '/../uploads/';
    $webPathPrefix = '../uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        if (!in_array($_FILES[$inputName]['type'], $allowedTypes)) {
            return $existingImage; // Không đúng định dạng -> giữ nguyên ảnh cũ
        }

        $originalName = basename($_FILES[$inputName]['name']);
        $safeName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
        $filename = time() . '_' . $safeName;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $filepath)) {
            return $webPathPrefix . $filename;
        }
    }

    return $existingImage;
}

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic     = $_POST['topic'] ?? '';
    $question  = $_POST['question'] ?? '';
    $answer1   = $_POST['answer1'] ?? '';
    $answer2   = $_POST['answer2'] ?? '';
    $answer3   = $_POST['answer3'] ?? '';
    $answer4   = $_POST['answer4'] ?? '';
    $correct   = $_POST['answer'] ?? '';
    $mc_id     = $_POST['mc_id'] ?? '';
    $image_url = handleImageUpload('image', $_POST['existing_image'] ?? '');

    try {
        if (!empty($mc_id)) {
            // Cập nhật
            $stmt = $conn->prepare("
                UPDATE mc_questions 
                SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=?
                WHERE mc_id=?
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, (int)$mc_id]);
        } else {
            // Thêm mới
            $stmt = $conn->prepare("
                INSERT INTO mc_questions 
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url]);
        }

        header('Location: ../pages/mc/mc_form.php');
        exit;
    } catch (PDOException $e) {
        echo 'Lỗi lưu dữ liệu: ' . $e->getMessage();
        exit;
    }
}
?>
