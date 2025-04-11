let selectedUserId = null;
let lastMessageId = 0;
let messageInterval;

document.addEventListener("DOMContentLoaded", function () {
  loadChatList();

  const searchInput = document.getElementById("searchInput");
  searchInput.addEventListener("input", function () {
    filterChatList(this.value.trim().toLowerCase());
  });
});

function startMessagePolling() {
  clearInterval(messageInterval);
  messageInterval = setInterval(() => {
    if (selectedUserId && lastMessageId !== null) {
      fetchMessages(selectedUserId);
    }
  }, 2000);
}

function loadChatList() {
  fetch("../../2coordinator/chat/fetch_chatlist.php")
    .then((response) => response.json())
    .then((data) => {
      const chatListContainer = document.querySelector(".scrollable-cards");
      chatListContainer.innerHTML = "";

      data.forEach((user) => {
        const dotColor = user.chat_stats === "online" ? "green" : "red";
        const unreadBadge =
          user.unread_count > 0
            ? `<i class="fa-regular fa-envelope ms-1 text-danger"></i>`
            : "";

        const chatCard = document.createElement("div");
        chatCard.classList.add("chat-card");
        chatCard.setAttribute("data-user-id", user.users_id);

        chatCard.innerHTML = `
          <div class="profile-info">
            <img src="${user.image_profile}" class="profile-img">
            <div class="profile-details">
              <span class="name">${user.fullname}</span>
              <span class="hte">${user.hte_name || ""}</span>
            </div>
          </div>
          <div class="right-info">
            <div class="time">
              ${user.duration}
              <span class="red-dot" style="background:${dotColor}"></span>
            </div>
            <div class="role">${capitalize(user.role)} ${unreadBadge}</div>
          </div>
        `;

        chatCard.addEventListener("click", function () {
          const userId = this.getAttribute("data-user-id");

          if (selectedUserId !== userId) {
            selectedUserId = userId;
            lastMessageId = 0; // Optional, kung gusto mo fresh start every switch
            clearMessages();
          }

          this.querySelector(".role i")?.remove();

          document
            .querySelectorAll(".chat-card")
            .forEach((c) => c.classList.remove("active-chat"));
          this.classList.add("active-chat");

          fetch("../../2coordinator/chat/mark_as_read.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `sender_id=${selectedUserId}`,
          });

          fetchMessages(selectedUserId);
          startMessagePolling();
        });

        chatListContainer.appendChild(chatCard);
      });
    })
    .catch((error) => {
      console.error("Error fetching chat list:", error);
    });
}

function fetchMessages(userId) {
  if (!userId) return; // Skip if no selected user

  fetch(
    `../../2coordinator/chat/fetch_messages.php?with_user_id=${userId}&last_id=${lastMessageId}`
  )
    .then((response) => response.json())
    .then((data) => {
      const messageContainer = document.querySelector(".message-content");

      if (data.status === "success" && data.messages.length > 0) {
        data.messages.forEach((msg) => {
          const isOwn = msg.sender_id == data.current_user;
          const messageRow = document.createElement("div");
          messageRow.classList.add("message-row", isOwn ? "right" : "left");

          const profileImage = isOwn
            ? data.current_user_image
            : data.receiver_image;

          messageRow.innerHTML = `
            <div class="message-wrapper ${isOwn ? "right" : "left"}">
              ${
                !isOwn
                  ? `<img src="${profileImage}" class="profile-img profile-img-left">`
                  : ""
              }
              <div class="bubble-container">
                <div class="message-bubble">
                  ${msg.message}
                  ${
                    isOwn
                      ? `<i class="fa-solid fa-trash delete-msg-icon" data-msg-id="${msg.message_id}"></i>`
                      : ""
                  }
                </div>
                <div class="message-time">${msg.created_at}</div>
              </div>
              ${
                isOwn
                  ? `<img src="${profileImage}" class="profile-img profile-img-right">`
                  : ""
              }
            </div>
          `;

          messageContainer.appendChild(messageRow);
          lastMessageId = msg.message_id;
        });

        addDeleteEvents();
        enableSwipeToDelete();

        // Scroll to bottom if new message
        messageContainer.scrollTop = messageContainer.scrollHeight;

        // ✅ RELOAD CHAT LIST
        loadChatList(); // ← this line is key
      }
    })
    .catch((error) => {
      console.error("Error fetching messages:", error);
    });
}

