<?php
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role'])) {
    header("Location: /chinnese-restaurant/login/");
    exit();
}



$username = $_SESSION['user']['name'] ?? '';
// var_dump($username); // Debug: Check session data 

$parts = explode(" ", $username);
$first_name = $parts[0];

$userRole = $_SESSION['user']['role'] ?? '';
$profilePicture = $_SESSION['user']['profile_picture'] ?? 'https://picsum.photos/40';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Messages</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/messages.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
  </head>
  <body class="flex">
    <main>
      <div class="content flex">
        <div class="inner-content flex">
          <div class="chat-list">
            <div class="chat-list-header">
              <div class="search-container">
                <div class="search-box">
                  <svg class="icon" style="margin-top: 0.1rem">
                    <use href="#search"></use>
                  </svg>
                  <input
                    type="text"
                    placeholder="Search messages, name etc"
                    id="searchInput"
                  />
                </div>
              </div>
              <div class="chat-list__header-actions">
                <button class="chat-list__header-actions__button">
                  <svg class="icon" title="Filter chats">
                    <use href="#fader"></use>
                  </svg>
                </button>
                <button class="chat-list__header-actions__button">
                  <svg class="icon" title="Add new chat">
                    <use href="#add"></use>
                  </svg>
                </button>
              </div>
            </div>
            <div class="chat-items" id="chatItems">
              <!-- Chat items will be populated by JavaScript -->
            </div>
          </div>

          <div class="chat-area" id="chatArea">
            <div class="chat-area-empty" id="emptyChatView">
              <h2>Your Messages</h2>
              <p>Open a chat to view</p>
            </div>

            <div class="chat-view" id="activeChatView" style="display: none">
              <div class="chat-header">
                <div class="chat-header-info">
                  <div class="chat-header-avatar" id="activeChatAvatar">JD</div>
                  <div class="chat-header-text">
                    <div class="chat-header-name" id="activeChatName">
                      John Doe
                    </div>
                    <div class="chat-header-status" id="activeChatStatus">
                      online
                    </div>
                  </div>
                </div>
                <div class="chat-header-actions">
                  <button class="chat-header-actions_button">
                    <svg class="icon" title="Voice call">
                      <use href="#phone"></use>
                    </svg>
                  </button>
                  <button class="chat-header-actions_button">
                    <svg class="icon" title="Video call">
                      <use href="#video"></use>
                    </svg>
                  </button>
                  <button class="chat-header-actions_button">
                    <svg class="icon" title="Sidebar Menu">
                      <use href="#sidebar-menu"></use>
                    </svg>
                  </button>
                  <svg class="icon" title="Menu">
                    <use href="#dots"></use>
                  </svg>
                </div>
              </div>

              <div class="messages-container" id="messagesContainer">
                <!-- Messages will be populated by JavaScript -->
              </div>

              <div class="message-input-container">
                <svg class="icon" title="Emoji"><use href="#emoji"></use></svg>
                <div class="message-input-box">
                  <input
                    type="text"
                    class="message-input"
                    placeholder="Type a message"
                    id="messageInput"
                  />
                </div>
                <svg class="icon" title="Attach">
                  <use href="#attach"></use>
                </svg>
                <button
                  class="send-button flex align-center justify-center"
                  title="Send"
                  id="sendButton"
                >
                  Send
                  <svg class="icon">
                    <use href="#send-button"></use>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Message context menu -->
          <div class="context-menu" id="contextMenu">
            <div class="context-menu-item" id="replyOption">
              <i class="fas fa-reply"></i> Reply
            </div>
            <div class="context-menu-item" id="forwardOption">
              <i class="fas fa-share"></i> Forward
            </div>
            <div class="context-menu-item" id="copyOption">
              <i class="fas fa-copy"></i> Copy
            </div>
            <div class="context-menu-item" id="deleteOption">
              <i class="fas fa-trash"></i> Delete
            </div>
          </div>

          <!-- Background context menu -->
          <div class="bg-context-menu" id="bgContextMenu">
            <div class="context-menu-item" id="selectMessagesOption">
              <i class="fas fa-check-square"></i> Select Messages
            </div>
            <div class="context-menu-item" id="closeChatOption">
              <i class="fas fa-times"></i> Close Chat
            </div>
          </div>

          <button class="close-chat" id="closeChatButton">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </main>
       <script>
// Pass PHP variables to JavaScript
const username = '<?php echo addslashes($first_name); ?>';
const userRole = '<?php echo addslashes($userRole); ?>';
const profilePicture = '<?php echo addslashes($profilePicture); ?>';
</script>
    <script src="../scripts/components.js"></script>
    <script src="../scripts/messages.js"></script>
  </body>
</html>
