<?php
// header('Content-Type: application/json; charset=utf-8');

// // Chỉ cho phép POST
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']);
//     exit;
// }

// require_once __DIR__ . '/../../env/config.php';

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// $conn->set_charset("utf8mb4");

// try {
//     // Lấy dữ liệu & loại bỏ khoảng trắng
//     $data = array_map('trim', $_POST);
//     $mc_id = filter_var($data['mc_id'] ?? null, FILTER_VALIDATE_INT);

//     // Danh sách trường bắt buộc
//     $requiredFields = [
//         'mc_topic', 'mc_question', 
//         'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 
//         'mc_correct_answer'
//     ];

//     foreach ($requiredFields as $field) {
//         if (empty($data[$field])) {
//             echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
//             exit;
//         }
//     }

//     // Cho phép mc_image_url để trống
//     $mc_image_url = !empty($data['mc_image_url']) ? $data['mc_image_url'] : null;

//     if ($mc_id) {
//         // Cập nhật
//         $sql = "UPDATE mc_questions 
//                 SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? 
//                 WHERE mc_id=?";
//         $params = [
//             $data['mc_topic'], $data['mc_question'], 
//             $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'], 
//             $data['mc_correct_answer'], $mc_image_url, $mc_id
//         ];
//         $types = "ssssssssi";
//     } else {
//         // Thêm mới
//         $sql = "INSERT INTO mc_questions 
//                 (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
//                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//         $params = [
//             $data['mc_topic'], $data['mc_question'], 
//             $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'], 
//             $data['mc_correct_answer'], $mc_image_url
//         ];
//         $types = "ssssssss";
//     }

//     // Thực thi
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param($types, ...$params);
//     $stmt->execute();
//     $stmt->close();

//     echo json_encode([
//         'status' => 'success',
//         'message' => $mc_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
//     ]);

// } catch (mysqli_sql_exception $e) {
//     echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
// } finally {
//     $conn->close();
// }

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']));
}

require_once __DIR__ . '/../../env/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $data = array_map('trim', $_POST);
    $mc_id = filter_var($data['mc_id'] ?? null, FILTER_VALIDATE_INT);

    $requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            exit(json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']));
        }
    }

    // Cho phép để trống ảnh
    $mc_image_url = !empty($data['mc_image_url']) ? $data['mc_image_url'] : null;

    if ($mc_id) {
        $sql = "UPDATE mc_questions 
                SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? 
                WHERE mc_id=?";
        $types = "ssssssssi";
        $params = [
            $data['mc_topic'], $data['mc_question'],
            $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'],
            $data['mc_correct_answer'], $mc_image_url, $mc_id
        ];
    } else {
        $sql = "INSERT INTO mc_questions 
                (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $types = "ssssssss";
        $params = [
            $data['mc_topic'], $data['mc_question'],
            $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'],
            $data['mc_correct_answer'], $mc_image_url
        ];
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => $mc_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
} finally {
    $conn?->close();
}
