document.addEventListener("DOMContentLoaded", () => {
    const trainerListContainer = document.querySelector(".student-list-scroll");
    const infoContainer = document.querySelector(".info-scroll");

    trainerListContainer.addEventListener("click", (e) => {
        const card = e.target.closest(".trainer-card");
        if (!card) return;

        const trainerId = card.getAttribute("data-id");

        document.querySelectorAll(".trainer-card").forEach(c => c.classList.remove("active-student"));
        card.classList.add("active-student");

        fetch(`../../1admin/establishment/fetch_trainer_details.php?trainer_id=${trainerId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    infoContainer.innerHTML = `<p class="text-danger small">${data.error}</p>`;
                    return;
                }

                renderTrainerInfo(data);
                fetchStudentInterns(trainerId);
                fetchTrainerDocuments(trainerId);
            })
            .catch(err => {
                console.error("Fetch trainer info error:", err);
                infoContainer.innerHTML = `<p class="text-danger">Unable to load trainer information.</p>`;
            });
    });

    function renderTrainerInfo(data) {
        const isEnabled = data.account_status === "Enabled";
        const badgeClass = isEnabled ? "bg-success" : "bg-secondary";
        const badgeText = isEnabled ? "Enabled" : "Disabled";

        infoContainer.innerHTML = `
        <div class="card shadow-sm mb-3 p-3 text-center">
            <div class="text-center">
                <img src="${data.image_profile}" class="rounded-circle mb-3 student-profile-img" width="100" height="100" alt="Profile">
                <h5 class="card-title">${data.full_name || ""}</h5>
                <p class="text-muted small">${data.designation || ""}</p>
            </div>

            <hr>
            <h6 class="text-start fw-semibold"><i class="fa-solid fa-user me-2"></i>Personal Details</h6>
            <p class="text-muted text-start"><i class="fa-solid fa-user-tag me-2"></i>Username: ${data.username || ""}</p>
            <p class="text-muted text-start"><i class="fa-solid fa-venus-mars me-2"></i>Sex: ${data.sex || ""}</p>
            <p class="text-muted text-start"><i class="fa-solid fa-location-dot me-2"></i>Address: ${data.address || ""}</p>

            <h6 class="text-start fw-semibold"><i class="fa-solid fa-envelope me-2"></i>Contact Information</h6>
            <p class="text-muted text-start"><i class="fa-solid fa-envelope-open-text me-2"></i>Email: ${data.email || ""}</p>
            <p class="text-muted text-start mb-3"><i class="fa-solid fa-phone me-2"></i>Phone: ${data.phone || ""}</p>

            <hr>
            <h6 class="text-start fw-semibold"><i class="fa-solid fa-circle-check me-2"></i>Account Status</h6>
            <p class="text-muted text-start">
                <i class="fa-solid fa-user-check me-2"></i>
                <span class="badge rounded-pill ${badgeClass}">${badgeText}</span>
            </p>
            <p class="text-muted text-start">
                <i class="fa-solid fa-calendar-plus me-2"></i>
                Created: ${data.created_on || ""}
            </p>

            <hr>
            <h6 class="text-start fw-semibold"><i class="fa-solid fa-building me-2"></i>Student Intern</h6>
            <ul class="list-group list-group-flush text-start mb-3 student-intern-list">
                <li class="list-group-item text-muted">Loading students...</li>
            </ul>

            <hr>
            <h6 class="text-start fw-semibold"><i class="fa-solid fa-file-alt me-2"></i>Document Submitted Files</h6>
            <ul class="list-group list-group-flush text-start mb-3 document-list">
                <li class="list-group-item text-muted">Loading documents...</li>
            </ul>
        </div>`;
    }

    function fetchStudentInterns(trainerId) {
        fetch(`../../1admin/establishment/fetch_students_by_hte.php?trainer_id=${trainerId}`)
            .then(res => res.json())
            .then(students => {
                const list = document.querySelector(".student-intern-list");
                if (!Array.isArray(students) || students.length === 0) {
                    list.innerHTML = `<li class="list-group-item text-muted px-0 border-0">No students assigned.</li>`;
                    return;
                }

                list.innerHTML = students.map(student => `
                    <li class="list-group-item px-0 border-0 d-flex justify-content-between align-items-start">
                        <div>
                            <i class="fa-solid fa-user-graduate me-2"></i>
                            <strong>${student.full_name}</strong><br>
                            <small class="text-muted">${student.course_year}</small>
                        </div>
                    </li>
                `).join('');
            })
            .catch(err => {
                console.error("Fetch student interns error:", err);
                document.querySelector(".student-intern-list").innerHTML = `<li class="list-group-item text-danger">Failed to load students.</li>`;
            });
    }

    function fetchTrainerDocuments(trainerId) {
        fetch(`../../1admin/establishment/fetch_trainer_documents.php?trainer_id=${trainerId}`)
            .then(res => res.json())
            .then(documents => {
                const docList = document.querySelector(".document-list");
                if (!Array.isArray(documents) || documents.length === 0) {
                    docList.innerHTML = `<li class="list-group-item text-muted">No documents submitted.</li>`;
                    return;
                }

                docList.innerHTML = documents.map(doc => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            ${doc.filename || "Untitled"}
                            <br>
                            <small class="text-muted">${doc.updated_on}</small>
                        </div>
                        <span class="d-flex gap-2">
                            <a class="btn-outline-icon" href="${doc.filepath}" target="_blank" title="View"><i class="fa-solid fa-eye"></i></a>
                            <a class="btn-outline-icon" href="${doc.filepath}" download title="Download"><i class="fa-solid fa-download"></i></a>
                        </span>
                    </li>
                `).join('');
            })
            .catch(err => {
                console.error("Fetch trainer documents error:", err);
                document.querySelector(".document-list").innerHTML = `<li class="list-group-item text-danger">Failed to load documents.</li>`;
            });
    }
});
