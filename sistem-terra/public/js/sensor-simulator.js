class SensorSimulator {
    constructor() {
        this.interval = null;
        this.isRunning = false;
        this.saveInterval = null;
    }
    generateRandomSensorData() {
        const randomSuhu = (Math.random() * (31 - 28) + 28).toFixed(1);
        const randomHum = Math.floor(Math.random() * (65 - 55) + 55);
        const randomLux = Math.floor(Math.random() * (1500 - 800) + 800);
        let statusSensor = 'Normal';
        if (randomSuhu > 30.5) {
            statusSensor = 'Warning - Suhu Tinggi';
        } else if (randomHum < 57) {
            statusSensor = 'Warning - Kelembapan Rendah';
        } else if (randomLux > 1400) {
            statusSensor = 'Warning - Cahaya Tinggi';
        }
        
        return {
            suhu: parseFloat(randomSuhu),
            kelembapan: randomHum,
            cahaya: randomLux,
            status: statusSensor,
            timestamp_sensor: new Date().toISOString()
        };
    }
    
    async saveSensorData() {
        try {
            const sensorData = this.generateRandomSensorData();
            this.updateUI(sensorData);
            await this.saveToBackendAPI(sensorData);
        } catch (error) {}
    }
    async saveToBackendAPI(sensorData) {
        try {
            console.log('[SENSOR] Sending data to:', window.ROBOT_API_URL + '/sensor/data');
            console.log('[SENSOR] Data:', sensorData);
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);
            
            const response = await fetch(window.ROBOT_API_URL + '/sensor/data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    suhu: sensorData.suhu,
                    kelembapan: sensorData.kelembapan,
                    cahaya: sensorData.cahaya,
                    status: sensorData.status,
                    timestamp: sensorData.timestamp_sensor
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                console.log('[SENSOR] Success:', result);
            } else {
                console.error('[SENSOR] Error:', response.status, response.statusText);
            }
            
        } catch (error) {
            console.error('[SENSOR] Network error:', error);
        }
    }
    
    async updateDetectionsWithSensor() {
        try {
            await fetch('/api/sensor/auto-update-detections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } catch (error) {}
    }
    
    updateUI(sensorData) {
        if (!window.location.pathname.includes('/sensor')) {
            return;
        }
        
        const suhuElement = document.getElementById('val-suhu');
        const humElement = document.getElementById('val-hum');
        const luxElement = document.getElementById('val-lux');
        if (suhuElement) suhuElement.innerText = sensorData.suhu;
        if (humElement) humElement.innerText = sensorData.kelembapan;
        if (luxElement) luxElement.innerText = sensorData.cahaya;
        if (window.sensorChart) {
            window.sensorChart.data.datasets[0].data.shift();
            window.sensorChart.data.datasets[1].data.shift();
            window.sensorChart.data.datasets[0].data.push(sensorData.suhu);
            window.sensorChart.data.datasets[1].data.push(sensorData.kelembapan);
            window.sensorChart.update();
        }
        const statusElement = document.querySelector('.text-green-600');
        if (statusElement) {
            const statusText = sensorData.status === 'Normal' ? 'ONLINE • STABIL' : 'ONLINE • WARNING';
            statusElement.textContent = statusText;
            if (sensorData.status !== 'Normal') {
                statusElement.classList.remove('text-green-600');
                statusElement.classList.add('text-yellow-600');
            } else {
                statusElement.classList.remove('text-yellow-600');
                statusElement.classList.add('text-green-600');
            }
        }
    }
    start() {
        if (this.isRunning) {
            return;
        }
        this.isRunning = true;
        this.saveSensorData();
        this.interval = setInterval(() => {
            this.saveSensorData();
        }, 10000);
        
        this.saveInterval = setInterval(() => {
            this.updateDetectionsWithSensor();
        }, 30000);
    }
    stop() {
        if (!this.isRunning) {
            return;
        }
        this.isRunning = false;
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
        if (this.saveInterval) {
            clearInterval(this.saveInterval);
            this.saveInterval = null;
        }
    }
}
window.sensorSimulator = new SensorSimulator();

document.addEventListener('DOMContentLoaded', function() {
    window.sensorSimulator.start();
});
window.autoUpdateAllDetections = () => {
    fetch('/api/sensor/auto-update-detections', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
};


