<?php
require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Format;

// ⚙️ Cấu hình Cloudinary (nếu chưa dùng CLOUDINARY_URL)
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'your_cloud_name',
        'api_key'    => 'your_api_key',
        'api_secret' => 'your_api_secret'
    ],
    'url' => [
        'secure' => true
    ]
]);

// Hàm xử lý ảnh minh họa
function handleImageUploadCloudinary($inputName = 'image') {
    global $cloudinary;

    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $tmpFilePath = $_FILES[$inputName]['tmp_name'];

        try {
            // Upload tạm thời với tên mặc định
            $result = $cloudinary->uploadApi()->upload($tmpFilePath);
            return $result['public_id'];
        } catch (Exception $e) {
            return ''; // Upload lỗi => bỏ qua
        }
    }

    return '';
}

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic     = $_POST['topic'] ?? '';
    $question  = $_POST['question'] ?? '';
    $answer1   = $_POST['answer1'] ?? '';
    $answer2   = $_POST['answer2'] ?? '';
    $answer3   = $_POST['answer3'] ?? '';
    $answer4   = $_POST['answer4'] ?? '';
    $correct   = $_POST['answer'] ?? '';
    $mc_id     = $_POST['mc_id'] ?? '';
    $existingImage = $_POST['existing_image'] ?? '';

    $publicId = handleImageUploadCloudinary('image'); // Trả về public_id nếu có ảnh mới
    $image_url = $existingImage;

    try {
        if (!empty($mc_id)) {
            // Cập nhật
            $stmt = $conn->prepare("
                UPDATE mc_questions 
                SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=?
                WHERE mc_id=?
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, (int)$mc_id]);

            // Nếu có ảnh mới thì đổi tên ảnh và cập nhật lại DB
            if ($publicId) {
                $newPublicId = 'mc_' . (int)$mc_id;
                $cloudinary->uploadApi()->rename($publicId, $newPublicId, ['overwrite' => true]);
                $newUrl = $cloudinary->image($newPublicId)->toUrl();
                
                $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=? WHERE mc_id=?");
                $stmt->execute([$newUrl, (int)$mc_id]);
            }
        } else {
            // Thêm mới
            $stmt = $conn->prepare("
                INSERT INTO mc_questions 
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, '']);
            $newId = $conn->lastInsertId();

            // Nếu có ảnh mới thì đổi tên theo mc_{id} và cập nhật lại DB
            if ($publicId) {
                $newPublicId = 'mc_' . $newId;
                $cloudinary->uploadApi()->rename($publicId, $newPublicId, ['overwrite' => true]);
                $newUrl = $cloudinary->image($newPublicId)->toUrl();

                $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=? WHERE mc_id=?");
                $stmt->execute([$newUrl, $newId]);
            }
        }

        header('Location: ../pages/mc/mc_form.php');
        exit;
    } catch (PDOException $e) {
        echo 'Lỗi lưu dữ liệu: ' . $e->getMessage();
        exit;
    } catch (Exception $e) {
        echo 'Lỗi xử lý ảnh: ' . $e->getMessage();
        exit;
    }
}
