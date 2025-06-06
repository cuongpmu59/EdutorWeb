// Các phần tử DOM
const questionIdInput = document.getElementById('questionId'); // Bạn cần thêm <input type="hidden" id="questionId" name="id" /> vào form
const questionInput = document.getElementById('question');
const answerInputs = [
  document.querySelector('[name="answer1"]'),
  document.querySelector('[name="answer2"]'),
  document.querySelector('[name="answer3"]'),
  document.querySelector('[name="answer4"]'),
];
const correctAnswerSelect = document.getElementById('correct_answer');
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');
const previewDiv = document.getElementById('preview');

const addNewBtn = document.querySelector('button[type="button"]:contains("Thêm mới"), #addNewBtn');
const deleteBtn = document.querySelector('button[onclick="deleteSelected()"]');
const updateBtn = document.getElementById('updateBtn');
const form = document.getElementById('questionForm');
const iframe = document.getElementById('questionTable');

let questions = [];  // Danh sách câu hỏi từ iframe (bạn có thể cập nhật khi load bảng)
let selectedIndex = -1;  // Dòng đang chọn trong bảng

// Hiện tại, bạn cần chắc chắn có input ẩn questionId trong form để lưu ID câu hỏi
// Nếu chưa có, bạn nên thêm dòng này trong form:
// <input type="hidden" id="questionId" name="id" />

// Hàm cập nhật preview MathJax
function updatePreview() {
  previewDiv.innerHTML = questionInput.value || '';
  MathJax.typesetPromise([previewDiv]);
}

// Xóa trắng form
function clearForm() {
  questionIdInput.value = '';
  questionInput.value = '';
  answerInputs.forEach(input => input.value = '');
  correctAnswerSelect.value = '';
  imageInput.value = '';
  imagePreview.style.display = 'none';
  imagePreview.src = '#';
  previewDiv.innerHTML = '';
  selectedIndex = -1;
}

// Tải dữ liệu câu hỏi lên form
function loadQuestionToForm(data) {
  questionIdInput.value = data.id || '';
  questionInput.value = data.question || '';
  answerInputs[0].value = data.answer1 || '';
  answerInputs[1].value = data.answer2 || '';
  answerInputs[2].value = data.answer3 || '';
  answerInputs[3].value = data.answer4 || '';
  correctAnswerSelect.value = data.correct_answer || '';

  if (data.image) {
    imagePreview.src = data.image;
    imagePreview.style.display = 'block';
  } else {
    imagePreview.style.display = 'none';
    imagePreview.src = '#';
  }
  updatePreview();
}

// Lắng nghe sự kiện message từ iframe (get_question.php)
window.addEventListener('message', function(event) {
  if (event.origin !== window.location.origin) return;  // bảo mật
  const data = event.data;
  if (data && typeof data === 'object') {
    loadQuestionToForm(data);

    // Lưu lại vị trí đã chọn
    if (questions.length > 0) {
      selectedIndex = questions.findIndex(q => q.id == data.id);
    }
  }
});

// Xử lý chọn dòng trong bảng (gọi từ iframe hoặc qua postMessage)
function selectQuestion(data) {
  loadQuestionToForm(data);
}

// Bắt sự kiện nhập để update preview
questionInput.addEventListener('input', updatePreview);

// Xem trước ảnh khi chọn file mới
imageInput.addEventListener('change', function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.style.display = 'none';
    imagePreview.src = '#';
  }
});

// Nút Thêm mới - gửi yêu cầu tạo bản ghi mới, sau đó load form rỗng
addNewBtn.addEventListener('click', function() {
  fetch('add_question.php', {
    method: 'POST',
  })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.newId) {
        clearForm();
        questionIdInput.value = data.newId;
        alert('Đã tạo câu hỏi mới với ID: ' + data.newId);
      } else {
        alert('Tạo câu hỏi mới thất bại: ' + (data.error || 'Lỗi không xác định'));
      }
    })
    .catch(err => alert('Lỗi mạng: ' + err));
});

// Nút Xóa
deleteBtn.addEventListener('click', function() {
  const id = questionIdInput.value;
  if (!id) {
    alert('Vui lòng chọn câu hỏi để xóa.');
    return;
  }
  if (!confirm('Bạn có chắc muốn xóa câu hỏi này không?')) return;

  fetch('delete_question.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'id=' + encodeURIComponent(id),
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Xóa thành công');
        clearForm();
        syncTable();
      } else {
        alert('Xóa thất bại: ' + (data.error || 'Lỗi không xác định'));
      }
    })
    .catch(err => alert('Lỗi mạng: ' + err));
});

// Nút Sửa (Update)
updateBtn.addEventListener('click', function() {
  if (!questionIdInput.value) {
    alert('Vui lòng chọn câu hỏi để sửa.');
    return;
  }
  if (!form.checkValidity()) {
    alert('Vui lòng nhập đầy đủ thông tin hợp lệ.');
    return;
  }
  // Dùng FormData để gửi cả file ảnh
  const formData = new FormData(form);

  fetch('update_question.php', {
    method: 'POST',
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Cập nhật thành công');
        syncTable();
      } else {
        alert('Cập nhật thất bại: ' + (data.error || 'Lỗi không xác định'));
      }
    })
    .catch(err => alert('Lỗi mạng: ' + err));
});

// Nút Hiển thị (đồng bộ lại bảng iframe)
function syncTable() {
  if (iframe) {
    iframe.src = iframe.src;
  }
}

// Điều khiển lên xuống dòng bảng qua phím mũi tên
window.addEventListener('keydown', function(e) {
  if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'INPUT' || e.target.isContentEditable) {
    return; // Không bắt khi đang nhập liệu
  }
  if (questions.length === 0) return;
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    selectedIndex++;
    if (selectedIndex >= questions.length) selectedIndex = 0;
    loadQuestionToForm(questions[selectedIndex]);
    scrollToRow(selectedIndex);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    selectedIndex--;
    if (selectedIndex < 0) selectedIndex = questions.length -1;
    loadQuestionToForm(questions[selectedIndex]);
    scrollToRow(selectedIndex);
  }
});

// Giúp cuộn iframe tới dòng được chọn (cần logic bên get_question.php)
function scrollToRow(index) {
  // Gửi message cho iframe để nó scroll tới dòng index
  iframe.contentWindow.postMessage({action: 'scrollToRow', index: index}, window.location.origin);
}

// Khi iframe load xong, lấy danh sách câu hỏi để dùng cho điều khiển phím lên xuống
iframe.addEventListener('load', function() {
  // Gửi message yêu cầu danh sách câu hỏi
  iframe.contentWindow.postMessage({action: 'getQuestions'}, window.location.origin);
});

// Lắng nghe trả về danh sách câu hỏi từ iframe
window.addEventListener('message', function(event) {
  if (event.origin !== window.location.origin) return;
  const data = event.data;
  if (data && data.action === 'sendQuestions' && Array.isArray(data.questions)) {
    questions = data.questions;
  }
})

document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('toggleTableBtn');
  const iframe = document.getElementById('questionIframe');

  toggleBtn.addEventListener('click', () => {
      if (iframe.style.display !== 'none') {
          iframe.style.display = 'none';
          toggleBtn.textContent = 'Hiện bảng câu hỏi';
      } else {
          iframe.style.display = 'block';
          toggleBtn.textContent = 'Ẩn bảng câu hỏi';
      }
  });
});

;
