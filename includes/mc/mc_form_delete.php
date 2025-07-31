<?php
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../env/dotenv.php';
require_once __DIR__ . '/../env/config.php'; // Cấu hình cloudinary

use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

try {
  // Kiểm tra request
  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['mc_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu hoặc sai phương thức yêu cầu']);
    exit;
  }

  $mc_id = intval($_POST['mc_id']);

  // Lấy thông tin ảnh trước khi xoá
  $stmt = $conn->prepare("SELECT mc_image FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy bản ghi']);
    exit;
  }

  $image_url = $row['mc_image'];
  $public_id = null;

  // Xử lý URL để lấy public_id nếu là ảnh Cloudinary
  if ($image_url && strpos($image_url, 'res.cloudinary.com') !== false) {
    $path = parse_url($image_url, PHP_URL_PATH);        // /your-cloud-name/image/upload/v1690000000/folder/abc_xyz.jpg
    $filename = basename($path);                        // abc_xyz.jpg
    $public_id = preg_replace('/\.[^.]+$/', '', $filename); // abc_xyz
    // Nếu bạn dùng folder trên Cloudinary thì cần lấy phần đầy đủ:
    // Ví dụ nếu ảnh URL là .../mc_images/abc_xyz.jpg → public_id = mc_images/abc_xyz
    $parts = explode('/', $path);
    $uploadIndex = array_search('upload', $parts);
    if ($uploadIndex !== false && isset($parts[$uploadIndex + 1])) {
      $public_path = array_slice($parts, $uploadIndex + 1);
      $public_id = preg_replace('/\.[^.]+$/', '', implode('/', $public_path));
    }
  }

  // Xoá bản ghi trong CSDL
  $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);

  // Xoá ảnh Cloudinary nếu có
  if ($public_id) {
    $uploadApi = new UploadApi();
    $uploadApi->destroy($public_id);
  }

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Lỗi máy chủ: ' . $e->getMessage()
  ]);
}
