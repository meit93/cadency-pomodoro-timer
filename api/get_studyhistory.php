<?php

session_start();
require '../db.php';

// ログインチェック
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
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

// 学習記録を取得
$stmt = $pdo->prepare('SELECT task, duration, recorded_at FROM study_history WHERE user_id = ? ORDER BY recorded_at DESC');
$stmt->execute([$user['id']]);
$sessions = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($sessions);