'use strict';

/* =========== */
/* --- DOM --- */
/* =========== */
/* - Hamburger Menu - */
const hamburgerBtn = document.querySelector('#hamburger-btn');
const headerNav = document.querySelector('.header-nav');
const closeNavBtn = document.querySelector('#close-nav-btn');
/* - Setting Menu - */
const settingsBtn = document.querySelector('#settings-btn');
const historyLink = document.querySelector('#history-link');
/* - Timer - */
const timerContainer = document.querySelector('#timer-container');
const modeElement = document.querySelector('.mode');
const pauseBtn = document.querySelector('#pause-btn');
const startBtn = document.querySelector('#start-btn');
const resetBtn = document.querySelector('#reset-btn');
const sessionDots = document.querySelectorAll('.session-dot');
const focusChime = new Audio('audio/focus_start.mp3');
const breakChime = new Audio('audio/break_start.mp3');
/* - Task - */
const currentTaskContent = document.querySelector('#current-task-content');
const returnBtn = document.querySelector('.return-btn');
const currentTaskCompleteBtn = document.querySelector('.current-task-complete-btn');
const taskInput = document.querySelector('#task-input');
const addBtn = document.querySelector('#add-btn');
const charlimitWarning = document.querySelector('#charlimit-warning');
const taskList = document.querySelector('.task-list');
const currentTaskBtn = document.querySelector('.current-task-btn');
const markCompletedBtn = document.querySelector('.mark-completed-btn');
const deleteTaskBtn = document.querySelector('.delete-task-btn');
const completedList = document.querySelector('.completed-list');
/* - Timer Setting Modal - */
const notificationPopup = document.querySelector('#notification-popup');
const overlay = document.querySelector('#overlay');
const settingsModal = document.querySelector('#settings-modal');
const closeSettingsBtn = document.querySelector('#close-settings-btn');
const saveSettingsBtn = document.querySelector('#save-settings-btn');
const resetSettingsBtn = document.querySelector('#reset-settings-btn');
const bgmToggleBtn = document.querySelector('#bgm-toggle-btn');
const bgmTypeInputs = document.querySelectorAll('input[name="bgm-type"]');
/* - Study History Modal - */
const historyModal = document.querySelector('#history-modal');
const closeHistoryBtn = document.querySelector('#close-history-btn');
const historyLoginBtn = document.querySelector('#history-login-btn');
/* - Logout Modal - */
const logoutLink = document.querySelector('#logout-link');
const logoutModal = document.querySelector('#logout-modal');
const closeLogoutBtn = document.querySelector('#close-logout-btn');
const logoutBtn = document.querySelector('#logout-btn');


/* ======================== */
/* --- State Management --- */
/* ======================== */
const TEST_MODE = false; // true : Test mode, false : Production mode

let focusTime = Number(localStorage.getItem('focusTime')) || 25;
let shortBreakTime = Number(localStorage.getItem('shortBreakTime')) || 5;
let longBreakTime = Number(localStorage.getItem('longBreakTime')) || 15;

let timeLeft = TEST_MODE ? 5 : focusTime * 60;
let timerId = null; // タイマーが動いていないときはnull
let pomodoroCount = 0; // 完了したFocus session数

let bgmType = localStorage.getItem('bgmType') || 'bgm1';
let bgmEnabled = localStorage.getItem('bgmEnabled') === 'true';

const bgmSounds = {
    bgm1: new Audio('audio/bgm1_white.mp3'),
    bgm2: new Audio('audio/bgm2_crowd.mp3')
};
bgmSounds.bgm1.loop = true; // ループ再生
bgmSounds.bgm2.loop = true;

let currentBgm = null; // 現在再生中のBGMを記録

/* ================= */
/* --- Functions --- */
/* ================= */
/* ------------------- */
/* - Timer Functions - */
/* ------------------- */
/* - Timer | Start - */
function startTimer() {
    if (timerId !== null) return; // 既にタイマーが動いている場合は何もしない

    // Focusモードを開始する瞬間にBGMも再生する
    if (bgmEnabled && modeElement.classList.contains('mode-focus')) {
        startBgm(bgmType);
    }

    timerId = setInterval(function () {
        timeLeft--;
        renderTime();

        if (timeLeft <= 0) {
            clearInterval(timerId);
            timerId = null;

            if (modeElement.classList.contains('mode-focus')) {
                const currentTask = currentTaskContent.textContent;
                if (currentTask !== 'No task selected') {
                    saveStudyLog(currentTask, focusTime);
                }

                pomodoroCount++;
                renderSessionDots();

                if (pomodoroCount % 4 === 0) {
                    setLongBreak(); // 4セッション毎に長休憩
                } else {
                    setShortBreak();
                }
                startTimer(); // 休憩タイマーを自動スタート

            } else {
                if (modeElement.classList.contains('mode-long-break')) {
                    pomodoroCount = 0;
                    renderSessionDots();
                }
                setFocus();
                startTimer();
            }
        }
    }, 1000); // 1000ミリ秒 = 1秒
}

