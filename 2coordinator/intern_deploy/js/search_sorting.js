document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("intern_deploy_Table");

    searchInput.addEventListener("keyup", function () {
        const searchValue = searchInput.value.toLowerCase();
        const rows = Array.from(table.querySelector("tbody").rows);
        rows.forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(searchValue)
                ? ""
                : "none";
        });
    });
});

// ✅ Sorting Functionality (Ascending/Descending)
let sortOrder = {}; // Track column sort order

function sortTable(columnIndex) {
    const table = document.getElementById("intern_deploy_Table");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.rows);

    sortOrder[columnIndex] = !sortOrder[columnIndex]; // Toggle sorting

    rows.sort((a, b) => {
        let aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
        let bValue = b.cells[columnIndex].textContent.trim().toLowerCase();

        if (!isNaN(aValue) && !isNaN(bValue)) {
            // Numeric Sort
            return sortOrder[columnIndex] ? aValue - bValue : bValue - aValue;
        }
        return sortOrder[columnIndex]
            ? aValue.localeCompare(bValue)
            : bValue.localeCompare(aValue);
    });

    rows.forEach((row) => tbody.appendChild(row)); // Re-append sorted rows
}
