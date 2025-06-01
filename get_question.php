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
$sql = "SELECT id, question, image, answer1, answer2, answer3, answer4, correct_answer FROM questions";
$result = $conn->query($sql);

// Hiển thị bảng
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: center;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th>ID</th><th>Câu hỏi</th><th>Hình ảnh</th>
            <th>A</th><th>B</th><th>C</th><th>D</th><th>Đúng</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        // Đường dẫn ảnh (nếu có)
        $imagePath = !empty($row['image']) ? "images/" . $row['image'] : "";
        $imgTag = $imagePath ? "<img src='$imagePath' width='100' onerror=\"this.onerror=null;this.alt='Không tìm thấy ảnh';\">" : "Không có ảnh";

        // Chuẩn hóa dữ liệu JSON để nhúng vào attribute data-row
        $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

        echo "<tr onclick='selectQuestion(JSON.parse(this.dataset.row))' data-row='$rowJson' style='cursor: pointer;'>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['question']) . "</td>";
        echo "<td>$imgTag</td>";
        echo "<td>" . htmlspecialchars($row['answer1']) . "</td>";
        echo "<td>" . htmlspecialchars($row['answer2']) . "</td>";
        echo "<td>" . htmlspecialchars($row['answer3']) . "</td>";
        echo "<td>" . htmlspecialchars($row['answer4']) . "</td>";
        echo "<td>" . strtoupper(str_replace('answer', '', $row['correct_answer'])) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Không có câu hỏi nào.";
}

$conn->close();
?>

<script>
// Gửi dữ liệu câu hỏi được chọn về parent (question_form.php)
function selectQuestion(data) {
    if (data.image) {
        data.image = "images/" + data.image; // Đường dẫn đúng thư mục ảnh
    }
    // Gửi về origin hiện tại, tăng bảo mật
    window.parent.postMessage(data, window.location.origin);
}
</script>
