?>
<div class="subtab-container">
  <div class="subtab-button active" data-subtab="mc_form_inner">📝 Nhập câu hỏi</div>
  <div class="subtab-button" data-subtab="mc_image">🖼️ Ảnh minh hoạ</div>
  <div class="subtab-button" data-subtab="mc_preview">👁️ Xem trước</div>
  <div class="subtab-button" data-subtab="mc_table">📋 Danh sách</div>
</div>

<div class="subtab-content active" id="mc_form_inner"><?php require 'pages/mc_form_inner.php'; ?></div>
<div class="subtab-content" id="mc_image"><?php require 'pages/mc_image.php'; ?></div>
<div class="subtab-content" id="mc_preview"><?php require 'pages/mc_preview.php'; ?></div>
<div class="subtab-content" id="mc_table"><?php require 'pages/mc_table.php'; ?></div>

<script>
  document.querySelectorAll(".subtab-button").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".subtab-button").forEach(b => b.classList.remove("active"));
      document.querySelectorAll(".subtab-content").forEach(c => c.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById(btn.dataset.subtab).classList.add("active");
    });
  });
</script>
<?php

