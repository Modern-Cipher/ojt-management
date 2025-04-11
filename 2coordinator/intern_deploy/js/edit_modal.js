function openAssignModal(userId, currentHteId) {
    document.getElementById('assignUserId').value = userId;
    loadOptions(userId, currentHteId);

    const assignModal = new bootstrap.Modal(document.getElementById('assignModal'), {
        backdrop: 'static',
        keyboard: false
    });
    assignModal.show();
}

function loadOptions(studentId) {
    fetch(`fetch_hte_status.php?student_id=${studentId}`)
        .then((response) => response.json())
        .then((data) => {
            const hteSelect = document.getElementById("hteSelect");
            const ojtStatusSelect = document.getElementById("ojtStatusSelect");

            hteSelect.innerHTML = `<option value="">Select Host Establishment</option>`;
            ojtStatusSelect.innerHTML = `<option value="">Select Status</option>`;

            // Populate HTE Names
            data.hte_names.forEach((hte) => {
                if (!hte.assigned) {
                    const option = document.createElement("option");
                    option.value = hte.id;
                    option.text = hte.name;
                    hteSelect.appendChild(option);
                }
            });

            // Populate OJT Status
            data.ojt_statuses.forEach((status) => {
                const option = document.createElement("option");
                option.value = status;
                option.text = status.charAt(0).toUpperCase() + status.slice(1);
                ojtStatusSelect.appendChild(option);
            });
        })
        .catch((error) => {
            console.error("❌ FETCH ERROR:", error);
        });
}


document.getElementById("assignForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("assign_student.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showToast("✅ Student updated successfully!");
                document.getElementById("assignForm").reset();
                const assignModal = bootstrap.Modal.getInstance(document.getElementById("assignModal"));
                assignModal.hide();
                fetchStudentList();
            } else {
                showToast(data.error || "❌ Update failed!", true);
            }
        })
        .catch((error) => {
            console.error("❌ SUBMIT ERROR:", error);
        });
});

function showToast(message, isError = false) {
    const toast = new bootstrap.Toast(document.getElementById("statusToast"));
    const toastBody = document.getElementById("toastMessage");
    toastBody.textContent = message;
    toast._element.classList.remove("text-bg-success", "text-bg-danger");
    toast._element.classList.add(isError ? "text-bg-danger" : "text-bg-success");
    toast.show();
}
