// Connect to WebSocket server with user ID
let socket;
const userId = $(".user_type").val() === 'patient' 
    ? $(".patient_id").val() 
    : $(".provider_id").val();

function connectWebSocket() {
    socket = new WebSocket(`ws://localhost:3000/?userId=${userId}`);

    socket.onopen = function (e) {
        console.log('Connected to WebSocket server');
    };

    socket.onmessage = function (event) {
        try {
            const data = JSON.parse(event.data);
            console.log('Message from server:', data);

            switch (data.type) {
                case 'message':
                case 'image':
                    handleIncomingMessage(data);
                    break;
                
                case 'typing':
                    handleTypingIndicator(data);
                    break;
            }
        } catch (error) {
            console.error('Error parsing WebSocket message:', error);
        }
    };

    socket.onclose = function (event) {
        if (event.wasClean) {
            console.log(`Connection closed cleanly, code=${event.code}, reason=${event.reason}`);
        } else {
            console.log('Connection died');
            // Attempt to reconnect after a delay
            setTimeout(connectWebSocket, 5000);
        }
    };

    socket.onerror = function (error) {
        console.log(`WebSocket error: ${error.message}`);
    };
}

connectWebSocket();

function handleIncomingMessage(data) {
    // Only process if the message is for the current chat
    const currentChatId = $(".send_text_to").val();
    if (currentChatId && currentChatId === data.senderId) {
        // Append message to chat
        appendMessageToChat(data);
    }
    
    // Update chat list regardless of current chat
    updateChatListItem(data);
}

function appendMessageToChat(data) {
    if ($(".all_chats").hasClass('hidden')) return;

    const now = new Date(data.timestamp);
    const formattedTime = dateSystem(now);
    
    let contentHtml = '';
    let seenUnseenIcon = '<i class="fas fa-check-double text-[#000000] ml-1"></i>';
    
    if (data.type === 'image') {
        contentHtml = `<a href="${data.message}" target="_blank" class="block"><img src="${data.message}" class="max-w-full max-h-60 rounded" /></a>`;
    } else {
        // Format message text: bold (**text**) and italic (*text*)
        contentHtml = data.message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }

    const messageHtml = `
        <div class="flex flex-col items-start gap-1 mb-2">
            <div onclick="showMsgTime(this)" class="bg-[#00000012] cursor-pointer p-2 rounded-lg text-[16px] chat_message whitespace-pre-wrap break-words font-sans leading-relaxed shadow">${contentHtml}</div>
            <span class="text-xs font-semibold text-[#000000] time_details hidden">
                <span class="font-normal">${formattedTime}</span>
            </span>
        </div>`;

    $(".all_chats").append(messageHtml);
    scrollToBottom();
}

function updateChatListItem(data) {
    const chatItem = $(`.chat-item[data-id="${data.senderId}"]`);
    const isCurrentUser = data.senderId === userId;
    
    if (chatItem.length) {
        // Update the chat item preview
        let previewText = data.type === 'image' 
            ? 'sent a picture' 
            : data.message.replace(/<[^>]+>/g, '').substring(0, 30);
        
        if (previewText.length > 30) previewText += '...';
        
        if (isCurrentUser) {
            chatItem.find(".patient_message").text('You: ' + previewText);
        } else {
            chatItem.find(".patient_message").text(previewText);
            // Add unread indicator if not the active chat
            if ($(".send_text_to").val() !== data.senderId) {
                chatItem.addClass('unread');
                const unreadCount = parseInt(chatItem.find(".related_unread").text() || 0) + 1;
                if (chatItem.find(".related_unread").length) {
                    chatItem.find(".related_unread").text(unreadCount);
                } else {
                    chatItem.find(".timeandseen").append(`
                        <span class="flex items-center justify-center w-5 h-5 mt-1 text-xs text-white bg-orange-500 rounded-full related_unread">
                            1
                        </span>
                    `);
                }
            }
        }
        
        // Update timestamp
        const formattedTime = dateSystem(new Date(data.timestamp));
        if (chatItem.find(".msg_time").length) {
            chatItem.find(".msg_time").text(formattedTime);
        } else {
            chatItem.find(".timeandseen").prepend(`<span class="text-xs text-gray-400 msg_time">${formattedTime}</span>`);
        }
        
        // Move to top of the list
        chatItem.prependTo(chatItem.closest('.users_list'));
    } else {
        // Create new chat item if it doesn't exist
        const formattedTime = dateSystem(new Date(data.timestamp));
        const newChatItem = `
            <div class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item unread"
                data-id="${data.senderId}" onclick="showMessage(this)">
                <div class="flex items-center gap-3 provider_details">
                    <div class="relative w-10 h-10 overflow-hidden image_section">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                            ${data.senderId.charAt(0).toUpperCase()}
                        </div>
                    </div>
                    <div class="name_section">
                        <p class="font-semibold text-[16px] provider_name">
                            <span class="naming">${data.senderId}</span>
                        </p>
                        <p class="text-sm text-gray-600 patient_message">${previewText}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end timeandseen">
                    <span class="text-xs text-gray-400 msg_time">${formattedTime}</span>
                    <span class="flex items-center mt-1">
                        <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                    </span>
                    <span class="flex items-center justify-center w-5 h-5 mt-1 text-xs text-white bg-orange-500 rounded-full related_unread">
                        1
                    </span>
                </div>
            </div>`;
        $('.users_list').prepend(newChatItem);
    }
}

function handleTypingIndicator(data) {
    if ($(".send_text_to").val() === data.senderId) {
        const typingIndicator = $(".typing-indicator");
        if (data.isTyping) {
            typingIndicator.removeClass('hidden');
        } else {
            typingIndicator.addClass('hidden');
        }
    }
}

// Example: Send a message through WebSocket
function sendSocketMessage(messageData) {
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify(messageData));
    }
}

// Update sendMessage function to use WebSocket
function sendMessage(passedThis) {
    let message = $(".message_text").val();
    if (!message.trim()) return;

    const send_text_to = $(".send_text_to").val();
    if (!send_text_to) return;

    // Send via WebSocket
    sendSocketMessage({
        type: 'message',
        receiverId: send_text_to,
        message: message
    });

    // Rest of your existing AJAX code...
    // (Keep your existing AJAX code for persistence)
}

// Update selectingImage function to use WebSocket
function selectingImage(input) {
    const file = input.files[0];
    if (!file) return;

    if (!file.type.match('image.*')) {
        alert('Please select an image file');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const send_text_to = $(".send_text_to").val();
        if (!send_text_to) return;

        // Send via WebSocket
        sendSocketMessage({
            type: 'image',
            receiverId: send_text_to,
            message: e.target.result
        });

        // Rest of your existing AJAX code...
        // (Keep your existing AJAX code for persistence)
    };
    reader.readAsDataURL(file);
}

// Add typing indicator handler
let typingTimeout;
$(".message_text").on('input', function() {
    const send_text_to = $(".send_text_to").val();
    if (!send_text_to) return;

    // Send typing start
    sendSocketMessage({
        type: 'typing',
        receiverId: send_text_to,
        isTyping: true
    });

    // Clear previous timeout
    if (typingTimeout) clearTimeout(typingTimeout);
    
    // Set timeout to send typing stop after 2 seconds of inactivity
    typingTimeout = setTimeout(() => {
        sendSocketMessage({
            type: 'typing',
            receiverId: send_text_to,
            isTyping: false
        });
    }, 2000);
});