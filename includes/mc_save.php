<?php
require_once __DIR__ . '/db_connection.php';
require_once 'env/config.php';

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => ['secure' => true]
]);

function uploadImage($tmpPath, $publicId = null): array {
    global $cloudinary;
    $options = ['folder' => 'mc_images'];
    if ($publicId) {
        $options['public_id'] = $publicId;
        $options['overwrite'] = true;
    }

    try {
        $response = $cloudinary->uploadApi()->upload($tmpPath, $options);
        return [
            'url' => $response['secure_url'] ?? '',
            'public_id' => $response['public_id'] ?? ''
        ];
    } catch (Exception $e) {
        return ['url' => '', 'public_id' => ''];
    }
}

function deleteImage($publicId) {
    global $cloudinary;
    if (!$publicId) return;
    try {
        $cloudinary->uploadApi()->destroy($publicId);
    } catch (Exception $e) {
        // Ignore error
    }
}

function processImageUpload(string $inputName, string $existingUrl = '', string $existingId = ''): array {
    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        deleteImage($existingId);
        return uploadImage($_FILES[$inputName]['tmp_name']);
    }
    return ['url' => $existingUrl, 'public_id' => $existingId];
}

function renameImage($oldId, $newId): array {
    global $cloudinary;
    try {
        $cloudinary->uploadApi()->rename($oldId, $newId, ['overwrite' => true]);
        $newUrl = $cloudinary->image($newId)->toUrl();
        return ['url' => $newUrl, 'public_id' => $newId];
    } catch (Exception $e) {
        return ['url' => '', 'public_id' => $oldId];
    }
}

// ======================= XỬ LÝ POST ===========================
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

    $image = processImageUpload('image', $oldUrl, $oldId);
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

            // Đổi tên ảnh nếu cần
            if ($publicId && $mc_id) {
                $newId = "mc_images/mc_$mc_id";
                $renamed = renameImage($publicId, $newId);
                if ($renamed['url']) {
                    $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=?, mc_image_public_id=? WHERE mc_id=?");
                    $stmt->execute([$renamed['url'], $renamed['public_id'], $mc_id]);
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'mc_id' => $mc_id]);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
        exit;
    }
}
http_response_code(403);
echo '<h3 style="font-family: sans-serif; color: #c00;">Truy cập không hợp lệ. Đây là endpoint xử lý, không hỗ trợ truy cập trực tiếp.</h3>';
exit;