<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../includes/db_connection.php';

$mc = null;

// Xử lý lưu form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic    = $_POST['topic'] ?? '';
    $question = $_POST['question'] ?? '';
    $answer1  = $_POST['answer1'] ?? '';
    $answer2  = $_POST['answer2'] ?? '';
    $answer3  = $_POST['answer3'] ?? '';
    $answer4  = $_POST['answer4'] ?? '';
    $correct  = $_POST['answer'] ?? '';
    $image_url = '';

    // Kiểm tra hợp lệ A–D
    if (!in_array($correct, ['A', 'B', 'C', 'D'])) {
        die('Đáp án đúng không hợp lệ.');
    }

    // Xử lý ảnh
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        $filename  = time() . '_' . basename($_FILES['image']['name']);
        $filepath  = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_url = '../../uploads/' . $filename;
        }
    } elseif (!empty($_POST['existing_image'])) {
        $image_url = $_POST['existing_image'];
    }

    // Thêm mới hoặc cập nhật
    if (!empty($_POST['mc_id'])) {
        $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? WHERE mc_id=?");
        $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, (int)$_POST['mc_id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url]);
    }

    header('Location: mc_form.php');
    exit;
}

// Truy vấn nếu có mc_id
if (!empty($_GET['mc_id'])) {
    $id = (int)$_GET['mc_id'];
    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$id]);
    $mc = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form_ui.css">

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <form id="mcForm" method="POST" enctype="multipart/form-data">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>

      <div id="mcMainContent" class="mc-columns">
        <!-- Cột trái -->
        <div class="mc-col mc-col-left">
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" required value="<?= htmlspecialchars($mc['mc_topic'] ?? '', ENT_QUOTES) ?>">
          </div>

          <div class="mc-field">
            <label for="mc_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="mc_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="mc_question" name="question" required><?= htmlspecialchars($mc['mc_question'] ?? '', ENT_QUOTES) ?></textarea>
            <div class="preview-box" id="preview-mc_question" style="display:none;"></div>
          </div>

          <?php
            $labels = ['A', 'B', 'C', 'D'];
            for ($i = 1; $i <= 4; $i++): 
              $label = $labels[$i - 1];
          ?>
            <div class="mc-field">
              <label for="mc_answer<?= $i ?>">Đáp án <?= $label ?>.
                <button type="button" class="toggle-preview" data-target="mc_answer<?= $i ?>"><i class="fa fa-eye"></i></button>
              </label>
              <input type="text"
                id="mc_answer<?= $i ?>"
                name="answer<?= $i ?>"
                required
                value="<?= htmlspecialchars($mc["mc_answer$i"] ?? '', ENT_QUOTES) ?>">
              <div class="preview-box" id="preview-mc_answer<?= $i ?>" style="display:none;"></div>
            </div>
          <?php endfor; ?>

          <div class="mc-field">
            <label for="mc_correct_answer">Đáp án đúng:</label>
            <select id="mc_correct_answer" name="answer" required>
              <?php foreach (['A', 'B', 'C', 'D'] as $label): 
                $selected = (($mc['mc_correct_answer'] ?? '') === $label) ? 'selected' : '';
              ?>
                <option value="<?= $label ?>" <?= $selected ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="mc-col mc-col-right">
          <div class="mc-image-zone">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview">
              <?php if (!empty($mc['mc_image_url'])): ?>
                <img src="<?= htmlspecialchars($mc['mc_image_url']) ?>" alt="Hình minh hoạ">
              <?php endif; ?>
            </div>
            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_remove_image">Xóa ảnh</button>
            </div>
            <?php if (!empty($mc['mc_image_url'])): ?>
              <input type="hidden" name="existing_image" value="<?= htmlspecialchars($mc['mc_image_url']) ?>">
            <?php endif; ?>
          </div>

          <div class="mc-buttons">
            <h4>Thao tác</h4>
            <button type="submit" id="mc_save">Lưu</button>
            <button type="button" id="mc_delete">Xóa</button>
            <button type="button" id="mc_reset">Làm lại</button>
            <button type="button" id="mc_view_list">Ẩn/hiện danh sách</button>
            <button type="button" id="mc_preview_exam">Làm đề</button>
          </div>
        </div>
      </div>

      <?php if (!empty($mc['mc_id'])): ?>
        <input type="hidden" id="mc_id" name="mc_id" value="<?= (int)$mc['mc_id'] ?>">
      <?php endif; ?>
    </form>

    <div id="mcTableWrapper" style="display:none;">
      <iframe id="mcTableFrame" src="mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>

    <div id="mcPreview" class="mc-preview-zone" style="display:none;">
      <h3>Xem trước toàn bộ</h3>
      <div id="mcPreviewContent"></div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../../js/form/mc_layout.js"></script>
  <script src="../../js/form/mc_preview.js"></script>
  <script src="../../js/form/mc_image.js"></script>
  <script src="../../js/form/mc_button.js"></script>
  <script src="../../js/form/mc_preview_all.js"></script>
  <script src="../../js/form/mc_listener.js"></script>
</body>
</html>
