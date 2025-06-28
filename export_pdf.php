<?php
require_once('tcpdf/tcpdf.php');
require 'db_connection.php';

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống');
$pdf->SetTitle('Danh sách câu hỏi');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Bảng HTML với ảnh
$html = '<h2 style="text-align:center;">Danh sách câu hỏi</h2>';
$html .= '<table border="1" cellpadding="4">
<thead>
<tr>
    <th style="width:25px;">ID</th>
    <th style="width:200px;">Câu hỏi</th>
    <th style="width:50px;">Đáp án đúng</th>
    <th style="width:100px;">Chủ đề</th>
    <th style="width:60px;">Ảnh</th>
</tr>
</thead><tbody>';

foreach ($rows as $row) {
    $imageUrl = $row['image'];
    // Resize ảnh qua Cloudinary thumbnail
    $thumb = !empty($imageUrl) ? preg_replace('/upload\//', 'upload/c_fill,h_50,w_50/', $imageUrl) : '';

    $html .= '<tr>
        <td>' . htmlspecialchars($row['id']) . '</td>
        <td>' . htmlspecialchars($row['question']) . '</td>
        <td style="text-align:center;"><b>' . strtoupper($row['correct_answer'][0]) . '</b></td>
        <td>' . htmlspecialchars($row['topic'] ?? '') . '</td>
        <td style="text-align:center;">';

    if ($thumb) {
        $html .= '<img src="' . $thumb . '" width="50">';
    }

    $html .= '</td></tr>';
}

$html .= '</tbody></table>';

// In ra PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('danh_sach_cau_hoi.pdf', 'I');
