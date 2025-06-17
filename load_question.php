<?php
require 'db_connection.php'; // kết nối đến CSDL

try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id ASC");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $index => $q) {
        $qnum = $index + 1;
        $id = htmlspecialchars($q['id']);
        $question = htmlspecialchars($q['question']);
        $image = htmlspecialchars($q['image']);
        $a1 = htmlspecialchars($q['answer1']);
        $a2 = htmlspecialchars($q['answer2']);
        $a3 = htmlspecialchars($q['answer3']);
        $a4 = htmlspecialchars($q['answer4']);

        //echo "<div class='question' data-q='q$qnum'>";
        echo "<div class='question'>{$row['question_text']}</div>";


        echo "<p>Câu $qnum: \\($question\\)</p>";

        if (!empty($image)) {
            echo "<img src='images/$image' alt='Hình minh họa' style='width: 250px; display:block; margin: 10px auto;'><br>";
        }

        echo "<label><input type='radio' name='q$qnum' value='a'> \\($a1\\)</label><br>";
        echo "<label><input type='radio' name='q$qnum' value='b'> \\($a2\\)</label><br>";
        echo "<label><input type='radio' name='q$qnum' value='c'> \\($a3\\)</label><br>";
        echo "<label><input type='radio' name='q$qnum' value='d'> \\($a4\\)</label>";

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p>Lỗi khi tải câu hỏi: " . $e->getMessage() . "</p>";
}
?>
