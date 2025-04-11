// toast.js

(function () {
    function createToastContainer() {
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className = "toast-container position-fixed bottom-0 end-0 p-3";
            document.body.appendChild(container);
        }
        return container;
    }

    window.showToast = function (message, type = "success") {
        const container = createToastContainer();
        const toast = document.createElement("div");
        toast.className = `toast align-items-center text-bg-${type} border-0 global-toast mb-2`;
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener("hidden.bs.toast", () => {
            toast.remove();
        });

        // Optional Auto Hide (3s)
        setTimeout(() => {
            bsToast.hide();
        }, 3000);
    };
})();