/* - Timer | 計算 - */
function formatTime() {
    const min = Math.floor(timeLeft / 60); // 分
    const sec = timeLeft % 60; // 秒
    return { min, sec };
}

/* - Timer | 表示 - */
function renderTime() {
    const time = formatTime();
    document.getElementById('minutes').textContent = String(time.min).padStart(2, '0');
    document.getElementById('seconds').textContent = String(time.sec).padStart(2, '0');
}

/* - Timer | 設定変更の反映 - */
// タイマー停止中に設定を変更した場合、表示中のモードに応じてtimeLeftを再計算し、renderTimeで反映
function syncTimeLeft() {
    if (timerId !== null) return; // タイマーが動いている間は、表示中の残り時間を変更しない

    if (modeElement.classList.contains('mode-focus')) {
        timeLeft = TEST_MODE ? 5 : focusTime * 60;
    } else if (modeElement.classList.contains('mode-short-break')) {
        timeLeft = TEST_MODE ? 5 : shortBreakTime * 60;
    } else if (modeElement.classList.contains('mode-long-break')) {
        timeLeft = TEST_MODE ? 5 : longBreakTime * 60;
    }
    renderTime();
}

/* - Timer | モード - */
function setFocus() {
    timeLeft = TEST_MODE ? 5 : focusTime * 60;
    renderTime();
    modeElement.classList.remove('mode-short-break', 'mode-long-break');
    modeElement.classList.add('mode-focus');
    timerContainer.classList.remove('short-break-mode', 'long-break-mode');
    timerContainer.classList.add('focus-mode');
    showNotification('🔔 Focus Time Started!');
    playFocusChime();
}

function setShortBreak() {
    timeLeft = TEST_MODE ? 5 : shortBreakTime * 60;
    renderTime();
    modeElement.classList.remove('mode-focus', 'mode-long-break');
    modeElement.classList.add('mode-short-break');
    timerContainer.classList.remove('focus-mode', 'long-break-mode');
    timerContainer.classList.add('short-break-mode');
    showNotification('☕ Short Break Started!');
    playBreakChime();
    stopBgm();
}

function setLongBreak() {
    timeLeft = TEST_MODE ? 5 : longBreakTime * 60;
    renderTime();
    modeElement.classList.remove('mode-focus', 'mode-short-break');
    modeElement.classList.add('mode-long-break');
    timerContainer.classList.remove('focus-mode', 'short-break-mode');
    timerContainer.classList.add('long-break-mode');
    showNotification('🌙 Long Break Started!');
    playBreakChime();
    stopBgm();
}


/* ------------------- */
/* - Session Counter - */
/* ------------------- */
function renderSessionDots() {
    sessionDots.forEach(dot => dot.classList.remove('active'));
    const activeCount = Math.min(pomodoroCount, 4);
    for (let i = 0; i < activeCount; i++) {
        sessionDots[i].classList.add('active');
    }
}

/* ---------------------- */
/* - Notification Sound - */
/* ---------------------- */
function playFocusChime() {
    focusChime.currentTime = 0;
    focusChime.play();
}

function playBreakChime() {
    breakChime.currentTime = 0;
    breakChime.play();
}

/* ----------------- */
/* - BGM Functions - */
/* ----------------- */
function startBgm(type) {
    stopBgm(); // 別のBGMを再生中の場合、先に止めてから切り替え

    currentBgm = bgmSounds[type]; // 「BGM1」「BGM2」に対応する音源を選ぶ
    currentBgm.currentTime = 0;   // 再生位置を最初に戻す
    currentBgm.play();
}

function stopBgm() {
    if (currentBgm) {
        currentBgm.pause();
        currentBgm.currentTime = 0;
        currentBgm = null;
    }
}

/* ------------------- */
/* - Modal Functions - */
/* ------------------- */
function closeModal() {
    overlay.classList.remove('show');
    settingsModal.classList.remove('show');
    historyModal.classList.remove('show');
    logoutModal.classList.remove('show');
}


