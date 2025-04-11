document.addEventListener("DOMContentLoaded", () => {
    // 🔥 Open file dialog
    document.querySelectorAll(".uploadBtn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const filenameId = btn.getAttribute("data-filename-id");
            const fileInput = document.getElementById(`pdfFileInput-${filenameId}`);
            fileInput.click();
        });
    });

    // 🔥 Display selected file
    document.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener("change", (e) => {
            const id = input.id.split("-")[1];
            const fileNameDisplay = document.getElementById(`fileNameDisplay-${id}`);
            fileNameDisplay.value = input.files[0]?.name || "";
        });
    });

    // 🔥 Submit File to PHP
    document.querySelectorAll(".submitBtn").forEach((btn) => {
        btn.addEventListener("click", () => {
            const filenameId = btn.getAttribute("data-filename-id");
            const fileInput = document.getElementById(`pdfFileInput-${filenameId}`);
            const file = fileInput.files[0];

            if (!file) {
                alert("Please select a PDF file.");
                return;
            }

            const formData = new FormData();
            formData.append("filename_id", filenameId);
            formData.append("pdfFile", file);

            fetch("upload_file.php", {
                method: "POST",
                body: formData,
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === "success") {
                        alert("✅ File uploaded!");
                        location.reload();
                    } else {
                        alert("❌ " + data.message);
                    }
                })
                .catch((err) => {
                    console.error(err);
                    alert("❌ Upload failed.");
                });
        });
    });
});
