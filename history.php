<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Study History</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/history.css">

    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v7.2.0/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Imperial+Script&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- header -->
    <header>
        <div id="logo">
            <a href="index.php"><img src="images/logo.svg" alt="Cadency" class="logo"></a>
        </div>

        <nav class="header-nav">
            <ul class="header-nav-list">
                <li>
                    <span class="login-user">
                        <i class="fa-solid fa-user"></i>
                        <?= htmlspecialchars(explode('@', $_SESSION['email'])[0]) ?>
                    </span>
                </li>

                <li>
                    <a href="#" id="logout-link">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Log Out
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <!-- /header -->

    <!-- main -->
    <main>
        <h1>Study History</h1>

        <div class="history-filter">
            <button data-range="today" class="active">Today</button>
            <button data-range="week">Week</button>
            <button data-range="month">Month</button>
        </div>

        <div class="history-stats">
            <div class="stat-card">
                <div class="stat-label">Total Time</div>
                <div class="stat-value" id="total-time-value">—<span class="stat-unit">min</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Completed Tasks</div>
                <div class="stat-value" id="session-count">—<span class="stat-unit">tasks</span></div>
            </div>
        </div>

        <ul id="history-list"></ul>

        <div class="history-actions">
            <button id="clear-history">Clear History</button>
        </div>
    </main>
    <!-- /main -->

    <!-- footer -->
    <footer>
        <p class="copyright">&copy; <?= date('Y') ?> Cadency</p>
    </footer>
    <!-- /footer -->

    <!-- JavaScript -->
    <script src="js/history.js"></script>
    <!-- /JavaScript -->
</body>

</html>