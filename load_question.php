<?php
require 'db_connection.php'; // Kết nối CSDL

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

    // Nếu có các biểu thức toán học nhỏ thì bọc lại
    if (preg_match('/(\\\frac|\\\sqrt|\\\sum|\\\int|[_^]|\\\cdot|\\\leq|\\\geq|\\\neq|\\\pi|{.+})/', $str)) {
        return '\(' . $str . '\)';
    }

    // Không có gì đặc biệt → thoát HTML an toàn
    return htmlspecialchars($str);
}



// Lấy chủ đề từ GET hoặc gán mặc định
$selectedTopic = $_GET['topic'] ?? 'TênChủĐềMặcĐịnh';

try {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE topic = :topic ORDER BY RAND() LIMIT 20");
    $stmt->bindParam(':topic', $selectedTopic);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as $index => $q) {
        $qnum = $index + 1;
        $id = (int)$q['id'];
        $image = htmlspecialchars($q['image']);

        $question = $q['question'];
        $answers = [
            'a' => $q['answer1'],
            'b' => $q['answer2'],
            'c' => $q['answer3'],
            'd' => $q['answer4']
        ];

        $correct = trim($q['correct_answer']);

        // Random thứ tự đáp án
        $shuffled = $answers;
        $keys = array_keys($shuffled);
        shuffle($keys);

        // Xác định đáp án đúng sau khi xáo trộn
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
            echo "<img src='images/$image' alt='Hình minh họa' style='width: 250px; display:block; margin: 10px auto;'><br>";
        }

        // In đáp án theo thứ tự mới
        foreach ($keys as $letter) {
            $newValue = $letter;
            $ansText = $answers[$letter];
            echo "<label class='option' data-opt='$newValue'><input type='radio' name='q$qnum' value='$newValue'> " . latexWrap($ansText) . "</label><br>";
        }

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p>Lỗi khi tải câu hỏi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
