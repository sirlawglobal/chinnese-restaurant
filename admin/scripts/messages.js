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

let activeChatId = null;
let lastClickedMessage = null;

function init() {
    fetchChats();
    setupEventListeners();
}

async function fetchChats(filter = "") {
    try {
        const response = await fetch(`../messages/get_chats.php${filter ? `?search=${encodeURIComponent(filter)}` : ""}`);
        const result = await response.json();
        if (result.data_type === "chats") {
            renderChatList(result.data);
        } else {
            console.error(result.message);
        }
    } catch (error) {
        console.error("Error fetching chats:", error);
    }
}

function renderChatList(chats) {
    chatItemsContainer.innerHTML = "";
    chats.forEach((chat) => {
        const chatItem = document.createElement("div");
        chatItem.className = "chat-item" + (activeChatId == chat.id ? " active" : "");
        chatItem.dataset.chatId = chat.id;
        chatItem.innerHTML = `
            <div class="chat-item-avatar">${chat.initials}</div>
            <div class="chat-item-content">
                <div class="chat-item-header">
                    <div>
                        <span class="chat-item-name">${chat.name}</span>
                        <span class="chat-item-tag">Customer</span>
                    </div>
                    <div class="chat-item-time">${chat.time}</div>
                </div>
                <div class="chat-item-message">
                    <span class="message-status ${chat.status}">
                        ${getStatusIcon(chat.status)}
                    </span>
                    <span class="chat-item-message-text">${chat.lastMessage}</span>
                    ${chat.unread > 0 ? `<div class="unread-badge">${chat.unread}</div>` : ""}
                </div>
            </div>
        `;
        chatItem.addEventListener("click", () => setActiveChat(chat.id));
        chatItemsContainer.appendChild(chatItem);
    });
}

function getStatusIcon(status) {
    switch (status) {
        case "sent":
        case "pending":
            return '<svg style="width: 1rem; height: 1rem"><use href="#st"></use></svg>';
        case "replied":
            return '<svg style="width: 1rem; height: 1rem"><use href="#dt"></use></svg>';
        case "failed":
        case "closed":
            return '<svg style="color: #1684FF; width: 1rem; height: 1rem"><use href="#dt"></use></svg>';
        default:
            return "";
    }
}

async function renderMessages(chatId) {
    console.log('Rendering messages for chatId:', chatId);
    try {
        const response = await fetch(`../messages/get_messages.php?chatId=${chatId}`);
        console.log('API response status:', response.status);
        const result = await response.json();
        console.log('API response data:', result);
        if (result.data_type === "chat") {
            messagesContainer.innerHTML = "";
            // Sort messages by full timestamp
            result.data.messages.sort((a, b) => new Date(a.time) - new Date(b.time));
            let lastDate = null;
            result.data.messages.forEach((message) => {
                console.log('Processing message:', message.text, 'Time:', message.time, 'is_admin:', message.is_admin);
                const isAdmin = message.is_admin === "1";
                const messageDate = new Date(message.time).toDateString();
                const currentDate = new Date().toDateString();
                const yesterdayDate = new Date(new Date().setDate(new Date().getDate() - 1)).toDateString();

                // Add date header only if it's a new date
                if (lastDate !== messageDate) {
                    const dateHeader = document.createElement("div");
                    dateHeader.className = "chat-date-header";
                    dateHeader.textContent = messageDate === currentDate ? "Today" :
                        messageDate === yesterdayDate ? "Yesterday" :
                        new Date(message.time).toLocaleDateString('en-US', { weekday: 'long' });
                    messagesContainer.appendChild(dateHeader);
                    lastDate = messageDate;
                }

                const messageElement = document.createElement("div");
                messageElement.className = `message ${isAdmin ? "message-outgoing" : "message-incoming"}`;
                messageElement.dataset.messageId = message.id;
                // Parse time and handle invalid date
                let displayTime = "Invalid Date";
                const dateObj = new Date(message.time);
                if (!isNaN(dateObj.getTime())) {
                    displayTime = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }).toLowerCase();
                } else {
                    console.error('Invalid date for message:', message.time);
                }
                messageElement.innerHTML = `
                    <div class="message-content">${message.text}</div>
                    <div class="message-time">${displayTime}</div>
                `;
                if (!isAdmin) {
                    const avatar = document.createElement("div");
                    avatar.className = "chat-item-avatar";
                    avatar.textContent = message.initials || "U";
                    messagesContainer.appendChild(avatar);
                }
                messagesContainer.appendChild(messageElement);
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else {
            console.error('Invalid data_type:', result.message);
        }
    } catch (error) {
        console.error('Error in renderMessages:', error);
    }
}

async function setActiveChat(chatId) {
    try {
        const response = await fetch(`../messages/get_chat.php?chatId=${chatId}`);
        const result = await response.json();
        if (result.data_type === "chat") {
            const chat = result.data;
            activeChatId = chatId;
            activeChatName.textContent = chat.name;
            activeChatAvatar.textContent = chat.initials;
            activeChatAvatar.style.backgroundImage = "";
            activeChatAvatar.style.color = "white";
            activeChatStatus.textContent = chat.status === "pending" ? "Pending" : "Replied";
            activeChatStatus.className = `chat-header-status ${chat.status === "pending" ? "" : "online"}`;
            emptyChatView.style.display = "none";
            activeChatView.style.display = "flex";
            renderMessages(chatId);
            document.querySelectorAll(".chat-item").forEach((item) => {
                item.classList.toggle("active", parseInt(item.dataset.chatId) === chatId);
            });
            if (chat.unread > 0) {
                await fetch("../messages/mark_read.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ chatId })
                });
                fetchChats(searchInput.value.trim()); // Refresh to remove badge
            }
            chatArea.classList.add("active");
        } else {
            console.error(result.message);
        }
    } catch (error) {
        console.error("Error setting active chat:", error);
    }
}

