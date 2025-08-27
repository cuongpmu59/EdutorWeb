<?php
header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD']!=='GET'){
    echo json_encode(['status'=>'error','message'=>'Phương thức không hợp lệ']); exit;
}
require_once __DIR__ . '/../../includes/env/db_connection.php';

try{
    $sql = "SELECT DISTINCT mc_topic FROM mc_questions ORDER BY mc_topic ASC";
    $stmt = $conn->query($sql);
    $topics = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if(!empty($row['mc_topic'])) $topics[] = $row['mc_topic'];
    }
    echo json_encode($topics,JSON_UNESCAPED_UNICODE);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Không thể lấy danh sách chủ đề','error'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
}