/* ---------------- */
/* - Notification - */
/* ---------------- */
function showNotification(message) {
    // アプリ内通知
    notificationPopup.textContent = message;
    notificationPopup.classList.add('show');
    setTimeout(function () {
        notificationPopup.classList.remove('show');
    }, 3000);

    // OS通知
    if (
        "Notification" in window &&
        Notification.permission === "granted"
    ) {
        new Notification(message);
    }
}


/* ---------------------- */
/* - Study Log Function - */
/* ---------------------- */
// 「タスク名」と「時間(分)」を受け取り、サーバーに保存
async function saveStudyLog(task, minutes) {
    const duration = minutes * 60; // 分を秒に変換
    try {
        // fetch : ブラウザからサーバーへデータの保存命令
        const response = await fetch('api/save_studyhistory.php', {
            method: 'POST', // データ送信
            headers: { 'Content-Type': 'application/json' }, // JSON形式で送信
            body: JSON.stringify({ task: task, duration: duration }) // 送信データをJSON形式に変換
        });
        // サーバー側で保存に失敗した場合
        if (!response.ok) {
            console.error('Failed to save study log:', response.status);
        }
    } catch (error) {
        // そもそも通信自体ができなかった場合(ネットワーク接続不可・サーバー落ち等)
        console.error('Network error:', error);
    }
}


/* ============== */
/* --- Events --- */
/* ============== */
/* - Hamburger Menu - */
hamburgerBtn.addEventListener('click', function () {
    headerNav.classList.toggle('open');
    overlay.classList.toggle('show');
});

overlay.addEventListener('click', function () {
    headerNav.classList.remove('open');
    overlay.classList.remove('show');
    closeModal();
});

closeNavBtn.addEventListener('click', function () {
    headerNav.classList.remove('open');
    overlay.classList.remove('show');
});

/* - Timer Buttons - */
startBtn.addEventListener('click', startTimer);

pauseBtn.addEventListener('click', function () {
    if (timerId !== null) {
        clearInterval(timerId);
        timerId = null;
        stopBgm(); // 一時停止中はBGMを止める
    }
});

resetBtn.addEventListener('click', function () {
    clearInterval(timerId);
    timerId = null;
    stopBgm(); // リセット時はBGMを止める
    pomodoroCount = 0;
    renderSessionDots();
    setFocus();
    renderTime();
});


/* - Add Task - */
addBtn.addEventListener('click', function () {
    const taskText = taskInput.value.trim();
    if (taskText === '') return;

    const li = document.createElement('li');
    li.innerHTML = `
        <input type="checkbox" class="task-check">
        <span class="task-text">${taskText}</span>
    `;
    taskList.appendChild(li);
    taskInput.value = '';
});

taskInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') addBtn.click();
});


/* - 文字数制限 - */
taskInput.addEventListener('input', function () {
    const currentLength = taskInput.value.length;
    if (currentLength >= 30) {
        charlimitWarning.textContent = 'Maximum 30 characters.';
    } else {
        charlimitWarning.textContent = `${currentLength}/30`;
    }
});


/* - Current Task - */
currentTaskBtn.addEventListener('click', function () {
    const checkedTasks = document.querySelectorAll('.task-check:checked');
    if (checkedTasks.length === 0) {
        alert('Please select a task.');
        return;
    }
    if (checkedTasks.length > 1) {
        alert('Please select exactly one task.');
        return;
    }
    const li = checkedTasks[0].parentElement;
    currentTaskContent.textContent = li.querySelector('.task-text').textContent;
    li.remove();
});

deleteTaskBtn.addEventListener('click', function () {
    document.querySelectorAll('.task-check:checked').forEach(function (checkbox) {
        checkbox.parentElement.remove();
    });
});


/* - Move to Completed List - */
markCompletedBtn.addEventListener('click', function () {
    document.querySelectorAll('.task-check:checked').forEach(function (checkbox) {
        const li = checkbox.parentElement;
        const completedLi = document.createElement('li');
        completedLi.textContent = li.querySelector('.task-text').textContent;
        completedList.appendChild(completedLi);
        li.remove();
    });
});

currentTaskCompleteBtn.addEventListener('click', function () {
    const taskText = currentTaskContent.textContent;
    if (taskText === 'No task selected') return;
    const completedLi = document.createElement('li');
    completedLi.textContent = taskText;
    completedList.appendChild(completedLi);
    currentTaskContent.textContent = 'No task selected';
});

returnBtn.addEventListener('click', function () {
    const taskText = currentTaskContent.textContent;
    if (taskText === 'No task selected') return;
    const li = document.createElement('li');
    li.innerHTML = `
        <input type="checkbox" class="task-check">
        <span class="task-text">${taskText}</span>
    `;
    taskList.appendChild(li);
    currentTaskContent.textContent = 'No task selected';
});


