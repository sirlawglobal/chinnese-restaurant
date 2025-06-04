// Sample data for chats with some having profile images
const chats = [
  {
    id: 1,
    name: "John Doe",
    initials: "JD",
    image: "https://picsum.photos/200/200?random=1",
    tag: "Customer",
    lastMessage: "Hey, how are you doing?",
    time: "10:30 AM",
    timestamp: new Date().getTime(),
    status: "read",
    unread: 0,
    online: true,
    messages: [
      { id: 1, text: "Hey there!", time: "10:20 AM", incoming: true },
      {
        id: 2,
        text: "Hi! How are you?",
        time: "10:22 AM",
        incoming: false,
      },
      {
        id: 3,
        text: "I'm good, thanks for asking. How about you?",
        time: "10:25 AM",
        incoming: true,
      },
      {
        id: 4,
        text: "Doing well! Just working on some projects.",
        time: "10:27 AM",
        incoming: false,
      },
      {
        id: 5,
        text: "Hey, how are you doing?",
        time: "10:30 AM",
        incoming: true,
      },
    ],
  },
  {
    id: 2,
    name: "Sarah Smith",
    initials: "SS",
    image: "https://picsum.photos/200/200?random=2",
    tag: "Kitchen Admin",
    lastMessage: "The order will be ready in 15 minutes",
    time: "9:45 AM",
    timestamp: new Date().getTime() - 3600000,
    status: "delivered",
    unread: 3,
    online: false,
    messages: [
      {
        id: 1,
        text: "Hi Sarah, what's the status of my order?",
        time: "9:30 AM",
        incoming: false,
      },
      {
        id: 2,
        text: "We're preparing it now",
        time: "9:35 AM",
        incoming: true,
      },
      {
        id: 3,
        text: "Great, how long will it take?",
        time: "9:40 AM",
        incoming: false,
      },
      {
        id: 4,
        text: "The order will be ready in 15 minutes",
        time: "9:45 AM",
        incoming: true,
      },
    ],
  },
  {
    id: 3,
    name: "Tech Support",
    initials: "TS",
    tag: "Support",
    lastMessage: "Please restart your computer and try again",
    time: "Yesterday",
    timestamp: new Date().getTime() - 86400000,
    status: "sent",
    unread: 0,
    online: false,
    messages: [
      {
        id: 1,
        text: "Hello, I'm having issues with my software",
        time: "Yesterday, 4:30 PM",
        incoming: false,
      },
      {
        id: 2,
        text: "Can you describe the problem?",
        time: "Yesterday, 4:35 PM",
        incoming: true,
      },
      {
        id: 3,
        text: "It keeps crashing when I try to save files",
        time: "Yesterday, 4:40 PM",
        incoming: false,
      },
      {
        id: 4,
        text: "Please restart your computer and try again",
        time: "Yesterday, 4:45 PM",
        incoming: true,
      },
    ],
  },
  {
    id: 4,
    name: "Family Group",
    initials: "FG",
    image: "https://picsum.photos/200/200?random=3",
    tag: "Group",
    lastMessage: "Mom: Don't forget about Sunday dinner!",
    time: "Yesterday",
    timestamp: new Date().getTime() - 90000000,
    status: "read",
    unread: 0,
    online: true,
    messages: [
      {
        id: 1,
        text: "Alice: Who's coming for dinner this weekend?",
        time: "Yesterday, 2:15 PM",
        incoming: true,
      },
      {
        id: 2,
        text: "Bob: I'll be there!",
        time: "Yesterday, 2:20 PM",
        incoming: true,
      },
      {
        id: 3,
        text: "I'll be there too",
        time: "Yesterday, 2:25 PM",
        incoming: false,
      },
      {
        id: 4,
        text: "Mom: Don't forget about Sunday dinner!",
        time: "Yesterday, 2:30 PM",
        incoming: true,
      },
    ],
  },
  {
    id: 5,
    name: "David Wilson",
    initials: "DW",
    image: "https://picsum.photos/200/200?random=4",
    tag: "Colleague",
    lastMessage: "Let's meet tomorrow to discuss the project",
    time: "Monday",
    timestamp: new Date().getTime() - 172800000,
    status: "read",
    unread: 0,
    online: false,
    messages: [
      {
        id: 1,
        text: "Hi David, do you have time to discuss the project?",
        time: "Monday, 11:00 AM",
        incoming: false,
      },
      {
        id: 2,
        text: "Sure, what time works for you?",
        time: "Monday, 11:15 AM",
        incoming: true,
      },
      {
        id: 3,
        text: "How about tomorrow at 2pm?",
        time: "Monday, 11:20 AM",
        incoming: false,
      },
      {
        id: 4,
        text: "Let's meet tomorrow to discuss the project",
        time: "Monday, 11:25 AM",
        incoming: true,
      },
    ],
  },
];

