let selectedUserId = null;
let lastMessageId = 0;
let messageInterval;
let chatListInterval;
let currentSearchTerm = ""; // Store the current search term

document.addEventListener("DOMContentLoaded", function () {
  loadChatList();

  const searchInput = document.getElementById("searchInput");
  searchInput.addEventListener("input", function () {
    currentSearchTerm = this.value.trim().toLowerCase();
    filterChatList(currentSearchTerm);
  });

  const attachmentInput = document.querySelector("input[type='file']");
  attachmentInput.addEventListener("change", handleFileUpload);

  // Handle image click for modal
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("chat-attachment-img")) {
      const imageUrl = e.target.src;
      const previewImage = document.getElementById("previewImage");
      previewImage.src = imageUrl;
      const modal = new bootstrap.Modal(
        document.getElementById("imagePreviewModal")
      );
      modal.show();
    }
  });

  // Start polling for chat list updates every 10 seconds
  startChatListPolling();
});

function startMessagePolling() {
  clearInterval(messageInterval);
  messageInterval = setInterval(() => {
    if (selectedUserId && lastMessageId !== null) {
      fetchMessages(selectedUserId);
    }
  }, 2000);
}

function startChatListPolling() {
  clearInterval(chatListInterval);
  chatListInterval = setInterval(() => {
    loadChatList();
  }, 10000); // Poll every 10 seconds
}

function loadChatList() {
  fetch("../../2coordinator/chat/fetch_chatlist.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("Fetched chat list:", data.map(u => ({
        id: u.users_id,
        name: u.fullname,
        unread: u.unread_count,
        is_online: u.is_online,
        duration: u.duration
      })));
      const chatListContainer = document.querySelector(".scrollable-cards");
      const existingCards = new Map(
        Array.from(chatListContainer.querySelectorAll(".chat-card")).map((card) => [
          card.getAttribute("data-user-id"),
          card
        ])
      );

      // Clear container to enforce new order
      chatListContainer.innerHTML = "";

      // Track which users are still present
      const updatedUserIds = new Set();

      data.forEach((user) => {
        // Apply search filter
        const name = user.fullname.toLowerCase();
        const hte = (user.hte_name || "").toLowerCase();
        if (currentSearchTerm && !name.includes(currentSearchTerm) && !hte.includes(currentSearchTerm)) {
          return; // Skip users that don't match the search term
        }

        updatedUserIds.add(user.users_id.toString());

        const dotColor = user.chat_stats === "online" ? "green" : "red";
        const newMessageLabel =
          user.unread_count > 0
            ? `<span class="new-message-label"><i class="fas fa-comment"></i> ${user.unread_count}</span>`
            : "";
        const roleDisplay = user.unread_count > 0 ? "" : `<div class="role">${capitalize(user.role)}</div>`;

        // Create or update card
        let chatCard = existingCards.get(user.users_id.toString());
        if (!chatCard) {
          chatCard = document.createElement("div");
          chatCard.classList.add("chat-card");
          chatCard.setAttribute("data-user-id", user.users_id);

          chatCard.innerHTML = `
            <div class="profile-info">
              <img src="${
                user.image_profile
              }" class="profile-img" onerror="this.src='../../upload_profile/siplogo.png';">
              <div class="profile-details">
                <span class="name">${user.fullname}</span>
                <span class="hte">${user.hte_name || ""}</span>
              </div>
            </div>
            <div class="right-info">
              <div class="time">
                ${user.is_online ? "" : user.duration || "No activity"}
                <span class="status-dot" style="background:${dotColor}"></span>
              </div>
              ${newMessageLabel}
              ${roleDisplay}
            </div>
          `;

          chatCard.addEventListener("click", function () {
            const userId = this.getAttribute("data-user-id");

            if (selectedUserId !== userId) {
              selectedUserId = userId;
              lastMessageId = 0;
              clearMessages();
            }

            // Clear new message label
            document.querySelectorAll(".new-message-label").forEach((label) => {
              if (
                label.closest(".chat-card").getAttribute("data-user-id") ===
                userId
              ) {
                label.remove();
              }
            });
            document
              .querySelectorAll(".chat-card")
              .forEach((c) => c.classList.remove("active-chat"));
            this.classList.add("active-chat");

            fetch("../../2coordinator/chat/mark_as_read.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `sender_id=${selectedUserId}`,
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.status === "success") {
                  loadChatList(); // Refresh chat list
                } else {
                  console.error("Mark as read failed:", data.message);
                }
              })
              .catch((error) => {
                console.error("Mark as read error:", error);
              });

            fetchMessages(selectedUserId);
            startMessagePolling();
          });
        } else {
          // Update existing card
          chatCard.querySelector(".time").innerHTML = `
            ${user.is_online ? "" : user.duration || "No activity"}
            <span class="status-dot" style="background:${dotColor}"></span>
          `;
          const rightInfo = chatCard.querySelector(".right-info");
          rightInfo.innerHTML = `
            <div class="time">
              ${user.is_online ? "" : user.duration || "No activity"}
              <span class="status-dot" style="background:${dotColor}"></span>
            </div>
            ${newMessageLabel}
            ${roleDisplay}
          `;
          // Move card to top to reflect new order
          chatListContainer.insertBefore(chatCard, chatListContainer.firstChild);
        }

        // Preserve or apply active-chat class
        if (user.users_id.toString() === selectedUserId) {
          chatCard.classList.add("active-chat");
        } else {
          chatCard.classList.remove("active-chat");
        }

        chatListContainer.appendChild(chatCard);
      });

      // Remove cards for users no longer in the list
      existingCards.forEach((card, userId) => {
        if (!updatedUserIds.has(userId)) {
          card.remove();
        }
      });

      // Apply search filter to ensure visibility
      filterChatList(currentSearchTerm);
    })
    .catch((error) => {
      console.error("Error fetching chat list:", error);
      showToast("Failed to load chat list", true);
    });
}

