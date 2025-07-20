<?php
// mc_form.php
// (đầu file) kiểm tra session, load db connection, v.v.

// Nếu có mc_id truyền vào (xem bảng và tải form), load dữ liệu:
$mc = null;
if (!empty($_GET['mc_id'])) {
  $id = intval($_GET['mc_id']);
  // giả sử kết nối DB ở $conn
  $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$id]);
  $mc = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Form Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/mc_form.css">
</head>
<body>
  <form id="mcForm" enctype="multipart/form-data">
    <div class="mc-columns">
      <!-- Cột trái: nhập liệu -->
      <div class="mc-col-left">
        <h2>Nhập câu trắc nghiệm
          <span id="mcTogglePreview"><i class="icon-eye"></i></span>
        </h2>
        <div class="mc-field">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="topic" value="<?= htmlspecialchars($mc['mc_topic'] ?? '') ?>">
        </div>
        <div class="mc-field">
          <label for="mc_question">Câu hỏi:</label>
          <textarea id="mc_question" name="question"><?= htmlspecialchars($mc['mc_question'] ?? '') ?></textarea>
        </div>
        <?php foreach (['A','B','C','D'] as $opt): ?>
        <div class="mc-field">
          <label for="mc_opt_<?= $opt ?>"><?= $opt ?>.</label>
          <input type="text" id="mc_opt_<?= $opt ?>" name="opt_<?= $opt ?>" value="<?= htmlspecialchars($mc['mc_opt_'.$opt] ?? '') ?>">
        </div>
        <?php endforeach; ?>
        <div class="mc-field">
          <label for="mc_answer">Đáp án:</label>
          <select id="mc_answer" name="answer">
            <?php foreach (['A','B','C','D'] as $opt): ?>
            <option value="<?= $opt ?>" <?= (isset($mc['mc_answer']) && $mc['mc_answer']==$opt)?'selected':'' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Cột phải -->
      <div class="mc-col-right">
        <!-- Khu vực 2: ảnh minh họa -->
        <div class="mc-image-zone">
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
        </div>

        <!-- Khu vực 3: các nút thao tác -->
        <div class="mc-buttons">
          <button type="button" id="mc_save">Lưu</button>
          <button type="button" id="mc_delete">Xóa</button>
          <button type="button" id="mc_reset">Làm lại</button>
          <button type="button" id="mc_view_list">Xem danh sách</button>
          <button type="button" id="mc_preview_exam">Làm đề</button>
        </div>
      </div>
    </div>

    <!-- Nếu có mc_id, đính kèm input ẩn -->
    <?php if (!empty($mc['mc_id'])): ?>
    <input type="hidden" id="mc_id" name="mc_id" value="<?= $mc['mc_id'] ?>">
    <?php endif; ?>
  </form>

  <!-- script chung -->
  <script src="mc/js/mc_layout.js"></script>
  <script src="mc/js/mc_preview.js"></script>
  <script src="mc/js/mc_image.js"></script>
  <script src="mc/js/mc_button.js"></script>
</body>
</html>