/* - Timer Setting Modal - */
settingsBtn.addEventListener('click', function () {
    if (
        "Notification" in window &&
        Notification.permission === "default"
    ) {
        Notification.requestPermission();
    }
    overlay.classList.add('show');
    settingsModal.classList.add('show');
});

closeSettingsBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);

saveSettingsBtn.addEventListener('click', function () {
    // 値(ユーザー入力した時間)を取得し、タイマーで使う変数の値を更新
    // このコードがないと保存してもすぐには反映されない
    focusTime = Number(document.querySelector('#focus-time').value);
    shortBreakTime = Number(document.querySelector('#short-break-time').value);
    longBreakTime = Number(document.querySelector('#long-break-time').value);

    // localStorageに保存
    localStorage.setItem('focusTime', focusTime);
    localStorage.setItem('shortBreakTime', shortBreakTime);
    localStorage.setItem('longBreakTime', longBreakTime);

    // タイマー停止中であれば、timeLeftを再計算してrenderTimeで表示に反映
    syncTimeLeft();

    alert('Saved');
    closeModal();
});

resetSettingsBtn.addEventListener('click', function () {
    // タイマーで使う変数を初期値に戻す
    focusTime = 25;
    shortBreakTime = 5;
    longBreakTime = 15;

    // localStorageを初期値に戻す
    localStorage.setItem('focusTime', focusTime);
    localStorage.setItem('shortBreakTime', shortBreakTime);
    localStorage.setItem('longBreakTime', longBreakTime);

    // モーダル内の表示を初期値に戻す
    document.querySelector('#focus-time').value = focusTime;
    document.querySelector('#short-break-time').value = shortBreakTime;
    document.querySelector('#long-break-time').value = longBreakTime;

    // タイマー停止中であれば、timeLeftを再計算してrenderTimeで表示に反映
    syncTimeLeft();

    alert('Settings reset.');
});

/* - BGM操作 - */
bgmToggleBtn.addEventListener('click', function () {
    bgmEnabled = !bgmEnabled;
    bgmToggleBtn.setAttribute('aria-pressed', String(bgmEnabled));

    const icon = bgmToggleBtn.querySelector('i');
    icon.className = bgmEnabled ? 'fa-solid fa-volume-high' : 'fa-solid fa-volume-xmark';

    localStorage.setItem('bgmEnabled', String(bgmEnabled));

    if (bgmEnabled) {
        // タイマー作動中かつFocusモードの場合のみ、その場ですぐに再生
        if (timerId !== null && modeElement.classList.contains('mode-focus')) {
            startBgm(bgmType);
        }
    } else {
        stopBgm();
    }
});

bgmTypeInputs.forEach(function (input) {
    input.addEventListener('change', function () {
        bgmType = input.value;
        localStorage.setItem('bgmType', bgmType);

        // BGMがONかつタイマーがFocus中の場合のみ、選択したBGMに切り替えて再生
        if (bgmEnabled && timerId !== null && modeElement.classList.contains('mode-focus')) {
            startBgm(bgmType);
        }
    });
});

/* - Study History Modal - */
if (historyLink) {
    historyLink.addEventListener('click', function (event) {
        event.preventDefault();
        overlay.classList.add('show');
        historyModal.classList.add('show');
    });
}

closeHistoryBtn.addEventListener('click', closeModal);

historyLoginBtn.addEventListener('click', function () {
    location.href = 'login.php';
});


/* - Logout Modal - */
if (logoutLink) {
    logoutLink.addEventListener('click', function (event) {
        event.preventDefault();
        overlay.classList.add('show');
        logoutModal.classList.add('show');
    });
}

closeLogoutBtn.addEventListener('click', closeModal);

logoutBtn.addEventListener('click', function () {
    location.href = 'logout.php';
});


/* ================== */
/* --- 初期化処理 --- */
/* ================== */
document.querySelector('#focus-time').value = focusTime;
document.querySelector('#short-break-time').value = shortBreakTime;
document.querySelector('#long-break-time').value = longBreakTime;

// BGMの表示を初期化
document.querySelector(`input[name="bgm-type"][value="${bgmType}"]`).checked = true;

bgmEnabled = false; // ページ読込時は常にOFFから始める(前回の変更を引き継がない)(ブラウザの自動再生ポリシー対策)
bgmToggleBtn.setAttribute('aria-pressed', 'false');
bgmToggleBtn.querySelector('i').className = 'fa-solid fa-volume-xmark';