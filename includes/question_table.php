<div id="listTab" class="tab-content">
  <table id="questionTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Câu hỏi</th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>Đúng</th>
        <th>Chủ đề</th>
        <th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $q): ?>
        <tr>
          <td><?= $q['id'] ?></td>
          <td><?= htmlspecialchars($q['question']) ?></td>
          <td><?= htmlspecialchars($q['answer1'] ?? '') ?></td>
          <td><?= htmlspecialchars($q['answer2'] ?? '') ?></td>
          <td><?= htmlspecialchars($q['answer3'] ?? '') ?></td>
          <td><?= htmlspecialchars($q['answer4'] ?? '') ?></td>
          <td><?= htmlspecialchars($q['correct_answer'] ?? '') ?></td>
          <td><?= htmlspecialchars($q['topic'] ?? '') ?></td>
          <td>
            <?php if (!empty($q['image'])): ?>
              <img src="<?= htmlspecialchars($q['image']) ?>" class="thumb" onclick="showImage(this.src)" onerror="this.style.display='none'">
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div id="previewArea" style="margin-top:20px;">
    <em>Chọn một câu hỏi để xem trước nội dung...</em>
  </div>
</div>
