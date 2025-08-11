<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../../includes/db_connection.php';

try {
    // Lấy dữ liệu POST
    $mc_id            = $_POST['mc_id'] ?? '';
    $mc_topic         = trim($_POST['mc_topic'] ?? '');
    $mc_question      = trim($_POST['mc_question'] ?? '');
    $mc_answer1       = trim($_POST['mc_answer1'] ?? '');
    $mc_answer2       = trim($_POST['mc_answer2'] ?? '');
    $mc_answer3       = trim($_POST['mc_answer3'] ?? '');
    $mc_answer4       = trim($_POST['mc_answer4'] ?? '');
    $mc_correct_answer= trim($_POST['mc_correct_answer'] ?? '');
    $mc_image_url     = trim($_POST['mc_image_url'] ?? ''); // URL ảnh Cloudinary (nếu có)

    // Kiểm tra dữ liệu bắt buộc
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_answer3 || !$mc_answer4 || !$mc_correct_answer) {
        exit('⚠️ Thiếu dữ liệu bắt buộc.');
    }

    if ($mc_id) {
        // Cập nhật
        $stmt = $pdo->prepare("
            UPDATE mc_questions 
            SET mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?, mc_image_url = ?
            WHERE mc_id = ?
        ");
        $ok = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_image_url, $mc_id
        ]);
        echo $ok ? "✅ Cập nhật câu hỏi thành công." : "❌ Lỗi khi cập nhật câu hỏi.";
    } else {
        // Thêm mới
        $stmt = $pdo->prepare("
            INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_image_url
        ]);
        echo $ok ? "✅ Thêm câu hỏi mới thành công." : "❌ Lỗi khi thêm câu hỏi.";
    }
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
