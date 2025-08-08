<?php // pages/mc/cloudinary_form.php ?>

<div style="max-width: 320px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <h3>📤 Upload Ảnh lên Cloudinary</h3>

    <input type="file" id="uploadImage" accept="image/*" />
    <br><br>

    <div id="previewContainer" style="display:none; text-align:center;">
        <img id="preview" src="" alt="Preview" style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:6px; border-radius:6px;">
        <br><br>
    </div>

    <button id="btnUpload" style="margin-right:8px;">📤 Upload</button>
    <button id="btnDelete" style="display:none;">🗑 Xóa ảnh</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const apiUrl = '../../includes/mc/cloudinary_action.php';
let currentPublicId = '';

function resetPreview() {
    $('#previewContainer').hide();
    $('#preview').attr('src', '');
    $('#uploadImage').val('');
    $('#btnDelete').hide();
    currentPublicId = '';
}

function toggleButton(selector, disabled) {
    $(selector).prop('disabled', disabled);
    if (selector === '#btnUpload') {
        $(selector).text(disabled ? '⏳ Đang tải lên...' : '📤 Upload');
    }
}

$('#uploadImage').on('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview').attr('src', e.target.result);
            $('#previewContainer').show();
            $('#btnDelete').hide();
            currentPublicId = '';
        };
        reader.readAsDataURL(file);
    } else {
        resetPreview();
    }
});

$('#btnUpload').on('click', function () {
    const file = $('#uploadImag
