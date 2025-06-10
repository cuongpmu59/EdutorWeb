<?php
require 'db_connection.php';

if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die('Lỗi khi tải file lên.');
}

$handle = fopen($_FILES['file']['tmp_name'], 'r');
if (!$handle) {
    die('Không thể mở file.');
}

$header = fgetcsv($handle); // Bỏ qua dòng tiêu đề
$count = 0;

while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
    if (count($data) < 6) continue;

    $question = mysqli_real_escape_string($conn, $data[0]);
    $a = mysqli_real_escape_string($conn, $data[1]);
    $b = mysqli_real_escape_string($conn, $data[2]);
    $c = mysqli_real_escape_string($conn, $data[3]);
    $d = mysqli_real_escape_string($conn, $data[4]);
    $correct = mysqli_real_escape_string($conn, strtoupper(trim($data[5])));
    $image = ''; // Không xử lý ảnh trong import CSV

    $sql = "INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, image)
            VALUES ('$question', '$a', '$b', '$c', '$d', '$correct', '$image')";
    mysqli_query($conn, $sql);
    $count++;
}

fclose($handle);
echo "Đã nhập thành công $count câu hỏi.";
mysqli_close($conn);
