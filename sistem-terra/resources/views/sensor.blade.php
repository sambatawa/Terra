<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-purple-100 text-purple-700 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                </span>
                Terra Dashboard: Monitoring IoT
            </h2>
            <div class="flex items-center gap-3 px-4 py-2 bg-white border border-purple-200 rounded-full shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <div class="flex flex-col leading-none">
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Status Alat</span>
                    <span class="text-xs font-bold text-green-600">ONLINE • STABIL</span>
                </div>
                <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
                <span class="text-xs font-bold text-gray-500">🔋 87%</span>
            </div>
        </div>
    </x-slot>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <div class="py-10 bg-[#F3F0FF] min-h-screen relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-purple-200/50 to-transparent pointer-events-none"></div>
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-purple-300/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-40 -left-20 w-72 h-72 bg-pink-300/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-[0_10px_30px_-10px_rgba(34,197,94,0.2)] border border-green-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-all group-hover:bg-red-100"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Suhu Udara</p>
                            <h3 class="text-4xl font-extrabold text-gray-800">
                                <span id="val-suhu">--</span><span class="text-lg text-gray-400 font-medium ml-1">°C</span>
                            </h3>
                        </div>
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">Status: Normal</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-[0_10px_30px_-10px_rgba(34,197,94,0.2)] border border-green-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-all group-hover:bg-blue-100"></div>                    
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Kelembaban Tanah</p>
                            <h3 class="text-4xl font-extrabold text-gray-800">
                                <span id="val-hum">--</span><span class="text-lg text-gray-400 font-medium ml-1">%</span>
                            </h3>
                        </div>
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">Kondisi: Basah Ideal</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-[0_10px_30px_-10px_rgba(34,197,94,0.2)] border border-green-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-50 rounded-bl-full -mr-4 -mt-4 transition-all group-hover:bg-yellow-100"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Intensitas Cahaya</p>
                            <h3 class="text-4xl font-extrabold text-gray-800">
                                <span id="val-lux">--</span><span class="text-lg text-gray-400 font-medium ml-1">Lux</span>
                            </h3>
                        </div>
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">Siang Hari (Cerah)</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl shadow-xl border border-purple-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <span class="bg-purple-100 p-1.5 rounded-lg text-purple-600">
                                <i class="fa-solid fa-chart-area w-5 h-5"></i>
                            </span>
                            Grafik Fluktuasi Sensor
                        </h3>
                        <span class="text-xs font-bold border-gray-200 rounded-lg text-gray-500 bg-gray-50 focus:ring-purple-500 focus:border-purple-500">
                            Live Data
                        </span>
                    </div>
                    <div class="h-72">
                        <canvas id="sensorChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-xl border border-purple-100 flex flex-col">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-purple-100 p-1.5 rounded-lg text-purple-600">
                            <i class="fa-solid fa-route w-5 h-5"></i>
                        </span>
                        Lokasi Perangkat
                    </h3>
                    <div class="flex-1 w-full bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 relative">
                        <div id="map-container" class="w-full h-full flex items-center justify-center text-gray-500 font-medium">
                            Memuat lokasi...
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-purple-50 rounded-xl border border-purple-100">
                        <p class="flex items-center gap-2 text-sm font-bold text-gray-700">
                            <span class="text-purple-600"></span> Koordinat:
                        </p>
                        <p id="realtime-coordinate" class="text-xs text-gray-500 mt-1 font-mono pl-6">-6.9301, 107.6293</p>
                        <p id="last-update-time" class="text-[10px] text-gray-400 pl-6 mt-1">Last Update: Just now</p>
                    </div>
                    <div class="relative group-hover:z-50 mt-2">
                        <div class="flex items-center gap-2">
                            <select id="disease-data-mode" class="text-xs font-bold border border-purple-200 rounded-lg text-purple-700 bg-red-50 focus:ring-purple-500 focus:border-purple-500 px-2 py-1">
                                <option value="latest">Terbaru</option>
                                <option value="all">Semua</option>
                            </select>
                            <button class="flex items-center gap-2 text-xs font-bold text-purple-700 bg-red-50 px-3 py-1 rounded-lg hover:bg-purple-100 transition-colors"
                                onmouseover="document.getElementById('disease-tooltip').classList.remove('opacity-0', 'invisible');"
                                onmouseout="document.getElementById('disease-tooltip').classList.add('opacity-0', 'invisible');">
                                 Info Penyakit
                            </button>
                        </div>
                        <div id="disease-tooltip" class="absolute top-full left-0 mt-2 w-64 p-3 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0 invisible transition-opacity duration-300 pointer-events-none z-50">
                            <h4 class="text-sm font-bold text-gray-800 mb-2 border-b pb-1">Statistik Penyakit</h4>
                            <div id="disease-tooltip-content">
                                <p class="text-xs text-gray-400">Memuat...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-3xl shadow-xl border border-purple-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-yellow-100 p-1.5 rounded-lg text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Riwayat Peringatan (Alert Log)
                    </h3>
                    <button class="text-xs font-bold text-purple-600 hover:underline">Lihat Semua</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-purple-50 text-purple-900">
                            <tr>
                                <th class="px-6 py-3 font-bold">Waktu</th>
                                <th class="px-6 py-3 font-bold">Sensor</th>
                                <th class="px-6 py-3 font-bold">Pesan</th>
                                <th class="px-6 py-3 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-600">10:45 AM</td>
                                <td class="px-6 py-4 font-bold text-yellow-600">Cahaya</td>
                                <td class="px-6 py-4 text-gray-700">Intensitas terlalu tinggi (>2000 Lux)</td>
                                <td class="px-6 py-4"><span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Warning</span></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-600">09:30 AM</td>
                                <td class="px-6 py-4 font-bold text-blue-600">Kelembaban</td>
                                <td class="px-6 py-4 text-gray-700">Tanah kering, pompa air menyala otomatis.</td>
                                <td class="px-6 py-4"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Resolved</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sensorLimits = {
            suhu: { min: 28, max: 31 },
            kelembapan: { min: 55, max: 65 }, 
            cahaya: { min: 800, max: 1500 } 
        };
        function normalizeTo100(value, min, max) {
            if (max - min === 0) return 0;
            const normalized = 100 * ((value - min) / (max - min));
            return Math.max(0, Math.min(100, normalized));
        }
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const sensorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1s', '2s', '3s', '4s', '5s', '6s', '7s', '8s', '9s', '10s'],
                datasets: [
                    {
                        label: 'Suhu (°C)',
                        data: [28, 28.5, 29, 29.2, 29, 28.8, 29, 29.5, 29.3, 29.4],
                        borderColor: '#EF4444', 
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#EF4444'
                    },
                    {
                        label: 'Kelembaban (%)',
                        data: [55, 56, 58, 60, 59, 60, 62, 61, 60, 59],
                        borderColor: '#3B82F6', // Blue
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#3B82F6'
                    },
                    {
                        label: 'Cahaya (Lux)',
                        data: [800, 850, 900, 950, 1000, 1050, 1100, 1150, 1200, 1250],
                        borderColor: '#F59E0B', // Orange
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#F59E0B'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, easing: 'linear' },
                scales: {
                    y: { 
                        beginAtZero: false, min: 0, max: 100,
                        grid: { color: '#F3F4F6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { labels: { usePointStyle: true, font: { family: "'Plus Jakarta Sans', sans-serif" } } }
                }
            }
        });

        async function updateSensorDataFromFirebase() {
            try {
                const response = await fetch('{{ route("api.sensor.current") }}');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const sensorData = result.data;
                    document.getElementById('val-suhu').innerText = sensorData.suhu || 0;
                    document.getElementById('val-hum').innerText = sensorData.kelembapan || 0;
                    document.getElementById('val-lux').innerText = sensorData.cahaya || 0;
                    if (sensorChart) {
                        const normalizedSuhu = normalizeTo100(sensorData.suhu || 0, sensorLimits.suhu.min, sensorLimits.suhu.max);
                        const normalizedKelembapan = normalizeTo100(sensorData.kelembapan || 0, sensorLimits.kelembapan.min, sensorLimits.kelembapan.max);
                        const normalizedCahaya = normalizeTo100(sensorData.cahaya || 0, sensorLimits.cahaya.min, sensorLimits.cahaya.max);
                        sensorChart.data.datasets[0].data.shift(); 
                        sensorChart.data.datasets[1].data.shift();
                        sensorChart.data.datasets[2].data.shift();
                        sensorChart.data.datasets[0].data.push(normalizedSuhu);
                        sensorChart.data.datasets[1].data.push(normalizedKelembapan);
                        sensorChart.data.datasets[2].data.push(normalizedCahaya);
                        sensorChart.update();
                    }
                    updateStatusIndicator(sensorData.status || 'Normal');
                }
            } catch (error) {
                console.error('Error updating sensor data:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            updateRealtimeLocation();
            setInterval(updateRealtimeLocation, 30000); 
        });
        function updateRealtimeLocation() {
            const mapContainer = document.getElementById('map-container');
            const coordinateElement = document.getElementById('realtime-coordinate');
            const updateTimeElement = document.getElementById('last-update-time');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const timestamp = new Date().toLocaleTimeString();
                        coordinateElement.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                        updateTimeElement.textContent = `Last Update: ${timestamp}`;
                        const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
                        mapContainer.innerHTML = `
                            <iframe 
                                src="${mapUrl}" 
                                width="100%" 
                                height="100%" 
                                style="border:0; border-radius: 12px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        `;
                    },
                    (error) => {
                        let errorMessage = '';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Akses lokasi ditolak oleh pengguna.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "Permintaan waktu tunggu lokasi habis.";
                                break;
                            default:
                                errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                                break;
                        }
                        mapContainer.innerHTML = `<div class="text-center p-4 text-red-500">Gagal memuat lokasi: ${errorMessage}</div>`;
                        coordinateElement.textContent = 'Akses Ditolak/Error';
                        updateTimeElement.textContent = 'Last Update: Gagal';
                        console.error('Geolocation Error:', error);
                    }
                );
            } else {
                mapContainer.innerHTML = `<div class="text-center p-4 text-red-500">Browser Anda tidak mendukung Geolocation API.</div>`;
                coordinateElement.textContent = 'Tidak Didukung';
                updateTimeElement.textContent = 'Last Update: N/A';
            }
        }
        function updateStatusIndicator(status) {
            const statusElement = document.querySelector('div.flex.flex-col.leading-none span.text-xs.font-bold');
            if (statusElement) {
                const statusText = status === 'Normal' ? 'ONLINE • STABIL' : 'ONLINE • WARNING';
                statusElement.textContent = statusText;
                if (status !== 'Normal') {
                    statusElement.classList.remove('text-green-600');
                    statusElement.classList.add('text-yellow-600');
                } else {
                    statusElement.classList.remove('text-yellow-600');
                    statusElement.classList.add('text-green-600');
                }
            }
        }
        
        //MENARIK GENERATE SENSOR AMBIL DATA DARI API
        async function generateSensorData() {
            try {
                const response = await fetch('{{ route("api.sensor.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                console.log('Sensor data generated:', result);
                updateSensorDataFromFirebase();
            } catch (error) {
                console.error('Error generating sensor data:', error);
            }
        }
        
        setInterval(updateSensorDataFromFirebase, 2000);
        setInterval(generateSensorData, 10000);
        updateSensorDataFromFirebase();
        generateSensorData();
        
        async function getDiseaseStats() {
            try {
                const response = await fetch('/history/refresh'); 
                const result = await response.json();
                if (result.success && result.detections && result.detections.length > 0) {
                    const dataMode = document.getElementById('disease-data-mode').value;
                    let diseaseStats = {};
                    
                    if (dataMode === 'latest') {
                        const latestDetection = result.detections[0];
                        diseaseStats = latestDetection.jumlah_disease_terdeteksi || {};
                    } else {
                        result.detections.forEach(detection => {
                            if (detection.jumlah_disease_terdeteksi) {
                                Object.entries(detection.jumlah_disease_terdeteksi).forEach(([name, count]) => {
                                    if (name !== 'sehat' && count > 0) {
                                        if (!diseaseStats[name]) {
                                            diseaseStats[name] = 0;
                                        }
                                        diseaseStats[name] += count;
                                    }
                                });
                            }
                        });
                        console.log('Debug - Disease stats from jumlah_disease_terdeteksi:', diseaseStats);
                    }
                    const activeDiseases = {};
                    Object.entries(diseaseStats).forEach(([name, count]) => {
                        if (count > 0 && name !== 'sehat') {
                            activeDiseases[name] = count;
                        }
                    });
                    
                    return activeDiseases;
                }
                return {}; 
            } catch (error) {
                console.error('Error fetching disease data:', error);
                return {};
            }
        }
        function updateDiseaseTooltip(diseaseData) {
            const tooltipContentElement = document.getElementById('disease-tooltip-content');
            if (!tooltipContentElement) {
                console.error('Disease tooltip content element not found');
                return;
            }
            const totalCases = Object.values(diseaseData).reduce((sum, count) => sum + count, 0);

            //TOOLTIP CARD
            let tooltipHtml = `
                <div class="flex justify-between items-center mb-2 pb-2 border-b">
                    <span class="text-xs font-bold text-gray-500">Total Kasus:</span>
                    <span class="text-sm font-bold text-red-600">${totalCases}</span>
                </div>
            `;
            
            if (Object.keys(diseaseData).length > 0) {
                tooltipHtml += Object.entries(diseaseData).map(([name, count]) => 
                    `<div class="flex justify-between py-1 border-b border-gray-100 last:border-b-0">
                        <span class="text-sm font-medium text-gray-700">${name}</span>
                        <span class="text-sm font-bold text-red-600">${count}</span>
                    </div>`
                ).join('');
            } else {
                tooltipHtml += '<p class="text-xs text-gray-500 mt-2">Tidak ada kasus penyakit tercatat.</p>';
            }
            
            tooltipContentElement.innerHTML = tooltipHtml;
        }
        // Event listener untuk mode change
        document.getElementById('disease-data-mode').addEventListener('change', async () => {
            const diseaseData = await getDiseaseStats();
            updateDiseaseTooltip(diseaseData);
        });
        async function updateAllData() {
            updateSensorDataFromFirebase();
            const diseaseData = await getDiseaseStats();
            updateDiseaseTooltip(diseaseData);
        }
        setInterval(updateAllData, 2000);
        updateAllData();
    </script>
</x-app-layout>