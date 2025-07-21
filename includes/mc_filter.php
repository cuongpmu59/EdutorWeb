<?php
require_once __DIR__ . '/../../includes/db_connection.php';
$stmt = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
?>
<select id="filter-topic">
  <option value="">-- Tất cả --</option>
  <?php foreach ($stmt as $row): ?>
    <option value="<?= htmlspecialchars($row['mc_topic']) ?>">
      <?= htmlspecialchars($row['mc_topic']) ?>
    </option>
  <?php endforeach; ?>
</select>

/* Dòng được chọn */
  #mcTable tbody tr.selected {
    background-color: #e0f7fa !important;
  }
  
  /* Bộ lọc tìm kiếm phía trên bảng */
  div.dataTables_filter {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
  }
  #mcTable_filter .filter-left,
  #mcTable_filter .filter-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  #mcTable_filter select {
    padding: 4px 8px;
  }
  
  /* Input file ẩn cho việc nhập Excel */
  #excelFile {
    display: none;
  }
  