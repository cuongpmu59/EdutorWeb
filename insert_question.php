<?php
require 'db_connection.php';
require 'dotenv.php';
require 'vendor/autoload.php';

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Configuration\Configuration;

// Cấu hình Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
    ],
    'url' => [
        'secure' => true
    ]
]);

function get_post($key) {
    return trim($_POST[$key] ?? '');
}

$question      = get_post('question');
$answer1       = get_post('answer1');
$answer2       = get_post('answer2');
$answer3       = get_post('answer3');
$answer4       = get_post('answer4');
$correct       = get_post('correct_answer');
$topic         = get_post('topic');
$image_url     = get_post('image_url');

try {
    // 1. Chèn câu hỏi mới
    $stmt = $conn->prepare("INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, topic, image) VALUES (?, ?, ?, ?, ?, ?, ?, '')");
    $stmt->execute([$question, $answer1, $answer2, $answer3, $answer4, $correct, $topic]);

    // 2. Lấy ID vừa chèn
    $id = $conn->lastInsertId();

    // 3. Nếu có ảnh tạm, đổi tên thành pic_ID
    if ($image_url && preg_match('/\/([^\/]+)\.(jpg|jpeg|png|gif)$/i', $image_url, $m)) {
        $old_public_id = pathinfo($m[1], PATHINFO_FILENAME); // temp_xxx
        $ext = strtolower($m[2]);
        $new_public_id = "pic_$id";

        try {
            $api = new AdminApi();
            $api->rename($old_public_id, $new_public_id, ['overwrite' => true]);

            // 4. Tạo URL mới theo chuẩn Cloudinary
            $new_url = "https://res.cloudinary.com/{$_ENV['CLOUDINARY_CLOUD_NAME']}/image/upload/{$new_public_id}.{$ext}";

            // 5. Cập nhật lại cột image
            $stmt = $conn->prepare("UPDATE questions SET image = ? WHERE id = ?");
            $stmt->execute([$new_url, $id]);
        } catch (Exception $e) {
            error_log("❌ Rename error: " . $e->getMessage());
            // Nếu rename lỗi thì giữ nguyên không cập nhật image
        }
    }

    echo json_encode(["status" => "success", "id" => $id]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
