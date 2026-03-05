<?php session_start(); $user = $_SESSION['user'] ?? null; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Clock-It</title>
</head>
<body>
    <nav class="header">
        <a href="index.php">dashboard</a>
        <a href="alarm.php">alarm</a>
        <a href="calendar.php">calendar</a>
        <a href="stopwatch.php">stopwatch</a>
        <?php if ($user): ?>
            <span class="muted" style="margin-left:12px">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
            <a href="logout.php" style="margin-left:8px">logout</a>
        <?php else: ?>
            <a href="login.php" style="margin-left:12px">sign in</a>
        <?php endif; ?>
    </nav>

    <main style="font-family:Arial,Helvetica,sans-serif;display:flex;flex-direction:column;align-items:center;gap:1rem;">
        <h1>Stopwatch</h1>

        <div id="stopwatch" style="background:#f7f7f9;border:1px solid #e1e4e8;padding:2rem;border-radius:8px;text-align:center;width:320px;">
            <div id="display" style="font-size:2rem;font-weight:600;letter-spacing:1px;margin-bottom:1rem">00:00.000</div>

            <div style="display:flex;gap:0.5rem;justify-content:center;margin-bottom:0.75rem">
                <button id="startBtn" style="padding:0.5rem 1rem">Start</button>
                <button id="stopBtn" style="padding:0.5rem 1rem" disabled>Stop</button>
                <button id="lapBtn" style="padding:0.5rem 1rem" disabled>Lap</button>
                <button id="resetBtn" style="padding:0.5rem 1rem" disabled>Reset</button>
            </div>

            <div style="text-align:left;max-height:160px;overflow:auto;border-top:1px solid #eee;padding-top:0.5rem">
                <ol id="laps" style="padding-left:1.2rem;margin:0"></ol>
            </div>
        </div>
    </main>

    todo
    <ul>
        <li>multiple stopwatches at once?</li>
    </ul>

    <script>
        // Stopwatch implementation using high-resolution timestamps
        const display = document.getElementById('display');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const resetBtn = document.getElementById('resetBtn');
        const lapBtn = document.getElementById('lapBtn');
        const lapsList = document.getElementById('laps');

        let running = false;
        let startTime = 0; // wall-clock when started (performance.now)
        let elapsedBefore = 0; // ms accumulated while paused
        let rafId = null;

        function formatTime(ms) {
            const totalMs = Math.floor(ms);
            const minutes = Math.floor(totalMs / 60000);
            const seconds = Math.floor((totalMs % 60000) / 1000);
            const milliseconds = totalMs % 1000;
            return String(minutes).padStart(2,'0') + ':' + String(seconds).padStart(2,'0') + '.' + String(milliseconds).padStart(3,'0');
        }

        function update() {
            const now = performance.now();
            const elapsed = elapsedBefore + (now - startTime);
            display.textContent = formatTime(elapsed);
            rafId = requestAnimationFrame(update);
        }

        startBtn.addEventListener('click', () => {
            if (!running) {
                startTime = performance.now();
                rafId = requestAnimationFrame(update);
                running = true;
                startBtn.textContent = 'Pause';
                stopBtn.disabled = false;
                lapBtn.disabled = false;
                resetBtn.disabled = false;
            } else {
                // pause
                running = false;
                cancelAnimationFrame(rafId);
                const now = performance.now();
                elapsedBefore += now - startTime;
                startBtn.textContent = 'Start';
            }
        });

        stopBtn.addEventListener('click', () => {
            if (running) {
                running = false;
                cancelAnimationFrame(rafId);
                const now = performance.now();
                elapsedBefore += now - startTime;
                startBtn.textContent = 'Start';
            }
            stopBtn.disabled = true;
            lapBtn.disabled = true;
        });

        resetBtn.addEventListener('click', () => {
            running = false;
            cancelAnimationFrame(rafId);
            startTime = 0;
            elapsedBefore = 0;
            display.textContent = '00:00.000';
            startBtn.textContent = 'Start';
            stopBtn.disabled = true;
            lapBtn.disabled = true;
            resetBtn.disabled = true;
            lapsList.innerHTML = '';
        });

        lapBtn.addEventListener('click', () => {
            const nowMs = running ? (elapsedBefore + (performance.now() - startTime)) : elapsedBefore;
            const li = document.createElement('li');
            li.textContent = formatTime(nowMs);
            lapsList.insertBefore(li, lapsList.firstChild);
        });
    </script>
</body>
</html>