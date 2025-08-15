<?php
// mc_table_import_excel.php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Không có file hoặc lỗi khi tải file lên']);
    exit;
}

// Nạp kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php';
// Nạp thư viện đọc Excel
require_once __DIR__ . '/../../includes/lib/SimpleXLSX.php';

try {
    $fileTmp = $_FILES['excelFile']['tmp_name'];

    if ($xlsx = SimpleXLSX::parse($fileTmp)) {
        $rows = $xlsx->rows();
        $rowCount = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Bỏ qua header

            // Lấy dữ liệu từng cột (chỉnh theo file Excel của bạn)
            $mc_topic          = trim($row[0] ?? '');
            $mc_question       = trim($row[1] ?? '');
            $mc_answer1        = trim($row[2] ?? '');
            $mc_answer2        = trim($row[3] ?? '');
            $mc_answer3        = trim($row[4] ?? '');
            $mc_answer4        = trim($row[5] ?? '');
            $mc_correct_answer = trim($row[6] ?? '');
            $mc_image_url      = trim($row[7] ?? '');

            if ($mc_question === '') continue;

            $stmt = $conn->prepare("
                INSERT INTO mc_questions
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssssss",
                $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_image_url
            );
            $stmt->execute();
            $rowCount++;
        }

        echo json_encode(['status' => 'success', 'message' => "Đã nhập $rowCount câu hỏi thành công"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => SimpleXLSX::parseError()]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