function fetchMessages(userId) {
  if (!userId) return;

  fetch(
    `../../2coordinator/chat/fetch_messages.php?with_user_id=${userId}&last_id=${lastMessageId}`
  )
    .then((response) => response.json())
    .then((data) => {
      console.log("Fetch messages response:", data);
      const messageContainer = document.querySelector(".message-content");

      if (data.status === "success") {
        if (data.messages.length > 0) {
          data.messages.forEach((msg) => {
            const isOwn = msg.sender_id == data.current_user;
            const messageRow = document.createElement("div");
            messageRow.classList.add("message-row", isOwn ? "right" : "left");
            messageRow.setAttribute("data-message-id", msg.message_id);

            const profileImage = isOwn
              ? data.current_user_image
              : data.receiver_image;

            let messageContent = msg.message;
            let attachmentHtml = "";
            if (msg.attachment) {
              const fileType = msg.attachment.file_type;
              const fileUrl = `../../2coordinator/chat/serve_file.php?path=${encodeURIComponent(
                msg.attachment.file_path
              )}`;
              if (fileType.startsWith("image/")) {
                attachmentHtml = `<img src="${fileUrl}" alt="${msg.attachment.original_name}" class="chat-attachment-img" style="cursor: pointer;">`;
              } else {
                attachmentHtml = `<a href="${fileUrl}" target="_blank" class="attachment-link"><i class="fa-solid fa-file"></i> View</a>`;
              }
            }

            messageRow.innerHTML = `
              <div class="message-wrapper ${
                isOwn ? "right" : "left"
              }">
                ${
                  !isOwn
                    ? `<img src="${profileImage}" class="profile-img" onerror="this.src='../../upload_profile/siplogo.png';">`
                    : ""
                }
                <div class="bubble-container">
                  <div class="message-bubble">
                    ${messageContent}
                    ${attachmentHtml}
                    ${
                      isOwn
                        ? `<i class="fa-solid fa-trash delete-msg-icon" data-msg-id="${msg.message_id}"></i>`
                        : ""
                    }
                  </div>
                  <div class="message-time">${
                    msg.created_at
                  }</div>
                </div>
                ${
                  isOwn
                    ? `<img src="${profileImage}" class="profile-img" onerror="this.src='../../upload_profile/siplogo.png';">`
                    : ""
                }
              </div>
            `;

            messageContainer.appendChild(messageRow);
            lastMessageId = Math.max(lastMessageId, msg.message_id);
          });

          addDeleteEvents();
          enableSwipeToDelete();
          // Scroll to the latest message
          setTimeout(() => {
            const lastMessage = messageContainer.lastElementChild;
            if (lastMessage) {
              lastMessage.scrollIntoView({ behavior: "auto", block: "end" });
              console.log("Scrolled to last message: messageId =", lastMessage.getAttribute("data-message-id"));
            }
            // Fallback scroll to bottom
            console.log("Scrolling to bottom: scrollTop =", messageContainer.scrollTop, "scrollHeight =", messageContainer.scrollHeight);
            messageContainer.scrollTop = messageContainer.scrollHeight;
          }, 100);
          console.log("Loaded", data.messages.length, "messages for user", userId);
        }
        loadChatList(); // Sync chat list
      } else {
        showToast(data.message || "Failed to load messages", true);
      }
    })
    .catch((error) => {
      console.error("Error fetching messages:", error);
      showToast("Network error loading messages", true);
    });
}

function clearMessages() {
  document.querySelector(".message-content").innerHTML = "";
}

function deleteMessage(messageId, iconElement) {
  const messageRow = document.querySelector(
    `.message-row[data-message-id="${messageId}"]`
  );
  if (!messageRow) {
    console.warn(`Message row with ID ${messageId} not found in UI`);
    return;
  }

  fetch("../../2coordinator/chat/delete_message.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `message_id=${messageId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Delete message response:", data);
      if (data.status === "success") {
        messageRow.classList.add("deleted");
        setTimeout(() => {
          messageRow.remove();
          loadChatList();
          fetchMessages(selectedUserId);
        }, 300);
        showToast(data.message || "Message deleted successfully", false);
      } else {
        showToast(data.message || "Failed to delete message", true);
      }
    })
    .catch((error) => {
      console.error("Error deleting message:", error);
      showToast("Network error deleting message", true);
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
  if (message === "" || !selectedUserId) {
    showToast("Please select a user and enter a message", true);
    return;
  }

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
      console.log("Send message response:", data);
      if (data.status === "success") {
        messageInput.value = "";
        fetchMessages(selectedUserId);
        showToast("Message sent successfully", false);
      } else {
        showToast(data.message || "Failed to send message", true);
      }
    })
    .catch((error) => {
      console.error("Error sending message:", error);
      showToast("Network error sending message", true);
    });
}

function handleFileUpload() {
  const file = this.files[0];
  if (!file || !selectedUserId) {
    showToast("Please select a user and a file", true);
    return;
  }

  const formData = new FormData();
  formData.append("attachment", file);
  formData.append("receiver_id", selectedUserId);

  fetch("../../2coordinator/chat/upload_attachment.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("Upload response:", data);
      if (data.status === "success") {
        showToast(`Attachment sent: ${file.name}`);
        fetchMessages(selectedUserId);
      } else {
        showToast(data.message || "Failed to upload attachment", true);
      }
    })
    .catch((err) => {
      console.error("Attachment Upload Error:", err);
      showToast("Network error uploading attachment", true);
    });

  this.value = "";
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