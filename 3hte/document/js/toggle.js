// document.addEventListener("DOMContentLoaded", function () {
//     const toggleBtn = document.getElementById("toggleCommentBtn");
//     const commentSection = document.getElementById("commentSection");
//     const commentInput = document.getElementById("commentInput");
//     const toggleIcon = document.getElementById("toggleIcon");

//     // Check saved state
//     const isCollapsed = localStorage.getItem("commentCollapsed") === "true";
//     setVisibility(isCollapsed);

//     toggleBtn.addEventListener("click", () => {
//         const currentlyHidden = commentSection.style.display === "none";
//         setVisibility(!currentlyHidden);
//         localStorage.setItem("commentCollapsed", !currentlyHidden);
//     });

//     function setVisibility(collapse) {
//         if (collapse) {
//             commentSection.style.display = "none";
//             commentInput.style.display = "none";
//             toggleIcon.classList.remove("fa-square-caret-down");
//             toggleIcon.classList.add("fa-square-caret-up");
//         } else {
//             commentSection.style.display = "block";
//             commentInput.style.display = "flex";
//             toggleIcon.classList.remove("fa-square-caret-up");
//             toggleIcon.classList.add("fa-square-caret-down");
//         }
//     }
// });
