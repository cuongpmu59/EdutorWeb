<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/config.php'; // File chứa cấu hình Cloudinary

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => CLOUD_NAME,
        'api_key'    => API_KEY,
        'api_secret' => API_SECRET,
    ],
    'url' => [
        'secure' => true
    ]
]);

function uploadImageToCloudinary($filePath, $publicId = null) {
    global $cloudinary;

    $options = [
        'folder' => 'mc_images'
    ];
    if ($publicId) {
        $options['public_id'] = $publicId;
        $options['overwrite'] = true;
    }

    $response = $cloudinary->uploadApi()->upload($filePath, $options);
    return [
        'url' => $response['secure_url'] ?? '',
        'public_id' => $response['public_id'] ?? ''
    ];
}

function deleteCloudinaryImage($publicId) {
    global $cloudinary;
    if ($publicId) {
        try {
            $cloudinary->uploadApi()->destroy($publicId);
        } catch (Exception $e) {
            // Không làm gì nếu xoá thất bại
        }
    }
}

function handleImageUpload($inputName = 'image', $existingImageUrl = '', $existingPublicId = '') {
    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        // Nếu có ảnh cũ thì xoá
        if ($existingPublicId) {
            deleteCloudinaryImage($existingPublicId);
        }

        $tempPath = $_FILES[$inputName]['tmp_name'];
        return uploadImageToCloudinary($tempPath);
    }

    // Không upload mới -> giữ ảnh cũ
    return [
        'url' => $existingImageUrl,
        'public_id' => $existingPublicId
    ];
}

// --- Xử lý POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic     = $_POST['topic'] ?? '';
    $question  = $_POST['question'] ?? '';
    $answer1   = $_POST['answer1'] ?? '';
    $answer2   = $_POST['answer2'] ?? '';
    $answer3   = $_POST['answer3'] ?? '';
    $answer4   = $_POST['answer4'] ?? '';
    $correct   = $_POST['answer'] ?? '';
    $mc_id     = $_POST['mc_id'] ?? '';
    $existing_image_url = $_POST['existing_image'] ?? '';
    $existing_public_id = $_POST['public_id'] ?? '';

    // Upload ảnh nếu có
    $imageData = handleImageUpload('image', $existing_image_url, $existing_public_id);
    $image_url = $imageData['url'];
    $public_id = $imageData['public_id'];

    try {
        if (!empty($mc_id)) {
            // Cập nhật
            $stmt = $conn->prepare("
                UPDATE mc_questions 
                SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=?, mc_image_public_id=?
                WHERE mc_id=?
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, $public_id, (int)$mc_id]);
        } else {
            // Thêm mới
            $stmt = $conn->prepare("
                INSERT INTO mc_questions 
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url, mc_image_public_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, $public_id]);
            $mc_id = $conn->lastInsertId();

            // Nếu có ảnh thì đổi tên public_id thành mc_{mc_id}
            if ($public_id && $mc_id) {
                $new_public_id = "mc_images/mc_" . $mc_id;
                $cloudinary->uploadApi()->rename($public_id, $new_public_id, ["overwrite" => true]);

                $new_url = 'https://res.cloudinary.com/' . CLOUD_NAME . '/image/upload/v' . time() . '/' . $new_public_id;

                // Cập nhật lại đường dẫn và public_id mới
                $stmt = $conn->prepare("
                    UPDATE mc_questions 
                    SET mc_image_url=?, mc_image_public_id=? 
                    WHERE mc_id=?
                ");
                $stmt->execute([$new_url, $new_public_id, $mc_id]);
            }
        }

        header('Location: ../pages/mc/mc_form.php');
        exit;
    } catch (PDOException $e) {
        echo 'Lỗi lưu dữ liệu: ' . $e->getMessage();
        exit;
    }
}
