// Xử lý xem trước công thức MathJax
const questionInput = document.getElementById('question');
const preview = document.getElementById('preview');

questionInput.addEventListener('input', () => {
    preview.innerHTML = questionInput.value;
    MathJax.typesetPromise([preview]);
});

// Xử lý hiển thị ảnh xem trước
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');

imageInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
    } else {
        imagePreview.style.display = 'none';
        imagePreview.src = '';
    }
});

// Xử lý gửi form bằng fetch (AJAX)
const form = document.getElementById('questionForm');
const iframe = document.getElementById('questionTable');

form.addEventListener('submit', function (e) {
    e.preventDefault(); // Ngăn submit truyền thống

    const formData = new FormData(form);

    fetch('save_question.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            alert(result);

            // Reset form
            form.reset();
            imagePreview.style.display = 'none';
            preview.innerHTML = '';

            // Làm mới bảng
            iframe.contentWindow.location.reload();
        })
        .catch(error => {
            alert('Lỗi khi gửi dữ liệu!');
            console.error('Error:', error);
        });
});

// Làm mới iframe hiển thị câu hỏi
function syncTable() {
    iframe.contentWindow.location.reload();
    iframe.scrollIntoView({ behavior: 'smooth' });
}

// Giao tiếp giữa iframe và form
let selectedQuestionId = null;

window.addEventListener('message', function (event) {
    const data = event.data;
    if (data && typeof data === 'object') {
        selectedQuestionId = data.id;

        document.getElementById('question').value = data.question_text || '';
        document.querySelector('[name="answer1"]').value = data.answer1 || '';
        document.querySelector('[name="answer2"]').value = data.answer2 || '';
        document.querySelector('[name="answer3"]').value = data.answer3 || '';
        document.querySelector('[name="answer4"]').value = data.answer4 || '';
        document.querySelector('[name="correct_answer"]').value = data.correct_answer || '';

        const preview = document.getElementById('preview');
        preview.innerHTML = data.question_text || '';
        MathJax.typesetPromise([preview]);

        const imagePreview = document.getElementById('imagePreview');
        if (data.image) {
            imagePreview.src = data.image;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }
    }
});

// Xử lý xoá câu hỏi
function deleteSelected() {
    if (!selectedQuestionId) {
        alert("Vui lòng chọn một dòng để xoá.");
        return;
    }

    if (!confirm("Bạn có chắc chắn muốn xoá câu hỏi này?")) return;

    fetch(`delete_question.php?id=${selectedQuestionId}`, {
        method: 'GET'
    })
        .then(response => response.text())
        .then(result => {
            alert(result);
            selectedQuestionId = null;
            form.reset();
            preview.innerHTML = '';
            imagePreview.style.display = 'none';
            iframe.contentWindow.location.reload();
        })
        .catch(err => {
            alert("Xoá thất bại!");
            console.error(err);
        });
}

// Xử lý cập nhật câu hỏi
function updateQuestion() {
    if (!selectedQuestionId) {
        alert("Vui lòng chọn câu hỏi để sửa.");
        return;
    }

    const formData = new FormData(form);
    formData.append('id', selectedQuestionId); // Gửi ID để cập nhật

    fetch('update_question.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            alert(result);
            form.reset();
            imagePreview.style.display = 'none';
            preview.innerHTML = '';
            iframe.contentWindow.location.reload();
            selectedQuestionId = null;
        })
        .catch(error => {
            alert('Lỗi khi cập nhật!');
            console.error(error);
        });
}

// Gắn sự kiện cho nút Sửa
document.addEventListener('DOMContentLoaded', () => {
    const updateBtn = document.getElementById('updateBtn');
    if (updateBtn) {
        updateBtn.addEventListener('click', updateQuestion);
    }
});