function closeActiveChat() {
    activeChatId = null;
    emptyChatView.style.display = "flex";
    activeChatView.style.display = "none";
    chatArea.classList.remove("active");
    document.querySelectorAll(".chat-item").forEach((item) => item.classList.remove("active"));
}

async function sendMessage() {
    const messageText = messageInput.value.trim();
    if (!messageText || !activeChatId) return;

    try {
        const response = await fetch("../messages/send_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ chatId: activeChatId, message: messageText })
        });
        const result = await response.json();
        if (result.data_type === "message" && result.message.includes("sent")) {
            messageInput.value = "";
            sendButton.classList.remove("active");
            renderMessages(activeChatId);
            fetchChats(searchInput.value.trim()); // Refresh to remove badge
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        console.error("Error sending message:", error);
    }
}

function showMessageContextMenu(e, messageElement) {
    e.preventDefault();
    lastClickedMessage = messageElement;
    contextMenu.style.display = "block";
    contextMenu.style.left = `${e.clientX}px`;
    contextMenu.style.top = `${e.clientY}px`;
    setTimeout(() => {
        document.addEventListener("click", () => {
            contextMenu.style.display = "none";
        }, { once: true });
    }, 100);
}

function showBackgroundContextMenu(e) {
    e.preventDefault();
    bgContextMenu.style.display = "block";
    bgContextMenu.style.left = `${e.clientX}px`;
    bgContextMenu.style.top = `${e.clientY}px`;
    setTimeout(() => {
        document.addEventListener("click", () => {
            bgContextMenu.style.display = "none";
        }, { once: true });
    }, 100);
}

function setupEventListeners() {
    chatItemsContainer.addEventListener("click", (e) => {
        const chatItem = e.target.closest(".chat-item");
        if (chatItem) {
            setActiveChat(parseInt(chatItem.dataset.chatId));
        }
    });
    messageInput.addEventListener("input", () => {
        sendButton.classList.toggle("active", messageInput.value.trim().length > 0);
    });
    messageInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter" && !e.shiftKey && messageInput.value.trim().length > 0) {
            sendMessage();
        }
    });
    sendButton.addEventListener("click", sendMessage);
    closeChatButton.addEventListener("click", closeActiveChat);
    searchInput.addEventListener("input", () => {
        fetchChats(searchInput.value.trim());
    });
    messagesContainer.addEventListener("contextmenu", (e) => {
        const messageElement = e.target.closest(".message");
        if (messageElement) {
            showMessageContextMenu(e, messageElement);
        } else {
            showBackgroundContextMenu(e);
        }
    });
    replyOption.addEventListener("click", () => {
        if (lastClickedMessage) {
            const messageId = lastClickedMessage.dataset.messageId;
            messageInput.value = `Replying to message ID ${messageId}: `;
            messageInput.focus();
        }
        contextMenu.style.display = "none";
    });
    forwardOption.addEventListener("click", () => {
        alert("Forward message functionality would go here");
        contextMenu.style.display = "none";
    });
    copyOption.addEventListener("click", () => {
        if (lastClickedMessage) {
            const messageText = lastClickedMessage.querySelector("div:first-child").textContent;
            navigator.clipboard.writeText(messageText);
        }
        contextMenu.style.display = "none";
    });
    deleteOption.addEventListener("click", async () => {
        if (lastClickedMessage && activeChatId) {
            const messageId = lastClickedMessage.dataset.messageId;
            try {
                const response = await fetch("../messages/delete_message.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ chatId: activeChatId, messageId })
                });
                const result = await response.json();
                if (result.data_type === "message" && result.message.includes("deleted")) {
                    renderMessages(activeChatId);
                    fetchChats(searchInput.value.trim());
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error("Error deleting message:", error);
            }
        }
        contextMenu.style.display = "none";
    });
    selectMessagesOption.addEventListener("click", () => {
        alert("Select messages functionality would go here");
        bgContextMenu.style.display = "none";
    });
    closeChatOption.addEventListener("click", () => {
        closeActiveChat();
        bgContextMenu.style.display = "none";
    });
    document.addEventListener("keydown", (e) => {
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === "w") {
            e.preventDefault();
            closeActiveChat();
        }
    });
}

init();