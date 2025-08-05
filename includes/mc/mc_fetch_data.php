<?php
require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json');
// require_once __DIR__ . '/../../includes/db_connection.php';
// require_once __DIR__ . '/../../env/config.php';

// header('Content-Type: application/json');
// header('X-Content-Type-Options: nosniff');

try {
  // ✅ DELETE - Nếu có POST delete_mc_id
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);

    if (!$mc_id) {
      echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }

    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = :mc_id");
    $stmt->execute(['mc_id' => $mc_id]);

    if ($stmt->rowCount() > 0) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy câu hỏi để xoá']);
      http_response_code(404);
    }
    exit;
  }

  // ✅ GET một bản ghi nếu có POST mc_id
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);

    if (!$mc_id) {
      echo json_encode(['error' => '❌ mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }

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
      http_response_code(404);
    }
    exit;
  }

  // ✅ GET toàn bộ danh sách (nếu không có POST mc_id hoặc delete_mc_id)
  $stmt = $conn->query("
    SELECT mc_id, mc_topic, mc_question, 
           mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
           mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows]);

} catch (PDOException $e) {
  echo json_encode(['data' => [], 'error' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
  http_response_code(500);
}

// ob_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// require_once __DIR__ . '/../../includes/db_connection.php';
// require_once __DIR__ . '/../../env/config.php';

// header('Content-Type: application/json');
// header('X-Content-Type-Options: nosniff');

// // ✅ LẤY TOÀN BỘ DỮ LIỆU CHO DATATABLES
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//   try {
//     $stmt = $conn->query("
//       SELECT mc_id, mc_topic, mc_question, 
//              mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
//              mc_correct_answer, 
//              mc_image_url
//       FROM mc_questions
//       ORDER BY mc_id DESC
//     ");
//     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     echo json_encode(['data' => $rows]);
//     exit;
//   } catch (PDOException $e) {
//     echo json_encode(['data' => [], 'error' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
//     http_response_code(500);
//     exit;
//   }
// }

// // ✅ LẤY 1 DÒNG DỮ LIỆU
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
//   $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);

//   if (!$mc_id) {
//     echo json_encode(['error' => '❌ mc_id không hợp lệ']);
//     http_response_code(400);
//     exit;
//   }

//   $stmt = $conn->prepare("
//     SELECT mc_id, mc_topic, mc_question, 
//            mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
//            mc_correct_answer,
//            mc_image_url,
//            mc_public_id
//     FROM mc_questions
//     WHERE mc_id = :mc_id
//     LIMIT 1
//   ");
//   $stmt->execute(['mc_id' => $mc_id]);
//   $data = $stmt->fetch(PDO::FETCH_ASSOC);

//   if ($data) {
//     echo json_encode($data);
//   } else {
//     echo json_encode(['error' => '❌ Không tìm thấy dữ liệu']);
//     http_response_code(404);
//   }
//   exit;
// }

// // ✅ XOÁ DỮ LIỆU
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
//   $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);

//   if (!$mc_id) {
//     echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
//     http_response_code(400);
//     exit;
//   }

//   // Xoá ảnh nếu có
//   $stmtOld = $conn->prepare("SELECT mc_public_id FROM mc_questions WHERE mc_id = ?");
//   $stmtOld->execute([$mc_id]);
//   $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

//   if ($old && !empty($old['mc_public_id'])) {
//     deleteImage($old['mc_public_id']);
//   }

//   $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
//   $stmt->execute([$mc_id]);

//   if ($stmt->rowCount() > 0) {
//     echo json_encode(['success' => '✅ Đã xoá']);
//   } else {
//     echo json_encode(['error' => '❌ Không tìm thấy dòng để xoá']);
//     http_response_code(404);
//   }
//   exit;
// }

// // ✅ LƯU (THÊM MỚI HOẶC CẬP NHẬT)
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
//   $mc_id = isset($_POST['mc_id']) ? (int) $_POST['mc_id'] : null;
//   $topic = $_POST['mc_topic'] ?? '';
//   $question = $_POST['mc_question'] ?? '';
//   $a = $_POST['mc_answer1'] ?? '';
//   $b = $_POST['mc_answer2'] ?? '';
//   $c = $_POST['mc_answer3'] ?? '';
//   $d = $_POST['mc_answer4'] ?? '';
//   $correct = $_POST['mc_correct_answer'] ?? '';
//   $image_url = null;
//   $public_id = null;

//   // ✅ Nếu có ảnh upload
//   if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//     $upload = uploadImage($_FILES['image']);
//     if (isset($upload['error'])) {
//       echo json_encode(['error' => $upload['error']]);
//       exit;
//     }
//     $image_url = $upload['url'];
//     $public_id = $upload['public_id'];
//   }

//   try {
//     if ($_POST['action'] === 'update' && $mc_id) {
//       // Xoá ảnh cũ nếu có
//       $stmtOld = $conn->prepare("SELECT mc_public_id FROM mc_questions WHERE mc_id = ?");
//       $stmtOld->execute([$mc_id]);
//       $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

//       if ($public_id && $old && !empty($old['mc_public_id'])) {
//         deleteImage($old['mc_public_id']);
//       }

//       $stmt = $conn->prepare("
//         UPDATE mc_questions SET
//           mc_topic = ?, mc_question = ?, 
//           mc_answer1 = ?, mc_answer2 = ?, 
//           mc_answer3 = ?, mc_answer4 = ?, 
//           mc_correct_answer = ?,
//           mc_image_url = COALESCE(?, mc_image_url),
//           mc_public_id = COALESCE(?, mc_public_id)
//         WHERE mc_id = ?
//       ");
//       $stmt->execute([$topic, $question, $a, $b, $c, $d, $correct, $image_url, $public_id, $mc_id]);

//       echo json_encode(['success' => '✅ Đã cập nhật']);
//       exit;
//     }

//     // ✅ THÊM MỚI
//     $stmt = $conn->prepare("
//       INSERT INTO mc_questions (
//         mc_topic, mc_question, mc_answer1, mc_answer2, 
//         mc_answer3, mc_answer4, mc_correct_answer, 
//         mc_image_url, mc_public_id
//       ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
//     ");
//     $stmt->execute([$topic, $question, $a, $b, $c, $d, $correct, $image_url, $public_id]);

//     echo json_encode(['success' => '✅ Đã thêm mới']);
//     exit;

//   } catch (Exception $e) {
//     echo json_encode(['error' => '❌ Lỗi xử lý: ' . $e->getMessage()]);
//     exit;
//   }
// }

// // ============================
// // 🔧 HÀM UPLOAD / XOÁ ẢNH
// // ============================
// function uploadImage($imageFile) {
//   $cloudName = CLOUDINARY_CLOUD_NAME;
//   $apiKey    = CLOUDINARY_API_KEY;
//   $apiSecret = CLOUDINARY_API_SECRET;

//   $timestamp = time();
//   $params_to_sign = ['timestamp' => $timestamp];
//   ksort($params_to_sign);
//   $signature_data = http_build_query($params_to_sign) . $apiSecret;
//   $signature = sha1($signature_data);

//   $postFields = [
//     'file' => new CURLFile($imageFile['tmp_name'], $imageFile['type'], $imageFile['name']),
//     'api_key' => $apiKey,
//     'timestamp' => $timestamp,
//     'signature' => $signature,
//   ];

//   $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
//   curl_setopt_array($ch, [
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POST => true,
//     CURLOPT_POSTFIELDS => $postFields,
//   ]);

//   $response = curl_exec($ch);
//   if (curl_errno($ch)) {
//     return ['error' => '❌ Lỗi upload ảnh'];
//   }

//   curl_close($ch);
//   $result = json_decode($response, true);
//   if (isset($result['secure_url'])) {
//     return ['url' => $result['secure_url'], 'public_id' => $result['public_id']];
//   }

//   return ['error' => '❌ Upload ảnh thất bại'];
// }

// function deleteImage($public_id) {
//   $cloudName = CLOUDINARY_CLOUD_NAME;
//   $apiKey    = CLOUDINARY_API_KEY;
//   $apiSecret = CLOUDINARY_API_SECRET;
//   $timestamp = time();

//   $params_to_sign = ['public_id' => $public_id, 'timestamp' => $timestamp];
//   ksort($params_to_sign);
//   $signature_data = http_build_query($params_to_sign) . $apiSecret;
//   $signature = sha1($signature_data);

//   $postFields = [
//     'public_id' => $public_id,
//     'api_key' => $apiKey,
//     'timestamp' => $timestamp,
//     'signature' => $signature,
//   ];

//   $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
//   curl_setopt_array($ch, [
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POST => true,
//     CURLOPT_POSTFIELDS => $postFields,
//   ]);

//   curl_exec($ch);
//   curl_close($ch);
// }
