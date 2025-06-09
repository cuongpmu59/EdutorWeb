<?php
// question_action.php
require 'db_connection.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

function sanitize($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

function handleImageUpload() {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'images/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            return $targetPath;
        }
    }
    return '';
}

if ($action === 'add') {
    $question = sanitize($conn, $_POST['question']);
    $a = sanitize($conn, $_POST['answer1']);
    $b = sanitize($conn, $_POST['answer2']);
    $c = sanitize($conn, $_POST['answer3']);
    $d = sanitize($conn, $_POST['answer4']);
    $correct = sanitize($conn, $_POST['correct_answer']);
    $image = handleImageUpload();

    $sql = "INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, image)
            VALUES ('$question', '$a', '$b', '$c', '$d', '$correct', '$image')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Đã thêm câu hỏi thành công!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm: ' . mysqli_error($conn)]);
    }

} elseif ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $question = sanitize($conn, $_POST['question']);
    $a = sanitize($conn, $_POST['answer1']);
    $b = sanitize($conn, $_POST['answer2']);
    $c = sanitize($conn, $_POST['answer3']);
    $d = sanitize($conn, $_POST['answer4']);
    $correct = sanitize($conn, $_POST['correct_answer']);

    // Kiểm tra và xử lý ảnh mới nếu có
    $imagePath = handleImageUpload();
    $imageSQL = $imagePath ? ", image = '$imagePath'" : "";

    $sql = "UPDATE questions SET
            question = '$question',
            answer1 = '$a',
            answer2 = '$b',
            answer3 = '$c',
            answer4 = '$d',
            correct_answer = '$correct'
            $imageSQL
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật câu hỏi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . mysqli_error($conn)]);
    }

} elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);

    // Xóa ảnh cũ nếu có
    $res = mysqli_query($conn, "SELECT image FROM questions WHERE id = $id");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if (!empty($row['image']) && file_exists($row['image'])) {
            unlink($row['image']);
        }
    }

    $sql = "DELETE FROM questions WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa câu hỏi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . mysqli_error($conn)]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ!']);
}

mysqli_close($conn);
