<?php
if (!function_exists('getQuestions')) {
  function getQuestions(PDO $conn, string $tableName): array {
    $rows = [];
    $topicFilter = $_GET['topic'] ?? '';

    try {
      $sql = $topicFilter !== ''
        ? "SELECT * FROM $tableName WHERE topic = :topic ORDER BY id DESC"
        : "SELECT * FROM $tableName ORDER BY id DESC";

      $stmt = $conn->prepare($sql);
      if ($topicFilter !== '') {
        $stmt->execute(['topic' => $topicFilter]);
      } else {
        $stmt->execute();
      }

      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // Optional: Log or handle error
    }

    return $rows;
  }

  function getTopics(PDO $conn, string $tableName): array {
    try {
      $stmt = $conn->query("SELECT DISTINCT topic FROM $tableName WHERE topic IS NOT NULL AND topic != '' ORDER BY topic");
      return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
      return [];
    }
  }
}