// DOM elements
const chatItemsContainer = document.getElementById("chatItems");
const chatArea = document.getElementById("chatArea");
const emptyChatView = document.getElementById("emptyChatView");
const activeChatView = document.getElementById("activeChatView");
const messagesContainer = document.getElementById("messagesContainer");
const activeChatName = document.getElementById("activeChatName");
const activeChatAvatar = document.getElementById("activeChatAvatar");
const activeChatStatus = document.getElementById("activeChatStatus");
const messageInput = document.getElementById("messageInput");
const sendButton = document.getElementById("sendButton");
const closeChatButton = document.getElementById("closeChatButton");
const searchInput = document.getElementById("searchInput");
const contextMenu = document.getElementById("contextMenu");
const replyOption = document.getElementById("replyOption");
const forwardOption = document.getElementById("forwardOption");
const copyOption = document.getElementById("copyOption");
const deleteOption = document.getElementById("deleteOption");
const bgContextMenu = document.getElementById("bgContextMenu");
const selectMessagesOption = document.getElementById("selectMessagesOption");
const closeChatOption = document.getElementById("closeChatOption");

// Current active chat and context menu state
let activeChatId = null;
let lastClickedMessage = null;

// Initialize the app
function init() {
  renderChatList();
  setupEventListeners();
  setActiveChat(1);
}

// Sort chats by most recent activity
function sortChatsByRecent() {
  chats.sort((a, b) => b.timestamp - a.timestamp);
}

// Render chat list
function renderChatList(filter = "") {
  chatItemsContainer.innerHTML = "";

  const filteredChats = filter
    ? chats.filter(
        (chat) =>
          chat.name.toLowerCase().includes(filter.toLowerCase()) ||
          chat.messages.some((msg) =>
            msg.text.toLowerCase().includes(filter.toLowerCase())
          )
      )
    : chats;

  sortChatsByRecent();

  filteredChats.forEach((chat) => {
    const chatItem = document.createElement("div");
    chatItem.className = "chat-item";
    chatItem.dataset.chatId = chat.id;

    if (activeChatId === chat.id) {
      chatItem.classList.add("active");
    }

    // Set avatar background image if exists
    const avatarStyle = chat.image
      ? `style="background-image: url('${chat.image}'); color: transparent;"`
      : "";

    chatItem.innerHTML = `
                    <div class="chat-item-avatar" ${avatarStyle}>${
      chat.initials
    }</div>
                    <div class="chat-item-content">
                        <div class="chat-item-header">
                            <div>
                                <span class="chat-item-name">${chat.name}</span>
                                <span class="chat-item-tag">${chat.tag}</span>
                            </div>
                            <div class="chat-item-time">${chat.time}</div>
                        </div>
                        <div class="chat-item-message">
                            <span class="message-status ${chat.status}">
                                ${getStatusIcon(chat.status)}
                            </span>
                            <span class="chat-item-message-text">${
                              chat.lastMessage
                            }</span>
                            ${
                              chat.unread > 0
                                ? `<div class="unread-badge">${chat.unread}</div>`
                                : ""
                            }
                        </div>
                    </div>
                `;

    chatItemsContainer.appendChild(chatItem);
  });
}

// Get status icon based on message status
function getStatusIcon(status) {
  switch (status) {
    case "sent":
      return '<svg style="width: 1rem; height: 1rem";><use href="#st"></use></svg>';
    case "delivered":
      return '<svg style="width: 1rem; height: 1rem";><use href="#dt"></use></svg>';
    case "read":
      return '<svg style="color: #1684FF; width: 1rem; height: 1rem";><use href="#dt"></use></svg>';
    default:
      return "";
  }
}

