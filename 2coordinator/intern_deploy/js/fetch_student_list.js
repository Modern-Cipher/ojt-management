document.addEventListener("DOMContentLoaded", function () {
fetchStudentList();
});

function fetchStudentList() {
fetch("fetch_student_list.php")
.then((response) => response.json())
.then((data) => {
    if (data.error) {
    console.error("❌ ERROR:", data.error);
    return;
    }
    renderStudentTable(data);
})
.catch((error) => {
    console.error("❌ FETCH ERROR:", error);
});
}

function openAssignModal(userId) {
document.getElementById("assignUserId").value = userId;
loadOptions(); // 👈 Auto-fetch dropdown data

const assignModal = new bootstrap.Modal(
document.getElementById("assignModal"),
{
    backdrop: "static",
    keyboard: false,
}
);
assignModal.show();
}

function renderStudentTable(data) {
    const tbody = document.querySelector("#intern_deploy_Table tbody");
    tbody.innerHTML = "";

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">No student data found</td></tr>`;
        return;
    }

    data.forEach((student) => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td>${student.no}</td>
            <td>${student.school_id}</td>
            <td>${student.fullname}</td>
            <td>${student.hte_name}</td>
            <td>${student.trainer_fullname}</td>
            <td><span class="badge bg-${getStatusColor(student.ojt_stats)}">${student.ojt_stats}</span></td>
            <td>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Assign / Update Deployment"
                    onclick="openAssignModal(${student.users_id}, ${student.hte_id ?? 'null'})">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        `;

        // ✅ Add event listener on entire row except the Edit button
        tr.addEventListener("click", (e) => {
            if (!e.target.closest("button")) {
                loadStudentDetails(student.users_id);
                loadOjtHistory(student.users_id);
            }
        });

        tbody.appendChild(tr);
    });
}


function getStatusColor(status) {
switch (status) {
case "deployed":
    return "success";
case "pending":
    return "secondary";
case "pulled-out":
    return "danger";
default:
    return "secondary";
}
}

function attachEditEvents() {
const editButtons = document.querySelectorAll(".edit-btn");
editButtons.forEach((button) => {
button.addEventListener("click", function () {
    const studentId = this.getAttribute("data-student-id");
    document.getElementById("assignUserId").value = studentId;
    loadOptions();
    const editModal = new bootstrap.Modal(
    document.getElementById("assignModal")
    );
    editModal.show();
});
});
}
