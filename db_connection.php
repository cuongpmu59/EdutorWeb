<?php
// Kết nối CSDL
$host = "sql210.infinityfree.com";
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Hiển thị dữ liệu
echo '<!DOCTYPE html>';
echo '<html><head><meta charset="UTF-8"><title>Hiển thị tiếng Việt</title></head><body>';

$stmt = $conn->query("SELECT * FROM questions");
echo "<table border='1'>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>" . htmlspecialchars($row['question_text'], ENT_QUOTES, 'UTF-8') . "</td></tr>";
}
echo "</table>";

echo '</body></html>';
?>
