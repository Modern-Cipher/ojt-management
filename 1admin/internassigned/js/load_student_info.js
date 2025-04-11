document.addEventListener("DOMContentLoaded", () => {
    const studentListContainer = document.querySelector(".student-list-scroll");
    const infoContainer = document.querySelector(".info-scroll");
  
    // Delegated click handler
    studentListContainer.addEventListener("click", (e) => {
      const card = e.target.closest(".student-card");
      if (!card) return;
  
      const studentId = card.getAttribute("data-id");
  
      // Highlight selected
      document
        .querySelectorAll(".student-card")
        .forEach((c) => c.classList.remove("active-student"));
      card.classList.add("active-student");
  
      // Fetch student details
      fetch(`../../1admin/internassigned/fetch_student_details.php?student_id=${studentId}`)
        .then((res) => res.json())
        .then((data) => {
          if (data.error) {
            infoContainer.innerHTML = `<p class="text-danger small">${data.error}</p>`;
            return;
          }
  
          renderStudentInfo(data);
          fetchStudentFiles(studentId);
        })
        .catch((err) => {
          console.error("Fetch student info error:", err);
          infoContainer.innerHTML = `<p class="text-danger">Unable to load student information.</p>`;
        });
    });
  
    function renderStudentInfo(student) {
      infoContainer.innerHTML = `
        <div class="card shadow-sm mb-3 p-3">
          <div class="text-center mb-3">
            <img src="${student.image_profile}" class="rounded-circle mb-2 student-profile-img" width="90" height="90" alt="Profile">
            <h6 class="fw-semibold m-0">${student.fname ?? ""} ${student.lname ?? ""}</h6>
          </div>
          <hr>
          <h6 class="text-start fw-semibold"><i class="fa-solid fa-user me-2"></i>Personal Details</h6>
          <p class="text-muted text-start mb-1"><i class="fa-solid fa-user-tag me-2"></i>Username: ${student.username ?? ""}</p>
          <p class="text-muted text-start mb-1"><i class="fa-solid fa-venus-mars me-2"></i>Sex: ${student.sex ?? ""}</p>
          <p class="text-muted text-start mb-1"><i class="fa-solid fa-location-dot me-2"></i>Address: ${student.address ?? ""}</p>
          <p class="text-muted text-start mb-3"><i class="fa-solid fa-graduation-cap me-2"></i>Course: ${student.course ?? ""} | ${student.year_section ?? ""}</p>
  
          <h6 class="text-start fw-semibold"><i class="fa-solid fa-envelope me-2"></i>Contact Information</h6>
          <p class="text-muted text-start mb-1"><i class="fa-solid fa-envelope-open-text me-2"></i>Email: ${student.email ?? ""}</p>
          <p class="text-muted text-start mb-3"><i class="fa-solid fa-phone me-2"></i>Phone: ${student.phone ?? ""}</p>
  
          <h6 class="text-start fw-semibold"><i class="fa-solid fa-building-columns me-2"></i>Institute</h6>
          <p class="text-muted text-start mb-3"><i class="fa-solid fa-graduation-cap me-2"></i>${student.institute ?? ""}</p>
  
          <hr>
          <h6 class="text-start fw-semibold"><i class="fa-solid fa-circle-check me-2"></i>Account Status</h6>
          <p class="text-muted text-start mb-1"><i class="fa-solid fa-user-check me-2"></i>${student.account_status ?? ""}</p>
          <p class="text-muted text-start mb-3"><i class="fa-solid fa-calendar-plus me-2"></i>${student.created_on ?? ""}</p>
  
          <!-- 🔽 Files will be appended here -->
        </div>
      `;
    }
  
    function fetchStudentFiles(studentId) {
      fetch(`../../1admin/internassigned/fetch_student_files.php?student_id=${studentId}`)
        .then(res => res.json())
        .then(fileData => {
          renderStudentFiles(fileData);
        })
        .catch(err => {
          console.error("File fetch error:", err);
        });
    }
  
    function renderStudentFiles(files) {
      const card = infoContainer.querySelector(".card");
  
      const renderList = (title, list, icon) => {
        if (!list || list.length === 0) return "";
  
        let items = list.map(file => `
          <li class="list-group-item d-flex justify-content-between align-items-center">
            ${file.file_name ?? "Untitled"}
            <span class="d-flex gap-2">
              <a href="${file.filepath}" target="_blank" class="btn-outline-icon" title="View"><i class="fa-solid fa-eye"></i></a>
              <a href="${file.filepath}" download class="btn-outline-icon" title="Download"><i class="fa-solid fa-download"></i></a>
            </span>
          </li>
        `).join("");
  
        return `
          <h6 class="text-start fw-semibold mt-3"><i class="fa-solid ${icon} me-2"></i>${title}</h6>
          <ul class="list-group list-group-flush text-start mb-3">${items}</ul>
        `;
      };
  
      card.innerHTML += renderList("Pre Submitted Files", files.pre, "fa-file-alt");
      card.innerHTML += renderList("Post Submitted Files", files.post, "fa-file-signature");
      card.innerHTML += renderList("Journal Submitted Files", files.journal, "fa-book-journal-whills");
    }
  });
  