

<div class="flex justify-center items-center min-h-[70vh] ">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl p-2 sm:p-6 min-h-[600px]">
        <h1 class="text-2xl font-bold text-center mb-2 text-gray-800">Stock Out</h1>
        <!-- Status Messages -->
        <div id="status-message" class="mb-4 p-3 rounded-lg text-sm hidden">
            <span id="status-text"></span>
        </div>

        <!-- Scanner box -->
        <div id="qr-reader"
                class="w-full aspect-square bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center mb-2">
                <div class="flex items-center justify-center h-full text-gray-500">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                        </svg>
                        <p class="text-base sm:text-lg">Camera will appear here</p>
                    </div>
                </div>
        </div>
        
        <div class="flex flex-col items-center gap-4 mb-2 w-full">
            <label for="camera-select" class="text-sm font-medium text-gray-700 dark:text-dark-200 w-full text-left sm:text-center">
                Select Camera Source
            </label>

            <select id="camera-select"
                class="w-full max-w-xs px-4 py-3 text-base rounded-lg border border-gray-300 bg-white text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:focus:ring-green-400 dark:focus:border-green-400">
                <option value="">Loading cameras...</option>
            </select>

            <div class="flex gap-4 w-full justify-center">
                <button id="start-btn"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg text-base font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Start Camera
                </button>
                <button id="stop-btn"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-lg text-base font-medium transition hidden">
                    Stop Camera
                </button>
            </div>
            
            <!-- Manual Input Fallback -->
            <div class="mt-4 w-full max-w-xs">
                <label class="block text-sm font-medium text-gray-700 mb-2">Or enter QR code manually:</label>
                <input type="text" id="manual-qr-input" 
                    class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base"
                    placeholder="Enter QR code value">
                <button id="manual-submit" 
                    class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-base font-medium transition">
                    Submit
                </button>
            </div>
        </div>

        <div id="qr-result"
            class="mt-6 p-4 text-center bg-green-50 border border-green-400 rounded-md text-green-700 font-semibold hidden">
            ✅ Scanned: <span id="qr-value" class="font-mono text-base break-words"></span>
        </div>

        <!-- Livewire Results Section -->
        @if(isset($showResult) && $showResult)
        <div class="mt-6 p-4 rounded-lg border {{ (isset($messageType) && $messageType === 'error') ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold {{ (isset($messageType) && $messageType === 'error') ? 'text-red-800' : 'text-green-800' }}">
                    {{ (isset($messageType) && $messageType === 'error') ? '❌ Error' : '✅ Found' }}
                </h3>
                <button wire:click="clearResult" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            @if(isset($foundSupplyProfile) && $foundSupplyProfile)
            <div class="bg-white p-4 rounded-lg border">
                <h4 class="font-semibold text-gray-900 mb-2">Supply Profile Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-base">
                    <div>
                        <span class="font-medium text-gray-600">SKU:</span>
                        <span class="ml-2 font-mono">{{ $foundSupplyProfile->supply_sku }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Description:</span>
                        <span class="ml-2">{{ $foundSupplyProfile->supply_description }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Current Stock:</span>
                        <span class="ml-2">{{ number_format($foundSupplyProfile->supply_qty, 2) }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Unit:</span>
                        <span class="ml-2">{{ $foundSupplyProfile->supply_uom }}</span>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <span class="font-medium">Note:</span> This is a direct supply profile scan. For stock-out operations, please scan the supply profile QR code and proceed.
                </div>
                <div class="flex gap-2 mt-4">
                    <button wire:click="selectSupplyProfileForStockOut({{ $foundSupplyProfile->id }})" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-base">Stock Out</button>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Stock Out Modal -->
        @if(isset($showStockOutModal) && $showStockOutModal && isset($selectedSupplyProfile) && $selectedSupplyProfile)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-end sm:items-center justify-center z-50">
            <div class="bg-white rounded-t-2xl sm:rounded-lg p-6 w-full max-w-md mx-0 sm:mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4 text-center">Stock Out Item</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                        <p class="text-base text-gray-900">{{ $selectedSupplyProfile->supply_description }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <p class="text-base font-mono text-gray-900">{{ $selectedSupplyProfile->supply_sku }}</p>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                            <p class="text-base text-gray-900">{{ number_format($selectedSupplyProfile->supply_qty, 2) }} {{ $selectedSupplyProfile->supply_uom }}</p>
                        </div>
                    </div>
                    <div>
                        <label for="stockOutQuantity" class="block text-sm font-medium text-gray-700 mb-1">Stock Out Quantity</label>
                        <input type="number" 
                            wire:model="stockOutQuantity" 
                            id="stockOutQuantity"
                            step="0.01"
                            min="0.01"
                            max="{{ $selectedSupplyProfile->supply_qty }}"
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                    <button wire:click="closeStockOutModal" 
                        class="w-full sm:w-auto px-4 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg text-base">
                        Cancel
                    </button>
                    <button wire:click="processStockOut" 
                        class="w-full sm:w-auto px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-base">
                        Process Stock Out
                    </button>
                </div>
            </div>
        </div>
        @endif

        <audio id="scanSound" src="{{ asset('scan.mp3') }}"></audio>
    </div>
</div>

<script>
    let html5Qr = null;
    let isInitialized = false;

    // Show status message
    function showStatus(message, type = 'info') {
        const statusDiv = document.getElementById('status-message');
        const statusText = document.getElementById('status-text');
        
        statusDiv.className = `mb-4 p-3 rounded-lg text-sm ${type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 
                                                           type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 
                                                           'bg-blue-100 text-blue-700 border border-blue-300'}`;
        statusText.textContent = message;
        statusDiv.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    }

    // Initialize camera access
    async function initializeCameraAccess() {
        const startBtn = document.getElementById('start-btn');
        
        // Check if running on HTTPS, localhost, or local network
        const isLocalNetwork = location.hostname.match(/^(localhost|127\.0\.0\.1|192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/);
        if (location.protocol !== 'https:' && !isLocalNetwork) {
            showStatus('Camera access requires HTTPS or local network. Please use HTTPS or access via localhost/local network.', 'error');
            return false;
        }

        // Check if media devices are supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            showStatus('Camera API not supported in this browser.', 'error');
            return false;
        }

        try {
            showStatus('Requesting camera permission...', 'info');
            
            // Request camera permission first
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment', // Prefer back camera on mobile
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            
            // Stop the stream immediately after getting permission
            stream.getTracks().forEach(track => track.stop());
            
            showStatus('Camera permission granted.', 'success');
            startBtn.disabled = false;
            isInitialized = true;
            return true;
            
        } catch (err) {
            console.error('Camera access error:', err);
            
            let errorMessage = 'Camera access denied or not available.';
            if (err.name === 'NotAllowedError') {
                errorMessage = 'Camera permission denied. Please allow camera access and refresh the page.';
            } else if (err.name === 'NotFoundError') {
                errorMessage = 'No camera found on this device.';
            } else if (err.name === 'NotSupportedError') {
                errorMessage = 'Camera not supported on this device.';
            } else if (err.name === 'NotReadableError') {
                errorMessage = 'Camera is in use by another application.';
            }
            
            showStatus(errorMessage, 'error');
            return false;
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await initializeCameraAccess();
        
        const qrRegionId = "qr-reader";
        const startBtn = document.getElementById("start-btn");
        const stopBtn = document.getElementById("stop-btn");
        const scanSound = document.getElementById("scanSound");
        const manualInput = document.getElementById("manual-qr-input");
        const manualSubmit = document.getElementById("manual-submit");

        startBtn.addEventListener('click', async () => {
            if (!isInitialized) {
                showStatus('Camera not initialized. Please refresh the page.', 'error');
                return;
            }

            if (!window.Html5Qrcode) {
                showStatus("QR scanner not loaded.", 'error');
                return;
            }

            // Disable start button and show loading
            startBtn.disabled = true;
            startBtn.textContent = 'Starting...';
            showStatus('Starting camera...', 'info');

            try {
                // Get available cameras
                const cameras = await Html5Qrcode.getCameras();
                if (cameras.length === 0) {
                    showStatus("No cameras found on this device.", 'error');
                    startBtn.disabled = false;
                    startBtn.textContent = 'Start Camera';
                    return;
                }

                // Use the first available camera
                const cameraId = cameras[0].id;
                
                html5Qr = await window.startQrScanner({
                    elementId: qrRegionId,
                    scanSoundId: 'scanSound',
                    cameraId: cameraId,
                    onScan: (decodedText) => {
                        document.getElementById("qr-value").textContent = decodedText;
                        document.getElementById("qr-result").classList.remove("hidden");

                        // ✅ Play scan sound
                        if (scanSound) {
                            scanSound.currentTime = 0;
                            scanSound.play().catch(err => {
                                console.warn("Sound playback failed:", err);
                            });
                        }

                        showStatus('QR Code scanned successfully!', 'success');
                        Livewire.emit('qrScanned', decodedText);
                    }
                });

                // Update UI
                startBtn.classList.add("hidden");
                stopBtn.classList.remove("hidden");
                showStatus('Camera started successfully', 'success');

            } catch (error) {
                console.error("Error starting camera", error);
                showStatus("Unable to start camera: " + error.message, 'error');
                
                // Reset button state
                startBtn.disabled = false;
                startBtn.textContent = 'Start Camera';
            }
        });

        stopBtn.addEventListener('click', async () => {
            if (!html5Qr) {
                showStatus('No scanner running', 'error');
                return;
            }
            
            // Disable stop button and show loading
            stopBtn.disabled = true;
            stopBtn.textContent = 'Stopping...';
            showStatus('Stopping camera...', 'info');
            
            try {
                await html5Qr.stop();
                
                // Update UI
                startBtn.classList.remove("hidden");
                stopBtn.classList.add("hidden");
                startBtn.disabled = false;
                startBtn.textContent = 'Start Camera';
                stopBtn.disabled = false;
                stopBtn.textContent = 'Stop Camera';
                
                // Clear QR reader display
                const qrReader = document.getElementById('qr-reader');
                qrReader.innerHTML = `
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                            </svg>
                            <p class="text-base sm:text-lg">Camera will appear here</p>
                        </div>
                    </div>
                `;
                
                showStatus('Camera stopped successfully', 'success');
                
            } catch (err) {
                console.error("Stop error", err);
                showStatus("Unable to stop camera: " + err.message, 'error');
                
                // Reset button state
                stopBtn.disabled = false;
                stopBtn.textContent = 'Stop Camera';
            }
        });

        // Manual QR code input
        manualSubmit.addEventListener('click', () => {
            const code = manualInput.value.trim();
            if (code) {
                document.getElementById("qr-value").textContent = code;
                document.getElementById("qr-result").classList.remove("hidden");
                showStatus('QR Code submitted manually!', 'success');
                
                // Emit to Livewire
                if (window.Livewire) {
                    window.Livewire.emit('qrScanned', code);
                }
                
                manualInput.value = '';
            } else {
                showStatus('Please enter a QR code value', 'error');
            }
        });

        // Enter key for manual input
        manualInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                manualSubmit.click();
            }
        });
    });
</script>

