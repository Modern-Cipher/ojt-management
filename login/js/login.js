document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const username = document.querySelector("input[name='username']");
    const password = document.querySelector("input[name='password']");

    form.addEventListener("submit", function (event) {
        if (username.value.trim() === "" || password.value.trim() === "") {
            alert("Please fill out all fields.");
            event.preventDefault();
        }
    });
});
