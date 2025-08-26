/* ===== Base ===== */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background: #f5f7fa;
  color: #333;
  line-height: 1.5;
  padding-top: 90px; /* chừa chỗ cho header fixed */
  overflow-y: scroll; /* chỉ có một thanh cuộn chung */
}

/* ===== Header ===== */
header {
  position: fixed;
  top: 0; left: 0; right: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #005f99;
  color: white;
  padding: 15px 30px;
  gap: 20px;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Logo trái */
.logo-left img {
  height: 55px;
  border-radius: 6px;
  display: block;
}

/* Giữa */
.header-center {
  flex: 2;
  text-align: center;
}
.header-center .exam-title {
  font-size: 20px;
  font-weight: bold;
}
.header-center .subject {
  font-size: 18px;
  margin-top: 4px;
}

/* Bên phải */
.header-right {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}
.header-right .time {
  font-size: 15px;
  font-weight: bold;
}
.progress-container {
  width: 180px;
  height: 12px;
  background: rgba(255,255,255,0.3);
  border-radius: 6px;
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  width: 0%;
  background: #ffcc00;
  transition: width 0.3s linear;
}

/* ===== Layout container ===== */
.container {
  display: flex;
  gap: 20px;
  padding: 20px;
  align-items: flex-start;
}
.left-col {
  flex: 2;
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0,0,0,.1);
}
.right-col {
  flex: 1;
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(0,0,0,.1);
  /* ❌ bỏ max-height + overflow-y để cuộn chung */
}

/* ===== Fieldset nhóm theo topic ===== */
.topic-block {
  border: 2px solid #005f99;
  border-radius: 8px;
  margin-bottom: 25px;
  padding: 15px 20px;
  background-color: #f9fbff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.topic-block legend {
  font-weight: bold;
  font-size: 18px;
  color: #005f99;
  padding: 0 10px;
}
.topic-block .qtext {
  margin: 10px 0 15px 0;
  font-size: 16px;
}
.topic-block .answers label {
  display: block;
  margin: 6px 0;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
}
.topic-block .answers label:hover {
  background-color: #eef6ff;
}

/* ===== Câu hỏi ===== */
.question {
  margin-bottom: 20px;
  padding: 15px;
  border-bottom: 1px solid #ddd;
}
.question h3 { 
  margin: 0 0 10px; 
}

/* ===== Đáp án A B C D tự co giãn ===== */
.answers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 8px 16px;
  margin-top: 10px;
}
.answers-grid label {
  display: flex;
  align-items: center;
  word-break: break-word;
  white-space: normal;
  cursor: pointer;
}

/* ===== Ảnh minh họa câu hỏi ===== */
.qimage {
  text-align: center;
  margin-top: 12px;
}
.qimage img {
  max-width: 90%;
  height: auto;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,.15);
}

/* ===== Phiếu trả lời ===== */
.answer-sheet h3 {
  text-align: center;
  margin-top: 0;
  margin-bottom: 15px;
  font-size: 18px;
  font-weight: bold;
  color: #005f99;
}
.answer-row {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}
.answer-row span {
  width: 25px;
  display: inline-block;
}

/* ===== Nút thao tác ===== */
.actions {
  margin-top: 20px;
  text-align: center;
}
button {
  padding: 10px 20px;
  margin: 0 8px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 15px;
  font-weight: bold;
}
#btnSubmit { background: #28a745; color: white; }
#btnShow { background: #ffc107; color: black; }
#btnSubmit:disabled, #btnShow:disabled {
  opacity: .5; cursor: not-allowed;
}

/* ===== Khi nộp bài thì làm mờ ===== */
.dim { opacity: .4; }

/* ===== Đáp án đúng highlight ===== */
.correct-answer {
  background-color: #d9fdd3 !important;
  font-weight: bold;
  color: #1d5e23;
}
