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
        <h1>Alarm</h1>

        <div style="background:#f7f7f9;border:1px solid #e1e4e8;padding:1.25rem;border-radius:8px;width:360px;">
            <div id="now" style="font-size:1.25rem;margin-bottom:0.5rem">Current time: --:--:--</div>

            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.75rem">
                <input id="alarmTime" type="time" step="60" style="flex:1;padding:0.4rem" />
                <button id="setBtn" style="padding:0.4rem 0.6rem">Set</button>
                <button id="clearBtn" style="padding:0.4rem 0.6rem" disabled>Clear</button>
            </div>

            <div id="status" style="color:#333;margin-bottom:0.5rem">No alarm set.</div>

            <div id="triggerControls" style="display:none;gap:0.5rem">
                <button id="stopSoundBtn" style="padding:0.4rem 0.6rem">Stop Sound</button>
            </div>
        </div>
    </main>

    todo
    <ul>
        <li>multiple alarms at once</li>
    </ul>

    <script>
        // Simple alarm that triggers when the clock matches the HH:MM value set in the time input
        const nowEl = document.getElementById('now');
        const alarmInput = document.getElementById('alarmTime');
        const setBtn = document.getElementById('setBtn');
        const clearBtn = document.getElementById('clearBtn');
        const statusEl = document.getElementById('status');
        const triggerControls = document.getElementById('triggerControls');
        const stopSoundBtn = document.getElementById('stopSoundBtn');

        let alarmTime = null; // string 'HH:MM'
        let alarmTriggered = false;
        let audioCtx = null;
        let osc = null;

        function pad(n){return String(n).padStart(2,'0');}

        function updateNow() {
            const d = new Date();
            const hh = pad(d.getHours());
            const mm = pad(d.getMinutes());
            const ss = pad(d.getSeconds());
            nowEl.textContent = `Current time: ${hh}:${mm}:${ss}`;

            if (alarmTime && !alarmTriggered) {
                if (alarmTime === `${hh}:${mm}`) {
                    triggerAlarm();
                }
            }
        }

        function triggerAlarm(){
            alarmTriggered = true;
            statusEl.textContent = `ALARM! (${alarmTime})`;
            statusEl.style.color = '#b00020';
            triggerControls.style.display = 'flex';
            playBeep();
            try { window.focus(); } catch(e){}
            alert('Alarm: ' + alarmTime);
        }

        function playBeep(){
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.0001, audioCtx.currentTime);
            osc.connect(gain).connect(audioCtx.destination);
            osc.start();
            // ramp up volume and alternate frequency for a beeping effect
            let on = true;
            osc._interval = setInterval(()=>{
                if (!osc) return;
                if (on) {
                    osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.2, audioCtx.currentTime + 0.02);
                } else {
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.02);
                }
                on = !on;
            }, 600);
        }

        function stopBeep(){
            if (osc) {
                clearInterval(osc._interval);
                try { osc.stop(); } catch(e){}
                try { osc.disconnect(); } catch(e){}
                osc = null;
            }
            triggerControls.style.display = 'none';
        }

        setBtn.addEventListener('click', ()=>{
            const v = alarmInput.value; // '' or 'HH:MM'
            if (!v) { alert('Please choose a time for the alarm.'); return; }
            alarmTime = v;
            alarmTriggered = false;
            statusEl.textContent = `Alarm set for ${alarmTime}`;
            statusEl.style.color = '#333';
            clearBtn.disabled = false;
        });

        clearBtn.addEventListener('click', ()=>{
            alarmTime = null;
            alarmTriggered = false;
            statusEl.textContent = 'No alarm set.';
            statusEl.style.color = '#333';
            clearBtn.disabled = true;
            stopBeep();
        });

        stopSoundBtn.addEventListener('click', ()=>{
            stopBeep();
        });

        // update clock every 500ms
        updateNow();
        setInterval(updateNow, 500);
    </script>
</body>
</html>