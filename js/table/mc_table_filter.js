// Tải danh sách chủ đề vào dropdown
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
    const table = $('#mcTable').DataTable();
  
    // Gọi hàm tải chủ đề
    loadTopicOptions();
  
    // Lọc bảng theo chủ đề
    if (topicFilter) {
      topicFilter.addEventListener('change', function () {
        const value = this.value;
        // Cột 1 là 'Chủ đề' trong bảng
        table.column(1).search(value).draw();
      });
    }
  
    // Lọc theo nội dung câu hỏi
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        const value = this.value;
        // Cột 2 là 'Câu hỏi'
        table.column(2).search(value).draw();
      });
    }
  });
  