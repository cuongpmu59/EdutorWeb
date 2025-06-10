<?php
require 'db_connection.php';

header('Content-Type: text/html; charset=utf-8');

$result = mysqli_query($conn, "SELECT * FROM questions ORDER BY id DESC");

echo '<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e0f7fa;
            cursor: pointer;
        }
        img {
            max-width: 80px;
            max-height: 80px;
        }
      </style>';

echo '<table>';
echo '<thead>
        <tr>
            <th>ID</th>
            <th>Câu hỏi</th>
            <th>Đáp án A</th>
            <th>Đáp án B</th>
            <th>Đáp án C</th>
            <th>Đáp án D</th>
            <th>Đáp án đúng</th>
            <th>Ảnh</th>
        </tr>
      </thead>';
echo '<tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    $imgTag = '';
    $imagePath = $row['image'];
    if (!empty($imagePath) && file_exists($imagePath)) {
        $imgTag = "<img src='{$imagePath}' alt='Ảnh'>";
    }

    // Dùng htmlspecialchars để tránh lỗi khi có dấu nháy
    echo "<tr onclick='selectRow(this)' 
            data-id='{$row['id']}'
            data-question=\"" . htmlspecialchars($row['question'], ENT_QUOTES) . "\"
            data-a=\"" . htmlspecialchars($row['answer1'], ENT_QUOTES) . "\"
            data-b=\"" . htmlspecialchars($row['answer2'], ENT_QUOTES) . "\"
            data-c=\"" . htmlspecialchars($row['answer3'], ENT_QUOTES) . "\"
            data-d=\"" . htmlspecialchars($row['answer4'], ENT_QUOTES) . "\"
            data-correct='{$row['correct_answer']}'
            data-image='" . htmlspecialchars($imagePath, ENT_QUOTES) . "'>
            <td>{$row['id']}</td>
            <td>{$row['question']}</td>
            <td>{$row['answer1']}</td>
            <td>{$row['answer2']}</td>
            <td>{$row['answer3']}</td>
            <td>{$row['answer4']}</td>
            <td>{$row['correct_answer']}</td>
            <td>{$imgTag}</td>
          </tr>";
}

echo '</tbody></table>';

mysqli_close($conn);
