<?php
// Bật hiển thị lỗi nếu có
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Gọi thư viện TCPDF
require_once('tcpdf/tcpdf.php');
require 'db_connection.php';

// Tạo đối tượng PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống');
$pdf->SetTitle('Danh sách câu hỏi');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

// Truy vấn dữ liệu
try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Bắt đầu nội dung HTML
$html = '<h2 style="text-align:center;">Danh sách câu hỏi</h2>';
$html .= '<table border="1" cellpadding="4">
<thead>
<tr>
    <th width="25">ID</th>
    <th width="200">Câu hỏi</th>
    <th width="50">Đáp án đúng</th>
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
    $thumb = !empty($imageUrl) ? preg_replace('/upload\//', 'upload/c_fill,h_50,w_50/', $imageUrl) : '';

    // Tải ảnh về tạm thời
    $imgTag = '';
    if (!empty($thumb)) {
        $imgData = @file_get_contents($thumb);
        if ($imgData) {
            $tempPath = tempnam(sys_get_temp_dir(), 'img') . '.jpg';
            file_put_contents($tempPath, $imgData);
            $imgTag = '<img src="@' . $tempPath . '" width="50">';
        } else {
            $imgTag = 'Không tải được ảnh';
        }
    }

    $html .= "<tr>
        <td>{$id}</td>
        <td>{$question}</td>
        <td style='text-align:center;'><b>{$correct}</b></td>
        <td>{$topic}</td>
        <td style='text-align:center;'>{$imgTag}</td>
    </tr>";
}

$html .= '</tbody></table>';

// In nội dung vào PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Xuất file PDF ra trình duyệt
$pdf->Output('danh_sach_cau_hoi.pdf', 'I');
