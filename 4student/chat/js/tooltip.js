document.addEventListener("DOMContentLoaded", () => {
    const tooltip = document.createElement("div");
    tooltip.className = "tooltip-box";
    document.body.appendChild(tooltip);

    document.addEventListener("mouseover", (e) => {
        const target = e.target.closest("[tooltip-data]");
        if (target) {
            tooltip.textContent = target.getAttribute("tooltip-data");
            const rect = target.getBoundingClientRect();
            tooltip.style.top = `${rect.bottom + 8}px`;
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.classList.add("show");
        }
    });

    document.addEventListener("mouseout", (e) => {
        if (e.target.closest("[tooltip-data]")) {
            tooltip.classList.remove("show");
        }
    });
});



document.addEventListener("DOMContentLoaded", () => {
    const tooltipSide = document.createElement("div");
    tooltipSide.className = "tooltip-side-box";
    document.body.appendChild(tooltipSide);

    document.addEventListener("mouseover", (e) => {
        const target = e.target.closest("[tooltip-side]");
        if (target) {
            tooltipSide.textContent = target.getAttribute("tooltip-side");
            const rect = target.getBoundingClientRect();
            tooltipSide.style.top = `${rect.top + rect.height / 2}px`;
            tooltipSide.style.left = `${rect.right + 8}px`;
            tooltipSide.classList.add("show");
        }
    });

    document.addEventListener("mouseout", (e) => {
        if (e.target.closest("[tooltip-side]")) {
            tooltipSide.classList.remove("show");
        }
    });
});
