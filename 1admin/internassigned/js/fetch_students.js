document.addEventListener("DOMContentLoaded", () => {
    const coordinatorListContainer = document.querySelector(".coordinator-list-scroll");
    const studentListContainer = document.querySelector(".student-list-scroll");
    const studentSearchInput = document.querySelector(".search-container input[placeholder='Search student...']");

    let studentData = []; // 🔄 Store fetched students for filtering

    // ✅ Event Delegation for Dynamically Loaded Coordinator Cards
    coordinatorListContainer.addEventListener("click", (e) => {
        const card = e.target.closest(".coordinator-card");
        if (!card) return;

        const coordinatorId = card.getAttribute("data-id");

        // ✅ Highlight the selected card
        document.querySelectorAll(".coordinator-card").forEach(c => c.classList.remove("active-student"));
        card.classList.add("active-student");

        // ✅ Clear previous search input
        if (studentSearchInput) studentSearchInput.value = "";

        // ✅ Fetch students based on selected coordinator
        fetch(`../../1admin/internassigned/fetch_students_by_coordinator.php?coordinator_id=${coordinatorId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    studentListContainer.innerHTML = `<p class="text-muted small">${data.error}</p>`;
                    return;
                }

                studentData = data;
                renderStudentList(data);
            })
            .catch(err => {
                console.error("Fetch error:", err);
                studentListContainer.innerHTML = `<p class="text-danger small">Failed to fetch student list.</p>`;
            });
    });

    // ✅ Render student cards
    function renderStudentList(data) {
        studentListContainer.innerHTML = "";

        if (data.length === 0) {
            studentListContainer.innerHTML = `<p class="text-muted small">No students assigned.</p>`;
            return;
        }

        data.forEach(student => {
            const studentCard = document.createElement("div");
            studentCard.className = "card mb-2 position-relative student-card";
            studentCard.setAttribute("data-id", student.users_id);

            studentCard.innerHTML = `
                <span class="position-absolute top-0 end-0 badge bg-success m-2">Student</span>
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="${student.image_profile || '../../upload_profile/siplogo.png'}" class="rounded-circle" width="45" height="45" alt="Profile">
                    <div>
                        <h6 class="card-title mb-1">${student.fname} ${student.lname}</h6>
                        <p class="card-text small text-muted mb-0">${student.course}</p>
                    </div>
                </div>
            `;

            studentListContainer.appendChild(studentCard);
        });
    }

    // ✅ Search students by name or course
    if (studentSearchInput) {
        studentSearchInput.addEventListener("input", () => {
            const keyword = studentSearchInput.value.toLowerCase().trim();
            const filtered = studentData.filter(student =>
                `${student.fname} ${student.lname}`.toLowerCase().includes(keyword) ||
                student.course.toLowerCase().includes(keyword)
            );

            renderStudentList(filtered);
        });
    }
});
