// C:\xampp\htdocs\ojt\2coordinator\chat\js\toast.js
function showToast(message, isError = false) {
    const toastContainer = document.createElement("div");
    toastContainer.classList.add("toast-container", "position-fixed", "top-0", "end-0", "p-3");
    toastContainer.style.zIndex = "99999";

    const toast = document.createElement("div");
    toast.classList.add("toast", "align-items-center", "border-0");
    toast.classList.add(isError ? "text-bg-danger" : "text-bg-success");
    toast.setAttribute("role", "alert");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    document.body.appendChild(toastContainer);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener("hidden.bs.toast", () => {
        toastContainer.remove();
    });
}