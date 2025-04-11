document.addEventListener("DOMContentLoaded", function () {
  fetchInterns();

  document
    .querySelector("#list_request_Table tbody")
    .addEventListener("change", function (e) {
      if (e.target.classList.contains("seminar-checkbox")) {
        const userId = e.target.getAttribute("data-user-id");
        const attended = e.target.checked;

        fetch("update_attendance.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `user_id=${userId}&attended=${attended}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              console.log("✅ Attendance updated");
            } else {
              console.error("❌ Update failed:", data.message);
            }
          })
          .catch((error) => {
            console.error("❌ FETCH ERROR:", error);
          });
      }
    });
});

function fetchInterns() {
  fetch("fetch_intern_list.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("HTTP error " + response.status);
      }
      return response.json();
    })
    .then((data) => {
      const tbody = document.querySelector("#list_request_Table tbody");
      tbody.innerHTML = "";

      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">No data available</td></tr>`;
        return;
      }

      data.forEach((intern) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
<td>${intern.no}</td>
<td>${intern.student_id}</td>
<td>${intern.fullname}</td>
<td>
    <label class="tooltip-wrapper" data-tooltip="Mark as Attended">
        <input type="checkbox" class="form-check-input seminar-checkbox"
            ${intern.attended === "yes" ? "checked" : ""}
            data-user-id="${intern.users_id}">
    </label>
</td>
<td><span class="badge ${
          intern.status === "enabled" ? "bg-success" : "bg-danger"
        }">${intern.status}</span></td>
<td>
    <label class="tooltip-wrapper" data-tooltip="Enable/Disable Account">
        <div class="form-check form-switch">
            <input class="form-check-input account-toggle" type="checkbox"
                data-user-id="${intern.users_id}" 
                ${intern.status === "enabled" ? "checked" : ""}>
        </div>
    </label>
</td>
`;

        tbody.appendChild(tr);
      });
    })
    .catch((error) => {
      console.error("❌ FETCH ERROR:", error);
      document.querySelector("#list_request_Table tbody").innerHTML = `
    <tr><td colspan="6" class="text-danger text-center">Error loading data. Check console.</td></tr>
`;
    });
}
