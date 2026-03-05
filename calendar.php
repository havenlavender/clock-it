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
        <a href="index.php">dashboard</a> |
        <a href="alarm.php">alarm</a> |
        <a href="calendar.php">calendar</a> |
        <a href="stopwatch.php">stopwatch</a>
    </nav>

    <main style="font-family:Arial,Helvetica,sans-serif;display:flex;gap:1rem;align-items:flex-start;">
        <section style="width:520px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                <button id="prev">◀ Prev</button>
                <h2 id="monthTitle" style="margin:0">Month</h2>
                <button id="next">Next ▶</button>
            </div>

            <div id="calendarGrid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;">
                <!-- day names + date cells will be injected here -->
            </div>
        </section>

        <aside style="width:260px;">
            <h3 id="selectedDateTitle">Select a date</h3>
            <div id="eventsList" style="min-height:120px;background:#f7f7f9;border:1px solid #eaeaea;padding:0.5rem;border-radius:6px;overflow:auto"></div>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem">
                <button id="addEventBtn">Add Event</button>
                <button id="clearEventsBtn">Clear Events</button>
            </div>
        </aside>
    </main>

    todo
    <ul>
        <li>maybe a list of upcoming events within the next couple of days</li>
    </ul>

    <script>
        // Simple month calendar with events stored in localStorage by date (YYYY-MM-DD)
        const monthTitle = document.getElementById('monthTitle');
        const grid = document.getElementById('calendarGrid');
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        const selectedDateTitle = document.getElementById('selectedDateTitle');
        const eventsList = document.getElementById('eventsList');
        const addEventBtn = document.getElementById('addEventBtn');
        const clearEventsBtn = document.getElementById('clearEventsBtn');

        let today = new Date();
        let viewYear = today.getFullYear();
        let viewMonth = today.getMonth(); // 0-11
        let selectedDate = null; // Date object

        function pad(n){return String(n).padStart(2,'0');}
        function ymd(d){return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());}

        function loadEvents(){
            try { return JSON.parse(localStorage.getItem('clockit_events')||'{}'); }
            catch(e){ return {}; }
        }
        function saveEvents(obj){ localStorage.setItem('clockit_events', JSON.stringify(obj)); }

        function render(){
            grid.innerHTML='';
            // day names header
            const names = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            names.forEach(n=>{
                const el=document.createElement('div');
                el.textContent = n;
                el.style.fontWeight='600';
                el.style.padding='6px 4px';
                grid.appendChild(el);
            });

            const first = new Date(viewYear, viewMonth, 1);
            const startDay = first.getDay();
            const daysInMonth = new Date(viewYear, viewMonth+1, 0).getDate();

            // fill blanks before first day
            for(let i=0;i<startDay;i++){
                const blank = document.createElement('div');
                blank.style.minHeight='60px';
                grid.appendChild(blank);
            }

            const events = loadEvents();

            for(let d=1; d<=daysInMonth; d++){
                const cell = document.createElement('div');
                const date = new Date(viewYear, viewMonth, d);
                const key = ymd(date);
                cell.dataset.date = key;
                cell.style.minHeight='60px';
                cell.style.border='1px solid #eee';
                cell.style.padding='6px';
                cell.style.borderRadius='6px';
                cell.style.cursor='pointer';

                if (key === ymd(today)) {
                    cell.style.background = '#fff8e1';
                    cell.style.boxShadow = 'inset 0 0 0 2px rgba(255,215,0,0.08)';
                }

                const top = document.createElement('div');
                top.textContent = d;
                top.style.fontWeight='600';
                cell.appendChild(top);

                if (events[key] && events[key].length) {
                    const dot = document.createElement('div');
                    dot.style.width='8px';
                    dot.style.height='8px';
                    dot.style.borderRadius='50%';
                    dot.style.background='#007bff';
                    dot.style.marginTop='6px';
                    cell.appendChild(dot);
                }

                cell.addEventListener('click', ()=>{
                    selectedDate = date;
                    renderSelected();
                });

                grid.appendChild(cell);
            }

            const title = first.toLocaleString(undefined,{month:'long', year:'numeric'});
            monthTitle.textContent = title;
            if (!selectedDate) selectedDate = today;
            renderSelected();
        }

        function renderSelected(){
            const key = ymd(selectedDate);
            selectedDateTitle.textContent = selectedDate.toDateString();
            const events = loadEvents();
            const list = events[key] || [];
            eventsList.innerHTML = '';
            if (list.length===0) eventsList.textContent = 'No events.';
            else {
                const ul = document.createElement('ul');
                ul.style.margin='0';
                ul.style.paddingLeft='1rem';
                list.forEach(ev=>{
                    const li = document.createElement('li');
                    li.textContent = ev;
                    ul.appendChild(li);
                });
                eventsList.appendChild(ul);
            }
        }

        prevBtn.addEventListener('click', ()=>{
            viewMonth--; if (viewMonth<0){ viewMonth=11; viewYear--; }
            render();
        });
        nextBtn.addEventListener('click', ()=>{
            viewMonth++; if (viewMonth>11){ viewMonth=0; viewYear++; }
            render();
        });

        addEventBtn.addEventListener('click', ()=>{
            const text = prompt('Event description for ' + selectedDate.toDateString());
            if (!text) return;
            const events = loadEvents();
            const key = ymd(selectedDate);
            events[key] = events[key] || [];
            events[key].push(text);
            saveEvents(events);
            render();
        });

        clearEventsBtn.addEventListener('click', ()=>{
            if (!confirm('Clear all events for ' + selectedDate.toDateString() + '?')) return;
            const events = loadEvents();
            const key = ymd(selectedDate);
            delete events[key];
            saveEvents(events);
            render();
        });

        // initial render
        render();
    </script>
</body>
</html>