document.addEventListener("DOMContentLoaded", () => {
    fetch("../2coordinator/fetch_dashboard_counts_coordinator.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Error fetching dashboard data:", data.error);
                return;
            }

            // Update dashboard counts
            document.querySelector(".dashboard-card:nth-child(1) h3").textContent = data.total_interns;
            document.querySelector(".dashboard-card:nth-child(2) h3").textContent = data.ongoing;
            document.querySelector(".dashboard-card:nth-child(3) h3").textContent = data.completed;
            document.querySelector(".dashboard-card:nth-child(4) h3").textContent = data.establishment;
            document.querySelector(".dashboard-card:nth-child(5) h3").textContent = data.pending;
        })
        .catch(err => {
            console.error("Fetch error:", err);
        });
});
