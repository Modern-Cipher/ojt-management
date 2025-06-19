let globalUsedWeekCounts = [];

function loadJournalTasks() {
  fetch("../../2coordinator/journal/fetch_journal_entries.php")
    .then((response) => {
      if (!response.ok) throw new Error("Failed to fetch journal entries.");
      return response.json();
    })
    .then((data) => {
      const tbody = document.getElementById("journalTableBody");
      const searchInput = document.getElementById("journalSearchInput");
      const weekSelect = document.getElementById("newCount");

      tbody.innerHTML = "";
      weekSelect.innerHTML = '<option value="">Select Week</option>';
      globalUsedWeekCounts = [];

      data.forEach((entry) => {
        const count = entry.count;
        const isValidCount =
          count && parseInt(count) >= 1 && parseInt(count) <= 20;
        const displayCount = isValidCount ? `Week ${count}` : "";

        const row = document.createElement("tr");
        row.dataset.filename = entry.filename;
        row.dataset.count = isValidCount ? count : "";
        row.innerHTML = `
          <td>${entry.filename}</td>
          <td>${displayCount}</td>
          <td>
              <i class="fa-solid fa-pen-to-square text-primary me-2 edit-btn" role="button" title="Edit"></i>
              <i class="fa-solid fa-trash text-danger delete-btn" role="button" title="Delete"></i>
          </td>
        `;
        tbody.appendChild(row);

        if (isValidCount) globalUsedWeekCounts.push(count.toString()); // Ensure string for comparison
      });

      // Debug: Log used week counts
      console.log("Used week counts after fetch:", globalUsedWeekCounts);

      // Update dropdown for editing (all weeks, as editing can reassign)
      for (let i = 1; i <= 20; i++) {
        const option = document.createElement("option");
        option.value = i.toString();
        option.textContent = `Week ${i}`;
        weekSelect.appendChild(option);
      }

      // Populate add form dropdown with disabled assigned weeks
      updateAddDropdown();

      // DELETE
      let pendingDeleteFilename = null;
      tbody.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const row = this.closest("tr");
          const filename = row.dataset.filename;
          pendingDeleteFilename = filename;
          document.getElementById(
            "deleteFilenameLabel"
          ).textContent = `"${filename}"`;
          new bootstrap.Modal(
            document.getElementById("confirmDeleteModal")
          ).show();
        });
      });

      document.getElementById("confirmDeleteBtn").onclick = function () {
        if (!pendingDeleteFilename) return;
        fetch("../../2coordinator/journal/delete_journal_entry.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ filename: pendingDeleteFilename }),
        })
          .then((res) => res.json())
          .then((result) => {
            if (result.success) {
              bootstrap.Modal.getInstance(
                document.getElementById("confirmDeleteModal")
              ).hide();
              loadJournalTasks();
              new bootstrap.Toast(
                document.getElementById("deleteToast")
              ).show();
            } else {
              alert("Delete failed: " + (result.error || "Unknown error"));
            }
            pendingDeleteFilename = null;
          })
          .catch((err) => {
            console.error("Delete failed", err);
            alert("Something went wrong.");
          });
      };

      // EDIT
      tbody.querySelectorAll(".edit-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const row = this.closest("tr");
          const oldFilename = row.dataset.filename;
          const oldCount = row.dataset.count;

          document.getElementById("oldFilename").value = oldFilename;
          document.getElementById("newFilename").value = oldFilename;
          document.getElementById("newCount").value =
            parseInt(oldCount) >= 1 && parseInt(oldCount) <= 20 ? oldCount : "";

          new bootstrap.Modal(
            document.getElementById("editJournalModal")
          ).show();
        });
      });

      // SEARCH
      if (searchInput) {
        searchInput.addEventListener("input", function () {
          const keyword = this.value.toLowerCase();
          tbody.querySelectorAll("tr").forEach((row) => {
            const taskText = row.children[0].textContent.toLowerCase();
            row.style.display = taskText.includes(keyword) ? "" : "none";
          });
        });
      }
    })
    .catch((error) => {
      console.error("Error loading journal tasks:", error);
      alert("Failed to load journal entries.");
    });
}

// Function to update add form dropdown with disabled assigned weeks
function updateAddDropdown() {
  const addSelect = document.getElementById("weekAssigned");
  addSelect.innerHTML = '<option value="">Select Week</option>';
  for (let i = 1; i <= 20; i++) {
    const opt = document.createElement("option");
    opt.value = i.toString();
    opt.textContent = `Week ${i}`;
    if (globalUsedWeekCounts.includes(i.toString())) {
      opt.disabled = true; // Disable if week is already assigned
    }
    addSelect.appendChild(opt);
  }
  console.log("Add dropdown updated, disabled weeks:", globalUsedWeekCounts);
}

// EDIT FORM SUBMIT
document
  .getElementById("editJournalForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const oldFilename = document.getElementById("oldFilename").value;
    const newFilename = document.getElementById("newFilename").value.trim();
    const newCount = document.getElementById("newCount").value;

    if (!newFilename || !newCount) {
      alert("Please provide both filename and week.");
      return;
    }

    fetch("../../2coordinator/journal/update_journal_entry.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ oldFilename, newFilename, newCount }),
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.success) {
          bootstrap.Modal.getInstance(
            document.getElementById("editJournalModal")
          ).hide();
          loadJournalTasks();
          new bootstrap.Toast(document.getElementById("successToast")).show();
        } else {
          alert("Update failed: " + (result.error || "Unknown error"));
        }
      })
      .catch((err) => {
        console.error("Update failed", err);
        alert("Something went wrong.");
      });
  });

// ADD FORM: Update dropdown on modal show
document
  .getElementById("journalModal")
  .addEventListener("shown.bs.modal", function () {
    updateAddDropdown();
  });

// ADD FORM SUBMIT
document.getElementById("journalForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("taskName").value.trim();
  const week = document.getElementById("weekAssigned").value;

  if (!name || !week) {
    const toast = new bootstrap.Toast(document.getElementById("successToast"));
    toast.show();
    document.getElementById("successToast").querySelector(".toast-body").textContent =
      "Please fill out all fields.";
    return;
  }

  fetch("../../2coordinator/journal/add_journal_entry.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ filename: name, count: week }),
  })
    .then((res) => res.json())
    .then((result) => {
      const toast = new bootstrap.Toast(document.getElementById("successToast"));
      if (result.success) {
        document.getElementById("taskName").value = ""; // Clear textbox
        document.getElementById("weekAssigned").value = ""; // Reset dropdown
        loadJournalTasks(); // Reload to update table and dropdown
        toast.show();
        document.getElementById("successToast").querySelector(".toast-body").textContent =
          "Journal entry added successfully.";
      } else {
        toast.show();
        document.getElementById("successToast").querySelector(".toast-body").textContent =
          "Add failed: " + (result.error || "Unknown error");
      }
    })
    .catch((err) => {
      console.error("Add failed", err);
      const toast = new bootstrap.Toast(document.getElementById("successToast"));
      toast.show();
      document.getElementById("successToast").querySelector(".toast-body").textContent =
        "An error occurred while adding the journal entry.";
    });
});

// INIT
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("journalModal");
  if (modal) {
    modal.addEventListener("shown.bs.modal", loadJournalTasks);
  }
});