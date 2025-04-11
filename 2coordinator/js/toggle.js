document.addEventListener("DOMContentLoaded", function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            placement: 'right', // Ensure tooltip shows on the right
            container: 'body' // Prevents clipping inside sidebar
        });
    });
});

// Hide Tooltip on Click
document.querySelectorAll(".menu-toggle").forEach(function(toggle) {
    toggle.addEventListener("click", function() {
        let tooltipInstance = bootstrap.Tooltip.getInstance(this.parentElement);
        if (tooltipInstance) {
            tooltipInstance.hide(); // Hide tooltip on click
        }
    });
});