<?php
require_once __DIR__ . '/../config/page_init.php';

$scriptPath = $_SERVER['SCRIPT_NAME'];
if (strpos($scriptPath, '/pages/') !== false) {
    $basePath = '../';
} else {
    $basePath = '';
}

if ($_SESSION['role'] !== 'visitor') {
    header('Location: index.php?page=dashboard');
    exit();
}

if (empty($_SESSION['visitor_info_completed'])) {
    header('Location: index.php?page=visitor_info');
    exit();
}

require_once __DIR__ . '/../classes/Patient.php';

$patient = new Patient($pdo);
$allPatients = $patient->getAllPatients();
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dashboard-content">
        <div class="dashboard-header">
            <h1>Visit Patient</h1>
        </div>

        <div class="dashboard-section">
            <div class="section-header-with-icon">
                <div class="section-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="section-title">
                    <h2>Scan Patient QR Code</h2>
                    <p>Use your camera to scan patient QR codes</p>
                </div>
            </div>
            <div class="scanner-button-container">
                <button id="openScannerBtn" class="btn btn-primary scanner-btn">
                    <i class="fas fa-camera"></i>
                    Open QR Scanner
                </button>
            </div>
        </div>
    </main>
</div>

<!-- QR Scanner Modal -->
<div id="scannerModal" class="scanner-modal">
    <div class="scanner-modal-content">
        <div class="scanner-modal-header">
            <h3>Scan QR Code</h3>
            <button id="closeScannerBtn" class="close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="qr-scanner-container">
            <div class="qr-reader">
                <div id="reader"></div>
                <div class="scan-line"></div>
            </div>
            <div class="scanner-hint">Align QR code within frame</div>
            <div id="scan-result" class="scan-result"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
.scanner-button-container {
    display: flex;
    justify-content: center;
    padding: 20px;
}

.scanner-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    font-size: 16px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.scanner-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.scanner-btn i {
    font-size: 20px;
}

/* Modal Styles */
.scanner-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    animation: fadeIn 0.3s ease-out;
    backdrop-filter: blur(8px);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.scanner-modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.scanner-modal-content {
    background: #1a1a2e;
    border-radius: 24px;
    padding: 25px;
    max-width: 380px;
    width: 90%;
    position: relative;
    animation: slideUp 0.3s ease-out;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    margin: auto;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.scanner-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    color: white;
}

.scanner-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.qr-scanner-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 0;
    background: transparent;
    border-radius: 0;
    box-shadow: none;
    max-width: 100%;
    margin: 0;
}

.qr-reader {
    width: 100%;
    max-width: 300px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

#reader {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    background: #000;
}

#reader video {
    border-radius: 16px;
    object-fit: cover;
    width: 100% !important;
    height: auto !important;
}

#reader__scan_region {
    border-radius: 16px;
}

#reader__dashboard_section_csr,
#reader__dashboard_section_swaplink,
#reader__dashboard_section_cameraPermission {
    display: none !important;
}

#reader__dashboard {
    padding: 0 !important;
}

#reader__scan_region {
    height: 300px !important;
}

.scan-line {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 2px;
    background: white;
    animation: scanMove 2s ease-in-out infinite;
    z-index: 10;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
}

@keyframes scanMove {
    0%, 100% {
        top: 20%;
    }
    50% {
        top: 80%;
    }
}

.scan-result {
    padding: 12px;
    border-radius: 8px;
    display: none;
    text-align: center;
    background: white;
    width: 100%;
    max-width: 320px;
    animation: slideIn 0.3s ease-out;
    font-size: 14px;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.scan-result.success {
    background-color: #00c853;
    color: white;
    border: none;
}

.scan-result.error {
    background-color: #ff5252;
    color: white;
    border: none;
}

.scanner-hint {
    color: rgba(255, 255, 255, 0.8);
    text-align: center;
    font-size: 13px;
    margin-top: 5px;
}
</style>

<script>
function viewPatientDetails(patientId) {
    window.location.href = 'index.php?page=patient_profiles&id=' + patientId;
}

// QR Code Scanner
document.addEventListener('DOMContentLoaded', function() {
    const html5QrCode = new Html5Qrcode("reader");
    const scanResult = document.getElementById('scan-result');
    const scannerModal = document.getElementById('scannerModal');
    const openScannerBtn = document.getElementById('openScannerBtn');
    const closeScannerBtn = document.getElementById('closeScannerBtn');
    let isScanning = false;

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning
        html5QrCode.stop().then(() => {
            isScanning = false;

            console.log('QR Code scanned:', decodedText);

            // Parse the QR code (it contains JSON data with patient ID)
            let patientId = null;
            try {
                const qrData = JSON.parse(decodedText);
                console.log('Parsed QR Data:', qrData);
                if (qrData.id) {
                    patientId = qrData.id;
                    console.log('Extracted Patient ID:', patientId);
                }
            } catch (e) {
                console.log('JSON parse error:', e);
                // Fallback: check if it's plain patient_id format
                if (decodedText.includes('patient_id:')) {
                    patientId = decodedText.replace('patient_id:', '');
                } else {
                    patientId = decodedText;
                }
            }

            if (patientId) {
                // Show success message with patient ID
                scanResult.style.display = 'block';
                scanResult.className = 'scan-result success';
                scanResult.innerHTML = '<p>QR Code scanned! Patient ID: ' + patientId + '<br>Redirecting to patient profile...</p>';

                // Close modal and redirect after a short delay
                setTimeout(() => {
                    closeScannerModal();
                    setTimeout(() => {
                        window.location.href = 'index.php?page=patient_profiles&id=' + patientId;
                    }, 300);
                }, 1500);
            } else {
                // Show error message
                scanResult.style.display = 'block';
                scanResult.className = 'scan-result error';
                scanResult.innerHTML = '<p>Invalid QR code format.<br>Raw data: ' + decodedText + '</p>';
                setTimeout(() => {
                    closeScannerModal();
                }, 3000);
            }
        }).catch(err => {
            console.error('Failed to stop scanning', err);
        });
    }

    function onScanFailure(error) {
        // Handle scan failure (this is normal during scanning)
        // console.warn(`Code scan error = ${error}`);
    }

    function startScanner() {
        if (isScanning) return;

        const config = {
            fps: 10,
            qrbox: { width: 200, height: 200 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isScanning = true;
        }).catch(err => {
            console.error('Error starting scanner', err);
            scanResult.style.display = 'block';
            scanResult.className = 'scan-result error';
            scanResult.innerHTML = '<p>Unable to access camera. Please ensure camera permissions are granted.</p>';
        });
    }

    function stopScanner() {
        if (!isScanning) return;

        html5QrCode.stop().then(() => {
            isScanning = false;
        }).catch(err => {
            console.error('Error stopping scanner', err);
        });
    }

    function openScannerModal() {
        scannerModal.classList.add('show');
        setTimeout(() => {
            startScanner();
        }, 300);
    }

    function closeScannerModal() {
        stopScanner();
        scannerModal.classList.remove('show');
        scanResult.style.display = 'none';
    }

    // Event listeners
    openScannerBtn.addEventListener('click', openScannerModal);
    closeScannerBtn.addEventListener('click', closeScannerModal);

    // Close modal when clicking outside
    scannerModal.addEventListener('click', function(e) {
        if (e.target === scannerModal) {
            closeScannerModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && scannerModal.classList.contains('show')) {
            closeScannerModal();
        }
    });
});
</script>
