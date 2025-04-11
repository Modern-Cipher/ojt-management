document.addEventListener("DOMContentLoaded", function () {
const toggleBtn = document.getElementById("toggleHistory");
const historySection = document.getElementById("historySection");
const toggleIcon = document.getElementById("toggleIcon");

toggleBtn.addEventListener("click", function () {
// Toggle visibility
if (historySection.classList.contains("hidden")) {
    historySection.classList.remove("hidden");
    // Change icon to UP
    toggleIcon.innerHTML = `
View All Deployment History 
<i class="fa-regular fa-square-caret-up"></i>
`;
} else {
    historySection.classList.add("hidden");
    // Change icon to DOWN
    toggleIcon.innerHTML = `
View All Deployment History 
<i class="fa-regular fa-square-caret-down"></i>
`;
}
});
});

function loadOjtHistory(studentId) {
fetch(`fetch_ojt_history.php?student_id=${studentId}`)
.then((res) => res.json())
.then((data) => {
    if (data.error) {
    console.warn("❌", data.error);
    return;
    }

    const section = document.getElementById("historySection");
    section.innerHTML = ""; // Clear previous

    if (data.history.length === 0) {
    section.innerHTML = "<p class='text-danger'>No history found.</p>";
    return;
    }

    data.history.forEach((record, index) => {
    section.innerHTML += `
                <hr>
                <div class="info-section">
                    <strong>${
                        index === 0 ? "Current Status:" : "Previous Status:"
                    }</strong> <br>
                    <strong>Status:</strong> <span class="badge bg-${getStatusColor(
                        record.new_status
                    )}">${record.new_status}</span><br>
                    <strong>Assigned:</strong> ${
                        record.new_hte_name || "-"
                    } <br>
                    <strong>Date Assigned:</strong> ${
                        record.date_assigned || "-"
                    } <br>
                    <strong>Date Changed:</strong> ${
                        record.date_changed || "-"
                    } <br>
                    <strong>Previous Status:</strong> ${
                        record.old_status || "-"
                    } <br>
                    <strong>Previous Assigned:</strong> ${
                        record.old_hte_name || "-"
                    } <br>
                </div>
            `;
    });
})
.catch((err) => console.error("❌ FETCH ERROR", err));
}
