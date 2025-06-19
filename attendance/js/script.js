document.addEventListener('DOMContentLoaded', () => {
    const isAttendancePage = document.getElementById('attendanceForm');
    const isCameraPage = document.getElementById('cameraForm');
    const isConfirmPage = document.getElementById('qrCode');

    if (isAttendancePage) {
        const form = document.getElementById('attendanceForm');
        const nextBtn = document.getElementById('nextBtn');
        const instituteSelect = document.getElementById('institute');
        const courseSelect = document.getElementById('course');

        // Restrict to one user per device
        if (localStorage.getItem('userRegistered')) {
            form.innerHTML = '<p class="text-danger text-center">This device has already been used to register. Only one user per device is allowed.</p>';
            nextBtn.style.display = 'none';
            return;
        }

        const courses = {
            'Institute of Engineering and Applied Technology': ['BS in Information Technology'],
            'Institute of Management': ['BS in Business Administration', 'BS in Hotel Management']
        };

        instituteSelect.addEventListener('change', () => {
            const selectedInstitute = instituteSelect.value;
            courseSelect.innerHTML = '<option value="" disabled selected>Select Course</option>';
            if (courses[selectedInstitute]) {
                courses[selectedInstitute].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.text = course;
                    courseSelect.appendChild(option);
                });
            }
        });

        function saveFormData() {
            const formData = {
                school_id: document.getElementById('school_id').value,
                first_name: document.getElementById('first_name').value,
                middle_name: document.getElementById('middle_name').value,
                last_name: document.getElementById('last_name').value,
                sex: document.getElementById('sex').value,
                email: document.getElementById('email').value,
                institute: document.getElementById('institute').value,
                course: document.getElementById('course').value
            };
            localStorage.setItem('attendanceFormData', JSON.stringify(formData));
        }

        function loadFormData() {
            const savedData = localStorage.getItem('attendanceFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                document.getElementById('school_id').value = formData.school_id || '';
                document.getElementById('first_name').value = formData.first_name || '';
                document.getElementById('middle_name').value = formData.middle_name || '';
                document.getElementById('last_name').value = formData.last_name || '';
                document.getElementById('sex').value = formData.sex || '';
                document.getElementById('email').value = formData.email || '';
                document.getElementById('institute').value = formData.institute || '';
                if (formData.institute && courses[formData.institute]) {
                    courseSelect.innerHTML = '<option value="" disabled selected>Select Course</option>';
                    courses[formData.institute].forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.text = course;
                        courseSelect.appendChild(option);
                    });
                    document.getElementById('course').value = formData.course || '';
                }
            }
        }

        nextBtn.addEventListener('click', () => {
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Form',
                    text: 'Please fill out all required fields.'
                });
                return;
            }
            saveFormData();
            window.location.href = 'camera.php';
        });

        loadFormData();
    }

    if (isCameraPage) {
        const form = document.getElementById('cameraForm');
        const backBtn = document.getElementById('backBtn');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const previewSection = document.getElementById('previewSection');
        const preview = document.getElementById('preview');
        const selfieData = document.getElementById('selfie_data');
        const cameraSelect = document.getElementById('cameraSelect');
        const submitSection = document.getElementById('submitSection');
        const selectImageBtn = document.getElementById('selectImageBtn');
        const imageInput = document.getElementById('imageInput');
        let stream = null;
        let devices = [];

        function loadFormData() {
            const savedData = localStorage.getItem('attendanceFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                document.getElementById('school_id').value = formData.school_id || '';
                document.getElementById('first_name').value = formData.first_name || '';
                document.getElementById('middle_name').value = formData.middle_name || '';
                document.getElementById('last_name').value = formData.last_name || '';
                document.getElementById('sex').value = formData.sex || '';
                document.getElementById('email').value = formData.email || '';
                document.getElementById('institute').value = formData.institute || '';
                document.getElementById('course').value = formData.course || '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No Data',
                    text: 'Please fill out the form first.',
                    willClose: () => {
                        window.location.href = 'attendance.php';
                    }
                });
            }
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        async function startCamera(deviceId) {
            try {
                stopStream();
                const constraints = {
                    video: {
                        deviceId: deviceId ? { exact: deviceId } : undefined,
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: deviceId ? undefined : 'user'
                    }
                };
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Camera Error',
                    text: 'No camera detected or browser not compatible. Please select an image instead.'
                });
                cameraSelect.innerHTML = '<option value="" disabled selected>No cameras available</option>';
            }
        }

        async function populateCameraDropdown() {
            try {
                await navigator.mediaDevices.getUserMedia({ video: true });
                devices = await navigator.mediaDevices.enumerateDevices();
                devices = devices.filter(device => device.kind === 'videoinput');
                
                cameraSelect.innerHTML = '';
                if (devices.length === 0) {
                    cameraSelect.innerHTML = '<option value="" disabled selected>No cameras found</option>';
                    Swal.fire({
                        icon: 'error',
                        title: 'No Camera Detected',
                        text: 'No camera detected or browser not compatible. Please select an image instead.'
                    });
                    return;
                }

                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.text = device.label || `Camera ${index + 1}`;
                    if (device.label.toLowerCase().includes('front')) {
                        option.text = 'Front Camera';
                    } else if (device.label.toLowerCase().includes('back')) {
                        option.text = 'Back Camera';
                    }
                    cameraSelect.appendChild(option);
                });

                const frontCamera = devices.find(device => device.label.toLowerCase().includes('front') || device.label.toLowerCase().includes('user'));
                startCamera(frontCamera ? frontCamera.deviceId : devices[0]?.deviceId);
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Camera Detected',
                    text: 'No camera detected or browser not compatible. Please select an image instead.'
                });
                cameraSelect.innerHTML = '<option value="" disabled selected>No cameras found</option>';
            }
        }

        selectImageBtn.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (!file || !['image/jpeg', 'image/png'].includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image',
                    text: 'Please select a JPEG or PNG image.'
                });
                imageInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = () => {
                const imageData = reader.result;
                preview.src = imageData;
                previewSection.classList.remove('d-none');
                captureBtn.classList.add('d-none');
                video.classList.add('d-none');
                selfieData.value = imageData;
                stopStream();
            };
            reader.readAsDataURL(file);
        });

        cameraSelect.addEventListener('change', () => {
            startCamera(cameraSelect.value);
        });

        captureBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/jpeg');
            preview.src = imageData;
            previewSection.classList.remove('d-none');
            captureBtn.classList.add('d-none');
            video.classList.add('d-none');
            selfieData.value = imageData;
            stopStream();
        });

        retakeBtn.addEventListener('click', () => {
            previewSection.classList.add('d-none');
            captureBtn.classList.remove('d-none');
            video.classList.remove('d-none');
            selfieData.value = '';
            imageInput.value = '';
            startCamera(cameraSelect.value || devices[0]?.deviceId);
        });

        confirmBtn.addEventListener('click', () => {
            if (!selfieData.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Selfie',
                    text: 'Please capture a selfie or select an image before confirming.'
                });
                return;
            }
            submitSection.classList.remove('d-none');
            window.scrollTo({ top: submitSection.offsetTop, behavior: 'smooth' });
        });

        backBtn.addEventListener('click', () => {
            stopStream();
            window.location.href = 'attendance.php';
        });

        form.addEventListener('submit', (e) => {
            if (!selfieData.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'No Selfie',
                    text: 'Please capture a selfie or select an image before submitting.'
                });
                return;
            }
            stopStream();
        });

        loadFormData();
        populateCameraDropdown();
    }

    if (isConfirmPage) {
        const resetBtn = document.getElementById('resetBtn');
        const downloadBtn = document.getElementById('downloadBtn');

        if (!qrCodeData || !attendanceId) {
            Swal.fire({
                icon: 'error',
                title: 'Data Error',
                text: 'QR code data or attendance ID is missing.'
            });
            return;
        }

        // Set userRegistered flag on page load (after successful submission)
        localStorage.setItem('userRegistered', 'true');

        // Hide reset button if download was completed
        if (localStorage.getItem(`downloadCompleted_${attendanceId}`)) {
            resetBtn.style.display = 'none';
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will delete your submission and the associated selfie image.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reset',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`delete.php?id=${attendanceId}`, { method: 'POST' })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    localStorage.removeItem('attendanceFormData');
                                    localStorage.removeItem('userRegistered');
                                    localStorage.removeItem(`downloadCompleted_${attendanceId}`);
                                    window.location.href = 'attendance.php';
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
                                    text: 'Failed to reset: ' + err.message
                                });
                            });
                    }
                });
            });
        }

        if (downloadBtn) {
            downloadBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Confirm Download',
                    text: 'Downloading will disable the Reset option. Proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, download',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        html2canvas(document.getElementById('cardContent'), {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: '#ffffff'
                        }).then(canvas => {
                            const link = document.createElement('a');
                            link.href = canvas.toDataURL('image/png');
                            link.download = `attendance_${attendanceId}.png`;
                            link.click();
                            localStorage.setItem(`downloadCompleted_${attendanceId}`, 'true');
                            resetBtn.style.display = 'none';
                            Swal.fire({
                                icon: 'success',
                                title: 'Download Complete',
                                text: 'Your attendance record has been downloaded.'
                            });
                        }).catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Download Error',
                                text: 'Failed to generate download: ' + err.message
                            });
                        });
                    }
                });
            });
        }
    }
});