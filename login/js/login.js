document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const username = document.querySelector("input[name='username']");
    const password = document.querySelector("input[name='password']");

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        if (username.value.trim() === "" || password.value.trim() === "") {
            showToast("Please fill out all fields.");
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch("login_api.php", {
                method: "POST",
                body: formData,
            });

            const text = await response.text();
            let data;

            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error("Invalid JSON response:", text);
                showToast("Server error. Please contact admin.");
                return;
            }

            if (data.status === "error") {
                showToast(data.message);
            } else if (data.status === "temppass") {
                showPasswordPrompt(data.user_id, data.role, data.redirect);
            } else if (data.status === "activate") {
                showActivationModal(data.user_id, data.guid);
            } else if (data.status === "success") {
                window.location.href = data.redirect;
            } else {
                showToast("Unexpected response.");
            }
        } catch (error) {
            console.error("Fetch failed:", error);
            showToast("Something went wrong. Please try again.");
        }
    });

    function showToast(message) {
        const toast = document.createElement("div");
        toast.className = "toast";
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function showPasswordPrompt(user_id, role, redirect) {
        const modal = document.createElement("div");
        modal.className = "modal-overlay";
        modal.innerHTML = `
            <div class="modal-box">
                <p>You are using a default password. Please set a new password to continue.</p>
                <div class="modal-actions">
                    <button onclick="showChangePasswordForm(${user_id}, '${redirect}')">Yes</button>
                    <button onclick="window.location.href='${redirect}'">Maybe Later</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    window.showChangePasswordForm = function (user_id, redirect) {
        document.querySelector('.modal-box').innerHTML = `
            <h3 class="mb-3">Set New Password</h3>
            <div class="form-group text-left">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" placeholder="Enter new password" class="form-control" />
            </div>
            <div class="form-group text-left">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" placeholder="Confirm new password" class="form-control" />
            </div>
            <button class="btn btn-primary btn-block mt-3" onclick="submitNewPassword(${user_id}, '${redirect}')">Update Password</button>
        `;
    };

    window.submitNewPassword = async function (user_id, redirect) {
        const newPass = document.getElementById("newPassword").value;
        const confirmPass = document.getElementById("confirmPassword").value;

        if (!newPass || !confirmPass) {
            showToast("Both fields are required.");
            return;
        }

        if (newPass !== confirmPass) {
            showToast("Passwords do not match.");
            return;
        }

        const formData = new FormData();
        formData.append("user_id", user_id);
        formData.append("new_password", newPass);

        const response = await fetch("../login/update_password.php", {
            method: "POST",
            body: formData,
        });

        const result = await response.text();

        if (result === "success") {
            showToast("Password updated successfully!");
            setTimeout(() => {
                window.location.href = redirect;
            }, 1500);
        } else {
            showToast("Update failed. Try again.");
        }
    };

    function showActivationModal(user_id, correct_guid) {
        const modal = document.createElement("div");
        modal.className = "modal-overlay";
        modal.innerHTML = `
            <div class="modal-box">
                <h4>Activate Your Account</h4>
                <p>Please enter the GUID sent to your email to activate your account.</p>
                <input type="text" id="entered_guid" class="form-control mb-2" placeholder="Enter GUID here" />
                <div class="modal-actions">
                    <button class="btn btn-success btn-sm" onclick="verifyGuid(${user_id}, '${correct_guid}')">Verify</button>
                    <button class="btn btn-secondary btn-sm" onclick="location.reload()">Cancel</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    window.verifyGuid = async function (user_id, correct_guid) {
        const entered = document.getElementById("entered_guid").value.trim();

        if (!entered) {
            showToast("GUID is required.");
            return;
        }

        const formData = new FormData();
        formData.append("user_id", user_id);
        formData.append("guid", entered);

        const res = await fetch("verify_guid.php", {
            method: "POST",
            body: formData,
        });

        const data = await res.json();

        if (data.status === "success") {
            showToast("Account activated! Please login.");
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || "Invalid GUID.");
        }
    };
});
