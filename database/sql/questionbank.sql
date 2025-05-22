-- Tạo CSDL nếu chưa có
CREATE DATABASE IF NOT EXISTS questionbank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE questionbank;

-- Tạo bảng lưu câu hỏi trắc nghiệm
CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question TEXT NOT NULL,
  image VARCHAR(255),
  answer1 VARCHAR(255) NOT NULL,
  answer2 VARCHAR(255) NOT NULL,
  answer3 VARCHAR(255),
  answer4 VARCHAR(255),
  correct_answer VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Chèn câu hỏi ví dụ
INSERT INTO questions (question, image, answer1, answer2, answer3, answer4, correct_answer)
VALUES (
  'Tính \\( 2^3 \\) bằng bao nhiêu?',
  'uploads/toan1.jpg',
  '6',
  '8',
  '9',
  '10',
  'answer2'
);
