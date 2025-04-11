function loadProfileCard(data) {
    document.querySelector(".profile-img2").src = data.image_profile;
    document.querySelector(".student-name").textContent = data.fullname;

    const infoSections = document.querySelectorAll(".info-section");

    infoSections[0].innerHTML = `
        <strong>Information</strong><br>
        <i class="fas fa-id-card"></i> ${data.school_id}<br>
        <i class="fas fa-user"></i> ${data.username}<br>
        <i class="fas fa-graduation-cap"></i> ${data.course}<br>
        <i class="fas fa-house"></i> 
        <a href="https://www.google.com/maps/search/${encodeURIComponent(data.address)}" target="_blank" class="link-hover">${data.address}</a>
    `;

    infoSections[1].innerHTML = `
        <strong>Contact Information</strong><br>
        <i class="fas fa-envelope"></i> 
        <a href="mailto:${data.email}" class="link-hover">${data.email}</a><br>
        <i class="fas fa-phone"></i> ${data.phone}
    `;

    infoSections[2].innerHTML = `
        <strong>Host Training Establishment</strong><br>
        <span class="badge ${data.ojt_status === "Deployed" ? "bg-success" : "bg-secondary"}">Status: ${data.ojt_status}</span><br>
        <i class="fas fa-building"></i> ${data.hte_name}<br>
        <i class="fas fa-location-dot"></i> ${data.hte_address}
    `;

    infoSections[3].innerHTML = `
        <strong>Host Trainer</strong><br>
        <i class="fas fa-user-tie"></i> ${data.trainer_name}<br>
        <i class="fas fa-briefcase"></i> ${data.trainer_designation}<br>
        <i class="fas fa-envelope"></i> 
        <a href="mailto:${data.trainer_email}" class="link-hover">${data.trainer_email}</a><br>
        <i class="fas fa-phone"></i> ${data.trainer_phone}
    `;
}
