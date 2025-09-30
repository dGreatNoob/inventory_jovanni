<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Test - CliqueHA</title>
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
        #video {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>📷 Camera Access Test</h1>
    
    <div class="debug-info">
        <strong>Debug Info:</strong><br>
        Protocol: <span id="protocol"></span><br>
        Hostname: <span id="hostname"></span><br>
        User Agent: <span id="userAgent"></span><br>
        Is Local Network: <span id="isLocalNetwork"></span><br>
        Camera API Supported: <span id="cameraSupported"></span>
    </div>

    <div id="status" class="status info">Initializing...</div>
    
    <button id="testBtn" onclick="testCamera()">Test Camera Access</button>
    <button id="listBtn" onclick="listCameras()">List Available Cameras</button>
    
    <video id="video" autoplay muted></video>
    
    <div id="cameraList"></div>

    <script>
        // Display debug info
        document.getElementById('protocol').textContent = location.protocol;
        document.getElementById('hostname').textContent = location.hostname;
        document.getElementById('userAgent').textContent = navigator.userAgent;
        
        // Check if local network
        const isLocalNetwork = location.hostname.match(/^(localhost|127\.0\.0\.1|192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/);
        document.getElementById('isLocalNetwork').textContent = isLocalNetwork ? 'Yes' : 'No';
        
        // Check camera API support
        const cameraSupported = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        document.getElementById('cameraSupported').textContent = cameraSupported ? 'Yes' : 'No';

        function updateStatus(message, type = 'info') {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = `status ${type}`;
        }

        async function testCamera() {
            updateStatus('Testing camera access...', 'info');
            
            try {
                // Check HTTPS/local network requirement
                if (location.protocol !== 'https:' && !isLocalNetwork) {
                    updateStatus('❌ Camera access requires HTTPS or local network!', 'error');
                    return;
                }

                // Check API support
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    updateStatus('❌ Camera API not supported in this browser', 'error');
                    return;
                }

                // Request camera access
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });

                // Display video
                const video = document.getElementById('video');
                video.srcObject = stream;
                
                updateStatus('✅ Camera access successful! Video should appear above.', 'success');
                
                // Stop after 5 seconds for testing
                setTimeout(() => {
                    stream.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                    updateStatus('Camera test completed. Stream stopped.', 'info');
                }, 5000);

            } catch (error) {
                console.error('Camera error:', error);
                let errorMsg = 'Camera access failed: ' + error.message;
                
                if (error.name === 'NotAllowedError') {
                    errorMsg = '❌ Camera permission denied. Please allow camera access.';
                } else if (error.name === 'NotFoundError') {
                    errorMsg = '❌ No camera found on this device.';
                } else if (error.name === 'NotSupportedError') {
                    errorMsg = '❌ Camera not supported on this device.';
                }
                
                updateStatus(errorMsg, 'error');
            }
        }

        async function listCameras() {
            updateStatus('Listing available cameras...', 'info');
            
            try {
                if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
                    updateStatus('❌ Camera enumeration not supported', 'error');
                    return;
                }

                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoInputs = devices.filter(device => device.kind === 'videoinput');
                
                const cameraList = document.getElementById('cameraList');
                cameraList.innerHTML = '<h3>Available Cameras:</h3>';
                
                if (videoInputs.length === 0) {
                    cameraList.innerHTML += '<p>No cameras found</p>';
                    updateStatus('❌ No cameras found on this device', 'error');
                } else {
                    videoInputs.forEach((device, index) => {
                        const label = device.label || `Camera ${index + 1}`;
                        cameraList.innerHTML += `<p>${index + 1}. ${label}</p>`;
                    });
                    updateStatus(`✅ Found ${videoInputs.length} camera(s)`, 'success');
                }
                
            } catch (error) {
                console.error('Camera enumeration error:', error);
                updateStatus('❌ Failed to enumerate cameras: ' + error.message, 'error');
            }
        }

        // Auto-test on load
        window.addEventListener('load', () => {
            updateStatus('Page loaded. Click "Test Camera Access" to begin.', 'info');
        });
    </script>
</body>
</html> 