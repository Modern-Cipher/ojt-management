document.addEventListener("DOMContentLoaded", () => {
    const coordinatorList = document.querySelector(".coordinator-list-scroll");
    const searchInput = document.querySelector(".search-container input[placeholder='Search coordinator...']");
    let coordinatorData = [];

    // ✅ Fetch coordinators from backend
    fetch("../../1admin/establishment/fetch_coordinators_establishment.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                coordinatorList.innerHTML = `<p class="text-danger">${data.error}</p>`;
                return;
            }

            coordinatorData = data;
            renderCoordinatorList(data);
        })
        .catch(err => {
            console.error("Error fetching coordinators:", err);
            coordinatorList.innerHTML = `<p class="text-danger">Failed to load coordinators.</p>`;
        });

    // ✅ Render coordinator cards
    function renderCoordinatorList(data) {
        coordinatorList.innerHTML = "";

        if (!data.length) {
            coordinatorList.innerHTML = `<p class="text-muted small">No coordinators found.</p>`;
            return;
        }

        data.forEach(coord => {
            const image = coord.image_profile || "../../upload_profile/siplogo.png";
            const name = coord.full_name || "";
            const designation = coord.designation || "";
            const role = coord.role || "";

            const card = document.createElement("div");
            card.className = "card mb-2 position-relative coordinator-card";
            card.setAttribute("data-id", coord.users_id);

            card.innerHTML = `
                <span class="position-absolute top-0 end-0 badge bg-primary m-2">${role}</span>
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="${image}" class="rounded-circle" width="45" height="45" alt="Profile">
                    <div>
                        <h6 class="card-title mb-1">${name}</h6>
                        <p class="card-text small text-muted mb-0">${designation}</p>
                    </div>
                </div>
            `;

            // ✅ Highlight on selection
            card.addEventListener("click", () => {
                document.querySelectorAll(".coordinator-card").forEach(c => c.classList.remove("active-student"));
                card.classList.add("active-student");

                // TODO: Replace with your fetch logic for trainers
                console.log("Selected Coordinator ID:", coord.users_id);
            });

            coordinatorList.appendChild(card);
        });
    }

    // ✅ Filter on typing
    searchInput.addEventListener("input", () => {
        const keyword = searchInput.value.toLowerCase().trim();

        const filtered = coordinatorData.filter(coord => {
            const fullName = (coord.full_name || "").toLowerCase();
            const designation = (coord.designation || "").toLowerCase();
            return fullName.includes(keyword) || designation.includes(keyword);
        });

        renderCoordinatorList(filtered);
    });
});
