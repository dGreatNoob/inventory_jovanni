<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner Test - CliqueHA</title>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .info { background-color: #d1ecf1; color: #0c5460; }
        #qr-reader {
            width: 100%;
            max-width: 400px;
            border: 2px solid #ccc;
            border-radius: 5px;
            margin: 10px 0;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #0056b3; }
        button:disabled { background: #6c757d; cursor: not-allowed; }
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <h1>📷 QR Scanner Test</h1>
    
    <div id="status" class="status info">Ready to test QR scanner</div>
    
    <div>
        <button id="startBtn" onclick="startScanner()">Start Scanner</button>
        <button id="stopBtn" onclick="stopScanner()" disabled>Stop Scanner</button>
    </div>
    
    <div id="qr-reader">
        <div style="display: flex; justify-content: center; align-items: center; height: 300px; color: #666;">
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px;">📷</div>
                <p>Camera will appear here</p>
            </div>
        </div>
    </div>
    
    <div id="result" class="result" style="display: none;">
        <strong>Scanned Result:</strong><br>
        <span id="scannedValue"></span>
    </div>

    <audio id="scanSound" src="/scan.mp3"></audio>

    <script>
        let qrScanner = null;
        let isRunning = false;

        function updateStatus(message, type = 'info') {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = `status ${type}`;
        }

        async function startScanner() {
            if (isRunning) {
                updateStatus('Scanner is already running', 'error');
                return;
            }

            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');

            // Disable start button and show loading
            startBtn.disabled = true;
            startBtn.textContent = 'Starting...';
            updateStatus('Starting QR scanner...', 'info');

            try {
                // Get available cameras
                const cameras = await Html5Qrcode.getCameras();
                if (cameras.length === 0) {
                    updateStatus('No cameras found on this device', 'error');
                    startBtn.disabled = false;
                    startBtn.textContent = 'Start Scanner';
                    return;
                }

                // Use the first available camera
                const cameraId = cameras[0].id;
                
                qrScanner = new Html5Qrcode('qr-reader');
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                await qrScanner.start(
                    cameraId,
                    config,
                    (decodedText) => {
                        // Play scan sound
                        const scanSound = document.getElementById('scanSound');
                        if (scanSound) {
                            scanSound.currentTime = 0;
                            scanSound.play().catch(err => {
                                console.warn('Sound playback failed:', err);
                            });
                        }

                        // Show result
                        document.getElementById('scannedValue').textContent = decodedText;
                        document.getElementById('result').style.display = 'block';
                        updateStatus('QR Code scanned successfully!', 'success');
                        
                        console.log('Scanned:', decodedText);
                    },
                    (errorMessage) => {
                        console.warn('QR scanning error:', errorMessage);
                    }
                );

                // Update UI
                isRunning = true;
                startBtn.classList.add('hidden');
                stopBtn.disabled = false;
                updateStatus('QR Scanner started successfully', 'success');

            } catch (error) {
                console.error('Scanner start error:', error);
                updateStatus('Failed to start scanner: ' + error.message, 'error');
                
                // Reset button state
                startBtn.disabled = false;
                startBtn.textContent = 'Start Scanner';
            }
        }

        async function stopScanner() {
            if (!qrScanner || !isRunning) {
                updateStatus('No scanner running', 'error');
                return;
            }

            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');

            // Disable stop button and show loading
            stopBtn.disabled = true;
            stopBtn.textContent = 'Stopping...';
            updateStatus('Stopping QR scanner...', 'info');

            try {
                await qrScanner.stop();
                
                // Update UI
                isRunning = false;
                startBtn.disabled = false;
                startBtn.textContent = 'Start Scanner';
                stopBtn.disabled = true;
                stopBtn.textContent = 'Stop Scanner';
                
                // Clear QR reader display
                const qrReader = document.getElementById('qr-reader');
                qrReader.innerHTML = `
                    <div style="display: flex; justify-content: center; align-items: center; height: 300px; color: #666;">
                        <div style="text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 10px;">📷</div>
                            <p>Camera will appear here</p>
                        </div>
                    </div>
                `;
                
                updateStatus('QR Scanner stopped successfully', 'success');
                
            } catch (error) {
                console.error('Stop error:', error);
                updateStatus('Error stopping scanner: ' + error.message, 'error');
                
                // Reset button state
                stopBtn.disabled = false;
                stopBtn.textContent = 'Stop Scanner';
            }
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (qrScanner && isRunning) {
                qrScanner.stop().catch(err => {
                    console.warn('Error stopping scanner on unload:', err);
                });
            }
        });
    </script>
</body>
</html> 