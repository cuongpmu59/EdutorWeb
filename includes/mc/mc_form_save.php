<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../includes/db_connection.php';

$response = ['status' => 'error', 'message' => 'Lỗi không xác định'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ.');
    }

    // Lấy dữ liệu POST
    $mc_id             = isset($_POST['mc_id']) ? filter_var($_POST['mc_id'], FILTER_VALIDATE_INT) : null;
    $mc_topic          = trim($_POST['mc_topic'] ?? '');
    $mc_question       = trim($_POST['mc_question'] ?? '');
    $mc_answer1        = trim($_POST['mc_answer1'] ?? '');
    $mc_answer2        = trim($_POST['mc_answer2'] ?? '');
    $mc_answer3        = trim($_POST['mc_answer3'] ?? '');
    $mc_answer4        = trim($_POST['mc_answer4'] ?? '');
    $mc_correct_answer = trim($_POST['mc_correct_answer'] ?? '');
    $mc_image_url      = trim($_POST['mc_image_url'] ?? ''); // URL ảnh Cloudinary (nếu có)

    // Kiểm tra dữ liệu bắt buộc
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_answer3 || !$mc_answer4 || !$mc_correct_answer) {
        throw new Exception('⚠️ Thiếu dữ liệu bắt buộc.');
    }

    if ($mc_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE mc_questions 
            SET mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?, mc_image_url = ?
            WHERE mc_id = ?
        ");
        $ok = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_image_url, $mc_id
        ]);
        if ($ok) {
            $response = ['status' => 'success', 'message' => '✅ Cập nhật câu hỏi thành công.'];
        } else {
            throw new Exception('❌ Lỗi khi cập nhật câu hỏi.');
        }
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_image_url
        ]);
        if ($ok) {
            $response = ['status' => 'success', 'message' => '✅ Thêm câu hỏi mới thành công.'];
        } else {
            throw new Exception('❌ Lỗi khi thêm câu hỏi.');
        }
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
