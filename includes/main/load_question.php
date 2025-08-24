<?php
require '../../includes/db_connection.php'; // Kết nối CSDL

function latexWrap($str) {
    $str = str_replace('\\\\', '\\', $str); // khôi phục dấu gạch chéo

    // Nếu đã chứa \[ hoặc \begin thì giữ nguyên (block math hoặc bảng)
    if (
        strpos($str, '\[') !== false ||
        strpos($str, '\begin') !== false ||
        strpos($str, '$$') !== false
    ) {
        return $str;
    }

    // Nếu có biểu thức toán thì bọc inline math
    if (preg_match('/(\\\frac|\\\sqrt|\\\sum|\\\int|[_^]|\\\cdot|\\\leq|\\\geq|\\\neq|\\\pi|{.+})/', $str)) {
        return '\(' . $str . '\)';
    }

    return htmlspecialchars($str);
}

$selectedTopic = $_GET['topic'] ?? 'TênChủĐềMặcĐịnh';

try {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE topic = :topic ORDER BY RAND() LIMIT 20");
    $stmt->bindParam(':topic', $selectedTopic);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $index => $q) {
        $qnum = $index + 1;
        $id = (int)$q['id'];
        $image = $q['image'];

        $question = $q['question'];
        $answers = [
            'a' => $q['answer1'],
            'b' => $q['answer2'],
            'c' => $q['answer3'],
            'd' => $q['answer4']
        ];

        $correct = strtolower(trim($q['correct_answer']));
        $shuffled = $answers;
        $keys = array_keys($shuffled);
        shuffle($keys);

        $newCorrect = '';
        foreach ($keys as $newLetter) {
            if ($answers[$newLetter] === $answers[$correct]) {
                $newCorrect = $newLetter;
                break;
            }
        }

        echo "<div class='question' id='q$qnum' data-q='q$qnum' data-correct='$newCorrect'>";
        echo '<p><strong>Câu ' . $qnum . ':</strong> ' . latexWrap($question) . '</p>';

        if (!empty($image)) {
            echo "<img src='" . htmlspecialchars($image) . "' alt='Hình minh họa' style='width: 250px; display:block; margin: 10px auto;'><br>";
        }

        foreach ($keys as $letter) {
            $ansText = $answers[$letter];
            echo "<label class='option' data-opt='$letter'><input type='radio' name='q$qnum' value='$letter'> " . latexWrap($ansText) . "</label><br>";
        }

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p>Lỗi khi tải câu hỏi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
