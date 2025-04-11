        // ✅ This defines the function so the inline onclick will find it
        function toggleAnnouncements(event) {
            event.preventDefault();
            let extraAnnouncements = document.getElementById("extra-announcements");
            let toggleText = document.querySelector(".view-history");

            if (extraAnnouncements.style.display === "none" || extraAnnouncements.style.display === "") {
                extraAnnouncements.style.display = "block";
                toggleText.innerHTML = "See less <i class='fa-solid fa-square-caret-up'></i>";
                localStorage.setItem("announcementsExpanded", "true");
            } else {
                extraAnnouncements.style.display = "none";
                toggleText.innerHTML = "View all history <i class='fa-solid fa-square-caret-down'></i>";
                localStorage.setItem("announcementsExpanded", "false");
            }
        }

        // ✅ On page load, check state and apply
        document.addEventListener("DOMContentLoaded", function() {
            let extraAnnouncements = document.getElementById("extra-announcements");
            let toggleText = document.querySelector(".view-history");
            let isExpanded = localStorage.getItem("announcementsExpanded") === "true";

            if (isExpanded) {
                extraAnnouncements.style.display = "block";
                toggleText.innerHTML = "See less <i class='fa-solid fa-square-caret-up'></i>";
            } else {
                extraAnnouncements.style.display = "none";
                toggleText.innerHTML = "View all history <i class='fa-solid fa-square-caret-down'></i>";
            }
        });