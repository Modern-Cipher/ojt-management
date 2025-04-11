document.addEventListener("DOMContentLoaded", function () {
    const listContainer = document.querySelector(".student-list-scroll");
    const searchInput = document.getElementById("studentSearch");
    const detailsContainer = document.getElementById("student-details");
    const modalImg = document.getElementById("modal-profile-img");

    let studentsData = [];

    // Fetch student list
    fetch("../../3hte/intern_list/fetch_students.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                listContainer.innerHTML = `<p class="text-danger">${data.error}</p>`;
                return;
            }

            studentsData = data;
            renderStudentList(studentsData);
        })
        .catch(err => {
            console.error("Fetch error:", err);
            listContainer.innerHTML = `<p class="text-danger">Failed to fetch student list.</p>`;
        });

    // Render student cards
    function renderStudentList(data) {
        listContainer.innerHTML = "";

        if (data.length === 0) {
            listContainer.innerHTML = `<p class="text-muted small">No matching students found.</p>`;
            return;
        }

        data.forEach(student => {
            const card = document.createElement("div");
            card.className = "card mb-2 student-card";
            card.dataset.id = student.users_id;
            card.innerHTML = `
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="${student.image_profile}" alt="Profile" class="rounded-circle" width="45" height="45">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">${student.fname} ${student.lname}</h6>
                            <span class="badge bg-secondary text-white">${capitalize(student.role)}</span>
                        </div>
                        <p class="card-text small text-muted mb-0">${student.course}</p>
                    </div>
                </div>
            `;
            listContainer.appendChild(card);

            // Add click event to fetch student details and highlight selected
            card.addEventListener("click", () => {
                document.querySelectorAll(".student-card").forEach(el => el.classList.remove("active-student"));
                card.classList.add("active-student");
                fetchStudentDetails(student.users_id);
            });
        });
    }

    // Search functionality
    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        const filtered = studentsData.filter(s => {
            const fullName = `${s.fname} ${s.lname}`.toLowerCase();
            const course = s.course.toLowerCase();
            return fullName.includes(query) || course.includes(query);
        });
        renderStudentList(filtered);
    });

    // Fetch and display selected student details
    function fetchStudentDetails(users_id) {
        fetch(`../../3hte/intern_list/fetch_student_details.php?id=${users_id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    detailsContainer.innerHTML = `<p class="text-danger">${data.error}</p>`;
                    return;
                }

                modalImg.src = data.image_profile || "../../upload_profile/siplogo.png";

                const course = data.course || "";
                const year = data.year_section || "";
                const address = data.address || "";
                const email = data.email || "";
                const phone = data.phone || "";
                const dean = data.dean || "";
                const coordinator = data.coordinator || "";
                const institute = data.institute || "";

                detailsContainer.innerHTML = `
                    <img src="${data.image_profile}" onerror="this.onerror=null;this.src='../../upload_profile/siplogo.png';"
                        class="rounded-circle mb-3 student-profile-img"
                        width="120" height="120" alt="Profile"
                        data-bs-toggle="modal" data-bs-target="#profileModal"
                        style="cursor: pointer; object-fit: cover;">

                    <h5 class="card-title d-flex justify-content-center align-items-center gap-2">
                        <i class="fa-solid fa-user"></i> ${data.fname} ${data.lname}
                    </h5>
                    <p class="card-text d-flex justify-content-center align-items-center gap-2 text-muted mb-1">
                        <i class="fa-solid fa-graduation-cap"></i> ${course}
                    </p>
                    <p class="card-text d-flex justify-content-center align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-location-dot"></i> ${address}
                    </p>

                    <hr>
                    <h6 class="fw-semibold text-start"><i class="fa-solid fa-address-book me-2"></i>Contact Information</h6>
                    <p class="card-text text-muted d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-envelope"></i> ${email}
                    </p>
                    <p class="card-text text-muted d-flex align-items-center gap-2">
                        <i class="fa-solid fa-phone"></i> ${phone}
                    </p>

                    <hr>
                    <h6 class="fw-semibold text-start"><i class="fa-solid fa-school me-2"></i>Higher Education Institution</h6>
                    <p class="card-text text-muted d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-building-columns"></i> Bulacan Agricultural State College
                    </p>
                    <p class="card-text text-muted d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-user-gear"></i> ${institute}
                    </p>
                    <p class="card-text text-muted d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-user-tie"></i> Dean: ${dean}
                    </p>
                    <p class="card-text text-muted d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-gear"></i> Coordinator: ${coordinator}
                    </p>

                    <hr>
                    <h6 class="fw-semibold text-start"><i class="fa-solid fa-file-alt me-2"></i>Documents</h6>
                    <ul class="list-group list-group-flush text-start" id="document-list">
                        <li class="list-group-item text-muted">Loading documents...</li>
                    </ul>
                `;

                // Fetch student documents
                fetch(`../../3hte/intern_list/fetch_student_documents.php?id=${users_id}`)
                    .then(res => res.json())
                    .then(docs => {
                        const docList = document.getElementById("document-list");
                        if (docs.length === 0) {
                            docList.innerHTML = `<li class="list-group-item text-muted">No documents submitted yet.</li>`;
                        } else {
                            docList.innerHTML = "";
                            docs.forEach(doc => {
                                docList.innerHTML += `
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ${doc.filename}
                                        <span class="d-flex gap-2">
                                            <a href="${doc.filepath}" target="_blank" class="btn-outline-icon" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="${doc.filepath}" download class="btn-outline-icon" title="Download">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </span>
                                    </li>
                                `;
                            });
                        }
                    });
            })
            .catch(err => {
                console.error("Details fetch error:", err);
                detailsContainer.innerHTML = `<p class="text-danger">Failed to load student details.</p>`;
            });
    }

    function capitalize(word) {
        return word.charAt(0).toUpperCase() + word.slice(1);
    }
});
