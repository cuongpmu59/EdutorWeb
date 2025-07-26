// Hàm tải danh sách chủ đề từ PHP
function loadTopicOptions() {
    fetch('../../ajax/mc_topic_options.php')
      .then(response => response.text())
      .then(html => {
        document.getElementById('topicFilter').innerHTML = html;
      })
      .catch(error => {
        console.error('❌ Lỗi khi tải danh sách chủ đề:', error);
      });
  }
  
  document.addEventListener('DOMContentLoaded', () => {
    const topicFilter = document.getElementById('topicFilter');
    const searchInput = document.getElementById('mcSearch');
  
    // Đảm bảo DataTable đã khởi tạo (phải gọi sau khi init ở file khác)
    const table = $('#mcTable').DataTable();
  
    // Tải danh sách chủ đề
    loadTopicOptions();
  
    // Lọc theo chủ đề (cột 1)
    if (topicFilter) {
      topicFilter.addEventListener('change', function () {
        const val = this.value;
        table.column(1).search(val).draw();
      });
    }
  
    // Tìm kiếm theo nội dung câu hỏi (cột 2)
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        const val = this.value;
        table.column(2).search(val).draw();
      });
    }
  });
  