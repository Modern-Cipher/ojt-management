document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("activationForm");

    form.addEventListener("submit", function(event) {
        const email = document.getElementById("email").value.trim();
        const otp = document.getElementById("otp").value.trim();

        if (email === "" || otp === "") {
            alert("All fields are required!");
            event.preventDefault(); // Prevent form submission
        }
    });
});
