document.addEventListener("DOMContentLoaded", () => {
    fetch("fetch_dashboard_counts.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Dashboard error:", data.error);
                return;
            }

            document.querySelector(".dashboard-grid .dashboard-card:nth-child(1) h3").textContent = data.total_interns;
            document.querySelector(".dashboard-grid .dashboard-card:nth-child(2) h3").textContent = data.ongoing;
            document.querySelector(".dashboard-grid .dashboard-card:nth-child(3) h3").textContent = data.completed;
            document.querySelector(".dashboard-grid .dashboard-card:nth-child(4) h3").textContent = data.establishment;
            document.querySelector(".dashboard-grid .dashboard-card:nth-child(5) h3").textContent = data.pending;
        })
        .catch(err => {
            console.error("Failed to fetch dashboard stats:", err);
        });
});
