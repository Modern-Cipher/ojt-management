document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const grid = document.querySelector(".dashboard-grid");
  
    // Create No Result Message
    const noResult = document.createElement("p");
    noResult.textContent = "No matching requirements found.";
    noResult.className = "text-center text-muted mt-3";
    noResult.style.display = "none";
    grid.parentElement.appendChild(noResult);
  
    // Create Clear Button
    const clearBtn = document.createElement("button");
    clearBtn.innerHTML = `<i class="fa-solid fa-xmark"></i>`;
    clearBtn.className = "btn btn-sm btn-outline-secondary ms-2";
    clearBtn.style.display = "none";
    searchInput.parentElement.appendChild(clearBtn);
  
    let debounceTimer;
  
    searchInput.addEventListener("keyup", function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        const query = searchInput.value.toLowerCase().trim();
        const cards = document.querySelectorAll(".dashboard-card");
        let matchCount = 0;
  
        cards.forEach((card) => {
          const title = card.querySelector("strong")?.textContent.toLowerCase() || "";
          const status = card.querySelector(".badge")?.textContent.toLowerCase() || "";
          const match = title.includes(query) || status.includes(query);
  
          card.style.display = match ? "block" : "none";
  
          // Optional highlight
          const header = card.querySelector("strong");
          if (header) {
            if (query && title.includes(query)) {
              const regex = new RegExp(`(${query})`, "gi");
              header.innerHTML = header.textContent.replace(regex, `<mark>$1</mark>`);
            } else {
              header.innerHTML = header.textContent;
            }
          }
          if (match) matchCount++;
        });
  
        noResult.style.display = matchCount === 0 ? "block" : "none";
        clearBtn.style.display = query ? "inline-block" : "none";
      }, 250); // Debounce 250ms
    });
  
    clearBtn.addEventListener("click", () => {
      searchInput.value = "";
      const cards = document.querySelectorAll(".dashboard-card");
      cards.forEach((card) => {
        card.style.display = "block";
        const header = card.querySelector("strong");
        if (header) header.innerHTML = header.textContent;
      });
      noResult.style.display = "none";
      clearBtn.style.display = "none";
    });
  });
  