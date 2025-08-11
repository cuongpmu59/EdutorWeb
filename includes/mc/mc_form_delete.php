<?php
require_once __DIR__ . '/../../includes/db_connection.php';
// require_once __DIR__ . '/../../env/config.php'; // Chứa CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET, CLOUDINARY_CLOUD_NAME

$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";
$upload_preset = "my_exam_preset";

header('Content-Type: application/json');

function getPublicIdFromUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    $parts = explode('/', $path);
    $uploadIndex = array_search('upload', $parts);
    if ($uploadIndex === false) return null;

    $publicParts = array_slice($parts, $uploadIndex + 1);
    if (preg_match('/^v\d+$/', $publicParts[0])) array_shift($publicParts);

    $filename = array_pop($publicParts);
    $publicId = substr($filename, 0, strrpos($filename, '.'));
    return implode('/', array_merge($publicParts, [$publicId]));
}

function deleteCloudinaryImage($publicId) {
    $timestamp = time();
    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1($stringToSign);

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";
    $data = [
        'public_id' => $publicId,
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mc_id = isset($_POST['mc_id']) ? filter_var($_POST['mc_id'], FILTER_VALIDATE_INT) : null;
    if (!$mc_id) {
        echo json_encode(['success' => false, 'message' => '❌ mc_id không hợp lệ.']);
        exit;
    }

    try {
        // 1. Lấy ảnh từ DB
        $stmt = $conn->prepare("SELECT image_url FROM mc_questions WHERE mc_id = ?");
        $stmt->execute([$mc_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['image_url'])) {
            $publicId = getPublicIdFromUrl($row['image_url']);
            if ($publicId) {
                $cloudRes = deleteCloudinaryImage($publicId);
                if (!isset($cloudRes['result']) || $cloudRes['result'] !== 'ok') {
                    echo json_encode(['success' => false, 'message' => '❌ Lỗi xoá ảnh Cloudinary.', 'cloud' => $cloudRes]);
                    exit;
                }
            }
        }

        // 2. Xoá câu hỏi trong DB
        $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
        $stmt->execute([$mc_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Xoá thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Không tìm thấy câu hỏi để xoá.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ Phương thức không hợp lệ.']);
}
