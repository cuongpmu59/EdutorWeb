<?php
// Cấu hình kết nối CSDL
$host = "sql210.infinityfree.com"; // hoặc sqlXXX.infinityfree.com tùy host bạn
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    echo "Kết nối thành công!";
} catch (PDOException $e) {
    echo "Lỗi kết nối server! " . $e->getMessage();
}
?>