function clearMessages() {
  document.querySelector(".message-content").innerHTML = "";
}

function deleteMessage(messageId, iconElement) {
  fetch("../../2coordinator/chat/delete_message.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `message_id=${messageId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const msgBubble = iconElement.closest(".message-row");
        if (msgBubble) {
          msgBubble.classList.add("deleted");
          setTimeout(() => msgBubble.remove(), 300);
        }
      }
    })
    .catch((error) => {
      console.error("Error deleting message:", error);
    });
}

function addDeleteEvents() {
  const deleteIcons = document.querySelectorAll(".delete-msg-icon");
  deleteIcons.forEach((icon) => {
    icon.addEventListener("click", function (e) {
      e.stopPropagation();
      const msgId = this.getAttribute("data-msg-id");
      deleteMessage(msgId, this);
    });
  });
}

function filterChatList(searchTerm) {
  const cards = document.querySelectorAll(".chat-card");
  cards.forEach((card) => {
    const name = card.querySelector(".name").textContent.toLowerCase();
    const hte = card.querySelector(".hte").textContent.toLowerCase();
    card.style.display =
      name.includes(searchTerm) || hte.includes(searchTerm) ? "flex" : "none";
  });
}

function capitalize(word) {
  return word.charAt(0).toUpperCase() + word.slice(1);
}

// ===== SEND MESSAGE =====
const messageForm = document.querySelector(".message-form");
const messageInput = messageForm.querySelector("input[type='text']");

messageForm.addEventListener("submit", handleSendMessage);
messageInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
    handleSendMessage(e);
  }
});

function handleSendMessage(e) {
  e.preventDefault();
  const message = messageInput.value.trim();
  if (message === "" || !selectedUserId) return;

  fetch("../../2coordinator/chat/send_message.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      receiver_id: selectedUserId,
      message: message,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        messageInput.value = "";
        fetchMessages(selectedUserId);
      }
    })
    .catch((error) => {
      console.error("Error sending message:", error);
    });
}

function enableSwipeToDelete() {
  const messages = document.querySelectorAll(".message-wrapper.right");

  messages.forEach((msg) => {
    let startX = 0;
    let moved = false;

    msg.addEventListener("touchstart", (e) => {
      startX = e.touches[0].clientX;
    });

    msg.addEventListener("touchmove", (e) => {
      const diff = startX - e.touches[0].clientX;
      if (diff > 30) {
        msg.classList.add("swipe-left");
        moved = true;
      }
    });

    msg.addEventListener("touchend", () => {
      if (!moved) {
        msg.classList.remove("swipe-left");
      }
    });

    const deleteIcon = msg.querySelector(".delete-msg-icon");
    if (deleteIcon) {
      deleteIcon.addEventListener("click", (e) => {
        e.stopPropagation();
        const msgId = deleteIcon.getAttribute("data-msg-id");
        deleteMessage(msgId, deleteIcon);
      });
    }
  });
}

// const attachmentInput = document.querySelector("input[type='file']");
// attachmentInput.addEventListener("change", function () {
//   const file = this.files[0];
//   if (!file) return;

//   const formData = new FormData();
//   formData.append("attachment", file);

//   fetch("../../2coordinator/chat/upload_attachment.php", {
//     method: "POST",
//     body: formData,
//   })
//     .then((res) => res.json())
//     .then((data) => {
//       if (data.status === "success") {
//         alert(`Attachment uploaded: ${data.file}`);

//       } else {
//         alert(data.message);
//       }
//     })
//     .catch((err) => {
//       console.error("Attachment Upload Error:", err);
//     });
// });
