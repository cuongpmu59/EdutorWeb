<?php
require_once __DIR__ . '/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo '<h3 style="font-family: sans-serif; color: #c00;">Truy cập không hợp lệ. Đây là endpoint xử lý, không hỗ trợ truy cập trực tiếp.</h3>';
    exit;
}

$topic    = $_POST['topic']    ?? '';
$question = $_POST['question'] ?? '';
$a1       = $_POST['answer1']  ?? '';
$a2       = $_POST['answer2']  ?? '';
$a3       = $_POST['answer3']  ?? '';
$a4       = $_POST['answer4']  ?? '';
$correct  = $_POST['answer']   ?? '';
$mc_id    = $_POST['mc_id']    ?? '';
$imageUrl = $_POST['existing_image'] ?? '';
$publicId = $_POST['public_id']      ?? '';

try {
    if ($mc_id) {
        // ==================== CẬP NHẬT ====================
        $stmt = $conn->prepare("
            UPDATE mc_questions SET 
                mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, 
                mc_answer3=?, mc_answer4=?, mc_correct_answer=?, 
                mc_image_url=?, mc_image_public_id=?
            WHERE mc_id=?
        ");
        $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $imageUrl, $publicId, (int)$mc_id]);

    } else {
        // ==================== THÊM MỚI ====================
        $stmt = $conn->prepare("
            INSERT INTO mc_questions 
            (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
             mc_correct_answer, mc_image_url, mc_image_public_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $imageUrl, $publicId]);
        $mc_id = $conn->lastInsertId();
    }

    // ==================== TRẢ VỀ KẾT QUẢ ====================
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'mc_id' => $mc_id]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
    exit;
}
