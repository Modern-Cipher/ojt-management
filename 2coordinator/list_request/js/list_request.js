document.addEventListener("DOMContentLoaded", function () {
fetchInterns();

document
.querySelector("#list_request_Table tbody")
.addEventListener("click", function (e) {
    const row = e.target.closest("tr");
    if (!row) return;

    const userId = row
    .querySelector(".seminar-checkbox")
    ?.getAttribute("data-user-id");
    if (!userId) return;

    loadProfile(userId);
});
});

function fetchInterns() {
fetch("fetch_intern_list.php")
.then((response) => response.json())
.then((data) => {
    const tbody = document.querySelector("#list_request_Table tbody");
    tbody.innerHTML = "";

    if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">No data available</td></tr>`;
    return;
    }

    data.forEach((intern) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
<td>${intern.no}</td>
<td>${intern.student_id}</td>
<td>${intern.fullname}</td>
<td>
    <input type="checkbox" class="form-check-input seminar-checkbox"
    ${intern.attended === "yes" ? "checked" : ""}
    data-user-id="${intern.users_id}">
</td>
<td><span class="badge ${
    intern.status === "enabled" ? "bg-success" : "bg-danger"
}">${intern.status}</span></td>
<td>
    <div class="form-check form-switch">
    <input class="form-check-input account-toggle" type="checkbox"
        data-user-id="${intern.users_id}" 
        ${intern.status === "enabled" ? "checked" : ""}>
    </div>
</td>
`;
    tbody.appendChild(tr);
    });
})
.catch((error) => {
    console.error("❌ FETCH ERROR:", error);
});
}

function loadProfile(userId) {
fetch(`profile_fetch.php?user_id=${userId}`)
.then((res) => res.json())
.then((data) => {
    if (data.error) {
    console.error(data.error);
    resetProfileCard();
    return;
    }

    document.querySelector(".profile-img2").src = data.image_profile;
    document.querySelector(".student-name").textContent = data.fullname;

    const infoSections = document.querySelectorAll(".info-section");

    infoSections[0].innerHTML = `
<span class="badge ${
    data.activated === "Activated" ? "bg-success" : "bg-danger"
}">${data.activated}</span><br>
<small>Created on: ${data.created_on || "-"}</small>
`;
    infoSections[1].innerHTML = `
<strong>Information</strong><br>
<i class="fas fa-id-card"></i> ${data.school_id || "-"}<br>
<i class="fas fa-user"></i> ${data.username || "-"}<br>
<i class="fas fa-graduation-cap"></i> ${data.course || "-"}<br>
<i class="fas fa-house"></i> 
${
data.address
? `<a href="https://www.google.com/maps/search/${encodeURIComponent(
    data.address
    )}" target="_blank" style="text-decoration:none; color:inherit;">${
    data.address
    }</a>`
: "-"
}
`;
    infoSections[2].innerHTML = `
<strong>Contact Information</strong><br>
<i class="fas fa-envelope"></i>
${
    data.email
    ? `<a href="mailto:${data.email}" style="text-decoration:none; color:inherit;" class="link-hover">${data.email}</a>`
    : "-"
}<br>
<i class="fas fa-phone"></i> ${data.phone || "-"}
`;
})
.catch((err) => {
    console.error("❌ Error loading profile:", err);
    resetProfileCard();
});
}

function resetProfileCard() {
document.querySelector(".profile-img2").src = "../../resources/siplogo.png";
document.querySelector(".student-name").textContent = "-";

const infoSections = document.querySelectorAll(".info-section");
infoSections[0].innerHTML = `
<span class="badge bg-secondary">-</span><br>
<small>Created on: -</small>
`;
infoSections[1].innerHTML = `
<strong>Information</strong><br>
<i class="fas fa-id-card"></i> -<br>
<i class="fas fa-user"></i> -<br>
<i class="fas fa-graduation-cap"></i> -<br>
<i class="fas fa-house"></i> -
`;
infoSections[2].innerHTML = `
<strong>Contact Information</strong><br>
<i class="fas fa-envelope"></i> -<br>
<i class="fas fa-phone"></i> -
`;
}
