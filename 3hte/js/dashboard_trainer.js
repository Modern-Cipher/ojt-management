document.addEventListener("DOMContentLoaded", () => {
    fetch("../3hte/fetch_dashboard_trainer.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Dashboard Error:", data.error);
                return;
            }

            // Safely get all dashboard cards
            const cards = document.querySelectorAll(".dashboard-card");

            // Check if the required cards exist before updating
            if (cards.length >= 2) {
                cards[0].querySelector("h3").textContent = data.students_deployed;
                cards[1].querySelector("h3").textContent = data.documents_submitted;
            }

            // Hide any extra cards (if more than 2 exist)
            for (let i = 2; i < cards.length; i++) {
                cards[i].style.display = "none";
            }

            // Hide charts section if it exists
            const chartSection = document.querySelector(".dashboard-graphs");
            if (chartSection) {
                chartSection.style.display = "none";
            }
        })
        .catch(err => {
            console.error("Trainer Dashboard Fetch Error:", err);
        });
});
