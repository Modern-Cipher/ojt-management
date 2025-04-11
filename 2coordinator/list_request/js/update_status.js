document.addEventListener("change", function (e) {
if (e.target.classList.contains("account-toggle")) {
const userId = e.target.getAttribute("data-user-id");
const status = e.target.checked ? "enabled" : "disabled";

showLoader(true); // Show loading spinner

fetch("update_account_status.php", {
    method: "POST",
    headers: {
    "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `user_id=${userId}&status=${status}`,
})
    .then((response) => response.json())
    .then((data) => {
    showLoader(false); // Hide loader
    if (data.success) {
        showToast("✅ Account status updated to " + status, "success");
        fetchInterns(); // Reload list without full page reload
    } else {
        showToast("❌ " + data.message, "danger");
    }
    })
    .catch((error) => {
    showLoader(false);
    showToast("❌ Error updating status", "danger");
    console.error("❌ FETCH ERROR:", error);
    });
}
});

// ✅ Show/Hide Loader
function showLoader(show) {
document.getElementById("loadingSpinner").style.display = show
? "block"
: "none";
}

// ✅ Show Toast
function showToast(message, type) {
const toast = new bootstrap.Toast(document.getElementById("statusToast"));
const toastElement = document.getElementById("statusToast");
const toastMessage = document.getElementById("toastMessage");

toastMessage.textContent = message;
toastElement.className = `toast align-items-center text-bg-${type} border-0`;
toast.show();
}
