<?php
session_start();
?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>AI Healthcare Assistant | Dashboard</title>

  <meta name="description" content="Get instant healthcare advice from our AI assistant" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  
  <style>
    .chat-container {
      display: flex;
      flex-direction: column;
      height: calc(100vh - 200px);
      max-height: 700px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .chat-header {
      background: linear-gradient(135deg, #2a7fba, #1a5f8b);
      color: white;
      padding: 15px 20px;
      text-align: center;
    }
    
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background-color: #f8fafc;
    }
    
    .message {
      margin-bottom: 15px;
      max-width: 80%;
      padding: 12px 16px;
      border-radius: 18px;
      line-height: 1.5;
      position: relative;
    }
    
    .user-message {
      background-color: #e3f2fd;
      color: #0d47a1;
      margin-left: auto;
      border-bottom-right-radius: 4px;
    }
    
    .ai-message {
      background-color: white;
      color: #253d4e;
      margin-right: auto;
      border-bottom-left-radius: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .chat-input-container {
      display: flex;
      padding: 15px;
      background-color: white;
      border-top: 1px solid #e5e5e5;
    }
    
    .chat-input {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid #e5e5e5;
      border-radius: 25px;
      outline: none;
      transition: border-color 0.3s;
    }
    
    .chat-input:focus {
      border-color: #2a7fba;
    }
    
    .send-button {
      background-color: #2a7fba;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 0 20px;
      margin-left: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .send-button:hover {
      background-color: #1a5f8b;
    }
    
    .typing-indicator {
      display: flex;
      padding: 10px 15px;
      background-color: white;
      border-radius: 18px;
      margin-bottom: 15px;
      width: fit-content;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .typing-dot {
      width: 8px;
      height: 8px;
      background-color: #7e7e7e;
      border-radius: 50%;
      margin: 0 3px;
      animation: typingAnimation 1.4s infinite ease-in-out;
    }
    
    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typingAnimation {
      0%, 60%, 100% { transform: translateY(0); }
      30% { transform: translateY(-5px); }
    }
    
    .message-time {
      font-size: 11px;
      color: #7e7e7e;
      margin-top: 5px;
      text-align: right;
    }
    
    .disclaimer {
      font-size: 12px;
      color: #7e7e7e;
      text-align: center;
      padding: 10px;
      background-color: #f8fafc;
      border-top: 1px solid #e5e5e5;
    }
    
    .copy-button {
      background: none;
      border: none;
      color: #2a7fba;
      cursor: pointer;
      font-size: 12px;
      margin-left: 10px;
    }
    
    .copy-button:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php
    
      if (!isset($_SESSION['role'])) {
          // Not logged in, redirect or show nothing
          header("Location: ../html/index.php");
          exit();
      }

      // Show sidebar based on role
      switch ($_SESSION['role']) {
          case 'admin':
              include("../lib/sidebar.php");
              break;
          case 'doctor':
              include("../lib/docSide.php");
              break;
          case 'patient':
              include("../lib/pSidebar.php");
              break;
          default:
              // Unknown role, redirect or show nothing
              header("Location: ../html/index.php");
              exit();
      }
      ?>
     
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-md-12">
                <div class="card mb-4">
                  <div class="chat-container">
                    <div class="chat-header">
                      <h4 class="mb-0"><i class="bx bx-bot me-2"></i> Healthcare AI Assistant</h4>
                      <p class="mb-0 small">Ask me anything about your health</p>
                    </div>
                    
                    <div class="chat-messages" id="chat-messages">
                      <!-- Messages will appear here -->
                      <div class="message ai-message">
                        <p>Hello! I'm your AI healthcare assistant. How can I help you today? Please describe any symptoms, medications, or health concerns you have.</p>
                        <div class="message-time">Just now</div>
                      </div>
                    </div>
                    
                    <div class="disclaimer">
                      <p><strong>Note:</strong> This AI provides general health information only. It's not a substitute for professional medical advice.</p>
                    </div>
                    
                    <div class="chat-input-container">
                      <input type="text" class="chat-input" id="user-input" placeholder="Type your health question here..." autocomplete="off">
                      <button class="send-button" id="send-button">
                        <i class="bx bx-send"></i> Send
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl">
              <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  &#169;
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , made with ❤️ by
                  <a href="https://themeselection.com" target="_blank" class="footer-link">ThemeSelection</a>
                </div>
              </div>
            </div>
          </footer>
          <!-- / Footer -->
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/js/main.js"></script>

  <script>
    let conversationHistory = [
      {
        role: "assistant",
        content: "Hello! I'm your AI healthcare assistant. How can I help you today? Please describe any symptoms, medications, or health concerns you have."
      }
    ];

    $(document).ready(function() {
      const chatMessages = $('#chat-messages');
      const userInput = $('#user-input');
      const sendButton = $('#send-button');
      
      // Auto-scroll to bottom of chat
      function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
      }
      
      // Add typing indicator
      function showTypingIndicator() {
        const typingHtml = `
          <div class="typing-indicator" id="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        `;
        chatMessages.append(typingHtml);
        scrollToBottom();
      }
      
      // Remove typing indicator
      function hideTypingIndicator() {
        $('#typing-indicator').remove();
      }
      
      // Format current time
      function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      }
      
      // Add message to chat
      function addMessage(role, text) {
        const messageClass = role === 'user' ? 'user-message' : 'ai-message';
        const time = getCurrentTime();
        
        const messageHtml = `
          <div class="message ${messageClass}">
            <p>${text}</p>
            <div class="message-time">${time}</div>
          </div>
        `;
        
        chatMessages.append(messageHtml);
        scrollToBottom();
      }
      
      // Summarize response to ~200 words
      function summarizeResponse(text) {
        const words = text.split(' ');
        if (words.length <= 200) return text;
        
        // Find a good cutoff point near 200 words
        let cutoff = 200;
        while (cutoff > 0 && !/[.!?]\s*$/.test(words.slice(0, cutoff).join(' '))) {
          cutoff--;
        }
        
        if (cutoff === 0) cutoff = 200; // If no sentence end found, just cut at 200
        
        return words.slice(0, cutoff).join(' ') + '... [response shortened]';
      }
      
      // Send message to OpenAI API
      async function sendToOpenAI(message) {
        showTypingIndicator();
        
        // Add user message to conversation history
        conversationHistory.push({
          role: "user",
          content: message
        });
        
        try {
          const response = await fetch('backchatbot.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message })
          });
          
          const data = await response.json();
          
          if (data.reply) {
            const fullResponse = data.reply;
            const summarizedResponse = summarizeResponse(fullResponse);
            
            // Add AI response to conversation history
            conversationHistory.push({
              role: "assistant",
              content: summarizedResponse
            });
            
            hideTypingIndicator();
            addMessage('ai', summarizedResponse);
            
            // Add copy button to the latest AI message
            const messages = $('.message.ai-message');
            const lastMessage = messages.last();
            lastMessage.append('<button class="copy-button" onclick="copyToClipboard(this)"><i class="bx bx-copy"></i> Copy</button>');
          } else {
            throw new Error(data.error || 'Invalid response from API');
          }
        } catch (error) {
          hideTypingIndicator();
          addMessage('ai', 'Sorry, I encountered an error. Please try again later.');
          console.error('Error calling OpenAI API:', error);
        }
      }
      
      // Handle send button click
      sendButton.click(function() {
        const message = userInput.val().trim();
        if (message) {
          addMessage('user', message);
          userInput.val('');
          sendToOpenAI(message);
        }
      });
      
      // Handle Enter key press
      userInput.keypress(function(e) {
        if (e.which === 13) {
          sendButton.click();
        }
      });
    });
    
    // Copy to clipboard function
    function copyToClipboard(button) {
      const messageText = $(button).siblings('p').text();
      navigator.clipboard.writeText(messageText).then(() => {
        $(button).html('<i class="bx bx-check"></i> Copied!');
        setTimeout(() => {
          $(button).html('<i class="bx bx-copy"></i> Copy');
        }, 2000);
      });
    }
  </script>
</body>
</html>

<?php
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';