<?php
// Kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php';

// Thiết lập header để trình duyệt hiểu đây là HTML
header('Content-Type: text/html; charset=UTF-8');

// Truy vấn danh sách chủ đề duy nhất, không rỗng
try {
    $stmt = $conn->query("
        SELECT DISTINCT mc_topic 
        FROM mc_questions 
        WHERE mc_topic IS NOT NULL AND mc_topic != '' 
        ORDER BY mc_topic ASC
    ");

    // Dòng mặc định
    echo '<option value="">-- Tất cả --</option>';

    // Lặp và in ra từng <option>
    foreach ($stmt as $row) {
        $topic = htmlspecialchars($row['mc_topic'], ENT_QUOTES, 'UTF-8');
        echo "<option value=\"$topic\">$topic</option>";
    }
} catch (PDOException $e) {
    echo '<option value="">(Lỗi truy vấn CSDL)</option>';
}
?>
