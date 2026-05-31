<?php
require_once '../database.php';

header('Content-Type: application/json');

if ($bazaErr) {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, author_name, author_email, content, content_html, created_at FROM entries WHERE status = 'approved' ORDER BY created_at DESC");
    $stmt->execute();
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'entries' => $entries]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych.']);
}
