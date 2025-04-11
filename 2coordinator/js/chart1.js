document.addEventListener("DOMContentLoaded", () => {
    fetch("../2coordinator/fetch_chart1_status.php")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById("chart1").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["Deployed", "Pending", "Pulled Out"],
                    datasets: [{
                        label: "Deployment Status",
                        data: [data.deployed, data.pending, data.pulled_out],
                        backgroundColor: ["#4CAF50", "#FFC107", "#F44336"],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Chart 1 Fetch Error:", error));
});
