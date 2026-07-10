<?php

session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* - emailでユーザー検索 - */
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?'); // usersテーブルからemail検索するSQLを準備
    $stmt->execute([$email]); // $emailの値でSQLを実行
    $user = $stmt->fetch(); // 実行結果を1行取り出して$userに代入

    /* - ログイン判定 - */
    if ($user && password_verify($password, $user['password'])) {
        // ユーザーが存在し、かつパスワードが一致した場合
        $_SESSION['email'] = $email; // ログイン状態としてemailをセッションに保存
        header('Location: index.php'); // index.phpへリダイレクト
        exit; // 以降の処理を行わない
    }

    /* - ログイン失敗時のリダイレクト - */
    $_SESSION['login_error'] = 'Invalid email or password.'; // エラーメッセージをセッションに一時保存
    header('Location: login.php');
    exit;
}

/* --エラーメッセージ取得 --- */
$error = $_SESSION['login_error'] ?? null; // セッションに値があればそれを、無ければnullを$errorに入れる
unset($_SESSION['login_error']); // 取り出したら削除
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log In</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">

    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v7.2.0/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Imperial+Script&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <?php include 'ga.php'; ?>
</head>

<body>
    <!-- header -->
    <header>
        <div id="logo">
            <a href="index.php"><img src="images/logo.svg" alt="Cadency" class="logo"></a>
        </div>
    </header>
    <!-- /header -->

    <!-- main -->
    <main>
        <div class="auth-container">
            <i class="fa-solid fa-user-lock auth-icon"></i>
            <h1>Log In</h1>

            <?php if (isset($error)): ?>
                <p class="error-message">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email" autocomplete="username" required>
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                <button type="submit">Log In</button>
            </form>

            <p class="auth-link">Do not have an account?
                <a href="register.php">Create Account</a>
            </p>

            <p class="guest-note">
                You can use the timer without an account.<br>
                Create an account to save your study history.
            </p>

        </div>
    </main>
    <!-- /main -->

    <!-- footer -->
    <footer>
        <p class="copyright">&copy; <?= date('Y') ?> Cadency</p>
    </footer>
    <!-- /footer -->
</body>

</html>