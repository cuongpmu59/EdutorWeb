<?php
require 'db_connection.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=cau_hoi_export.csv');

// BOM cho UTF-8 (Excel nhận dạng tiếng Việt đúng)
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Ghi tiêu đề
fputcsv($output, ['ID', 'Chủ đề', 'Câu hỏi', 'Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D', 'Đáp án đúng']);

$result = mysqli_query($conn, "SELECT id, topic, question, answer1, answer2, answer3, answer4, correct_answer FROM questions");

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['topic'],
        $row['question'],
        $row['answer1'],
        $row['answer2'],
        $row['answer3'],
        $row['answer4'],
        $row['correct_answer']
    ]);
}

fclose($output);
mysqli_close($conn);
