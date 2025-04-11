// ✅ dashboard_stats_chart.js

document.addEventListener("DOMContentLoaded", () => {
    fetch("../1admin/fetch_chart_data.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Chart data error:", data.error);
                return;
            }

            renderDeploymentStatusChart(data);
        })
        .catch(err => {
            console.error("Error fetching chart data:", err);
        });
});

function renderDeploymentStatusChart(data) {
    const ctx = document.getElementById("chart1").getContext("2d");

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Deployed", "Pending", "Pulled Out"],
            datasets: [{
                label: "Student Deployment Status",
                data: [data.deployed, data.pending, data.pulled_out],
                backgroundColor: [
                    "#198754",   // green
                    "#ffc107",   // yellow
                    "#dc3545"    // red
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "bottom"
                },
                title: {
                    display: true,
                    text: "OJT Status Overview"
                }
            }
        }
    });
}
