@extends('layout.base')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
<title>Dashboard</title>

<div class="dashboard-wrapper d-flex">
    <div class="container-fluid py-5 flex-grow-1 bg-white min-vh-100">
        <header class="mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Plastic & Non-Plastic Analysis Overview</p>
            <hr>
        </header>

        <div class="row g-4 mb-4">
            @auth
            @if(Auth::user()->utype === 'ADM')
            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Users</h6>
                        <h3 class="fw-bold text-success">{{ $userCount ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            @endif
            @endauth

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Plastic Detected - All Time</h6>
                        <h3 class="fw-bold text-danger" id="plasticCount">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Non-Plastic Detected - All Time</h6>
                        <h3 class="fw-bold text-warning" id="nonPlasticCount">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Weekly Detection Trends</h6>
                        <p class="text-muted fst-italic">Note: This chart requires timestamps in Firebase to function.</p>
                        <canvas id="weeklyPlasticChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold">Calendar</h6>
                        <p class="text-muted fst-italic">Hover over a date to see detections.</p>
                        <table class="table table-bordered text-center calendar-table w-100 mb-0" id="calendar-table"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hover-popup" class="position-absolute bg-dark text-white p-2 rounded small shadow" style="display:none; z-index:1000;"></div>

<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-database-compat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
function initializeDashboard() {
    const firebaseConfig = {
        apiKey: "AIzaSyDx7HErgazhqZq-rzJIM-4nFhMUA5byDzY",
        authDomain: "plastic-sensor.firebaseapp.com",
        databaseURL: "https://plastic-sensor-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "plastic-sensor",
        storageBucket: "plastic-sensor.appspot.com",
        messagingSenderId: "973658571653",
        appId: "1:973658571653:web:ba344e62c400e993f5baec"
    };

    if (typeof firebase === "undefined" || !firebase.apps) {
        console.error("Firebase SDK did not load!");
        return;
    }

    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();
    console.log("Firebase loaded ✅");

    // Chart.js
    const ctx = document.getElementById('weeklyPlasticChart').getContext('2d');
    const weeklyPlasticChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1','Week 2','Week 3','Week 4'],
            datasets: [
                { label:'Plastic Detections', data:[0,0,0,0], borderColor:'#dc3545', backgroundColor:'rgba(220,53,69,0.2)', fill:true, tension:0.4 },
                { label:'Non-Plastic Detections', data:[0,0,0,0], borderColor:'#ffc107', backgroundColor:'rgba(255,193,7,0.2)', fill:true, tension:0.4 }
            ]
        },
        options: {
            responsive:true,
            plugins:{ legend:{ labels:{ color:'#333' } } },
            scales: { x:{ ticks:{ color:'#333' }, grid:{ color:'rgba(0,0,0,0.05)' } }, y:{ ticks:{ color:'#333' }, grid:{ color:'rgba(0,0,0,0.05)' }, beginAtZero:true } }
        }
    });

    // Recursive count function
    function countPlasticLogs(obj) {
        let plastic = 0, nonPlastic = 0;
        if (typeof obj !== 'object' || obj === null) return {plastic, nonPlastic};
        Object.values(obj).forEach(val => {
            if (val === "Detected") plastic++;
            else if (val === "Clear") nonPlastic++;
            else if (typeof val === 'object') {
                const result = countPlasticLogs(val);
                plastic += result.plastic;
                nonPlastic += result.nonPlastic;
            }
        });
        return {plastic, nonPlastic};
    }

    // Store full Firebase data globally for hover lookup
    let fullData = {};

    // Firebase listener
    database.ref().on('value', snapshot => {
        fullData = snapshot.val() || {};
        const result = countPlasticLogs(fullData);

        document.getElementById('plasticCount').textContent = result.plastic;
        document.getElementById('nonPlasticCount').textContent = result.nonPlastic;

        weeklyPlasticChart.data.datasets[0].data = [result.plastic,result.plastic,result.plastic,result.plastic];
        weeklyPlasticChart.data.datasets[1].data = [result.nonPlastic,result.nonPlastic,result.nonPlastic,result.nonPlastic];
        weeklyPlasticChart.update();
    });

    // Calendar
    const today = new Date();
    function generateCalendar(year, month) {
        const table = document.getElementById("calendar-table");
        table.innerHTML = "";
        const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
        const thead = document.createElement("thead");
        const headerRow = document.createElement("tr");
        days.forEach(d => { const th = document.createElement("th"); th.textContent=d; th.classList.add("p-2"); headerRow.appendChild(th); });
        thead.appendChild(headerRow); table.appendChild(thead);
        const tbody = document.createElement("tbody");
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month+1,0).getDate();
        let date=1;
        for(let i=0;i<6;i++){
            const row=document.createElement("tr");
            for(let j=0;j<7;j++){
                const cell=document.createElement("td");
                cell.classList.add("p-2","calendar-cell");
                if(i===0 && j<firstDay || date>daysInMonth){ cell.textContent=""; }
                else{
                    cell.textContent=date;
                    cell.dataset.date = `${year}-${(month+1).toString().padStart(2,'0')}-${date.toString().padStart(2,'0')}`;
                    // Hover listener
                    cell.addEventListener('mouseenter', (e)=>{
                        const d = e.target.dataset.date;
                        let plastic=0, nonPlastic=0;
                        if(fullData[d] && fullData[d].logs && fullData[d].logs.Obstacle){
                            Object.values(fullData[d].logs.Obstacle).forEach(val=>{
                                if(val==="Detected") plastic++;
                                else if(val==="Clear") nonPlastic++;
                            });
                        }
                        const popup = document.getElementById('hover-popup');
                        popup.innerHTML = `Plastic: ${plastic}<br>Non-Plastic: ${nonPlastic}`;
                        popup.style.display="block";
                        popup.style.top = (e.pageY + 10) + "px";
                        popup.style.left = (e.pageX + 10) + "px";
                    });
                    cell.addEventListener('mouseleave', ()=>{
                        const popup = document.getElementById('hover-popup');
                        popup.style.display="none";
                    });
                    date++;
                }
                row.appendChild(cell);
            }
            tbody.appendChild(row);
            if(date>daysInMonth) break;
        }
        table.appendChild(tbody);
    }
    generateCalendar(today.getFullYear(), today.getMonth());
}

window.addEventListener('load', initializeDashboard);
</script>
@endsection
