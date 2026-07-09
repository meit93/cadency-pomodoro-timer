<?php

/* - Database Settings - */
// Railway上では環境変数から接続情報を取得・ローカル(XAMPP)では環境変数がないため ?: の右側の値を使用
define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost'); // データベースの場所
define('DB_PORT', getenv('MYSQLPORT')     ?: '3306');      // 接続するポート番号
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'cadency');   // データベース名
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');      // ログインユーザー名
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');          // ログインパスワード


/* - Database Connection - */
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // エラー時に例外を投げる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // 結果を連想配列で取得
            PDO::ATTR_EMULATE_PREPARES   => false,                   // SQL文の安全チェックをMySQL本体にやってもらう(より安全)
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('DB connection failed.');
}
