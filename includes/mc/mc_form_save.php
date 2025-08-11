<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../../includes/db_connection.php';

try {
    $mc_id = $_POST['mc_id'] ?? '';
    $mc_topic = $_POST['mc_topic'] ?? '';
    $mc_question = $_POST['mc_question'] ?? '';
    $mc_answer1 = $_POST['mc_answer1'] ?? '';
    $mc_answer2 = $_POST['mc_answer2'] ?? '';
    $mc_answer3 = $_POST['mc_answer3'] ?? '';
    $mc_answer4 = $_POST['mc_answer4'] ?? '';
    $mc_correct_answer = $_POST['mc_correct_answer'] ?? '';

    if (!$mc_topic||!$mc_question||!$mc_answer1 ||!$mc_answer2||!$mc_answer3||!$mc_answer4||!$mc_correct_answer) {
        exit('⚠️ Thiếu dữ liệu bắt buộc.');
    }

    if ($mc_id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE mc_questions 
            SET mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?
            WHERE mc_id = ?
        ");
        $ok = $stmt->execute([$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_id]);
        echo $ok ? "✅ Cập nhật câu hỏi thành công." : "❌ Lỗi khi cập nhật câu hỏi.";
    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer]);
        echo $ok ? "✅ Thêm câu hỏi mới thành công." : "❌ Lỗi khi thêm câu hỏi.";
    }
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
