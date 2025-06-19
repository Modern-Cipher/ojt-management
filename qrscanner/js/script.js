document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const dataDisplay = document.getElementById('dataDisplay');
    const badgeContainer = document.getElementById('badgeContainer');
    const resetDisplay = document.getElementById('resetDisplay');
    const cameraSelect = document.getElementById('cameraSelect');
    const imageZoomModal = new bootstrap.Modal(document.getElementById('imageZoomModal'));
    const zoomedImage = document.getElementById('zoomedImage');
    let stream = null;
    let devices = [];

    async function enumerateCameras() {
        try {
            const deviceInfos = await navigator.mediaDevices.enumerateDevices();
            devices = deviceInfos.filter(device => device.kind === 'videoinput');
            cameraSelect.innerHTML = '<option value="">Select Camera</option>';
            devices.forEach((device, index) => {
                const label = device.label || `Camera ${index + 1}`;
                cameraSelect.innerHTML += `<option value="${device.deviceId}">${label}</option>`;
            });
            if (devices.length > 0) {
                cameraSelect.value = devices[0].deviceId; // Default to first camera
                startScanner(devices[0].deviceId);
            } else {
                displayError('No cameras found');
            }
        } catch (err) {
            console.error('Error enumerating cameras:', err);
            displayError('Failed to list cameras');
        }
    }

    async function startScanner(deviceId) {
        if (stream) {
            stopStream();
        }
        try {
            const constraints = {
                video: {
                    deviceId: deviceId ? { exact: deviceId } : undefined,
                    facingMode: deviceId ? undefined : 'environment'
                }
            };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
            video.play();
            scanQRCode();
        } catch (err) {
            console.error('Camera access error:', err);
            displayError(`Camera Error: ${err.message}`);
        }
    }

    function stopStream() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function scanQRCode() {
        if (!video.videoWidth || !video.videoHeight) {
            requestAnimationFrame(scanQRCode);
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            fetchData(code.data);
        }

        requestAnimationFrame(scanQRCode);
    }

    function fetchData(qrCodeData) {
        fetch('process_scan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_code_data: qrCodeData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayData(data.data);
                displayBadges(data.messages);
            } else {
                displayError(data.message);
            }
        })
        .catch(err => {
            displayError('Fetch Error: Failed to fetch data');
        });
    }

    function displayData(data) {
        dataDisplay.innerHTML = `
            <div class="card-body">
                <img src="${data.selfie_image_path}" alt="Selfie" class="selfie-img" data-bs-toggle="modal" data-bs-target="#imageZoomModal" data-image="${data.selfie_image_path}">
                <div class="info mt-2">
                    <p><strong>School ID:</strong> ${data.school_id}</p>
                    <p><strong>Name:</strong> ${data.first_name} ${data.middle_name ? data.middle_name + ' ' : ''}${data.last_name}</p>
                    <p><strong>Sex:</strong> ${data.sex}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Institute:</strong> ${data.institute}</p>
                    <p><strong>Course:</strong> ${data.course}</p>
                    <p><strong>Validated on:</strong> ${new Date().toLocaleString()}</p>
                </div>
            </div>
        `;
        document.querySelector('.selfie-img').addEventListener('click', () => {
            zoomedImage.src = data.selfie_image_path;
            imageZoomModal.show();
        });
    }

    function displayBadges(messages) {
        badgeContainer.innerHTML = '';
        messages.forEach(msg => {
            const badgeClass = msg.includes('Warning') || msg.includes('Already Scanned') ? 'error' : '';
            const badgeHTML = `<span class="badge-card ${badgeClass}">${msg}</span>`;
            badgeContainer.insertAdjacentHTML('beforeend', badgeHTML);
        });
    }

    function displayError(message) {
        badgeContainer.innerHTML = `<span class="badge-card error">${message}</span>`;
    }

    function resetDisplayContent() {
        dataDisplay.innerHTML = `
            <div class="card-body shimmer-content">
                <div class="shimmer-circle mx-auto"></div>
                <div class="shimmer-text mt-3">
                    <div class="shimmer-line"></div>
                    <div class="shimmer-line"></div>
                    <div class="shimmer-line"></div>
                </div>
            </div>
        `;
        badgeContainer.innerHTML = '';
    }

    cameraSelect.addEventListener('change', () => {
        const deviceId = cameraSelect.value;
        if (deviceId) {
            startScanner(deviceId);
        } else {
            stopStream();
        }
    });

    resetDisplay.addEventListener('click', resetDisplayContent);

    enumerateCameras();

    window.addEventListener('beforeunload', stopStream);
});