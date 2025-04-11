document.addEventListener("DOMContentLoaded", () => {
    const coordinatorList = document.querySelector(".coordinator-list-scroll");
    const searchInput = document.querySelector(".search-container input[placeholder='Search coordinator...']");
    let coordinatorData = [];

    // Fetch coordinators
    fetch("../../1admin/internassigned/fetch_coordinators.php")
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

    // Render cards and allow for selection
    function renderCoordinatorList(data) {
        coordinatorList.innerHTML = "";

        if (data.length === 0) {
            coordinatorList.innerHTML = `<p class="text-muted small">No coordinators found.</p>`;
            return;
        }

        data.forEach(coord => {
            const card = document.createElement("div");
            card.className = "card mb-2 position-relative coordinator-card";
            card.setAttribute("data-id", coord.users_id);
            card.innerHTML = `
                <span class="position-absolute top-0 end-0 badge bg-primary m-2">${coord.role}</span>
                <div class="card-body d-flex align-items-center gap-3">
                    <img src="${coord.image_profile}" class="rounded-circle" width="45" height="45" alt="Profile">
                    <div>
                        <h6 class="card-title mb-1">${coord.full_name}</h6>
                        <p class="card-text small text-muted mb-0">${coord.designation}</p>
                    </div>
                </div>
            `;

            // ✅ Card click for selection highlight
            card.addEventListener("click", () => {
                document.querySelectorAll(".coordinator-card").forEach(c => c.classList.remove("active-student"));
                card.classList.add("active-student");
            });

            coordinatorList.appendChild(card);
        });
    }

    // Search filter
    searchInput.addEventListener("input", () => {
        const keyword = searchInput.value.toLowerCase().trim();
        const filtered = coordinatorData.filter(coord =>
            coord.full_name.toLowerCase().includes(keyword) ||
            coord.designation.toLowerCase().includes(keyword)
        );
        renderCoordinatorList(filtered);
    });
});
