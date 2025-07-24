<?php
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/config.php';

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

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
    $options = ['folder' => 'mc_images'];
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
            // Bỏ qua lỗi
        }
    }
}

function handleImageUpload($inputName = 'image', $existingUrl = '', $existingId = '') {
    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        if ($existingId) deleteCloudinaryImage($existingId);
        $tempPath = $_FILES[$inputName]['tmp_name'];
        return uploadImageToCloudinary($tempPath);
    }
    return ['url' => $existingUrl, 'public_id' => $existingId];
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic    = $_POST['topic']    ?? '';
    $question = $_POST['question'] ?? '';
    $a1       = $_POST['answer1']  ?? '';
    $a2       = $_POST['answer2']  ?? '';
    $a3       = $_POST['answer3']  ?? '';
    $a4       = $_POST['answer4']  ?? '';
    $correct  = $_POST['answer']   ?? '';
    $mc_id    = $_POST['mc_id']    ?? '';
    $oldUrl   = $_POST['existing_image'] ?? '';
    $oldId    = $_POST['public_id']      ?? '';

    $image = handleImageUpload('image', $oldUrl, $oldId);
    $imageUrl = $image['url'];
    $publicId = $image['public_id'];

    try {
        if ($mc_id) {
            // Cập nhật
            $stmt = $conn->prepare("
                UPDATE mc_questions SET 
                    mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, 
                    mc_answer3=?, mc_answer4=?, mc_correct_answer=?, 
                    mc_image_url=?, mc_image_public_id=?
                WHERE mc_id=?
            ");
            $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $imageUrl, $publicId, (int)$mc_id]);
        } else {
            // Thêm mới
            $stmt = $conn->prepare("
                INSERT INTO mc_questions 
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
                 mc_correct_answer, mc_image_url, mc_image_public_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $imageUrl, $publicId]);
            $mc_id = $conn->lastInsertId();

            // Đổi tên ảnh nếu có
            if ($publicId && $mc_id) {
                $newId = "mc_images/mc_$mc_id";
                $cloudinary->uploadApi()->rename($publicId, $newId, ['overwrite' => true]);

                // Cập nhật lại CSDL
                $stmt = $conn->prepare("
                    UPDATE mc_questions 
                    SET mc_image_url=?, mc_image_public_id=? 
                    WHERE mc_id=?
                ");
                $newUrl = $cloudinary->image($newId)->toUrl();
                $stmt->execute([$newUrl, $newId, $mc_id]);
            }
        }

        // Trả về JSON nếu gọi qua AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'mc_id' => $mc_id]);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
        exit;
    }
}
