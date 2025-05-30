<?php
// Cấu hình kết nối CSDL
$servername = "localhost";
$username = "root";
$password = "";
$database = "ten_csdl"; // 👉 đổi tên cơ sở dữ liệu cho đúng

// Kiểm tra ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID không hợp lệ!";
    exit;
}

$id = intval($_GET['id']);

// Kết nối CSDL
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Chuẩn bị câu lệnh DELETE
$stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Đã xoá câu hỏi thành công!";
} else {
    echo "Xoá thất bại: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
