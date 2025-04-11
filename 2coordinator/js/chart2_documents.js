document.addEventListener("DOMContentLoaded", () => {
    fetch("../2coordinator/fetch_chart2_documents.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Chart 2 Fetch Error:", data.error);
                return;
            }

            const ctx = document.getElementById("chart2").getContext("2d");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["Pre-deployment", "Post-deployment", "Trainer Document", "Journal"],
                    datasets: [{
                        label: "Document Submissions",
                        data: [data.pre, data.post, data.hte, data.journal],
                        borderColor: "#4e73df",
                        backgroundColor: "rgba(78, 115, 223, 0.2)",
                        tension: 0.4,
                        borderWidth: 2,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: "#4e73df"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error("Fetch error (Chart 2):", err);
        });
});
