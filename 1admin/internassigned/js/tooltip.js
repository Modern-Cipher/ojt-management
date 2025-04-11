document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("[data-tooltip]").forEach(element => {
        const originalTooltip = element.getAttribute("data-tooltip"); // Store original text

        // ✅ Detect tooltip length and add `data-tooltip-length` attribute
        if (originalTooltip && originalTooltip.length > 25) {
            element.setAttribute("data-tooltip-length", "long"); // Mark long tooltips
        }

        // ✅ Hide tooltip on click
        element.addEventListener("click", function() {
            this.removeAttribute("data-tooltip"); // Remove only for clicked element

            // Restore tooltip after 500ms
            setTimeout(() => {
                if (!this.getAttribute("data-tooltip")) { // Restore only if still missing
                    this.setAttribute("data-tooltip", originalTooltip);

                    // Reapply length detection
                    if (originalTooltip.length > 25) {
                        this.setAttribute("data-tooltip-length", "long");
                    }
                }
            }, 500); // Adjust delay as needed
        });
    });
});


document.addEventListener("DOMContentLoaded", function() {
    // Initialize Bootstrap tooltips and auto-adjust placement
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            boundary: 'window', // Prevents tooltip from going off-screen
            fallbackPlacements: ['top', 'bottom', 'left', 'right'], // Auto-adjust
            html: true // Allows <br/> tags inside tooltips
        });
    });

    // Hide Bootstrap tooltip on click
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
        element.addEventListener("click", function() {
            var tooltipInstance = bootstrap.Tooltip.getInstance(this);
            if (tooltipInstance) {
                tooltipInstance.hide();
            }
        });
    });
});