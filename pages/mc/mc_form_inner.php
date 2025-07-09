<input type="hidden" id="mc_id">

<div class="form-group">
  <label for="mc_topic">📚 Chủ đề:</label>
  <input type="text" id="mc_topic" class="form-control" placeholder="Nhập chủ đề...">
</div>

<div class="form-group">
  <label for="mc_question">🧠 Câu hỏi:</label>
  <textarea id="mc_question" rows="3" class="form-control" placeholder="Nhập nội dung câu hỏi..."></textarea>
</div>

<div class="form-group">
  <label for="mc_answer1">A:</label>
  <input type="text" id="mc_answer1" class="form-control" placeholder="Đáp án A">
</div>

<div class="form-group">
  <label for="mc_answer2">B:</label>
  <input type="text" id="mc_answer2" class="form-control" placeholder="Đáp án B">
</div>

<div class="form-group">
  <label for="mc_answer3">C:</label>
  <input type="text" id="mc_answer3" class="form-control" placeholder="Đáp án C">
</div>

<div class="form-group">
  <label for="mc_answer4">D:</label>
  <input type="text" id="mc_answer4" class="form-control" placeholder="Đáp án D">
</div>

<div class="form-group">
  <label for="mc_correct_answer">✅ Đáp án đúng:</label>
  <select id="mc_correct_answer" class="form-control">
    <option value="">-- Chọn --</option>
    <option value="A">A</option>
    <option value="B">B</option>
    <option value="C">C</option>
    <option value="D">D</option>
  </select>
</div>

<div class="form-actions" style="margin-top: 20px;">
  <button type="button" id="mc_saveBtn" class="btn-save">💾 Lưu</button>
  <button type="button" id="mc_resetBtn" class="btn-reset">🔄 Làm mới</button>
  <button type="button" id="mc_deleteBtn" class="btn-delete">🗑️ Xoá</button>
</div>
