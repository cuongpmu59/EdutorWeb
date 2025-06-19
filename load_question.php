<?php
require 'db_connection.php'; // Kết nối CSDL

// Hàm tự động bọc \(...\) nếu trong nội dung chưa có LaTeX
function latexWrap($str) {
    $str = str_replace('\\\\', '\\', $str); // Khôi phục lại dấu \
    if (
        strpos($str, '\(') === false &&
        strpos($str, '\[') === false &&
        strpos($str, '$') === false
    ) {
        return '\(' . $str . '\)';
    }
    return $str;
}

try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id ASC");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $index => $q) {
        $qnum = $index + 1;

        $id = (int)$q['id'];
        $image = htmlspecialchars($q['image']);

        $question = $q['question'];
        $a1 = $q['answer1'];
        $a2 = $q['answer2'];
        $a3 = $q['answer3'];
        $a4 = $q['answer4'];

        echo "<div class='question' data-q='q$qnum'>";

        // Hiển thị câu hỏi và đáp án: chỉ bọc \(...\) nếu chưa có LaTeX
        echo '<p>Câu ' . $qnum . ': ' . latexWrap($question) . '</p>';

        if (!empty($image)) {
            echo "<img src='images/$image' alt='Hình minh họa' style='width: 250px; display:block; margin: 10px auto;'><br>";
        }

        echo '<label><input type="radio" name="q' . $qnum . '" value="a"> ' . latexWrap($a1) . '</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="b"> ' . latexWrap($a2) . '</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="c"> ' . latexWrap($a3) . '</label><br>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="d"> ' . latexWrap($a4) . '</label>';

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p>Lỗi khi tải câu hỏi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
