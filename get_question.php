<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = "sql210.infinityfree.com";
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

// Kết nối MySQL
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ bảng
$sql = "SELECT id, question_text, answer1, answer2, answer3, answer4, correct_answer, image FROM questions";
$result = $conn->query($sql);

// Hiển thị bảng
if ($result->num_rows > 0) {
    echo "<table border='1' id='questionTable' style='border-collapse: collapse; width: 100%; text-align: center;'>";
    echo "<tr style='background-color: #f2f2f2;'><th>ID</th><th>Câu hỏi</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Đúng</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        // Mỗi dòng có sự kiện onclick
        echo "<tr onclick='selectQuestion(" . json_encode($row) . ")' style='cursor:pointer;'>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["question_text"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["answer1"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["answer2"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["answer3"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["answer4"]) . "</td>";
        echo "<td>" . strtoupper(str_replace('answer', '', $row["correct_answer"])) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Không có câu hỏi nào.";
}

$conn->close();
?>

<script>
// Gửi dữ liệu về parent window (question_form.html)
function selectQuestion(data) {
    if (data.image) {
        data.image = "uploads/" + data.image; // điều chỉnh theo thư mục ảnh của bạn
    }
    window.parent.postMessage(data, "*");
}
</script>
