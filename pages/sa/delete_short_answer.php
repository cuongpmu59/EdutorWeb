<?php
require '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['sa_id'] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM sa_questions WHERE sa_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'ID không hợp lệ.']);
    }
}
?>
