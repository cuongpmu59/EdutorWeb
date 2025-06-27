<?php
require 'db_connection.php';
require 'vendor/autoload.php'; // Dành cho Composer và TCPDF

use TCPDF;

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=de_thi_trac_nghiem.pdf");

// Tạo PDF mới
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hệ thống');
$pdf->SetTitle('Đề thi trắc nghiệm');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);

// ====== Phần thông tin thí sinh ======
$html = <<<HTML
<style>
  .info-table td { padding: 6px; }
</style>
<h2 style="text-align:center;">ĐỀ THI TRẮC NGHIỆM</h2>
<table class="info-table" width="100%" border="1" cellspacing="0" cellpadding="4">
  <tr>
    <td><strong>Họ và tên:</strong> ......................................................</td>
    <td><strong>Lớp:</strong> ................................................</td>
  </tr>
  <tr>
    <td><strong>Số báo danh:</strong> ..........................................</td>
    <td><strong>Môn thi:</strong> ............................................</td>
  </tr>
</table>
<br>
HTML;

// ====== Lấy câu hỏi từ CSDL ======
$topic = $_GET['topic'] ?? '';
$sql = "SELECT * FROM questions";
$params = [];

if (!empty($topic)) {
    $sql .= " WHERE topic = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $topic);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$index = 1;
while ($row = $result->fetch_assoc()) {
    $q = htmlspecialchars($row['question']);
    $a = htmlspecialchars($row['answer1']);
    $b = htmlspecialchars($row['answer2']);
    $c = htmlspecialchars($row['answer3']);
    $d = htmlspecialchars($row['answer4']);

    $html .= <<<QHTML
<p><strong>Câu {$index}:</strong> {$q}</p>
<ul style="list-style-type: none; padding-left: 15px;">
  <li>A. {$a}</li>
  <li>B. {$b}</li>
  <li>C. {$c}</li>
  <li>D. {$d}</li>
</ul>
<br>
QHTML;

    $index++;
}

// ====== In nội dung ra PDF ======
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('de_thi_trac_nghiem.pdf', 'I');
