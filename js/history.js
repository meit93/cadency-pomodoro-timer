'use strict';

/* =========== */
/* --- DOM --- */
/* =========== */
const historyList = document.querySelector('#history-list');
const totalTimeValue = document.querySelector('#total-time-value');
const sessionCount = document.querySelector('#session-count');
const clearHistoryBtn = document.querySelector('#clear-history');
const filterButtons = document.querySelectorAll('.history-filter button');


/* ======================== */
/* --- State Management --- */
/* ======================== */
let allLogs = [];
let activeRange = 'today';
let currentFilteredLogs = [];


/* ================= */
/* --- Functions --- */
/* ================= */
function renderLogs(filteredLogs) {

    currentFilteredLogs = filteredLogs;
    historyList.innerHTML = '';

    if (filteredLogs.length === 0) {
        const li = document.createElement('li');
        li.className = 'empty-state';
        li.textContent = 'No study sessions recorded yet.';
        historyList.appendChild(li);
        setStats(0, 0);
        return;
    }

    let totalMinutes = 0;

    filteredLogs.forEach(log => {
        const minutes = Math.floor(log.duration / 60);
        const date = new Date(log.recorded_at);

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const hh = String(date.getHours()).padStart(2, '0');
        const min = String(date.getMinutes()).padStart(2, '0');
        const dateStr = `${yyyy}/${mm}/${dd}`;
        const timeStr = `${hh}:${min}`;

        const li = document.createElement('li');
        li.innerHTML = `
            <div class="log-accent"></div>
            <div class="log-body">
                <div class="log-task">${escapeHtml(log.task)}</div>
                <div class="log-date">${dateStr} &nbsp;·&nbsp; ${timeStr}</div>
            </div>
            <div class="log-duration">${minutes}<span>min</span></div>
        `;
        historyList.appendChild(li);

        totalMinutes += minutes;
    });

    const uniqueTaskCount = new Set(filteredLogs.map(log => log.task)).size;
    setStats(totalMinutes, uniqueTaskCount);
}

function setStats(minutes, uniqueTasks) {
    totalTimeValue.innerHTML = `${minutes}<span class="stat-unit">min</span>`;
    sessionCount.innerHTML = `${uniqueTasks}<span class="stat-unit">tasks</span>`;
}

function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}


/* ---------------- */
/* - Date Filters - */
/* ---------------- */
function isToday(recorded_at) {
    const today = new Date();
    const date = new Date(recorded_at);
    return (
        today.getFullYear() === date.getFullYear() &&
        today.getMonth() === date.getMonth() &&
        today.getDate() === date.getDate()
    );
}

function isThisWeek(recorded_at) {
    const today = new Date();
    const date = new Date(recorded_at);
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate() - 7);
    return date >= sevenDaysAgo;
}

function isThisMonth(recorded_at) {
    const today = new Date();
    const date = new Date(recorded_at);
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    return date >= thirtyDaysAgo;
}

function getFilteredLogs(range) {
    if (range === 'today') return allLogs.filter(log => isToday(log.recorded_at));
    if (range === 'week') return allLogs.filter(log => isThisWeek(log.recorded_at));
    if (range === 'month') return allLogs.filter(log => isThisMonth(log.recorded_at));
    return allLogs;
}


/* ------------------ */
/* - Fetch from API - */
/* ------------------ */
async function fetchStudyHistory() {
    try {
        const response = await fetch('api/get_studyhistory.php');

        if (!response.ok) {
            console.error('Failed to fetch study history:', response.status);
            return;
        }

        allLogs = await response.json();
        renderLogs(getFilteredLogs(activeRange));

    } catch (error) {
        console.error('Network error:', error);
    }
}


/* ============== */
/* --- Events --- */
/* ============== */
filterButtons.forEach(button => {
    button.addEventListener('click', function () {
        filterButtons.forEach(b => b.classList.remove('active'));
        button.classList.add('active');
        activeRange = button.dataset.range;
        renderLogs(getFilteredLogs(activeRange));
    });
});

clearHistoryBtn.addEventListener('click', async function () {
    const confirmed = confirm('Delete all study history?');
    if (!confirmed) return;

    try {
        const response = await fetch('api/delete_studyhistory.php', {
            method: 'DELETE'
        });

        if (!response.ok) {
            console.error('Failed to delete study history:', response.status);
            return;
        }

        allLogs = [];
        renderLogs([]);

    } catch (error) {
        console.error('Network error:', error);
    }
});


/* ======================= */
/* --- Initial Display --- */
/* ======================= */
fetchStudyHistory();