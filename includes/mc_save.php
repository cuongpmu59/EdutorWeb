<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../includes/db_connection.php';

try {
    // Nhận dữ liệu từ form
    $mc_id        = isset($_POST['mc_id']) ? intval($_POST['mc_id']) : null;
    $mc_topic     = $_POST['mc_topic'] ?? '';
    $mc_question  = $_POST['mc_question'] ?? '';
    $mc_answer1   = $_POST['mc_answer1'] ?? '';
    $mc_answer2   = $_POST['mc_answer2'] ?? '';
    $mc_answer3   = $_POST['mc_answer3'] ?? '';
    $mc_answer4   = $_POST['mc_answer4'] ?? '';
    $mc_correct   = $_POST['mc_correct'] ?? '';
    $existing_img = $_POST['existing_image'] ?? '';
    $image_url    = $existing_img;

    // === Xử lý ảnh nếu có ===
    if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['mc_image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['mc_image']['tmp_name'], $targetPath)) {
            $image_url = 'uploads/images/' . $fileName;

            // Xóa ảnh cũ nếu có
            if ($existing_img && file_exists('../' . $existing_img)) {
                unlink('../' . $existing_img);
            }
        } else {
            throw new Exception("Lỗi khi tải ảnh lên.");
        }
    }

    // === Thêm mới hoặc cập nhật ===
    if ($mc_id) {
        // Cập nhật
        $sql = "UPDATE mc_questions SET
                    mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, 
                    mc_answer3 = ?, mc_answer4 = ?, mc_correct = ?, mc_image_url = ?
                WHERE mc_id = ?";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2,
            $mc_answer3, $mc_answer4, $mc_correct, $image_url, $mc_id
        ]);
    } else {
        // Thêm mới
        $sql = "INSERT INTO mc_questions 
                    (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct, mc_image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2,
            $mc_answer3, $mc_answer4, $mc_correct, $image_url
        ]);
    }

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Không thể lưu vào cơ sở dữ liệu.");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
