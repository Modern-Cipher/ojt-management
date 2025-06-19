// ✅ FINAL CLEAN VERSION - Fully Working File Comment System + File Actions + Toast + Card Style + Proper Toggle + Auto Load Comments + Badge-Styled Original File Name with Larger Normal Font

// 📂 Show Toast Function
function showToast(message, type) {
  const toastContainer = document.getElementById("toastContainer");
  if (!toastContainer) return;
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-bg-${type} border-0 m-2`;
  toast.role = "alert";
  toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
  toastContainer.appendChild(toast);
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();
}

// ✅ Date Formatter
function formatDate(datetime) {
  const date = new Date(datetime);
  const options = {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  };
  return date.toLocaleString("en-US", options).replace(",", "");
}

// 📂 Load Files
document.addEventListener("DOMContentLoaded", () => {
  fetch("fetch_pre_files.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.files) {
        const grid = document.querySelector(".dashboard-grid");
        grid.innerHTML = "";

        data.files.forEach((file) => {
          const card = document.createElement("div");

          const status = file.upload_status?.toLowerCase();

          const statusCard =
            file.upload_status === "accepted"
              ? "border border-success"
              : file.upload_status === "processing"
              ? "border border-secondary"
              : file.upload_status === "rejected"
              ? "border border-danger"
              : "border border-muted";

          const fileNameBadge =
            file.upload_status === "accepted"
              ? "bg-success text-white"
              : file.upload_status === "processing"
              ? "bg-secondary text-white"
              : file.upload_status === "rejected"
              ? "bg-danger text-white"
              : "bg-light text-dark";

          card.className = `dashboard-card card shadow-sm p-3 mb-3 ${statusCard}`;

          const fileInputId = `pdfFileInput-${file.filename_id}`;
          const displayId = `fileNameDisplay-${file.filename_id}`;
          const isAccepted = file.upload_status === "accepted";

          card.innerHTML = `
              <div class="card-header bg-white border-0 pb-2 d-flex justify-content-between align-items-center">
                  <div>
                      <strong>${file.filename}</strong><br />
                      <small>Checked by ${file.checker_name}</small><br />
                      <small>Week ${file.count}</small>
                  </div>
                  <div class="text-end">
                      <span class="badge px-2 py-1 fw-medium text-capitalize 
                          ${
                            file.upload_status === "accepted"
                              ? "bg-success text-white"
                              : file.upload_status === "processing"
                              ? "bg-secondary text-white"
                              : file.upload_status === "rejected"
                              ? "bg-danger text-white"
                              : "bg-light text-dark"
                          } rounded">
                          ${file.upload_status || "No File Upload"}
                      </span><br />
                      <small>${file.updated_on ? formatDate(file.updated_on) : ""}</small>
                  </div>
              </div>
  
              <div class="card-body pt-0">
                  <div class="d-flex my-3 gap-2">
                      <input type="text" class="form-control form-control-sm" id="${displayId}" readonly placeholder="Select a PDF file" />
                      <input type="file" id="${fileInputId}" accept="application/pdf" style="display:none;">
                      <button class="btn btn-sm btn-outline-dark uploadBtn" 
                          data-filename-id="${file.filename_id}" 
                          data-input-id="${fileInputId}" 
                          data-display-id="${displayId}" 
                          ${isAccepted ? "disabled" : ""}
                          title="Upload File">
                          <i class="fa-solid fa-upload"></i>
                      </button>
                  </div>
                  <span class="badge ${fileNameBadge} px-2 py-1 mb-2 d-block text-start fw-normal" style="font-size: 0.9rem;">${
                    file.original_file_name !== "-"
                      ? file.original_file_name
                      : "No file uploaded"
                  }</span>
  
                  <div class="text-end d-flex justify-content-end gap-2 flex-wrap mb-2">
                      <button class="btn btn-sm btn-outline-dark viewBtn" data-path="${
                        file.filepath || ""
                      }" ${!file.filepath ? "disabled" : ""} title="View File">
                          <i class="fa-solid fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-dark downloadBtn" data-path="${
                        file.filepath || ""
                      }" ${!file.filepath ? "disabled" : ""} title="Download File">
                          <i class="fa-solid fa-download"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-dark deleteBtn" data-upload-id="${
                        file.uploads_id || ""
                      }" data-filepath="${file.filepath || ""}" ${
                        !file.uploads_id || isAccepted ? "disabled" : ""
                      } title="Delete File">
                          <i class="fa-solid fa-trash"></i>
                      </button>
                      <button class="btn btn-sm btn-success submitBtn" data-filename-id="${
                        file.filename_id
                      }" ${isAccepted ? "disabled" : ""} title="Submit File">
                          <span>Submit</span>
                      </button>
                  </div>
  
                  <hr />
                  <p class="text-center text-muted mb-2 toggle-comment" data-filename-id="${
                    file.filename_id
                  }" style="cursor:pointer;">
                      View all comments <i class="fa-solid fa-square-caret-down"></i>
                  </p>
                  <hr />
                  <div class="comment-section scrollable-comments" id="commentSection-${
                    file.filename_id
                  }" style="display:none;" data-loaded="false"></div>
                  <div class="input-group mb-3" id="commentInput-${
                    file.filename_id
                  }" style="display:none;">
                      <input type="text" class="form-control" placeholder="Reply Comment" />
                      <button class="btn btn-outline-success submitCommentBtn" data-filename-id="${
                        file.filename_id
                      }">
                          <i class="fa-solid fa-paper-plane"></i>
                      </button>
                  </div>
              </div>
          `;

          grid.appendChild(card);
          loadComments(file.filename_id);
          restoreToggle(file.filename_id);
        });

        attachEvents();
      }
    })
    .catch((err) => {
      console.error("❌ Fetch Error:", err);
      showToast("Something went wrong while fetching.", "danger");
    });
});

// ✅ Comment System Core Functions
function loadComments(filenameId) {
  const section = document.getElementById(`commentSection-${filenameId}`);
  fetch(`fetch_file_comments.php?filename_id=${filenameId}`)
    .then((res) => res.json())
    .then((data) => {
      section.innerHTML = "";
      if (data.comments) {
        data.comments.forEach((c) => {
          const imageProfile = c.image_profile
            ? `../../upload_profile/${c.image_profile}`
            : `../../resources/siplogo.png`; // Default if empty

          const div = document.createElement("div");
          div.className = "d-flex align-items-start mb-2";
          div.innerHTML = `
              <img src="${imageProfile}" alt="User" class="rounded-circle comment-avatar">
              <div class="ms-2 w-100 d-flex justify-content-between">
                <div class="ms-2">
                  <strong>${c.fullname}</strong>
                  ${
                    c.checkedby_id
                      ? `<span class="badge bg-info">Replied</span>`
                      : ""
                  }<br />
                  <small class="text-muted">${formatDate(c.created_at)}</small><br /><br />
                  <p class="m-0">${c.comment}</p>
                </div>
                ${
                  c.commenter_id == sessionUserId
                    ? `
                <button class="btn btn-sm btn-outline-danger deleteCommentBtn" data-comment-id="${c.file_comment_id}">
                  <i class="fa-solid fa-trash"></i>
                </button>`
                    : ""
                }
              </div>
              <hr/>
            `;
          section.appendChild(div);
        });
        attachDeleteComment(filenameId);
      }
    })
    .catch((err) => {
      showToast("❌ Failed to load comments.", "danger");
      console.error(err);
    });
}

function restoreToggle(filenameId) {
  const commentSection = document.getElementById(`commentSection-${filenameId}`);
  const inputSection = document.getElementById(`commentInput-${filenameId}`);
  const state = localStorage.getItem(`toggle-comment-${filenameId}`);
  if (state === "show") {
    commentSection.style.display = "block";
    inputSection.style.display = "flex";
  } else {
    commentSection.style.display = "none";
    inputSection.style.display = "none";
  }
}

function attachEvents() {
  document.querySelectorAll(".submitCommentBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const filenameId = btn.dataset.filenameId;
      const input = document.querySelector(`#commentInput-${filenameId} input`);
      const comment = input.value.trim();

      if (!comment) {
        showToast("❌ Please enter a comment.", "warning");
        return;
      }

      fetch("post_file_comment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ filename_id: filenameId, comment }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            showToast("✅ Comment posted!", "success");
            input.value = "";
            loadComments(filenameId);
          } else {
            showToast("❌ " + data.message, "danger");
          }
        })
        .catch((err) => {
          showToast("❌ Comment post failed.", "danger");
          console.error(err);
        });
    });
  });

  document.querySelectorAll(".toggle-comment").forEach((btn) => {
    btn.addEventListener("click", () => {
      const filenameId = btn.dataset.filenameId;
      const commentSection = document.getElementById(`commentSection-${filenameId}`);
      const inputSection = document.getElementById(`commentInput-${filenameId}`);
      const isVisible = commentSection.style.display !== "none";

      if (isVisible) {
        commentSection.style.display = "none";
        inputSection.style.display = "none";
        localStorage.setItem(`toggle-comment-${filenameId}`, "hide");
      } else {
        commentSection.style.display = "block";
        inputSection.style.display = "flex";
        localStorage.setItem(`toggle-comment-${filenameId}`, "show");
      }

      // ✅ Load comments if not yet loaded
      if (!commentSection.dataset.loaded) {
        loadComments(filenameId);
        commentSection.dataset.loaded = "true";
      }
    });
  });

  // 🔥 Bind File Actions here
  attachFileActions();
}

