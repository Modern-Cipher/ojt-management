
document.addEventListener("DOMContentLoaded", function() {
    const courseSelect = document.getElementById("courseSelect");
    const roleSelect = document.getElementById("roleSelect");

    // Courses by Institute (Modify as needed)
    const courses = {
        "Institute of Engineering and Applied Technology": [
            "BS in Information Technology",
            "BS in Geodetic Engineering",
            "BS in Food Technology"
        ],
        "Institute of Management": [
            "BS in Business Administration",
            "BS in Hospitality Management"
        ]
    };

    roleSelect.addEventListener("change", function() {
        if (roleSelect.value === "coordinator") {
            // Clear existing options
            courseSelect.innerHTML = "";

            // Add default "Choose Course Title" option
            let defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "Choose Course Title";
            defaultOption.selected = true;
            defaultOption.disabled = true;
            courseSelect.appendChild(defaultOption);

            // Populate courses dynamically
            Object.keys(courses).forEach((institute) => {
                courses[institute].forEach((course) => {
                    let option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            });
        }
    });
});
