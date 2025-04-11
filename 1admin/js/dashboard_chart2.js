// dashboard_chart2.js

document.addEventListener("DOMContentLoaded", () => {
    fetch("../1admin/fetch_chart_documents.php") // Matches your uploaded PHP file
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById("chart2").getContext("2d");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["Pre", "Post", "Trainer (HTE)", "Journal"],
                    datasets: [{
                        label: "Documents Submitted",
                        data: [
                            data.pre_count,
                            data.post_count,
                            data.trainer_count,
                            data.journal_count
                        ],
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4 // creates the wave curve
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: "index", intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error("Chart2 Fetch Error:", err);
        });
});
