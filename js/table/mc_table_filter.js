// Tải danh sách chủ đề vào select
function loadTopicOptions() {
    fetch('../../includes/mc_topic_options.php')
      .then(response => response.text())
      .then(html => {
        document.getElementById('topicFilter').innerHTML = html;
      })
      .catch(error => {
        console.error('Lỗi khi tải danh sách chủ đề:', error);
      });
  }
  
  document.addEventListener('DOMContentLoaded', () => {
    loadTopicOptions();
  });
  