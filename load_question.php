<?php
require 'db_connection.php'; // Kết nối CSDL

try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id ASC");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $index => $q) {
        $qnum = $index + 1;

        // Giữ nguyên nội dung LaTeX, không mã hóa
        $id = (int)$q['id']; // ID là số nên ép kiểu
        $image = htmlspecialchars($q['image']); // Chỉ encode phần tên file ảnh

        $question = $q['question'];
        $a1 = $q['answer1'];
        $a2 = $q['answer2'];
        $a3 = $q['answer3'];
        $a4 = $q['answer4'];

        echo "<div class='question' data-q='q$qnum'>";

        // Hiển thị câu hỏi với LaTeX: dùng nháy đơn để giữ nguyên dấu \
        echo '<p>Câu ' . $qnum . ': \(' . $question . '\)</p>';

        // Hình minh họa nếu có
        if (!empty($image)) {
            echo "<img src='images/$image' alt='Hình minh họa' style='width: 250px; display:block; margin: 10px auto;'><br>";
        }

        // Hiển thị đáp án, cũng dùng LaTeX inline \(...\)
        echo '<label><input type="radio" name="q' . $qnum . '" value="a"> \(' . $a1 . '\)</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="b"> \(' . $a2 . '\)</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="c"> \(' . $a3 . '\)</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="d"> \(' . $a4 . '\)</label>';

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p>Lỗi khi tải câu hỏi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
