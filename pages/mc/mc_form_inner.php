// =======================
// File: pages/mc/mc_form_inner.php
// =======================
?>
<input type="hidden" id="mc_id">
<label>Chủ đề:</label>
<input type="text" id="mc_topic">
<label>Câu hỏi:</label>
<textarea id="mc_question" rows="3"></textarea>
<label>Đáp án A:</label><input type="text" id="mc_answer1">
<label>Đáp án B:</label><input type="text" id="mc_answer2">
<label>Đáp án C:</label><input type="text" id="mc_answer3">
<label>Đáp án D:</label><input type="text" id="mc_answer4">
<label>Đáp án đúng:</label>
<select id="mc_correct_answer">
  <option value="">-- Chọn --</option>
  <option value="A">A</option>
  <option value="B">B</option>
  <option value="C">C</option>
  <option value="D">D</option>
</select>
<div style="margin-top:15px;">
  <button type="button" id="mc_saveBtn" class="btn-primary">💾 Lưu</button>
  <button type="button" id="mc_resetBtn" class="btn-secondary">🔄 Làm mới</button>
  <button type="button" id="mc_deleteBtn" class="btn-danger">🗑️ Xoá</button>
</div>
<?php