function attachDeleteComment(filenameId) {
  const section = document.getElementById(`commentSection-${filenameId}`);
  section.querySelectorAll(".deleteCommentBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const commentId = btn.dataset.commentId;
      if (!commentId) return;

      fetch("delete_file_comment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ file_comment_id: commentId }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            showToast("✅ Comment deleted", "success");
            loadComments(filenameId);
          } else {
            showToast("❌ " + data.message, "danger");
          }
        })
        .catch((err) => {
          showToast("❌ Delete failed.", "danger");
          console.error(err);
        });
    });
  });
}

// ✅ FILE ACTIONS ONLY
function attachFileActions() {
  document.querySelectorAll(".uploadBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const inputId = btn.dataset.inputId;
      const displayId = btn.dataset.displayId;
      const input = document.getElementById(inputId);
      input.click();
      input.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (file) {
          document.getElementById(displayId).value = file.name;
        }
      });
    });
  });

  document.querySelectorAll(".submitBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const filenameId = btn.dataset.filenameId;
      const input = document.getElementById(`pdfFileInput-${filenameId}`);
      const file = input.files[0];

      if (!file) {
        showToast("❌ Please select a PDF file.", "warning");
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
            showToast("✅ File uploaded!", "success");
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast("❌ " + data.message, "danger");
          }
        })
        .catch((err) => {
          showToast("❌ Upload failed.", "danger");
          console.error(err);
        });
    });
  });

  document.querySelectorAll(".viewBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const path = btn.dataset.path;
      if (path) {
        window.open(path, "_blank");
      } else {
        showToast("❌ No file to view.", "warning");
      }
    });
  });

  document.querySelectorAll(".downloadBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const path = btn.dataset.path;
      if (path) {
        const a = document.createElement("a");
        a.href = path;
        a.download = path.split("/").pop();
        a.click();
      } else {
        showToast("❌ No file to download.", "warning");
      }
    });
  });

  document.querySelectorAll(".deleteBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const uploadId = btn.dataset.uploadId;
      const filepath = btn.dataset.filepath;
      if (!uploadId) {
        showToast("❌ No file to delete.", "warning");
        return;
      }

      if (!confirm("Are you sure you want to delete this file?")) return;

      fetch("delete_file.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ uploads_id: uploadId, filepath }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            showToast("✅ File deleted", "success");
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast("❌ " + data.message, "danger");
          }
        })
        .catch((err) => {
          showToast("❌ Deletion failed.", "danger");
          console.error(err);
        });
    });
  });
}