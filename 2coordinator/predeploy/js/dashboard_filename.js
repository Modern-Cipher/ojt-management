document.addEventListener("DOMContentLoaded", () => {
    const timelineContainer = document.querySelector(".timeline");
    const cardContainer = document.getElementById("cardContainer");
    const coordinatorId = document.getElementById("coordinatorInfo").getAttribute("data-id");
    let currentStudentId = null;

    // Toast
    const toastContainer = document.createElement("div");
    toastContainer.classList.add("toast-container", "position-fixed", "bottom-0", "end-0", "p-3");
    document.body.appendChild(toastContainer);

    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.classList.add("toast", "align-items-center", "text-bg-" + type, "border-0");
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener("hidden.bs.toast", () => {
            toast.remove();
        });
    }

    // Card click
    cardContainer.addEventListener("click", (e) => {
        const card = e.target.closest(".custom-card");
        if (card) {
            const studentId = card.getAttribute("data-id");
            currentStudentId = studentId;
            loadStudentFiles(studentId);
            highlightCard(card);
        }
    });

    function highlightCard(selectedCard) {
        document.querySelectorAll(".custom-card").forEach((card) => card.classList.remove("active"));
        selectedCard.classList.add("active");
    }

    function formatDate(dateString) {
        const options = {
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        };
        return new Date(dateString).toLocaleString("en-US", options);
    }

    function loadStudentFiles(studentId) {
        fetch(`fetch_filename.php?student_id=${studentId}`)
            .then((response) => response.json())
            .then((data) => displayTimeline(data))
            .catch((error) => {
                console.error("Error fetching files:", error);
                showToast("Error loading files.", "danger");
            });
    }

    function displayTimeline(files) {
        timelineContainer.innerHTML = "";

        if (files.length === 0) {
            timelineContainer.innerHTML = '<p class="text-muted">No documents available.</p>';
            return;
        }

        files.forEach((file) => {
            let statusClass = "",
                icon = "",
                statusText = "",
                statusBadge = "";

            if (file.upload_status === "accepted") {
                statusClass = "success";
                icon = "fa-check";
                statusText = "Accepted";
                statusBadge = "bg-success";
            } else if (file.upload_status === "processing") {
                statusClass = "secondary";
                icon = "fa-question";
                statusText = "Processing";
                statusBadge = "bg-secondary";
            } else {
                statusClass = "danger";
                icon = "fa-xmark";
                statusText = "Rejected";
                statusBadge = "bg-danger";
            }

            const filename = file.filename || "";
            const timestamp = file.updated_on ? formatDate(file.updated_on) : "";
            const filepath = file.filepath || "";

            const item = document.createElement("div");
            item.classList.add("timeline-item", "mb-3", "position-relative");

            item.innerHTML = `
                <div class="timeline-left d-flex flex-column align-items-center">
                    <div class="timeline-dot ${statusClass}">
                        <i class="fa-solid ${icon}"></i>
                    </div>
                    <div class="timeline-line flex-grow-1"></div>
                </div>

                <div class="file-card p-3 shadow-sm d-flex flex-column gap-3 w-100">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="d-flex flex-column">
                            <span class="filename fw-semibold mb-1">${filename}</span>
                            <small class="timestamp text-muted">${timestamp}</small>
                        </div>
                        <span class="badge rounded-pill ${statusBadge}">${statusText}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap w-100">
                        <div class="d-flex gap-2">
                            ${
                                filepath
                                    ? `
                            <a href="${filepath}" target="_blank" class="btn btn-outline-dark btn-sm icon-btn view-btn" tooltip-data="View File">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="${filepath}" download class="btn btn-outline-dark btn-sm icon-btn download-btn" tooltip-data="Download File">
                                <i class="fa-solid fa-download"></i>
                            </a>` : ""
                            }
                            <button class="btn btn-outline-dark btn-sm icon-btn edit-btn" tooltip-data="Update Status" data-id="${file.uploads_id}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">View All Comments</span>
                            <div class="form-check form-switch m-0" tooltip-data="See more">
                                <input class="form-check-input toggle-comments" type="checkbox" role="switch" data-id="${file.uploads_id}">
                            </div>
                        </div>
                    </div>

                    <div class="comment-container w-100 mt-2" data-id="${file.uploads_id}" style="display:none;">
                        <div class="comments-list" data-id="${file.uploads_id}"></div><br>
                        <form class="comment-form d-flex gap-2 justify-content-end flex-grow-1 mb-2" data-id="${file.uploads_id}">
                            <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                            <button type="submit" class="btn btn-dark btn-sm">Send</button>
                        </form>
                    </div>
                </div>
            `;

            timelineContainer.appendChild(item);

            // Edit button
            const editBtn = item.querySelector(".edit-btn");
            editBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const uploadsId = editBtn.getAttribute("data-id");
                showEditDropdown(uploadsId, editBtn);
            });

            // Toggle comments
            const toggle = item.querySelector(".toggle-comments");
            toggle.addEventListener("change", (e) => {
                const uploadsId = e.target.getAttribute("data-id");
                const container = item.querySelector(`.comment-container[data-id="${uploadsId}"]`);
                if (e.target.checked) {
                    container.style.display = "block";
                    loadComments(uploadsId, container.querySelector(".comments-list"));
                } else {
                    container.style.display = "none";
                }
            });

            // Comment submit
            const commentForm = item.querySelector(".comment-form");
            commentForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const input = commentForm.querySelector(".comment-input");
                const comment = input.value.trim();
                const uploadsId = commentForm.getAttribute("data-id");
                if (comment !== "") {
                    postComment(uploadsId, comment, () => {
                        input.value = "";
                        const commentList = item.querySelector(`.comments-list[data-id="${uploadsId}"]`);
                        loadComments(uploadsId, commentList);
                        showToast("Comment added.", "success");
                    });
                }
            });
        });
    }

    function showEditDropdown(uploadsId, button) {
        const existingDropdown = document.querySelector(".dropdown-edit");
        if (existingDropdown) existingDropdown.remove();

        const currentStatus = button.closest(".file-card").querySelector(".badge").textContent.trim().toLowerCase();

        const dropdown = document.createElement("div");
        dropdown.classList.add("dropdown-edit");

        let options = "";
        if (currentStatus === "accepted") {
            options = `<option value="rejected">Reject</option>`;
        } else if (currentStatus === "rejected") {
            options = `<option value="accepted">Accept</option>`;
        } else {
            options = `
                <option value="accepted">Accept</option>
                <option value="rejected">Reject</option>
            `;
        }

        dropdown.innerHTML = `
            <select class="form-select form-select-sm status-select" data-id="${uploadsId}">
                <option disabled selected>Change Status</option>
                ${options}
            </select>
        `;

        button.parentElement.appendChild(dropdown);

        dropdown.style.position = "absolute";
        dropdown.style.top = `${button.offsetTop + button.offsetHeight + 5}px`;
        dropdown.style.left = `${button.offsetLeft}px`;
        dropdown.style.zIndex = "9999";

        dropdown.querySelector(".status-select").addEventListener("change", (e) => {
            const newStatus = e.target.value;
            updateStatus(uploadsId, newStatus);
            dropdown.remove();
        });

        document.addEventListener("click", function handler(e) {
            if (!dropdown.contains(e.target) && !e.target.closest(".edit-btn")) {
                dropdown.remove();
                document.removeEventListener("click", handler);
            }
        });
    }

    function updateStatus(uploadsId, status) {
        if (!uploadsId || !currentStudentId) {
            showToast("Missing Upload ID or Student ID.", "warning");
            return;
        }
        fetch("update_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                uploadsId,
                status,
                checkedby_id: coordinatorId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showToast("Status updated!", "success");
                    loadStudentFiles(currentStudentId);
                } else {
                    showToast("Failed to update status", "danger");
                }
            })
            .catch((err) => {
                console.error(err);
                showToast("Error updating status", "danger");
            });
    }

    function loadComments(uploadsId, commentList) {
        fetch(`fetch_comments.php?uploads_id=${uploadsId}&uploadedby_id=${currentStudentId}`)
            .then((response) => response.json())
            .then((data) => {
                commentList.innerHTML = "";

                if (data.status !== "success" || data.comments.length === 0) {
                    commentList.innerHTML = `<p class="text-muted small">No comments yet.</p>`;
                    return;
                }

                data.comments.forEach((comment) => {
                    const isOwnComment = comment.commenter_id == parseInt(coordinatorId);

                    const item = document.createElement("div");
                    item.classList.add("comment-item");

                    item.innerHTML = `
                        <img src="${comment.profile_image}" alt="Profile" class="comment-profile">
                        <div class="comment-details">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="comment-author">${comment.fullname}</div>
                                    <div class="comment-timestamp">${comment.created_at}</div>
                                </div>
                                ${
                                    isOwnComment
                                        ? `<button class="btn btn-sm btn-danger delete-comment" data-id="${comment.file_comment_id}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>`
                                        : ""
                                }
                            </div>
                            <div class="comment-text">${comment.comment}</div>
                        </div>
                    `;

                    commentList.appendChild(item);
                });

                // Delete event listener
                commentList.querySelectorAll(".delete-comment").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        const commentId = btn.getAttribute("data-id");
                        deleteComment(commentId, uploadsId, commentList);
                    });
                });
            })
            .catch((err) => {
                console.error("Error fetching comments:", err);
            });
    }

    function deleteComment(commentId, uploadsId, commentList) {
        fetch("delete_comment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ commentId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showToast("Comment deleted", "success");
                    loadComments(uploadsId, commentList);
                } else {
                    showToast("Failed to delete comment", "danger");
                }
            })
            .catch((err) => {
                console.error(err);
                showToast("Error deleting comment", "danger");
            });
    }

    function postComment(uploadsId, comment, callback) {
        fetch("insert_comment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                uploadsId,
                comment,
                commenter_id: coordinatorId,
                uploadedby_id: currentStudentId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) callback();
                else showToast("Failed to post comment", "danger");
            })
            .catch((err) => {
                console.error(err);
                showToast("Error posting comment", "danger");
            });
    }
});
