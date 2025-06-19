document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const dataDisplay = document.getElementById('dataDisplay');
    let stream = null;
    let lastScannedData = null;

    async function startScanner() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            video.play();
            scanQRCode();
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Camera Error',
                text: 'Unable to access camera: ' + err.message
            });
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

        if (code && code.data !== lastScannedData) {
            lastScannedData = code.data;
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
                if (data.ip_match) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Scan',
                        text: 'This QR code was scanned from the same IP address, indicating possible unauthorized scanning.'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Fetch Error',
                text: 'Failed to fetch data: ' + err.message
            });
        });
    }

    function displayData(data) {
        dataDisplay.classList.remove('shimmer');
        dataDisplay.innerHTML = `
            <div class="card-body">
                <img src="/ojt/${data.selfie_image_path || 'resources/placeholder.png'}" alt="Selfie">
                <div class="info">
                    <h5>Submitted Information</h5>
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
        setTimeout(() => {
            dataDisplay.classList.add('shimmer');
            dataDisplay.innerHTML = `
                <div class="card-body">
                    <div class="shimmer-content">
                        <div class="shimmer-circle"></div>
                        <div class="shimmer-text">
                            <div class="shimmer-line"></div>
                            <div class="shimmer-line"></div>
                            <div class="shimmer-line"></div>
                        </div>
                    </div>
                </div>
            `;
            lastScannedData = null;
        }, 3000);
    }

    startScanner();

    window.addEventListener('beforeunload', stopStream);
});