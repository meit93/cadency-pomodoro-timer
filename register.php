<?php

session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* - 入力チェック - */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // メールアドレスの形式が正しくない場合
        $_SESSION['register_error'] = 'Invalid email address.'; // エラーメッセージをセッションに一時保存
        header('Location: register.php'); // register.phpへリダイレクト
        exit; // 以降の処理を行わない
    }

    if (strlen($password) < 8) {
        // パスワードが8文字未満の場合
        $_SESSION['register_error'] = 'Password must be at least 8 characters.';
        header('Location: register.php');
        exit;
    }

    /* - メール重複チェック- */
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?'); // 同じemailのユーザーがいないか調べるSQLを準備
    $stmt->execute([$email]); // SQLを実行

    if ($stmt->fetch()) {
        // 実行結果を取り出してみて該当データがあった場合(登録済)
        $_SESSION['register_error'] = 'This email is already taken.';
        header('Location: register.php');
        exit;
    }

    /* - 新規ユーザー登録 - */
    $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)'); // ユーザー登録用SQLを準備
    $stmt->execute([
        $email,
        password_hash($password, PASSWORD_DEFAULT) // パスワードをデフォルトのアルゴリズムでハッシュ化
    ]);

    header('Location: login.php'); // login.phpへリダイレクト
    exit;
}

/* - エラーメッセージ取得 - */
$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account</title>

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
            <i class="fa-solid fa-user-plus auth-icon"></i>
            <h1>Create Account</h1>

            <?php if (isset($error)): ?>
                <p class="error-message">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST">

                <input type="email" name="email" placeholder="Email" autocomplete="username" required>

                <input type="password" name="password" placeholder="Password (8+ characters)"
                    autocomplete="new-password" minlength="8" required>

                <button type="submit">
                    Create Account
                </button>

            </form>

            <p class="auth-link">Already have an account?
                <a href="login.php">Log In</a>
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