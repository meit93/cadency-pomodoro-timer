<?php

session_start();
require '../db.php';

// ログインチェック
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

// JSONデータ受取
$data     = json_decode(file_get_contents('php://input'), true);
$task     = $data['task'] ?? '';
$duration = $data['duration'] ?? null;

// 検証
if (!is_string($task) || mb_strlen($task) > 30) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid task']));
}

if (!is_int($duration) || $duration <= 0) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid duration']));
}

// user_idを取得
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$_SESSION['email']]);
$user = $stmt->fetch();

// 該当ユーザーが見つからない場合
if (!$user) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

// 学習記録を保存
$stmt = $pdo->prepare('INSERT INTO study_history (user_id, task, duration) VALUES (?, ?, ?)');
$stmt->execute([$user['id'], $task, $duration]);

http_response_code(201);
echo json_encode(['message' => 'Saved']);