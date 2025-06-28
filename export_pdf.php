<?php
// Hiển thị lỗi (dùng khi debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Gọi thư viện và kết nối CSDL
require_once('tcpdf/tcpdf.php');
require 'db_connection.php';

// Tạo đối tượng PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống');
$pdf->SetTitle('Danh sách câu hỏi');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10); // ✅ Tự động ngắt trang
$pdf->AddPage();

// ✅ Font Unicode hỗ trợ tiếng Việt
$pdf->SetFont('dejavusans', '', 12);

// Truy vấn dữ liệu
try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Khởi tạo danh sách file tạm để xoá sau
$tempFiles = [];

// Bắt đầu HTML
$html = '<h2 style="text-align:center;">Danh sách câu hỏi</h2>';
$html .= '<table border="1" cellpadding="4">
<thead>
<tr>
    <th width="25">ID</th>
    <th width="200">Câu hỏi</th>
    <th width="50">Đáp án</th>
    <th width="100">Chủ đề</th>
    <th width="60">Ảnh</th>
</tr>
</thead><tbody>';

// Duyệt qua từng câu hỏi
foreach ($rows as $row) {
    $id = htmlspecialchars($row['id']);
    $question = htmlspecialchars($row['question']);
    $correct = strtoupper($row['correct_answer'][0]);
    $topic = htmlspecialchars($row['topic'] ?? '');
    $imageUrl = $row['image'];
    $thumb = '';

    // ✅ Tạo thumbnail từ ảnh gốc nếu hợp lệ
    if (!empty($imageUrl) && strpos($imageUrl, 'upload/') !== false) {
        $thumb = preg_replace('/upload\//', 'upload/c_fill,h_50,w_50/', $imageUrl);
    }

    // ✅ Tải ảnh và gắn vào thẻ <img>
    $imgTag = '';
    if (!empty($thumb)) {
        $imgData = @file_get_contents($thumb);
        if ($imgData) {
            $tempPath = tempnam(sys_get_temp_dir(), 'img') . '.jpg';
            file_put_contents($tempPath, $imgData);
            $imgTag = '<img src="@' . $tempPath . '" width="50">';
            $tempFiles[] = $tempPath;
        } else {
            $imgTag = 'Lỗi ảnh';
        }
    }

    // ✅ Giới hạn độ dài câu hỏi (200 ký tự)
    $shortQuestion = mb_strimwidth($question, 0, 200, '...');

    $html .= "<tr>
        <td>{$id}</td>
        <td>{$shortQuestion}</td>
        <td style='text-align:center;'><b>{$correct}</b></td>
        <td>{$topic}</td>
        <td style='text-align:center;'>{$imgTag}</td>
    </tr>";
}

$html .= '</tbody></table>';

// Xuất nội dung HTML ra PDF
$pdf->writeHTML($html, true, false, true, false, '');

// ✅ Xoá các file ảnh tạm sau khi dùng
foreach ($tempFiles as $file) {
    @unlink($file);
}

// Xuất file PDF ra trình duyệt
$pdf->Output('danh_sach_cau_hoi.pdf', 'I');
