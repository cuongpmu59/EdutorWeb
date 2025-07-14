<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Câu hỏi Trắc nghiệm</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    .tab-group {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }

    .tab, .sub-tab {
      padding: 8px 16px;
      border: 1px solid #007bff;
      background-color: #e6f0ff;
      cursor: pointer;
      border-radius: 5px;
      transition: all 0.2s ease;
    }

    .tab:hover, .sub-tab:hover {
      background-color: #cce0ff;
    }

    .tab.active, .sub-tab.active {
      background-color: #007bff;
      color: white;
    }

    .sub-tabs, .content {
      display: none;
    }

    .sub-tabs.active, .content.active {
      display: block;
    }

    .content {
      padding: 15px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      border-radius: 5px;
      margin-bottom: 15px;
    }

    h2 {
      margin-top: 0;
    }
  </style>
</head>
<body>

  <h2>🧠 Chọn loại câu hỏi</h2>
  <div class="tab-group" id="main-tabs">
    <button class="tab" data-main="mc">MC: Nhiều lựa chọn</button>
    <button class="tab" data-main="tf">TF: Đúng/Sai</button>
    <button class="tab" data-main="sa">SA: Trả lời ngắn</button>
  </div>

  <div id="sub-tabs-container">
    <div class="sub-tabs" data-sub="mc">
      <button class="sub-tab" data-content="mc-input">📥 Nhập liệu</button>
      <button class="sub-tab" data-content="mc-image">🖼️ Xem ảnh minh hoạ</button>
      <button class="sub-tab" data-content="mc-preview">👀 Xem trước toàn bộ</button>
    </div>
    <div class="sub-tabs" data-sub="tf">
      <button class="sub-tab" data-content="tf-input">📥 Nhập liệu</button>
      <button class="sub-tab" data-content="tf-image">🖼️ Xem ảnh minh hoạ</button>
      <button class="sub-tab" data-content="tf-preview">👀 Xem trước toàn bộ</button>
    </div>
    <div class="sub-tabs" data-sub="sa">
      <button class="sub-tab" data-content="sa-input">📥 Nhập liệu</button>
      <button class="sub-tab" data-content="sa-image">🖼️ Xem ảnh minh hoạ</button>
      <button class="sub-tab" data-content="sa-preview">👀 Xem trước toàn bộ</button>
    </div>
  </div>

  <div id="contents">
    <!-- MC -->
    <div class="content" id="mc-input">📘 Nhập liệu: Form nhập câu hỏi nhiều lựa chọn.</div>
    <div class="content" id="mc-image">🖼️ Ảnh minh hoạ: Xem ảnh hoặc chọn ảnh từ máy.</div>
    <div class="content" id="mc-preview">👀 Xem trước: Hiển thị đầy đủ câu hỏi MC.</div>

    <!-- TF -->
    <div class="content" id="tf-input">📘 Nhập liệu: Câu hỏi đúng/sai đơn giản.</div>
    <div class="content" id="tf-image">🖼️ Ảnh minh hoạ: Chọn ảnh cho câu đúng/sai.</div>
    <div class="content" id="tf-preview">👀 Xem trước: Hiển thị câu đúng/sai hoàn chỉnh.</div>

    <!-- SA -->
    <div class="content" id="sa-input">📘 Nhập liệu: Gõ câu hỏi và đáp án ngắn.</div>
    <div class="content" id="sa-image">🖼️ Ảnh minh hoạ: Thêm ảnh hỗ trợ câu hỏi ngắn.</div>
    <div class="content" id="sa-preview">👀 Xem trước: Tổng quan câu hỏi ngắn.</div>
  </div>

  <script>
    const mainTabs = document.querySelectorAll('.tab');
    const subTabsGroups = document.querySelectorAll('.sub-tabs');
    const contents = document.querySelectorAll('.content');

    mainTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Active main tab
        mainTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const mainId = tab.getAttribute('data-main');

        // Show correct sub-tabs
        subTabsGroups.forEach(group => {
          if (group.getAttribute('data-sub') === mainId) {
            group.classList.add('active');
            // Auto click first sub-tab
            const firstSub = group.querySelector('.sub-tab');
            if (firstSub) firstSub.click();
          } else {
            group.classList.remove('active');
          }
        });

        // Hide all content
        contents.forEach(c => c.classList.remove('active'));
      });
    });

    document.querySelectorAll('.sub-tab').forEach(subTab => {
      subTab.addEventListener('click', () => {
        const siblings = subTab.parentElement.querySelectorAll('.sub-tab');
        siblings.forEach(s => s.classList.remove('active'));
        subTab.classList.add('active');

        const contentId = subTab.getAttribute('data-content');
        contents.forEach(c => {
          c.classList.toggle('active', c.id === contentId);
        });
      });
    });

    // Auto click the first main tab
    mainTabs[0].click();
  </script>

</body>
</html>
