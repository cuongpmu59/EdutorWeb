<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../env/config.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  // Nếu có POST mc_id → trả về 1 bản ghi cụ thể
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = intval($_POST['mc_id']);

    $stmt = $conn->prepare("
      SELECT mc_id, mc_topic, mc_question, 
             mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
             mc_correct_answer, mc_image_url
      FROM mc_questions
      WHERE mc_id = :mc_id
      LIMIT 1
    ");
    $stmt->execute(['mc_id' => $mc_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      echo json_encode($data);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy dữ liệu']);
    }
    exit;
  }

  // Nếu không có POST mc_id → trả về danh sách toàn bộ
  $stmt = $conn->query("
    SELECT 
      mc_id, mc_topic, mc_question, 
      mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
      mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows]);
} catch (PDOException $e) {
  echo json_encode([
    'data' => [],
    'error' => $e->getMessage()
  ]);
}

// ✅ XOÁ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
  $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);
  if (!$mc_id) {
    http_response_code(400);
    echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
    exit;
  }

  $stmt = $conn->prepare("SELECT mc_public_id FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);
  $old = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($old && !empty($old['mc_public_id'])) {
    deleteImage($old['mc_public_id']);
  }

  $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);
  echo json_encode(['success' => '✅ Đã xoá']);
  exit;
}

// ✅ THÊM / SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $mc_id = isset($_POST['save_mc_id']) ? (int) $_POST['save_mc_id'] : null;

  $topic    = trim($_POST['mc_topic'] ?? '');
  $question = trim($_POST['mc_question'] ?? '');
  $a = trim($_POST['mc_answer1'] ?? '');
  $b = trim($_POST['mc_answer2'] ?? '');
  $c = trim($_POST['mc_answer3'] ?? '');
  $d = trim($_POST['mc_answer4'] ?? '');
  $correct  = strtoupper(trim($_POST['mc_correct_answer'] ?? ''));

  if (!in_array($correct, ['A', 'B', 'C', 'D'])) {
    echo json_encode(['error' => '❌ Đáp án đúng phải là A, B, C hoặc D']);
    exit;
  }

  $image_url = null;
  $public_id = null;

  // ✅ Nếu có upload ảnh
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload = uploadImage($_FILES['image']);
    if (isset($upload['error'])) {
      echo json_encode(['error' => $upload['error']]);
      exit;
    }
    $image_url = $upload['url'];
    $public_id = $upload['public_id'];
  }

  try {
    if ($_POST['action'] === 'update' && $mc_id) {
      // Nếu có ảnh mới → xoá ảnh cũ
      if ($image_url && $public_id) {
        $stmtOld = $conn->prepare("SELECT mc_public_id FROM mc_questions WHERE mc_id = ?");
        $stmtOld->execute([$mc_id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);
        if ($old && !empty($old['mc_public_id'])) {
          deleteImage($old['mc_public_id']);
        }
      }

      $stmt = $conn->prepare("
        UPDATE mc_questions SET 
          mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, 
          mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?, 
          mc_image_url = COALESCE(?, mc_image_url),
          mc_public_id = COALESCE(?, mc_public_id)
        WHERE mc_id = ?
      ");
      $stmt->execute([$topic, $question, $a, $b, $c, $d, $correct, $image_url, $public_id, $mc_id]);

      echo json_encode(['success' => '✅ Đã cập nhật']);
      exit;
    }

    // ✅ THÊM MỚI
    $stmt = $conn->prepare("
      INSERT INTO mc_questions (
        mc_topic, mc_question, mc_answer1, mc_answer2,
        mc_answer3, mc_answer4, mc_correct_answer,
        mc_image_url, mc_public_id
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$topic, $question, $a, $b, $c, $d, $correct, $image_url, $public_id]);

    echo json_encode(['success' => '✅ Đã thêm mới']);
    exit;

  } catch (PDOException $e) {
    echo json_encode(['error' => '❌ DB Error: ' . $e->getMessage()]);
    exit;
  }
}

// ✅ HÀM UPLOAD ẢNH
function uploadImage($imageFile) {
  $cloudName = CLOUDINARY_CLOUD_NAME;
  $apiKey = CLOUDINARY_API_KEY;
  $apiSecret = CLOUDINARY_API_SECRET;
  $timestamp = time();

  $params_to_sign = ['timestamp' => $timestamp];
  ksort($params_to_sign);
  $signature_data = http_build_query($params_to_sign) . $apiSecret;
  $signature = sha1($signature_data);

  $postFields = [
    'file' => new CURLFile($imageFile['tmp_name'], $imageFile['type'], $imageFile['name']),
    'api_key' => $apiKey,
    'timestamp' => $timestamp,
    'signature' => $signature
  ];

  $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/upload");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields
  ]);

  $response = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($response, true);

  if (isset($result['secure_url'])) {
    return ['url' => $result['secure_url'], 'public_id' => $result['public_id']];
  }

  return ['error' => '❌ Upload ảnh thất bại'];
}

// ✅ HÀM XOÁ ẢNH
function deleteImage($public_id) {
  $cloudName = CLOUDINARY_CLOUD_NAME;
  $apiKey = CLOUDINARY_API_KEY;
  $apiSecret = CLOUDINARY_API_SECRET;
  $timestamp = time();

  $params_to_sign = ['public_id' => $public_id, 'timestamp' => $timestamp];
  ksort($params_to_sign);
  $signature_data = http_build_query($params_to_sign) . $apiSecret;
  $signature = sha1($signature_data);

  $postFields = [
    'public_id' => $public_id,
    'api_key' => $apiKey,
    'timestamp' => $timestamp,
    'signature' => $signature
  ];

  $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields
  ]);

  curl_exec($ch);
  curl_close($ch);
}
