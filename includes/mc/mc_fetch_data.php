<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json; charset=utf-8');

// Chặn rác output
if (ob_get_length()) ob_clean();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
        $mc_id = intval($_POST['mc_id']);
        $stmt = $conn->prepare("
            SELECT mc_id, mc_topic, mc_question, 
                   mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
                   mc_correct_answer, mc_image_url
            FROM mc_questions
            WHERE mc_id = :mc_id
            LIMIT 1
        ");
        $stmt->execute(['mc_id' => $mc_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($data ?: ['error' => '❌ Không tìm thấy dữ liệu'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // GET toàn bộ danh sách
    $stmt = $conn->query("
        SELECT mc_id, mc_topic, mc_question, 
               mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
               mc_correct_answer, mc_image_url
        FROM mc_questions
        ORDER BY mc_id DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
    exit;
} catch (PDOException $e) {
    echo json_encode(['data' => [], 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
