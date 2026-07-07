<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadency</title>

    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v7.2.0/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Imperial+Script&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap">
</head>

<body>
    <div id="notification-popup"></div>
    <!-- header -->
    <header>
        <div id="logo">
            <a href="index.php"><img src="images/logo.svg" alt="Cadency" class="logo"></a>
        </div>

        <!-- Hamburger button (mobile only) -->
        <button id="hamburger-btn" class="btn">
            <i class="fa-solid fa-bars"></i>
        </button>
        <!-- /Hamburger button -->

        <nav class="header-nav">
            <!-- Close nav button (mobile only) -->
            <button id="close-nav-btn" class="btn close-nav-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <!-- /Close nav button -->
            <ul class="header-nav-list">
                <li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <a href="history.php"><i class="fa-solid fa-book"></i>Study History</a>
                    <?php else: ?>
                        <a href="#" id="history-link"><i class="fa-solid fa-book"></i>Study History</a>
                    <?php endif; ?>
                </li>

                <li>
                    <button id="settings-btn" class="btn">
                        <i class="fa-solid fa-clock"></i>Timer Settings
                    </button>
                </li>

                <?php if (isset($_SESSION['email'])): ?>
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

                <?php else: ?>
                    <li>
                        <a href="login.php">
                            <i class="fa-solid fa-user"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <!-- /header -->

    <!-- main -->
    <main>
        <div id="timer-container" class="focus-mode">
            <div class="timer-panel">
                <div class="mode mode-focus">
                    <span class="mode-label-focus">Focus</span>
                    <span class="mode-label-short-break">Short Break</span>
                    <span class="mode-label-long-break">Long Break</span>
                </div>

                <h1 id="timer">
                    <span id="minutes">25</span>:<span id="seconds">00</span>
                </h1>

                <div class="timer-btns">
                    <button id="pause-btn" class="btn pause-btn"><i class="fa-regular fa-circle-pause"></i></button>
                    <button id="start-btn" class="btn start-btn"><i class="fa-solid fa-play"></i></button>
                    <button id="reset-btn" class="btn reset-btn"><i class="fa-solid fa-arrows-rotate"></i></button>
                </div>

                <div id="session-counter">
                    <div class="session-dot"></div>
                    <div class="session-dot"></div>
                    <div class="session-dot"></div>
                    <div class="session-dot"></div>
                </div>

                <div class="current-task">
                    <span class="current-task-icon"><i class="fa-solid fa-angle-right"></i></span>
                    <p id="current-task-content">No task selected</p>
                    <div class="current-task-action-btn">
                        <button class="btn return-btn" title="Return to Task List">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                        </button>
                        <button class="btn current-task-complete-btn" title="Mark as Completed">
                            <i class="fa-regular fa-circle-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="task-panel">
            <span class="task-title">Tasks</span>

            <div class="active-tasks">
                <div class="add-task">
                    <input type="text" id="task-input" placeholder="Add task..." maxlength="30">
                    <button id="add-btn" class="btn add-btn">
                        <i class="fa-solid fa-circle-plus"></i>
                    </button>
                </div>
                <p id="charlimit-warning"></p>

                <ul class="task-list">
                </ul>

                <div class="task-list-actions">
                    <button class="btn current-task-btn" title="Set as Current Task">
                        <i class="fa-solid fa-angle-right"></i>
                    </button>

                    <button class="btn mark-completed-btn" title="Mark as Completed">
                        <i class="fa-regular fa-circle-check"></i>
                    </button>

                    <button class="btn delete-task-btn" title="Delete Selected Tasks">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="completed-tasks">
                <div class="completed-title">
                    <i class="fa-regular fa-circle-check"></i>
                    <span class="completed-label">Complete</span>
                    <i class="fa-regular fa-circle-check"></i>
                </div>

                <ul class="completed-list"></ul>
            </div>

        </div>
    </main>
    <!-- /main -->

    <!-- footer -->
    <footer>
        <p class="copyright">&copy; <?= date('Y') ?> Cadency</p>
    </footer>
    <!-- /footer -->

    <!-- Timer Setting Modal -->
    <div id="overlay"></div>

    <div id="settings-modal">

        <div class="settings-header">
            <h2 class="modal-title">Timer Settings</h2>
            <button id="close-settings-btn" class="btn close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="settings-content">
            <label>
                Focus Time
                <input id="focus-time" type="number" min="1" max="180">
            </label>

            <label>
                Short Break
                <input id="short-break-time" type="number" min="1" max="60">
            </label>

            <label>
                Long Break
                <input id="long-break-time" type="number" min="1" max="120">
            </label>

            <div class="bgm-settings">
                <div class="bgm-toggle-row">
                    <span>Background Sound</span>
                    <button id="bgm-toggle-btn" class="btn bgm-toggle" aria-pressed="false">
                        <i class="fa-solid fa-volume-xmark"></i>
                    </button>
                </div>

                <div class="bgm-type-row">
                    <label>
                        <input type="radio" name="bgm-type" value="bgm1" checked>
                        BGM 1
                    </label>
                    <label>
                        <input type="radio" name="bgm-type" value="bgm2">
                        BGM 2
                    </label>
                </div>
            </div>

            <button id="save-settings-btn" class="btn modal-btn">Save</button>

            <button id="reset-settings-btn" class="btn">Reset to Default</button>
        </div>
    </div>
    <!-- /Setting Modal -->

    <!-- Study History Modal -->
    <div id="history-modal">
        <div class="settings-header">
            <h2 class="modal-title">Study History</h2>
            <button id="close-history-btn" class="btn close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="settings-content">
            <p class="modal-message">
                Study History requires an account.<br>
                Log in or create an account to save and track your study records.
            </p>
            <button id="history-login-btn" class="btn modal-btn">Log In</button>
        </div>
    </div>
    <!-- /Study History Modal -->

    <!-- Logout Modal -->
    <div id="logout-modal">
        <div class="settings-header">
            <h2 class="modal-title">Log Out</h2>
            <button id="close-logout-btn" class="btn close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="settings-content">
            <p class="modal-message">
                Are you sure you want to log out?
            </p>
            <button id="logout-btn" class="btn modal-btn">Log Out</button>
        </div>
    </div>
    <!-- /Logout Modal -->

    <!-- JavaScript -->
    <script src="js/script.js"></script>
    <!-- /JavaScript -->
</body>

</html>