function loadStudentDetails(studentId) {
fetch(`fetch_student_details.php?student_id=${studentId}`)
.then((res) => res.json())
.then((data) => {
    if (data.error) {
    console.warn("❌", data.error);
    return;
    }

    const fallback = (val) => (val && val !== "null" ? val : "-");
    const googleMaps = (address) =>
    address && address !== "null"
        ? `<a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
            address
        )}" target="_blank">${address}</a>`
        : "-";
    const mailTo = (email) =>
    email && email !== "null"
        ? `<a href="mailto:${email}">${email}</a>`
        : "-";

    const statusColorMap = {
    deployed: "success",
    pending: "secondary",
    rejected: "warning",
    "pulled-out": "danger",
    };

    const statusColor =
    statusColorMap[data.ojt_stats?.toLowerCase()] || "secondary";

    document.querySelector(".student-name").textContent = `${fallback(
    data.fname
    )} ${fallback(data.lname)}`;

    document.querySelector(".info-section:nth-of-type(1)").innerHTML = `
        <strong>Information</strong> <br> <br>
            <i class="fa-solid fa-id-card"></i> ${fallback(
                data.school_id
            )}<br>
            <i class="fa-solid fa-user"></i> ${fallback(data.username)}<br>
            <i class="fa-solid fa-graduation-cap"></i> ${fallback(
                data.course
            )}<br>
            <i class="fa-solid fa-house"></i> ${googleMaps(data.address)}
        `;

    document.querySelector(".info-section:nth-of-type(2)").innerHTML = `
                <br><strong>Contact Information</strong> <br> <br>
            <i class="fa-solid fa-envelope"></i> ${mailTo(data.email)}<br>
            <i class="fa-solid fa-phone"></i> ${fallback(data.phone)}
        `;

    document.querySelector(".info-section:nth-of-type(3)").innerHTML = `
                <br><strong>Host Training Establishment</strong> <br> <br>
            <span class="badge bg-${statusColor}">Status: ${fallback(
    data.ojt_stats
    )}</span><br>
            <i class="fa-solid fa-building"></i> ${fallback(
                data.hte_name
            )}<br>
            <i class="fa-solid fa-location-dot"></i> ${googleMaps(
                data.hte_address
            )}
        `;

    document.querySelector(".info-section:nth-of-type(4)").innerHTML = `
                <br><strong>Host Trainer</strong> <br> <br>
            <i class="fa-solid fa-user-tie"></i> ${fallback(
                data.trainer_fname
            )} ${fallback(data.trainer_lname)}<br>
            <i class="fa-solid fa-briefcase"></i> ${fallback(
                data.designation
            )} <br>
            <i class="fa-solid fa-envelope"></i> ${mailTo(
                data.trainer_email
            )}<br>
            <i class="fa-solid fa-phone"></i> ${fallback(
                data.trainer_phone
            )}
        `;

    const img = document.querySelector(".profile-img2");
    img.src =
    data.image_profile && data.image_profile !== "null"
        ? `../../upload_profile/${data.image_profile}`
        : "../../resources/siplogo.png";
})
.catch((err) => {
    console.error("❌ FETCH ERROR", err);
});
}
