<?php
// Cấu hình kết nối CSDL
$host = "sql210.infinityfree.com"; // hoặc sqlXXX.infinityfree.com tùy host bạn
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

try {
    // Thiết lập kết nối PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Thiết lập chế độ lỗi để phát hiện lỗi rõ ràng
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Thông báo lỗi rõ ràng và dừng thực thi
    die("Lỗi kết nối server! " . $e->getMessage());
}
?>
