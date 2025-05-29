<?php
// Cấu hình kết nối MySQL - thay thông tin tương ứng trên InfinityFree
$host = "sql210.infinityfree.com"; // ví dụ: sql304.epizy.com
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

// Kết nối
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn bảng questions
$sql = "SELECT id, question_text FROM questions";
$result = $conn->query($sql);

// Hiển thị bảng HTML
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Question</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["question_text"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "Không có câu hỏi nào.";
}
$conn->close();
?>
