<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Giờ bạn có thể dùng biến môi trường:
$cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'];
$apiKey    = $_ENV['CLOUDINARY_API_KEY'];
$apiSecret = $_ENV['CLOUDINARY_API_SECRET'];
