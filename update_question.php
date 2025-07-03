<?php
require 'db_connection.php';
require 'dotenv.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$topic = $_POST['topic'] ?? '';
$question = $_POST['question'] ?? '';
$answer1 = $_POST['answer1'] ?? '';
$answer2 = $_POST['answer2'] ?? '';
$answer3 = $_POST['answer3'] ?? '';
$answer4 = $_POST['answer4'] ?? '';
$correct = $_POST['correct_answer'] ?? '';
$imageUrl = $_POST['image_url'] ?? '';
$deleteImage = isset($_POST['delete_image']);

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // Nếu cần xóa ảnh cũ trên Cloudinary
    if ($deleteImage) {
        $publicId = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_FILENAME);
        if ($publicId) {
            $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
            $apiKey = getenv('CLOUDINARY_API_KEY');
            $apiSecret = getenv('CLOUDINARY_API_SECRET');
            $url = "https://api.cloudinary.com/v1_1/$cloudName/image/destroy";

            $timestamp = time();
            $signature = sha1("public_id=$publicId&timestamp=$timestamp$apiSecret");

            $data = [
                'public_id' => $publicId,
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ]);
            curl_exec($ch);
            curl_close($ch);
        }
        $imageUrl = '';
    }

    $stmt = $conn->prepare("UPDATE questions SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?, topic=?, image=? WHERE id=?");
    $stmt->execute([$question, $answer1, $answer2, $answer3, $answer4, $correct, $topic, $imageUrl, $id]);

    echo json_encode([
        'status' => 'success',
        'message' => '✅ Cập nhật thành công!',
        'new_image_url' => $imageUrl
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => '❌ Lỗi: ' . $e->getMessage()]);
}
