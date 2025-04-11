let internData = [];
let filteredDataList = [];
let currentSort = {
  column: null,
  direction: "asc",
};

document.addEventListener("DOMContentLoaded", function () {
  fetchInterns();

  // Search input
  document.getElementById("searchInput").addEventListener("input", function () {
    filterAndRender();
  });
});

function fetchInterns() {
  fetch("fetch_intern_list.php")
    .then((response) => response.json())
    .then((data) => {
      internData = data;
      filteredDataList = [...internData]; // initial copy
      renderTable();
    })
    .catch((error) => {
      console.error("❌ FETCH ERROR:", error);
      document.querySelector("#list_request_Table tbody").innerHTML = `
            <tr><td colspan="6" class="text-danger text-center">Error loading data.</td></tr>
        `;
    });
}

function filterAndRender() {
  const search = document.getElementById("searchInput").value.toLowerCase();
  filteredDataList = internData.filter(
    (intern) =>
      intern.student_id.toLowerCase().includes(search) ||
      intern.fullname.toLowerCase().includes(search)
  );
  applySort();
  renderTable();
}

function applySort() {
  if (currentSort.column !== null) {
    filteredDataList.sort((a, b) => {
      const col = currentSort.column;
      let valA = a[col] ?? "";
      let valB = b[col] ?? "";

      if (typeof valA === "string") valA = valA.toLowerCase();
      if (typeof valB === "string") valB = valB.toLowerCase();

      if (valA < valB) return currentSort.direction === "asc" ? -1 : 1;
      if (valA > valB) return currentSort.direction === "asc" ? 1 : -1;
      return 0;
    });
  }
}

function renderTable() {
  const tbody = document.querySelector("#list_request_Table tbody");
  tbody.innerHTML = "";

  if (filteredDataList.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">No data found</td></tr>`;
    return;
  }

  filteredDataList.forEach((intern) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
        <td>${intern.no}</td>
        <td>${intern.student_id}</td>
        <td>${intern.fullname}</td>
        <td>
            <input type="checkbox" class="form-check-input seminar-checkbox"
                ${intern.attended === "yes" ? "checked" : ""}
                data-user-id="${intern.users_id}" 
                data-bs-toggle="tooltip" 
                title="Mark as Attended">
        </td>
        <td><span class="badge ${
          intern.status === "enabled" ? "bg-success" : "bg-danger"
        }">${intern.status}</span></td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input account-toggle" type="checkbox"
                    data-bs-toggle="tooltip" title="Enable/Disable Account"
                    data-user-id="${intern.users_id}" 
                    ${intern.status === "enabled" ? "checked" : ""}>
            </div>
        </td>
    `;
    tbody.appendChild(tr);
  });
}

function sortTable(columnIndex) {
  const columns = ["no", "student_id", "fullname", "attended", "status"];
  const column = columns[columnIndex];

  if (currentSort.column === column) {
    currentSort.direction = currentSort.direction === "asc" ? "desc" : "asc";
  } else {
    currentSort.column = column;
    currentSort.direction = "asc";
  }

  applySort();
  renderTable();
}