// Render messages for active chat
function renderMessages(chatId) {
  const chat = chats.find((c) => c.id === chatId);
  if (!chat) return;

  messagesContainer.innerHTML = "";

  chat.messages.forEach((message) => {
    const messageElement = document.createElement("div");
    messageElement.className = `message ${
      message.incoming ? "message-incoming" : "message-outgoing"
    }`;
    messageElement.dataset.messageId = message.id;

    messageElement.innerHTML = `
                    <div>${message.text}</div>
                    <div class="message-time">
                        ${message.time}
                        ${
                          !message.incoming
                            ? `<span class="message-status">${getStatusIcon(
                                "read"
                              )}</span>`
                            : ""
                        }
                    </div>
                `;

    messagesContainer.appendChild(messageElement);
  });

  // Scroll to bottom
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Set up active chat
function setActiveChat(chatId) {
  const chat = chats.find((c) => c.id === chatId);
  if (!chat) return;

  activeChatId = chatId;

  // Update UI
  activeChatName.textContent = chat.name;

  // Set avatar background image if exists
  if (chat.image) {
    activeChatAvatar.style.backgroundImage = `url('${chat.image}')`;
    activeChatAvatar.textContent = "";
    activeChatAvatar.style.color = "transparent";
  } else {
    activeChatAvatar.style.backgroundImage = "";
    activeChatAvatar.textContent = chat.initials;
    activeChatAvatar.style.color = "white";
  }

  activeChatStatus.textContent = chat.online ? "online" : "last seen today";
  activeChatStatus.className = `chat-header-status ${
    chat.online ? "online" : ""
  }`;

  // Show active chat view
  emptyChatView.style.display = "none";
  activeChatView.style.display = "flex";

  // Render messages
  renderMessages(chatId);

  // Update chat list highlights
  document.querySelectorAll(".chat-item").forEach((item) => {
    item.classList.toggle("active", parseInt(item.dataset.chatId) === chatId);
  });

  // Mark messages as read
  if (chat.unread > 0) {
    chat.unread = 0;
    renderChatList(searchInput.value.trim());
  }

  // Update timestamp to now (most recent)
  // chat.timestamp = new Date().getTime();
  // sortChatsByRecent();
  renderChatList(searchInput.value.trim());

  // For mobile view, show chat area
  chatArea.classList.add("active");
}

// Close active chat
function closeActiveChat() {
  activeChatId = null;
  emptyChatView.style.display = "flex";
  activeChatView.style.display = "none";
  chatArea.classList.remove("active");

  // Update chat list highlights
  document.querySelectorAll(".chat-item").forEach((item) => {
    item.classList.remove("active");
  });
}

// Send a new message
function sendMessage() {
  const messageText = messageInput.value.trim();
  if (!messageText || !activeChatId) return;

  const chat = chats.find((c) => c.id === activeChatId);
  if (!chat) return;

  // Create new message
  const now = new Date();
  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  const timeString = `${hours}:${minutes}`;

  const newMessage = {
    id: chat.messages.length + 1,
    text: messageText,
    time: timeString,
    incoming: false,
  };

  // Add to chat
  chat.messages.push(newMessage);
  chat.lastMessage = messageText;
  chat.time = timeString;
  chat.status = "sent";
  chat.timestamp = new Date().getTime();

  // Update UI
  renderMessages(activeChatId);
  sortChatsByRecent();
  renderChatList(searchInput.value.trim());

  // Clear input
  messageInput.value = "";
  sendButton.classList.remove("active");

  // Simulate reply after 1-3 seconds
  if (Math.random() > 0.3) {
    // 70% chance of reply
    setTimeout(() => {
      const replies = [
        "Sure, sounds good!",
        "I'll get back to you on that.",
        "Thanks for letting me know.",
        "Can we discuss this later?",
        "I'm busy right now, talk later?",
        "Got it!",
      ];

      const replyMessage = {
        id: chat.messages.length + 1,
        text: replies[Math.floor(Math.random() * replies.length)],
        time: `${hours}:${(now.getMinutes() + 1).toString().padStart(2, "0")}`,
        incoming: true,
      };

      chat.messages.push(replyMessage);
      chat.lastMessage = replyMessage.text;
      chat.time = replyMessage.time;
      chat.status = "delivered";
      chat.timestamp = new Date().getTime();

      if (activeChatId === chat.id) {
        renderMessages(activeChatId);
      }

      sortChatsByRecent();
      renderChatList(searchInput.value.trim());
    }, 1000 + Math.random() * 2000);
  }
}

// Show message context menu
function showMessageContextMenu(e, messageElement) {
  e.preventDefault();
  lastClickedMessage = messageElement;

  // Position the context menu
  contextMenu.style.display = "block";
  contextMenu.style.left = `${e.clientX}px`;
  contextMenu.style.top = `${e.clientY}px`;

  // Hide menu when clicking elsewhere
  const hideMenu = () => {
    contextMenu.style.display = "none";
    document.removeEventListener("click", hideMenu);
  };

  setTimeout(() => {
    document.addEventListener("click", hideMenu);
  }, 100);
}

// Show background context menu
function showBackgroundContextMenu(e) {
  e.preventDefault();

  // Position the context menu
  bgContextMenu.style.display = "block";
  bgContextMenu.style.left = `${e.clientX}px`;
  bgContextMenu.style.top = `${e.clientY}px`;

  // Hide menu when clicking elsewhere
  const hideMenu = () => {
    bgContextMenu.style.display = "none";
    document.removeEventListener("click", hideMenu);
  };

  setTimeout(() => {
    document.addEventListener("click", hideMenu);
  }, 100);
}

// Set up event listeners
function setupEventListeners() {
  // Chat item clicks
  chatItemsContainer.addEventListener("click", (e) => {
    const chatItem = e.target.closest(".chat-item");
    if (chatItem) {
      const chatId = parseInt(chatItem.dataset.chatId);
      setActiveChat(chatId);
    }
  });

  // Message input events
  messageInput.addEventListener("input", () => {
    sendButton.classList.toggle("active", messageInput.value.trim().length > 0);
  });

  messageInput.addEventListener("keypress", (e) => {
    document.addEventListener("keydown", (e) => {
      if (e.shiftKey && e.key === "Enter") {
        e.preventDefault();
      }
    });
    if (e.key === "Enter" && messageInput.value.trim().length > 0) {
      sendMessage();
    }
  });

  // Send button click
  sendButton.addEventListener("click", sendMessage);

  // Close chat button (for mobile)
  closeChatButton.addEventListener("click", closeActiveChat);

  // Search input
  searchInput.addEventListener("input", () => {
    renderChatList(searchInput.value.trim());
  });

  // Message context menu
  messagesContainer.addEventListener("contextmenu", (e) => {
    const messageElement = e.target.closest(".message");
    if (messageElement) {
      showMessageContextMenu(e, messageElement);
    } else {
      showBackgroundContextMenu(e);
    }
  });

  // Context menu options
  replyOption.addEventListener("click", () => {
    if (lastClickedMessage) {
      const messageId = lastClickedMessage.dataset.messageId;
      const chat = chats.find((c) => c.id === activeChatId);
      if (chat) {
        const message = chat.messages.find((m) => m.id == messageId);
        if (message) {
          messageInput.value = `Replying to: ${message.text}`;
          messageInput.focus();
        }
      }
    }
    contextMenu.style.display = "none";
  });

  forwardOption.addEventListener("click", () => {
    alert("Forward message functionality would go here");
    contextMenu.style.display = "none";
  });

  copyOption.addEventListener("click", () => {
    if (lastClickedMessage) {
      const messageText =
        lastClickedMessage.querySelector("div:first-child").textContent;
      navigator.clipboard.writeText(messageText);
    }
    contextMenu.style.display = "none";
  });

  deleteOption.addEventListener("click", () => {
    if (lastClickedMessage && activeChatId) {
      const messageId = lastClickedMessage.dataset.messageId;
      const chat = chats.find((c) => c.id === activeChatId);
      if (chat) {
        const messageIndex = chat.messages.findIndex((m) => m.id == messageId);
        if (messageIndex > -1) {
          chat.messages.splice(messageIndex, 1);
          renderMessages(activeChatId);

          // Update last message if needed
          if (chat.messages.length > 0) {
            const lastMsg = chat.messages[chat.messages.length - 1];
            chat.lastMessage = lastMsg.text;
            chat.time = lastMsg.time;
          } else {
            chat.lastMessage = "";
            chat.time = "";
          }

          renderChatList(searchInput.value.trim());
        }
      }
    }
    contextMenu.style.display = "none";
  });

  // Background context menu options
  selectMessagesOption.addEventListener("click", () => {
    alert("Select messages functionality would go here");
    bgContextMenu.style.display = "none";
  });

  closeChatOption.addEventListener("click", () => {
    closeActiveChat();
    bgContextMenu.style.display = "none";
  });

  // Keyboard shortcuts
  document.addEventListener("keydown", (e) => {
    // Ctrl+Shift+W to close chat
    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === "w") {
      e.preventDefault();
      closeActiveChat();
    }
  });
}

// Initialize the app
init();
