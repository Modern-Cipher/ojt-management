document.addEventListener("DOMContentLoaded", () => {
    const coordinatorList = document.querySelector(".coordinator-list-scroll");
    const trainerList = document.querySelector(".student-list-scroll");
    const trainerSearchInput = document.querySelector(".div2 input[placeholder='Search trainer...']");
    let allTrainers = [];

    // When coordinator is clicked
    coordinatorList.addEventListener("click", (e) => {
        const card = e.target.closest(".coordinator-card");
        if (!card) return;

        const coordinatorId = card.getAttribute("data-id");

        // Highlight the selected coordinator
        document.querySelectorAll(".coordinator-card").forEach(el => el.classList.remove("active-student"));
        card.classList.add("active-student");

        // Fetch trainers under selected coordinator
        fetch(`../../1admin/establishment/fetch_trainers_by_coordinator.php?coordinator_id=${coordinatorId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    trainerList.innerHTML = `<p class="text-danger small">${data.error}</p>`;
                    return;
                }
                allTrainers = data;
                renderTrainerList(data);
            })
            .catch(err => {
                console.error("Fetch error:", err);
                trainerList.innerHTML = `<p class="text-danger small">Unable to fetch trainer list.</p>`;
            });
    });

    // Live search bar
    trainerSearchInput.addEventListener("input", () => {
        const keyword = trainerSearchInput.value.toLowerCase().trim();
        const filtered = allTrainers.filter(trainer => {
            const fullName = (trainer.full_name || "").toLowerCase();
            const designation = (trainer.designation || "").toLowerCase();
            return fullName.includes(keyword) || designation.includes(keyword);
        });
        renderTrainerList(filtered);
    });

    // Render trainer list and handle click
    function renderTrainerList(data) {
        trainerList.innerHTML = "";

        if (!data.length) {
            trainerList.innerHTML = `<p class="text-muted small">No trainers found.</p>`;
            return;
        }

        data.forEach(trainer => {
            const image = trainer.image_profile || "../../upload_profile/siplogo.png";
            const fullName = trainer.full_name || "";
            const designation = trainer.designation || "";
            const role = trainer.role || "";

            const card = document.createElement("div");
            card.className = "card mb-2 position-relative trainer-card";
            card.setAttribute("data-id", trainer.users_id);
            card.innerHTML = `
                <span class="position-absolute top-0 end-0 badge bg-success m-2">${role}</span>
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="${image}" class="rounded-circle" width="45" height="45" alt="Profile">
                    <div>
                        <h6 class="card-title mb-1">${fullName}</h6>
                        <p class="card-text small text-muted mb-0">Designation: ${designation}</p>
                    </div>
                </div>
            `;

            // ✅ Highlight trainer on click
            card.addEventListener("click", () => {
                document.querySelectorAll(".trainer-card").forEach(c => c.classList.remove("active-student"));
                card.classList.add("active-student");

                // OPTIONAL: trigger trainer details fetch here if needed
                // fetchTrainerInfo(trainer.users_id);
            });

            trainerList.appendChild(card);
        });
    }
});
