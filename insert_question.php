<?php
include 'db_connection.php';

$id = $_POST["id"];
$question = $_POST["question"];
$answer1 = $_POST["answer1"];
$answer2 = $_POST["answer2"];
$answer3 = $_POST["answer3"];
$answer4 = $_POST["answer4"];
$correct_answer = $_POST["correct_answer"];
$imagePath = "";

if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $uploadDir = "images/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $imagePath = $targetFile;
    }
}

if (!empty($id)) {
    if (!empty($imagePath)) {
        $sql = "UPDATE questions SET question=?, image=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $question, $imagePath, $answer1, $answer2, $answer3, $answer4, $correct_answer, $id);
    } else {
        $sql = "UPDATE questions SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $question, $answer1, $answer2, $answer3, $answer4, $correct_answer, $id);
    }
} else {
    $sql = "INSERT INTO questions (question, image, answer1, answer2, answer3, answer4, correct_answer)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $question, $imagePath, $answer1, $answer2, $answer3, $answer4, $correct_answer);
}

if ($stmt->execute()) {
    header("Location: question_form.php");
    exit;
} else {
    echo "Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
