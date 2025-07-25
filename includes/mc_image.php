<?php
// includes/mc_image.php
// Xử lý ảnh minh họa gửi lên qua AJAX (upload, đổi tên, trả kết quả)

require_once __DIR__ . '/../env/config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Đảm bảo Cloudinary SDK được autoload

use Cloudinary\Cloudinary;

// Kiểm tra nếu gọi trực tiếp không phải POST thì chặn
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Truy cập không hợp lệ']);
    exit;
}

// ======= Thiết lập Cloudinary =======
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => ['secure' => true]
]);

// ======= Hàm xóa ảnh cũ nếu cần =======
function deleteImage($publicId, $cloudinary) {
    if (!$publicId) return;
    try {
        $cloudinary->uploadApi()->destroy($publicId);
    } catch (Exception $e) {
        // Bỏ qua lỗi
    }
}

// ======= Hàm upload ảnh mới =======
function uploadImage($tmpPath, $cloudinary, $publicId = null): array {
    $options = ['folder' => 'mc_images'];
    if ($publicId) {
        $options['public_id'] = $publicId;
        $options['overwrite'] = true;
    }

    try {
        $res = $cloudinary->uploadApi()->upload($tmpPath, $options);
        return [
            'url' => $res['secure_url'] ?? '',
            'public_id' => $res['public_id'] ?? ''
        ];
    } catch (Exception $e) {
        return ['url' => '', 'public_id' => ''];
    }
}

// ======= Hàm rename ảnh =======
function renameImage($oldId, $newId, $cloudinary): array {
    try {
        $cloudinary->uploadApi()->rename($oldId, $newId, ['overwrite' => true]);
        $newUrl = $cloudinary->image($newId)->toUrl();
        return ['url' => $newUrl, 'public_id' => $newId];
    } catch (Exception $e) {
        return ['url' => '', 'public_id' => $oldId];
    }
}

// ======= Hàm xử lý toàn bộ ảnh =======
function processImageUpload(string $inputName, string $oldUrl, string $oldId, $mc_id = null, $cloudinary): array {
    if (!empty($_FILES[$inputName]['name']) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        // Xoá ảnh cũ nếu có
        deleteImage($oldId, $cloudinary);

        // Upload ảnh mới
        $upload = uploadImage($_FILES[$inputName]['tmp_name'], $cloudinary);
        $newUrl = $upload['url'];
        $newId  = $upload['public_id'];

        // Nếu có mc_id => rename luôn
        if ($newId && $mc_id) {
            $targetId = "mc_images/mc_$mc_id";
            $renamed = renameImage($newId, $targetId, $cloudinary);
            return [
                'url'       => $renamed['url'] ?: $newUrl,
                'public_id' => $renamed['public_id'] ?: $newId
            ];
        }

        return [
            'url'       => $newUrl,
            'public_id' => $newId
        ];
    }

    // Trường hợp không upload mới => giữ nguyên
    return [
        'url'       => $oldUrl,
        'public_id' => $oldId
    ];
}

// ======= XỬ LÝ DỮ LIỆU GỬI TỪ FORM =======

$oldUrl = $_POST['existing_image'] ?? '';
$oldId  = $_POST['public_id']      ?? '';
$mc_id  = $_POST['mc_id']          ?? null;

$result = processImageUpload('image', $oldUrl, $oldId, $mc_id, $cloudinary);

// ======= TRẢ KẾT QUẢ JSON =======
header('Content-Type: application/json');
echo json_encode([
    'success'   => (bool)$result['url'],
    'url'       => $result['url'],
    'public_id' => $result['public_id']
]);
exit;
