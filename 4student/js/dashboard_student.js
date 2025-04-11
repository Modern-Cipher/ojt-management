document.addEventListener("DOMContentLoaded", () => {
    fetch("../4student/fetch_student_dashboard_data.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Error:", data.error);
                return;
            }

            // Set text content for file submission counts
            document.querySelector(".dashboard-card:nth-child(1) h3").textContent = data.pre_submitted;
            document.querySelector(".dashboard-card:nth-child(2) h3").textContent = data.post_submitted;
            document.querySelector(".dashboard-card:nth-child(3) h3").textContent = data.journal_submitted;

            // Create a status badge inside the 4th card
            const statusCard = document.querySelector(".dashboard-card:nth-child(4) .dashboard-right");

            // Clear any existing text
            statusCard.innerHTML = "";

            // Create the badge element
            const badge = document.createElement("span");
            badge.classList.add("badge", "rounded-pill", "px-3", "py-2", "small", "text-capitalize");

            // Set badge text
            badge.textContent = data.status;

            // Set badge color class
            badge.classList.remove("bg-success", "bg-warning", "bg-danger", "bg-secondary");
            if (data.status === "deployed") {
                badge.classList.add("bg-success");
            } else if (data.status === "pending") {
                badge.classList.add("bg-warning");
            } else if (data.status === "pulled out") {
                badge.classList.add("bg-danger");
            } else {
                badge.classList.add("bg-secondary");
            }

            statusCard.appendChild(badge);
        })
        .catch(err => {
            console.error("Fetch student dashboard error:", err);
        });
});
