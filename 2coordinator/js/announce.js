document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById("toggle-announcement-btn");
    const extraAnnouncements = document.getElementById("extra-announcements");

    // Initial state load
    let isExpanded = localStorage.getItem("announcementsExpandedCoordinator") === "true";

    function updateToggleUI() {
        if (isExpanded) {
            extraAnnouncements.style.display = "block";
            toggleBtn.innerHTML = 'See Less <i class="fa-solid fa-square-caret-up"></i>';
        } else {
            extraAnnouncements.style.display = "none";
            toggleBtn.innerHTML = 'View all history <i class="fa-solid fa-square-caret-down"></i>';
        }
    }

    updateToggleUI();

    toggleBtn.addEventListener("click", function(e) {
        e.preventDefault();
        isExpanded = !isExpanded;
        localStorage.setItem("announcementsExpandedCoordinator", isExpanded.toString());
        updateToggleUI();
    });
});