<?php
require 'db_connection.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=cau_hoi_export.csv');

$output = fopen('php://output', 'w');

// Ghi dòng tiêu đề
fputcsv($output, ['Câu hỏi', 'Đáp án A', 'Đáp án B', 'Đáp án C', 'Đáp án D', 'Đáp án đúng']);

$result = mysqli_query($conn, "SELECT question, answer1, answer2, answer3, answer4, correct_answer FROM questions");

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
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
