<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mahasiswa Activities</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: #0f172a;
      color: #1e293b;
      min-height: 100vh;
    }

   body::before {
  content: "";
  position: fixed;
  inset: 0;

  background: radial-gradient(
    circle at top left,
    rgba(255,255,255,0.06),
    rgba(10,15,30,0.2)
  );

  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  pointer-events: none;
  z-index: -1;
}

.hidden {
  display: none !important;
}

   body::after {
  content: '';
  position: fixed;
  inset: 0;
  background: rgba(15,23,42,0.55);
  z-index: -1;
  pointer-events: none;
}

    .container {
      max-width: 1200px;
      margin: 80px auto;
      padding: 60px;
      background: rgba(255,255,255,0.65);
      backdrop-filter: blur(120px) saturate(1.1);
      -webkit-backdrop-filter: blur(120px) saturate(1.1);
      border-radius: 32px;
      box-shadow: 0 40px 120px rgba(0,0,0,0.35);
      border: 1px solid rgba(255,255,255,0.4);
      animation: fadeSlide 1.1s ease;
    }

    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .branding {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 60px;
      align-items: center;
      margin-bottom: 70px;
    }

    .photo-row { display: flex; gap: 20px; }

    .photo-row img {
      width: 100%;
      max-width: 180px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.35);
    }

    .name-block h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(42px, 5vw, 64px);
      color: #1e3a8a;
      margin-bottom: 14px;
    }

    .name-block p {
      letter-spacing: 2px;
      text-transform: uppercase;
      font-size: 15px;
      color: #334155;
    }

    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 10px;
      color: #0f172a;
    }

    .hint {
      font-size: 14px;
      color: #475569;
      margin-bottom: 22px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .hint span {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #3b82f6;
      opacity: 0.8;
    }

    .buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 28px;
    }

    .buttons button {
      padding: 10px 18px;
      border-radius: 999px;
      border: none;
      background: #1e3a8a;
      color: white;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
    }

    .buttons button:hover {
      background: #2563eb;
      transform: translateY(-2px);
    }

    .table-wrapper {
      max-height: 180px;
      overflow: hidden;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .table-wrapper.show { opacity: 1; }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0,0,0,0.18);
    }

    thead {
      background: linear-gradient(90deg, #1e3a8a, #3b82f6);
      color: white;
    }

    th, td {
      padding: 16px;
      text-align: center;
      height: 56px;
    }

    tbody tr { background: rgba(248,250,252,0.85); }

    .status { font-weight: 600; color: #065f46; }

    .quote {
      margin-top: 70px;
      text-align: center;
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      color: #1f2933;
    }

    @media (max-width: 768px) {
      .container { padding: 24px 14px; }
      .branding { grid-template-columns: 1fr; text-align: center; }
      .photo-row { justify-content: center; }
      .photo-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
  }

  .photo-row img {
    max-width: 100%;
    width: 100%;
    height: auto;
    border-radius: 16px;
    }
  }

  .photo-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.photo-modal.hidden {
  display: none;
}

.photo-modal img {
  width: auto;
  max-width: 85vw;
  max-height: 80vh;
  object-fit: contain;
  border-radius: 18px;        
  box-shadow: 0 25px 60px rgba(0,0,0,0.45);

  animation: photoZoomIn 0.35s ease;
}

@keyframes photoZoomIn {
  from {
    transform: scale(0.88);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

  .checkin {
  margin-top: 80px;
  padding: 40px;
  border-radius: 24px;
  background: rgba(255,255,255,0.55);
  backdrop-filter: blur(18px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.checkin-text {
  margin: 12px 0 26px;
  font-size: 16px;
  color: #334155;
}

.checkin-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.checkin-buttons button {
  padding: 12px 22px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
  background: #0f172a;
  color: white;
}

.checkin-buttons button:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.checkin-result {
  margin-top: 22px;
  font-weight: 500;
  color: #1e293b;
}
.category {
  margin-top: 80px;
  padding: 40px;
  border-radius: 24px;
  background: rgba(255,255,255,0.55);
  backdrop-filter: blur(18px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.category-text {
  margin: 12px 0 26px;
  font-size: 16px;
  color: #334155;
}

.category-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
}

.category-buttons button {
  padding: 12px 20px;
  border-radius: 999px;
  border: none;
  background: #1e3a8a;
  color: white;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}

.category-buttons button:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.category-result {
  margin-top: 28px;
  font-weight: 500;
  color: #1e293b;
  line-height: 1.6;
}

.action {
  margin-top: 80px;
  padding: 40px;
  border-radius: 24px;
  background: rgba(255,255,255,0.55);
  backdrop-filter: blur(18px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.action-text {
  margin: 12px 0 26px;
  font-size: 16px;
  color: #334155;
}

.action-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
}

.action-buttons button {
  padding: 12px 20px;
  border-radius: 999px;
  border: none;
  background: #0f172a;
  color: white;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}

.action-buttons button:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.action-result {
  margin-top: 24px;
  font-weight: 500;
  color: #1e293b;
}

.history {
  margin-top: 80px;
  padding: 40px;
  border-radius: 24px;
  background: rgba(255,255,255,0.55);
  backdrop-filter: blur(18px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);

  overflow: hidden;
  will-change: transform, opacity;
  transform: translateZ(0);
}

.history-text {
  margin: 12px 0 22px;
  font-size: 15px;
  color: #334155;
}

.history-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.history-list li {
  padding: 14px 18px;
  border-radius: 14px;
  background: rgba(248,250,252,0.9);
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  font-size: 14px;
  color: #1e293b;
}

.history-list li.empty {
  text-align: center;
  color: #64748b;
  background: transparent;
  box-shadow: none;
}

.history-buttons button {
  padding: 12px 20px;
  border-radius: 999px;
  border: none;
  background: #0f172a;
  color: white;
  cursor: pointer;
  font-weight: 500;
  transition: 0.3s;
}
.hidden {
  display: none;
}

.confetti {
  position: fixed;
  width: 8px;
  height: 8px;
  background: hsl(var(--hue), 90%, 60%);
  top: -10px;
  animation: fall 2s linear forwards;
  border-radius: 50%;
  z-index: 9999;
}

@keyframes fall {
  to {
    transform: translateY(110vh) rotate(360deg);
    opacity: 0;
  }
}

.category-cta {
  margin-top: 28px;
  display: flex;
  justify-content: center;
}

.category-cta button {
  padding: 14px 34px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #2563eb, #1e40af);
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 12px 30px rgba(37,99,235,0.35);
  transition: all 0.3s ease;
}

.category-cta button:hover {
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 18px 40px rgba(37,99,235,0.45);
}

.mini-game {
  margin-top: 24px;
  padding: 24px;
  border-radius: 20px;
  background: rgba(255,255,255,0.8);
  text-align: center;
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {

  .mini-game {
    padding-bottom: 120px;
  }
}

@media (max-width: 768px) {
  .action {
    padding-bottom: 180px;
  }
}

.game-task {
  font-weight: 600;
  font-size: 18px;
  margin-bottom: 14px;
}

.game-timer {
  font-size: 16px;
  margin-bottom: 18px;
  color: #1e40af;
}

.mini-game button {
  padding: 12px 26px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
  font-weight: 600;
  cursor: pointer;
}

.game-sidebar {
  position: fixed;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  width: 280px;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.25);
  border: 1px solid rgba(255,255,255,0.6);
  z-index: 1000;
  display: none;
  flex-direction: column;
  gap: 15px;
}

.game-sidebar.show {
  display: flex;
  animation: slideInRight 0.4s ease;
}

@keyframes slideInRight {
  from { opacity: 0; transform: translateY(-50%) translateX(100%); }
  to { opacity: 1; transform: translateY(-50%) translateX(0); }
}

.sidebar-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.sidebar-title {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.progress-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: conic-gradient(#22c55e 0deg 180deg, #e2e8f0 180deg 360deg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #059669;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(34,197,94,0.3);
}

.validation-box {
  background: rgba(248,250,252,0.8);
  padding: 12px 16px;
  border-radius: 12px;
  border-left: 4px solid #3b82f6;
  font-size: 13px;
  line-height: 1.4;
}

.validation-success { border-left-color: #059669 !important; }
.validation-warning { border-left-color: #d97706 !important; }
.validation-error { border-left-color: #dc2626 !important; }

.validation-icon {
  font-size: 16px;
  margin-right: 8px;
}

.time-display {
  text-align: center;
  padding: 12px;
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  color: white;
  border-radius: 12px;
  font-weight: 500;
  font-size: 14px;
}

@media (max-width: 768px) {
  .game-sidebar {
    position: fixed;
    bottom: 12px;
    left: 50%;
    right: auto;
    top: auto;
    transform: translateX(-50%);
    width: calc(100vw - 32px);
    max-width: 420px;
    z-index: 9999;
  }
}
  </style>
</head>
<body>

<div class="container">

  <section class="branding">
    <div class="photo-row">
      <img src="profile1.jpg" alt="photo 1" onclick="openPhoto(this.src)">
      <img src="profile2.jpg" alt="photo 2" onclick="openPhoto(this.src)">
      <img src="profile3.jpg" alt="photo 3" onclick="openPhoto(this.src)">
    </div>
    <div class="name-block">
      <h1>Rosi Amelia</h1>
      <p id="today"></p>
    </div>
  </section>

  <section>
    <h2>Ocii's Creative Activities</h2>
    <div class="hint">
      <span></span><span></span><span></span>
      <p>Double tap an activity to view details</p>
    </div>

    <div class="buttons">
      <button ondblclick="handleActivity('Editing Video','Video Editing')">Editing</button>
      <button ondblclick="handleActivity('Photography','Photography')">Photography</button>
      <button ondblclick="handleActivity('Filmmaking','Filmmaking')">Filmmaker</button>
      <button ondblclick="handleActivity('Coding JavaScript','Programming')">Coding</button>
      <button ondblclick="handleActivity('Sleeping','Resting')">Sleep</button>
      <button ondblclick="handleActivity('Playing MLBB','Gaming')">MLBB</button>
    </div>

    <div id="tableWrapper" class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Hobby</th>
            <th>Current Activity</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Rosi Amelia</td>
            <td id="hobby">-</td>
            <td class="status" id="activity">Idle</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <section class="checkin">
  <h2>Your Creative Check-In</h2>
  <p class="checkin-text">
    Have you done any creative activity today?
  </p>

  <div class="checkin-buttons">
    <button onclick="checkInYes()">Yes, already ✨</button>
    <button onclick="checkInNo()">Not yet</button>
  </div>
  <div id="checkinResult" class="checkin-result"></div>
</section>

<section class="category hidden">
  <h2>Choose an Activity Category</h2>
  <p class="category-text">
    Pick a category and discover activities you can do right now.
  </p>

  <div class="category-buttons">
    <button onclick="showCategory('word')">✍️ Word & Thinking</button>
    <button onclick="showCategory('visual')">🎨 Visual & Creative</button>
    <button onclick="showCategory('coding')">💻 Logic & Coding</button>
    <button onclick="showCategory('movement')">🚴 Movement & Body</button>
    <button onclick="showCategory('relax')">🎮 Relax & Fun</button>
  </div>

  <div id="categoryResult" class="category-result"></div>

  <div class="category-cta hidden" id="categoryCTA">
    <button onclick="openAction()">🚀 Start Activity</button>
  </div>
</section>

<section class="action hidden">
  <h2>Start a Small Action</h2>
  <p class="action-text">
    Choose one task and complete it below.
  </p>

  <div class="action-buttons" id="actionButtons"></div>
  <div id="actionResult" class="action-result"></div>

  <div id="miniGame" class="mini-game hidden">

    <div id="gameSidebar" class="game-sidebar">
      <div class="sidebar-header">
        <div class="progress-circle" id="progressCircle">
          <span id="progressText">0%</span>
        </div>
        <h4 class="sidebar-title" id="sidebarTitle">Task Progress</h4>
      </div>

      <div id="sidebarValidation" class="validation-box">
        📝 Start typing...
      </div>
      <div class="time-display">
        ⏳ <span id="sidebarTimeLeft">60</span>s
      </div>
    </div>

  <div style="position: relative; z-index: 2;">
    <p id="gameTask" class="game-task"></p>
    <div id="gameArea"></div>
    <div class="game-timer">
      ⏳ Time left: <span id="timeLeft">60</span>s
    </div>
    <button id="finishBtn" onclick="completeGame()" disabled style="margin-top: 15px;">
      ✅ Finish & Save
    </button>
  </div>
</div>

  </div>
</section>

<section class="history hidden">
<section class="history">
  <h2>Activity History</h2>
  <p class="history-text">
    Your recent actions will appear here.
  </p>

  <ul id="historyList" class="history-list">
    <li class="empty">No activity yet. Start one above ✨</li>
  </ul>
  <div class="quote">
    “Small consistent actions shape remarkable growth.”
  </div>
</section>
</section>

  <div id="photoModal" class="photo-modal hidden">
      <img id="modalImage">
   </div>
  </div>


<script>
let activeCategory = null;
let timer = null;
let currentGame = null;
let gameResult = null; 
const miniGames = {
  word: [
    { type: "text", task: "Write 3 simple sentences", time: 90 },
    { type: "text", task: "Write your feeling today", time: 60 },
    { type: "text", task: "Write 5 English words", time: 45 }
  ],
  visual: [
    { type: "color", task: "Choose your favorite color", time: 40 },
    { type: "click", task: "Click the box 5 times", time: 45 },
    { type: "draw", task: "Draw anything simple", time: 60 }
  ],
  coding: [
    { type: "code", task: "Type: console.log('Hello')", time: 90 },
    { type: "code", task: "Create a variable named score", time: 90 },
    { type: "code", task: "Write 1 JavaScript comment", time: 60 }
  ],
  movement: [
    { type: "confirm", task: "Stretch your body now", time: 45 },
    { type: "confirm", task: "Walk for one minute", time: 60 },
    { type: "confirm", task: "Do 10 slow jumps", time: 50 }
  ],
  relax: [
    { task: "Listen to one song quietly", time: 60 },
    { task: "Play one short game or MLBB", time: 90 },
    { task: "Close eyes and breathe slowly", time: 45 }
  ]
};

const student = {
  name: 'Rosi Amelia',
  activity: 'Idle',
  hobby: '-',
  update(activityName, hobbyName) {
    this.activity = activityName;
    this.hobby = hobbyName;
    document.getElementById('activity').textContent = this.activity;
    document.getElementById('hobby').textContent = this.hobby;
  }
};

let lastActivity = null;

function updateSidebar(title, progress, message) {
  document.getElementById("sidebarTitle").textContent = title;
  document.getElementById("progressText").textContent = Math.round(progress) + "%";
  
  const circle = document.getElementById("progressCircle");
  const gradientAngle = (progress / 100) * 360;
  circle.style.background = `conic-gradient(#22c55e 0deg ${gradientAngle}deg, #e2e8f0 ${gradientAngle}deg 360deg)`;
  
  const validationEl = document.getElementById("sidebarValidation");
  validationEl.innerHTML = `<span class="validation-icon">${getValidationIcon(progress)}</span>${message}`;
  validationEl.className = `validation-box validation-${getValidationType(progress)}`;
}

function getValidationIcon(progress) {
  if (progress >= 100) return "✅";
  if (progress >= 50) return "🔥";
  if (progress > 0) return "⚠️";
  return "📝";
}

function getValidationType(progress) {
  if (progress >= 100) return "success";
  if (progress >= 50) return "warning";
  return "error";
}

function startTimer(seconds) {
  let timeLeft = seconds;

  clearInterval(timer);

  timer = setInterval(() => {
    document.getElementById("timeLeft").textContent = timeLeft;
    document.getElementById("sidebarTimeLeft").textContent = timeLeft;

    if (timeLeft <= 0) {
      clearInterval(timer);
      gameResult = "fail";

      updateSidebar(
        document.getElementById("sidebarTitle").textContent,
        0,
        "⏰ Time is up! Task failed."
      );

      const inputs = document
        .getElementById("gameArea")
        .querySelectorAll("textarea, input");

      inputs.forEach(el => el.disabled = true);

      return;
    }

    timeLeft--;
  }, 1000);
}

function enableFinish() {
  if (gameResult === "fail") return;

  gameResult = "success";
  const btn = document.getElementById("finishBtn");
  btn.disabled = false;
  btn.style.background =
    "linear-gradient(135deg, #22c55e, #16a34a)";
}

function disableFinish() {
  if (gameResult === "success") gameResult = null;

  const btn = document.getElementById("finishBtn");
  btn.disabled = true;
  btn.style.background =
    "linear-gradient(135deg, #6b7280, #4b5563)";
}

function handleActivity(activityName, hobbyName) {
  const table = document.getElementById('tableWrapper');
  if (lastActivity === activityName && table.classList.contains('show')) {
    table.classList.remove('show');
  } else {
    table.classList.add('show');
    student.update(activityName, hobbyName);
    lastActivity = activityName;
  }
}

function showCategory(key) {
  const categoryResult = document.getElementById('categoryResult');
  const categoryCTA = document.getElementById('categoryCTA');
  const actionSection = document.querySelector('.action');
  const buttonsArea = document.getElementById('actionButtons');

  if (activeCategory === key) {
    categoryResult.innerHTML = '';
    categoryCTA.classList.add('hidden');
    actionSection.classList.add('hidden');
    buttonsArea.innerHTML = '';
    activeCategory = null;
    return;
  }

  activeCategory = key;
  actionSection.classList.add('hidden');
  
  categoryResult.innerHTML = `
    <div style="background: rgba(248,250,252,0.8); padding: 20px; border-radius: 16px; margin-top: 20px;">
      <h3 style="color: #1e3a8a; margin-bottom: 10px;">🎯 ${categories[key][0]}</h3>
      <p style="color: #475569;">Great choice! This category includes: ${categories[key].slice(1, 4).join(', ')} and more.</p>
    </div>
  `;
  categoryCTA.classList.remove('hidden');
}

function openAction() {
  document.querySelector('.action').classList.remove('hidden');
  document.getElementById('categoryCTA').classList.add('hidden');
  
  const buttonsArea = document.getElementById('actionButtons');
  if (activeCategory && miniGames[activeCategory]) {
    const buttons = miniGames[activeCategory]
      .map((g, i) => 
        `<button onclick="startGame('${activeCategory}', ${i})" style="min-width: 160px; padding: 14px 20px;">
          🎮 ${g.task}
        </button>`
      )
      .join('');
    buttonsArea.innerHTML = buttons;
  }
}

function startGame(category, index) {
  document.getElementById("actionResult").innerHTML = "";
  const game = miniGames[category][index];
  currentGame = {
  category,
  index,
  task: game.task
};
  const area = document.getElementById("gameArea");
  const miniGame = document.getElementById("miniGame");

  document.getElementById("finishBtn").disabled = true;
  document.getElementById("finishBtn").style.background = "linear-gradient(135deg, #6b7280, #4b5563)";
  area.innerHTML = "";
  miniGame.classList.remove("hidden");
  document.getElementById("gameTask").textContent = "🎯 " + game.task;

  document.getElementById("gameSidebar").classList.add("show");
  updateSidebar(game.task, 0, "Start your task...");

  if (game.type === "text") {
    area.innerHTML = `
      <div style="text-align:center;">
        <textarea id="textInput" rows="4" placeholder="Write 3 COMPLETE sentences with proper punctuation (. ! ?)" style="width:100%;padding:12px;font-family:inherit;border:2px solid #e2e8f0;border-radius:12px;font-size:16px;"></textarea>
      </div>`;
    
    const textarea = document.getElementById("textInput");
    textarea.addEventListener("input", e => {
      const text = e.target.value.trim();
      const sentences = text.split(/[.!?,]+/).filter(s => s.trim().length > 0);
      const wordCount = text.split(/\s+/).filter(w => w.length > 0).length;
      const progress = Math.min((sentences.length / 3) * 100, 100);
      
updateSidebar(
  game.task,
  progress,
  sentences.length >= 3 && wordCount >= 12
    ? "Perfect! 3+ sentences ready!"
    : `⚠️ ${sentences.length}/3 sentences (${wordCount} words)`
);

  if (sentences.length >= 3) {
     enableFinish();
  } else {
    disableFinish();
      }
    });

  } else if (game.type === "code") {
    const codeKeywords = game.task.includes("console.log") ? ["console", "log"] : 
                        game.task.includes("score") ? ["score", "let", "const"] : ["//"];
    
    area.innerHTML = `
      <textarea id="codeInput" rows="3" placeholder="Write your code here..." style="width:100%;padding:12px;font-family:monospace;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;"></textarea>`;
    
    const textarea = document.getElementById("codeInput");
    textarea.addEventListener("input", e => {
      const code = e.target.value.toLowerCase().trim();
      const hasKeywords = codeKeywords.every(keyword => code.includes(keyword));
      const hasLength = code.length >= 10;
      const progress = hasKeywords && hasLength ? 100 : (code.length / 20) * 100;
      
      updateSidebar(game.task, progress, 
        hasKeywords && hasLength ? "Code syntax correct!" : "⚠️ Check keywords & length"
      );
      
      if (hasKeywords && hasLength) enableFinish();
    });

  } else if (game.type === "color") {
    area.innerHTML = `<input type="color" id="colorInput" style="width:120px;height:120px;border-radius:50%;border:none;box-shadow:0 10px 25px rgba(0,0,0,0.2);cursor:pointer;">`;
    document.getElementById("colorInput").addEventListener("input", () => {
      updateSidebar(game.task, 100, "Beautiful color chosen!");
      enableFinish();
    });

  } else if (game.type === "click") {
    let count = 0;
    area.innerHTML = `
      <button id="clickBox" style="padding:20px 40px;font-size:20px;font-weight:600;background:#3b82f6;color:white;border:none;border-radius:16px;cursor:pointer;box-shadow:0 8px 20px rgba(59,130,246,0.4);">
        Click me <span id="clickCount">0</span>/5
      </button>`;
    
    const box = document.getElementById("clickBox");
    const countSpan = document.getElementById("clickCount");
    
    box.onclick = () => {
      count++;
      countSpan.textContent = count;
      const progress = (count / 5) * 100;
      
      updateSidebar(game.task, progress, `${count}/5 clicks - ${count >= 5 ? "✅" : "🔥"}`);
      
      if (count >= 5) {
        box.textContent = "Perfect! 5/5";
        box.style.background = "#22c55e";
        enableFinish();
      }
    };

  } else if (game.type === "draw" || game.type === "confirm") {
    area.innerHTML = `
      <div style="text-align:center;padding:25px;">
        <div style="font-size:52px;margin-bottom:20px;">${game.type === "draw" ? "🖌️" : "💪"}</div>
        <p style="font-size:20px;font-weight:600;margin-bottom:20px;">${game.task}</p>
        <label style="display:flex;align-items:center;gap:12px;justify-content:center;font-size:18px;background:rgba(248,250,252,0.8);padding:18px 30px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
          <input type="checkbox" id="confirmCheck" style="width:24px;height:24px;">
          Yes, I did it!
        </label>
      </div>`;
    
    document.getElementById("confirmCheck").addEventListener("change", e => {
      if (e.target.checked) {
        updateSidebar(game.task, 100, "Task confirmed!");
        enableFinish();
      setTimeout(() => {
        document.getElementById("gameSidebar").classList.remove("show");
        }, 300);
      }
    });

 } else {

  area.innerHTML = `
    <div style="text-align:center;padding:25px;">
      <div style="font-size:52px;margin-bottom:20px;">😌</div>
      <p style="font-size:20px;font-weight:600;margin-bottom:20px;">
        ${game.task}
      </p>
      <label style="display:flex;align-items:center;gap:12px;justify-content:center;
        font-size:18px;background:rgba(248,250,252,0.8);padding:18px 30px;
        border-radius:16px;">
        <input type="checkbox" id="relaxConfirm" style="width:24px;height:24px;">
        Yes, I really did it
      </label>
    </div>
  `;

  document.getElementById("relaxConfirm").addEventListener("change", e => {
    if (e.target.checked) {
      updateSidebar(game.task, 100, "😌 Relax confirmed!");
      enableFinish();
      setTimeout(() => {
        document.getElementById("gameSidebar").classList.remove("show");
      }, 300);
    }
  });
}

  startTimer(game.time);
}

function completeGame() {
  const finishBtn = document.getElementById("finishBtn");
  const gameName = currentGame?.task || "Unknown Task";

  clearInterval(timer);
  document.getElementById("miniGame").classList.add("hidden");
  document.getElementById("gameSidebar").classList.remove("show");

  if (gameResult === "success") {
    document.getElementById("actionResult").innerHTML = `
      <div style="background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white; padding: 25px; border-radius: 20px; text-align: center;">
        <div style="font-size:48px;">🎉</div>
        <h3>Succes!</h3>
        <p>Succesful to do <strong>${gameName}</strong></p>
      </div>
    `;

    addToHistory(`Succesful to do "${gameName}"`);
    celebrate();

  } else {
    document.getElementById("actionResult").innerHTML = `
      <div style="background: linear-gradient(135deg, #ef4444, #b91c1c);
        color: white; padding: 25px; border-radius: 20px; text-align: center;">
        <div style="font-size:48px;">😢</div>
        <h3>Failed</h3>
        <p>Failed to do <strong>${gameName}</strong></p>
        <small>Try again! 💙</small>
      </div>
    `;

    addToHistory(`Failed to do "${gameName}", Try again?`);
  }
  
  gameResult = null;
    currentGame = null;

  document.querySelectorAll("button").forEach(btn => {
    btn.disabled = false;
  });

  finishBtn.style.background =
    "linear-gradient(135deg, #6b7280, #4b5563)";
}

const categories = {
  word: ['Writing stories or poetry','Journaling','Public speaking','Debating ideas','Reading books or articles','Script writing','Copywriting'],
  visual: ['Photography','Videography','Filmmaking','Graphic design','Illustration','Content creation','Photo editing'],
  coding: ['Web development','Game development','App development','UI logic design','Problem solving','Automation scripting','Algorithm thinking'],
  movement: ['Cycling','Jogging','Dancing','Workout','Martial arts','Stretching routines','Outdoor activities'],
  relax: ['Gaming','Listening to music','Watching movies','Casual streaming','Hobby scrolling','Light entertainment']
};

const today = new Date();
document.getElementById('today').textContent = today.toLocaleDateString('en-US', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
});

const activityHistory = [];

function addToHistory(actionName) {
  const time = new Date().toLocaleString('en-US', {weekday: 'short', hour: '2-digit', minute: '2-digit'});
  activityHistory.unshift(`${time} : ${actionName}`);
  renderHistory();
}

function renderHistory() {
  const list = document.getElementById('historyList');
  if (activityHistory.length === 0) {
    list.innerHTML = '<li class="empty">No activity yet. Start one above ✨</li>';
    return;
  }
  list.innerHTML = '';
  activityHistory.slice(0, 5).forEach(item => {
    const li = document.createElement('li');
    li.textContent = item;
    list.appendChild(li);
  });
}

function checkInYes() {
  document.getElementById('checkinResult').innerHTML = "✨ Amazing! You've made a step today. Keep up the consistency, okay?💙";
  celebrate();
}

function checkInNo() {
  document.getElementById('checkinResult').innerHTML = "🌱 It's okay. Every process starts with a small intention. Let's choose one activity below. 👇";
  document.querySelector('.category').classList.remove('hidden');
  celebrate();
  document.getElementById('categoryCTA').classList.remove('hidden');
  document.querySelector('.history').classList.remove('hidden');
}

function celebrate() {
  for (let i = 0; i < 40; i++) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.left = Math.random() * 100 + 'vw';
    confetti.style.animationDelay = Math.random() + 's';
    confetti.style.setProperty('--hue', Math.random() * 360);
    document.body.appendChild(confetti);
    setTimeout(() => confetti.remove(), 2000);
  }
}

function openPhoto(src) {
  const modal = document.getElementById("photoModal");
  const img = document.getElementById("modalImage");

  if (!modal || !img) return;

  img.src = src;
  modal.classList.remove("hidden");
}

document.getElementById("photoModal").addEventListener("click", function () {
  this.classList.add("hidden");
});

</script>

</body>
</html>
