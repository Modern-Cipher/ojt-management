document.addEventListener('DOMContentLoaded', () => {
    const attendanceButton = document.getElementById('attendanceButton');
    const attendanceModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
    const attendanceTableBody = document.querySelector('#attendanceTable tbody');
    const searchInput = document.getElementById('attendanceSearchInput');
    const printButton = document.getElementById('printButton');
    const exportButton = document.getElementById('exportButton');
    const loadingSpinner = document.getElementById('attendanceLoadingSpinner');
    let attendanceData = [];

    // Open attendance modal and fetch data
    attendanceButton.addEventListener('click', () => {
        attendanceModal.show();
        fetchAttendanceData();
    });

    // Fetch attendance data
    function fetchAttendanceData() {
        loadingSpinner.style.display = 'block';
        fetch('../../2coordinator/list_request/fetch_attendance.php')
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                if (data.error) {
                    alert(data.error);
                    return;
                }
                attendanceData = data.data;
                renderTable(attendanceData);
            })
            .catch(error => {
                loadingSpinner.style.display = 'none';
                alert('Error fetching attendance data');
                console.error(error);
            });
    }

    // Render table
    function renderTable(data) {
        attendanceTableBody.innerHTML = '';
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td><img src="${row.selfie_image_path}" alt="Selfie" class="thumbnail-img" data-img-src="${row.selfie_image_path}"></td>
                <td>${row.school_id}</td>
                <td>${row.full_name}</td>
                <td>${row.course}</td>
                <td>${row.email}</td>
                <td>${row.registered_on}</td>
                <td>${row.validated_on}</td>
                <td class="remarks">${row.remarks}</td>
            `;
            attendanceTableBody.appendChild(tr);
        });

        // Attach image click event after rendering
        attachImageClickEvents();
    }

    // Attach click events for image thumbnails
    function attachImageClickEvents() {
        document.querySelectorAll('.thumbnail-img').forEach(img => {
            img.addEventListener('click', () => {
                const imgSrc = img.getAttribute('data-img-src');
                const imageModal = new bootstrap.Modal(document.getElementById('imageCardModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                document.getElementById('cardImage').src = imgSrc;
                imageModal.show();

                // Ensure attendance modal remains open
                const attendanceModalEl = document.getElementById('attendanceModal');
                if (!attendanceModalEl.classList.contains('show')) {
                    attendanceModal.show();
                }
            });
        });
    }

    // Search functionality
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredData = attendanceData.filter(row =>
            row.school_id.toLowerCase().includes(searchTerm) ||
            row.full_name.toLowerCase().includes(searchTerm) ||
            row.course.toLowerCase().includes(searchTerm) ||
            row.email.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredData);
    });

    // Sorting functionality
    function sortTable(columnIndex) {
        const headers = ['attendance_id', 'school_id', 'full_name', 'course', 'email', 'registered_on', 'validated_on'];
        const key = headers[columnIndex];
        attendanceData.sort((a, b) => {
            const aValue = a[key] || '';
            const bValue = b[key] || '';
            return aValue.localeCompare(bValue);
        });
        renderTable(attendanceData);
    }

    // Attach sort event to headers
    document.querySelectorAll('#attendanceTable thead th').forEach((th, index) => {
        if (index < 8) { // Exclude remarks column
            th.addEventListener('click', () => sortTable(index));
        }
    });

    // Print functionality
    printButton.addEventListener('click', () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Attendance List</title>
                <style>
                    @page {
                        size: 13in 8.5in;
                        margin: 0.5in;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                    }
                    @media print {
                        body {
                            width: 13in;
                            height: 8.5in;
                            -webkit-print-color-adjust: exact;
                        }
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 10pt;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 6px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .remarks {
                        font-size: 8pt;
                        white-space: normal;
                    }
                    img {
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                    }
                    h2 {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <h2>Attendance List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Image</th>
                            <th>School ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Email</th>
                            <th>Registered On</th>
                            <th>Validated On</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${attendanceTableBody.innerHTML}
                    </tbody>
                </table>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });

    // Export to Excel
    exportButton.addEventListener('click', () => {
        const ws_data = [
            ['No.', 'School ID', 'Name', 'Course', 'Email', 'Registered On', 'Validated On', 'Remarks'],
            ...attendanceData.map((row, index) => [
                index + 1,
                row.school_id,
                row.full_name,
                row.course,
                row.email,
                row.registered_on,
                row.validated_on,
                row.remarks
            ])
        ];

        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Attendance');
        XLSX.writeFile(wb, 'attendance_list.xlsx');
    });
});