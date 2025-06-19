<?php
require 'db_connection.php'; // Kết nối CSDL

// Hàm tự động bọc \(...\) nếu trong nội dung chưa có LaTeX
function latexWrap($str) {
    $str = str_replace('\\\\', '\\', $str); // Khôi phục dấu \ bị thoát

    // Nếu đã có cú pháp LaTeX hoặc ký hiệu đặc trưng, giữ nguyên hoặc bọc lại
    if (
        strpos($str, '\(') !== false ||
        strpos($str, '\[') !== false ||
        strpos($str, '$') !== false
    ) {
        return $str; // đã có LaTeX
    }

    // Nếu chứa các biểu thức toán thường gặp, ta bọc
    if (preg_match('/(\\\frac|\\\sqrt|\\\sum|\\\int|[_^]|\\\begin|\\\end)/', $str)) {
        return '\(' . $str . '\)';
    }

    // Ngược lại, là văn bản thuần túy → giữ nguyên
    return htmlspecialchars($str); // Tránh lỗi HTML nếu có ký tự đặc biệt
}


// Bắt đầu HTML
echo '<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  
  <script>
    window.MathJax = {
    tex: {
        inlineMath: [['\\(', '\\)']],
        displayMath: [['\\[', '\\]']],
        processEscapes: true
    },
    svg: {
    fontCache: 'global'
        }
    };
</script>

  <script id="MathJax-script" async
          src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f9f9f9;
    }
    .question {
        background: #fff;
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.05);
    }
    .question p {
        font-weight: bold;
    }
    .question label {
        display: block;
        margin-bottom: 6px;
    }
    img {
        max-width: 250px;
        display: block;
        margin: 10px auto;
    }
  </style>
</head>
<body>
<h2>Danh sách câu hỏi</h2>
';

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
        echo '<p>Câu ' . $qnum . ': ' . latexWrap($question) . '</p>';

        if (!empty($image)) {
            echo "<img src='images/$image' alt='Hình minh họa'><br>";
        }

        echo '<label><input type="radio" name="q' . $qnum . '" value="a"> ' . latexWrap($a1) . '</label>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="b"> ' . latexWrap($a2) . '</label>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="c"> ' . latexWrap($a3) . '</label>';
        echo '<label><input type="radio" name="q' . $qnum . '" value="d"> ' . latexWrap($a4) . '</label>';

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Lỗi khi tải câu hỏi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo '</body></html>';
?>
