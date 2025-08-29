<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán học phí</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      padding: 40px;
      text-align: center;
    }

    .payment-box {
      max-width: 800px;
      margin: auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h1 {
      color: #28a745;
      margin-bottom: 20px;
    }

    .info {
      text-align: left;
      margin-bottom: 20px;
      font-size: 16px;
      line-height: 1.6;
    }

    .info strong {
      display: inline-block;
      width: 200px;
    }

    .qr-code {
      margin-top: 20px;
    }

    .qr-code img {
      width: 250px;
      height: auto;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 8px;
    }

    .back-button {
      margin-top: 30px;
      display: inline-block;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border-radius: 6px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="payment-box">
    <h1>Thông tin chuyển khoản học phí</h1>

    <div class="info">
      <p><strong>Chủ tài khoản:</strong> Phùng Khắc Cường</p>
      <p><strong>Ngân hàng:</strong> ACB</p>
      <p><strong>Số tài khoản:</strong> 37687817</p>
      <p><strong>Nội dung chuyển khoản:</strong> Họ tên học viên - Học phí</p>
    </div>

    <div class="qr-code">
      <p>Quét mã QR để thanh toán nhanh:</p>
      <img src="../../pages/image/nganhang.png" alt="Mã QR thanh toán học phí tới tài khoản Phùng Khắc Cường">
    </div>

    <a href="../../pages/main/index.html" class="back-button">← Quay lại đăng ký</a>
  </div>

</body>
</html>
