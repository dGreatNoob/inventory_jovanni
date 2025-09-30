import './bootstrap';
import { initFlowbite } from 'flowbite';
import ApexCharts from 'apexcharts';
import Swal from 'sweetalert2';
import { Html5Qrcode } from 'html5-qrcode';
// import 'flowbite';

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
    if (document.getElementById("area-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("area-chart"), options);
        chart.render();
    }
    if (document.getElementById("column-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("column-chart"), options2);
        chart.render();
    }
});

window.Swal = Swal;
window.Html5Qrcode = Html5Qrcode;

// QR CODE SCANNER
window.populateCameraList = function (selectId = 'camera-select') {
    const select = document.getElementById(selectId);
    if (!select) return;

    Html5Qrcode.getCameras().then(cameras => {
        select.innerHTML = '<option value="">Select a camera...</option>';
        cameras.forEach(camera => {
            const option = document.createElement("option");
            option.value = camera.id;
            option.textContent = camera.label || `Camera ${select.length}`;
            select.appendChild(option);
        });
    }).catch((error) => {
        console.error('Camera enumeration error:', error);
        select.innerHTML = '<option value="">Unable to fetch camera list</option>';
    });
};

window.startQrScanner = function ({
    elementId = 'qr-reader',
    scanSoundId = 'scanSound',
    onScan = null,
    emitEvent = null,
    cameraId = null,
    qrboxSize = 250,
}) {
    if (!cameraId) {
        throw new Error('No camera selected!');
    }

    let html5Qr = new Html5Qrcode(elementId);
    let isScanned = false;
    let isRunning = false;
    const scanSound = document.getElementById(scanSoundId);

    const config = {
        fps: 10,
        qrbox: { width: qrboxSize, height: qrboxSize },
        aspectRatio: 1.0
    };

    const startPromise = html5Qr.start(
        cameraId,
        config,
        (decodedText) => {
            if (isScanned) return;
            isScanned = true;

            // Play scan sound
            if (scanSound) {
                scanSound.currentTime = 0;
                scanSound.play().catch((error) => {
                    console.warn('Sound playback failed:', error);
                });
            }

            // Call onScan callback if provided
            if (onScan && typeof onScan === 'function') {
                onScan(decodedText);
            }

            // Show success message
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'QR Code Scanned',
                    text: decodedText,
                    confirmButtonColor: '#16a34a',
                    heightAuto: false
                }).then(() => {
                    isScanned = false;
                });
            }, 1500);
        },
        (errorMessage) => {
            // Handle scanning errors (optional)
            console.warn('QR scanning error:', errorMessage);
        }
    );

    return startPromise.then(() => {
        isRunning = true;
        return {
            stop: () => {
                if (!isRunning) {
                    return Promise.resolve();
                }
                isRunning = false;
                return html5Qr.stop().then(() => {
                    html5Qr.clear();
                    return true;
                }).catch((error) => {
                    console.error('Stop error:', error);
                    throw error;
                });
            },
            isRunning: () => isRunning,
            html5Qr: html5Qr
        };
    }).catch((error) => {
        console.error('Scanner start error:', error);
        throw new Error('Failed to start scanner: ' + error.message);
    });
};





