// =======================
// File: pages/tf/tf_form_inner.php
// =======================
?>
<input type="hidden" id="tf_id">
<label>Chủ đề:</label>
<input type="text" id="tf_topic">
<label>Đề bài:</label>
<textarea id="tf_main_question" rows="2"></textarea>
<label>Mệnh đề:</label>
<textarea id="tf_statement" rows="2"></textarea>
<label>Đúng hay Sai?</label>
<select id="tf_correct_answer">
  <option value="">-- Chọn --</option>
  <option value="Đúng">Đúng</option>
  <option value="Sai">Sai</option>
</select>
<div style="margin-top:15px;">
  <button type="button" id="tf_saveBtn" class="btn-primary">💾 Lưu</button>
  <button type="button" id="tf_resetBtn" class="btn-secondary">🔄 Làm mới</button>
  <button type="button" id="tf_deleteBtn" class="btn-danger">🗑️ Xoá</button>
</div>
<?php

