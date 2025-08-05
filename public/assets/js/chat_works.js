

function handleIncomingMessage(data) {
    // Only process if the message is for the current chat
    const currentChatId = $(".send_text_to").val();
    if (currentChatId && currentChatId === data.senderId) {
        appendMessageToChat(data);
    }

    updateChatListItem(data);
}

function appendMessageToChat(data) {
    if ($(".all_chats").hasClass('hidden')) return;
    const now = new Date(data.timestamp);
    const formattedTime = dateSystem(now);
    let contentHtml = '';
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

    // Send "seen" status back to sender since message is displayed in active chat
    sendSocketMessage({
        type: 'seen',
        receiverId: data.senderId,
        senderId: userId
    });
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
        let now = new Date();
        let hours = now.getHours() % 12 || 12;
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let ampm = now.getHours() >= 12 ? 'PM' : 'AM';
        let formattedTime = `${hours}.${minutes} ${ampm}`;
        if (chatItem.find(".msg_time").length) {
            chatItem.find(".msg_time").text(formattedTime);
        } else {
            chatItem.find(".timeandseen").prepend(`<span class="text-xs text-gray-400 msg_time">${formattedTime}</span>`);
        }

        // Move to top of the list
        chatItem.prependTo(chatItem.closest('.users_list'));
    } else {
        // Create new chat item if it doesn't exist
        let now = new Date();
        let hours = now.getHours() % 12 || 12;
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let ampm = now.getHours() >= 12 ? 'PM' : 'AM';
        let formattedTime = `${hours}.${minutes} ${ampm}`;
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



function handleOnlineStatus(data) {
    const currentChatId = $(".send_text_to").val();
    if (currentChatId && data.onlineUsers.includes(currentChatId)) {
        $(".activity_status").removeClass('text-gray-400').addClass('text-green-500');
        $(".activity_status").text('Online');
    } else if (currentChatId) {
        $(".activity_status").removeClass('text-green-500').addClass('text-gray-400');
        $(".activity_status").text('Offline');
    }
}

// Example: Send a message through WebSocket
function sendSocketMessage(messageData) {
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify(messageData));
    }
}



// Chatting System
function adjustChatHeight() {
    const headerHeight = 50;
    const chatingSection = document.querySelector('.chating_section');
    if (chatingSection) {
        const windowHeight = window.innerHeight;
        chatingSection.style.height = `${windowHeight - headerHeight}px`;
    }
}





adjustChatHeight();
window.addEventListener('resize', adjustChatHeight);



var user_type = $(".user_type").val();
var linkone = '';
var linktwo = '';
var linkthree = '';
var linkFour = '/update-message-seen';
if (user_type == 'patient') {
    linkone = '/fetch-related-chats';
    linktwo = '/add-new-message';
    linkthree = '/send-image-message';
} else {
    linkone = '/provider/fetch-related-chats';
    linktwo = '/provider/send-message';
    linkthree = '/provider/send-image-message';
}




let emptyTextTemplate = `<div class="flex flex-col items-center justify-center h-full min-h-[400px] text-center">
                        <div class="relative mb-8">
                            <div class="w-24 h-24 rounded-full bg-[#2889AA] opacity-10 animate-pulse"></div>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#2889AA]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-800 mb-2">It's quiet here...</h3>
                        <p class="text-gray-500 max-w-md px-4 mb-6">Send your first message and start the conversation!</p>
                    </div>`;





function dateSystem(selectedTime) {
    let dateObj = new Date(selectedTime);
    let day = String(dateObj.getDate()).padStart(2, '0');
    let month = String(dateObj.getMonth() + 1).padStart(2, '0');
    let year = dateObj.getFullYear();
    let hours = dateObj.getHours();
    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `<span class="cursor-pointer time" onclick="toggleDate(this)"> ${hours}.${minutes} ${ampm} <span class="msg_date hidden">| ${day}/${month}/${year} </span></span>`;
}







function showMessage(passedThis) {
    $(".chat-item").removeClass('active_chatlist');
    $(passedThis).addClass('active_chatlist');
    let dataID = $(passedThis).data('id');
    let imageSection = $(passedThis).children(".provider_details").children('.image_section').html();
    let providerName = $(passedThis).children(".provider_details").children('.name_section').children('.provider_name').html();
    var plandata = '<span class="bg-red-100 text-red-500 border border-red-500 text-sm rounded-lg px-2 ml-1 py-1 font-normal capitalize">No Plan</span>';

    // Set default to Offline initially
    $(".activity_status").removeClass('text-green-500').addClass('text-gray-400');
    $(".activity_status").text('Offline');
    $(".all_chats").html("");

    $(".message-container").removeClass("make_hide");
    $(".chat-list-container").addClass("make_hide");
    $(".first_part").addClass("make_hide");

    
    let token = $(".token").val();
    $.ajax({
        url: '/provider/fetch-users-subscription',
        type: 'POST',
        data: {
            _token: token,
            user_id: dataID,
        },
        success: function (response) {
            if (response.recurring_option != null) {
                plandata = '<span class="bg-blue-100 text-blue-500 border border-blue-500 text-sm rounded-lg px-2 ml-1 py-1 font-normal capitalize">' + response.recurring_option + ' ' + response.plan + '</span>' 
            }else{
                plandata = '<span class="bg-red-100 text-red-500 border border-red-500 text-sm rounded-lg px-2 ml-1 py-1 font-normal capitalize">No Plan</span>';
            }
        },
        error: function (xhr, status, error) {
            console.error('Message send failed:', error);
        }
    });


    $.ajax({
        url: linkone,
        type: 'POST',
        data: {
            _token: token,
            message_with: dataID,
        },
        success: function (response) {
            $(".img_div").html(imageSection);
            $(".picked_user_name").html(providerName + plandata);
            $(".message_topbar").removeClass("hidden").addClass("flex");
            $(".all_chats").removeClass("hidden");
            $(".message_sending").removeClass("hidden");
            $(".send_text_to").val(dataID);

            let unreadElem = $(passedThis).children(".timeandseen").children(".related_unread");
            let thisUnreads = unreadElem.length ? Number(unreadElem.html()) : 0;
            let total_unreads = Number($(".unread_tol").html());
            let newUnreads = (total_unreads - thisUnreads);
            $(".unread_tol").html(newUnreads);
            // console.log(newUnreads, thisUnreads);
            $(passedThis).children(".timeandseen").children(".related_unread").remove();


            $(passedThis).removeClass("unread");

            // Request online status update
            if (socket && socket.readyState === WebSocket.OPEN) {
                socket.send(JSON.stringify({
                    type: 'getOnlineStatus'
                }));
            }

            if (!Array.isArray(response) || response.length == 0) {
                $(".all_chats").html(emptyTextTemplate);
            } else {
                let messageData = '';
                response.forEach(msg => {
                    var formattedTime = dateSystem(msg.created_at);
                    let isSent = msg.sent_by == dataID;
                    let isSeen = msg.status == 'seen';
                    let contentHtml = '';
                    var seenUnseenIcon = '';

                    if (msg.message_type == 'image' && (msg.main_message)) {
                        let imgSrc = msg.main_message;
                        contentHtml = `<a href="${imgSrc}" target="_blank" class="block"><img src="${imgSrc}" class="max-w-full max-h-60 rounded" /></a>`;
                    } else {
                        // Format message text: bold (**text**) and italic (*text*)
                        let formattedMessage = msg.main_message
                            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                            .replace(/\*(.*?)\*/g, '<em>$1</em>');
                        contentHtml = formattedMessage;
                    }

                    if (isSent) {
                        messageData += `<div class="flex flex-col items-start gap-1 mb-2">
                            <div onclick="showMsgTime(this)" class="bg-[#00000012] cursor-pointer p-2 rounded-lg text-[16px] chat_message whitespace-pre-wrap break-words font-sans leading-relaxed shadow">${contentHtml}</div>
                            <span class="text-xs font-semibold text-[#000000] time_details hidden">
                                <span class="font-normal">${formattedTime}</span>
                            </span>
                        </div>`;
                    } else {
                        if (isSeen) {
                            seenUnseenIcon = '<i class="fas fa-check-double text-blue-500 ml-1"></i>';
                        } else {
                            seenUnseenIcon = '<i class="fas fa-check-double text-[#000000] ml-1 unseen_icons"></i>';
                        }

                        messageData += `<div class="flex flex-col items-end gap-1 mb-2">
                            <div onclick="showMsgTime(this)" class="bg-[#2889AA] text-white cursor-pointer p-2 rounded-lg text-[16px] chat_message whitespace-pre-wrap break-words font-sans leading-relaxed shadow">${contentHtml}</div>
                            <span class="text-xs text-[#000000] time_details hidden">
                                ${formattedTime} ${seenUnseenIcon}
                            </span>
                        </div>`;
                    }
                });
                $(".all_chats").html(messageData);
                $(".all_chats").addClass('opacity-0');

                // seen on opening a chat as well.
                sendSocketMessage({
                    type: 'seen',
                    receiverId: String(dataID),
                    senderId: String(userId)
                });



                // Scroll to bottom
                setTimeout(() => {
                    $(".all_chats").scrollTop($(".all_chats")[0].scrollHeight);
                    $(".all_chats").removeClass('opacity-0');
                }, 100);
            }
        },
        error: function (xhr, status, error) {
            console.error('Message send failed:', error);
        }
    });
}


function showMsgTime(passedThis) {
    $(passedThis).toggleClass("time_activated");
    $(passedThis).parent().children(".time_details").toggleClass("hidden");
}









function searchList(passedThis) {
    const searchTerm = passedThis.value.toLowerCase();
    const chatItems = document.querySelectorAll('.chat-item');
    let anyMatch = false;
    if (searchTerm == '') {
        chatItems.forEach(item => {
            item.classList.remove('hidden');
        });
        $(".no_match").addClass("hidden");
        return;
    }
    chatItems.forEach(item => {
        const providerName = item.querySelector('.provider_name').textContent.toLowerCase();
        if (providerName.includes(searchTerm)) {
            item.classList.remove('hidden');
            anyMatch = true;
        } else {
            item.classList.add('hidden');
        }
    });
    if (!anyMatch) {
        $(".no_match").removeClass("hidden");
    } else {
        $(".no_match").addClass("hidden");
    }
}



function showAllList(passedThis) {
    $(".chat-item").removeClass('hidden');
    $(".all_list").addClass("active_tab");
    $(".unread_list").removeClass("active_tab");
}





function showUnreadFilter(passedThis) {
    $(".chat-item").addClass('hidden');
    $(".unread").removeClass('hidden');
    $(".unread_list").addClass("active_tab");
    $(".all_list").removeClass("active_tab");
}








function sendMessage(passedThis) {
    // Get message and escape HTML special characters
    let message = $(".message_text").val()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
    if (!message.trim()) return; // Don't send empty messages

    var send_text_to = $(".send_text_to").val();
    if (!send_text_to) return;



    var now = new Date();
    var formattedTime = dateSystem(now);

    let formattedMessage = message
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');

    let new_message = `<div class="flex flex-col items-end gap-1 mb-2">
                        <div onclick="showMsgTime(this)" class="bg-[#2889AA] text-white cursor-pointer p-2 rounded-lg text-[16px] chat_message whitespace-pre-wrap break-words font-sans leading-relaxed shadow">${formattedMessage}</div>
                        <span class="text-xs text-[#000000] time_details hidden">
                            ${formattedTime} <i class="fas fa-check-double text-[#000000] ml-1 unseen_icons"></i>
                        </span>
                    </div>`;

    // do ajax work 
    let token = $(".token").val();
    $.ajax({
        url: linktwo,
        type: 'POST',
        data: {
            _token: token,
            message: message,
            send_text_to: send_text_to
        },
        success: function (response) {
            if (response.message == 'success') {
                $(".all_chats").append(new_message);
                $(".message_text").val("");

                const chatItem = $(`.chat-item[data-id="${send_text_to}"]`);

                if (chatItem.length) {
                    let previewText = formattedMessage.replace(/<[^>]+>/g, '').replace(/<br\s*\/?>/gi, ' ').substring(0, 30);
                    if (formattedMessage.replace(/<[^>]+>/g, '').length > 30) {
                        previewText += '...';
                    }

                    chatItem.find(".patient_message").html('You: ' + previewText);

                    if (!chatItem.find(".msg_time").length) {
                        chatItem.find(".timeandseen").prepend(`<span class="text-xs text-gray-400 msg_time">${formattedTime}</span>`);
                    } else {
                        chatItem.find(".msg_time").html(formattedTime);
                    }

                    if (!chatItem.find(".fa-check-double").length) {
                        chatItem.find(".timeandseen").append(`
                            <span class="flex items-center mt-1">
                                <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                            </span>
                        `);
                    }

                    const usersList = chatItem.closest('.users_list');
                    if (usersList.length) {
                        chatItem.prependTo(usersList);
                    }
                } else {
                    const newChatItem = `
                        <div class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item unread"
                            data-id="${send_text_to}" onclick="showMessage(this)">
                            <div class="flex items-center gap-3 provider_details">
                                <div class="relative w-10 h-10 overflow-hidden image_section">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                                        ${send_text_to.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div class="name_section">
                                    <p class="font-semibold text-[16px] provider_name">
                                        <span class="naming">${send_text_to}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 patient_message">You: ${message.substring(0, 30)}${message.length > 30 ? '...' : ''}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end timeandseen">
                                <span class="text-xs text-gray-400 msg_time">${formattedTime}</span>
                                <span class="flex items-center mt-1">
                                    <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                                </span>
                            </div>
                        </div>`;
                    $('.users_list').prepend(newChatItem);
                }

                // Scroll to bottom
                const chatContainer = document.querySelector('.all_chats');
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }

                // Send via WebSocket
                sendSocketMessage({
                    type: 'message',
                    receiverId: send_text_to,
                    message: message
                });
            } else {
                toastr.error('Error, Could not send!');
            }
        },
        error: function (xhr, status, error) {
            console.error('Message send failed:', error);
            toastr.error('Failed to send message');
        }
    });
}






function sendOnEnter(event, textarea) {
    if (event.key == "Enter" && !event.shiftKey) {
        event.preventDefault();
        const sendBtn = textarea.closest('.p-4').querySelector('button[onclick^="sendMessage"]');
        if (sendBtn) {
            sendBtn.click();
        }
    }
}





function boldText(button) {
    const $textarea = $('.message_text');
    const textarea = $textarea[0];
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = $textarea.val().substring(start, end);

    if (selectedText) {
        // Wrap selected text with ** for bold (Markdown style)
        const newText = $textarea.val().substring(0, start) +
            '**' + selectedText + '**' +
            $textarea.val().substring(end);
        $textarea.val(newText);

        // Set cursor position after the bold markers
        textarea.selectionStart = start + 2;
        textarea.selectionEnd = end + 2;
    } else {
        // If no text selected, just insert bold markers and position cursor between them
        const newText = $textarea.val().substring(0, start) +
            '****' +
            $textarea.val().substring(end);
        $textarea.val(newText);
        textarea.selectionStart = start + 2;
        textarea.selectionEnd = start + 2;
    }

    $textarea.focus();
}





function italicText(button) {
    const $textarea = $('.message_text');
    const textarea = $textarea[0];
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = $textarea.val().substring(start, end);

    if (selectedText) {
        // Wrap selected text with * for italic (Markdown style)
        const newText = $textarea.val().substring(0, start) +
            '*' + selectedText + '*' +
            $textarea.val().substring(end);
        $textarea.val(newText);

        // Set cursor position after the italic markers
        textarea.selectionStart = start + 1;
        textarea.selectionEnd = end + 1;
    } else {
        // If no text selected, just insert italic markers and position cursor between them
        const newText = $textarea.val().substring(0, start) +
            '**' +
            $textarea.val().substring(end);
        $textarea.val(newText);
        textarea.selectionStart = start + 1;
        textarea.selectionEnd = start + 1;
    }

    $textarea.focus();
}





function selectingImage(input) {
    const file = input.files[0];
    if (!file) return;

    // Check if the file is an image
    if (!file.type.match('image.*')) {
        toastr.error('Please select an image file');
        return;
    }

    const now = new Date();
    var formattedTime = dateSystem(now);

    const reader = new FileReader();
    reader.onload = function (e) {
        const send_text_to = $(".send_text_to").val();
        if (!send_text_to) return;


        const imageMessage = `
            <div class="flex flex-col items-end gap-1 mb-2">
                <div class="bg-[#2889AA] p-2 rounded-lg chat_message" onclick="showMsgTime(this)">
                    <a href="${e.target.result}" target="_blank" class="block"><img src="${e.target.result}" class="max-w-full max-h-60 rounded" /></a>
                </div>
                <span class="text-xs text-[#000000] time_details hidden">
                    ${formattedTime} <i class="fas fa-check-double text-[#000000] ml-1 unseen_icons"></i>
                </span>
            </div>`;

        let token = $(".token").val();
        let formData = new FormData();
        formData.append('_token', token);
        formData.append('send_text_to', send_text_to);
        formData.append('image', file);

        $.ajax({
            url: linkthree,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.message == 'success') {
                    $(".all_chats").append(imageMessage);
                    $(input).val('');


                    const chatItem = $(`.chat-item[data-id="${send_text_to}"]`);
                    if (chatItem.length) {
                        chatItem.find(".patient_message").html('You: Sent a picture');

                        if (!chatItem.find(".msg_time").length) {
                            chatItem.find(".timeandseen").prepend(`<span class="text-xs text-gray-400 msg_time">${formattedTime}</span>`);
                        } else {
                            chatItem.find(".msg_time").html(formattedTime);
                        }

                        if (!chatItem.find(".fa-check-double").length) {
                            chatItem.find(".timeandseen").append(`
                            <span class="flex items-center mt-1">
                                <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                            </span>
                        `);
                        }

                        const usersList = chatItem.closest('.users_list');
                        if (usersList.length) {
                            chatItem.prependTo(usersList);
                        }
                    } else {
                        const newChatItem = `
                        <div class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item unread"
                            data-id="${send_text_to}" onclick="showMessage(this)">
                            <div class="flex items-center gap-3 provider_details">
                                <div class="relative w-10 h-10 overflow-hidden image_section">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                                        ${send_text_to.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div class="name_section">
                                    <p class="font-semibold text-[16px] provider_name">
                                        <span class="naming">${send_text_to}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 patient_message">You: You sent a picture</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end timeandseen">
                                <span class="text-xs text-gray-400 msg_time">${formattedTime}</span>
                                <span class="flex items-center mt-1">
                                    <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                                </span>
                            </div>
                        </div>`;
                        $('.users_list').prepend(newChatItem);
                    }


                    setTimeout(() => {
                        scrollToBottom();
                    }, 50);


                    // Send via WebSocket
                    sendSocketMessage({
                        type: 'image',
                        receiverId: send_text_to,
                        message: e.target.result
                    });
                } else {
                    toastr.error(response.error);
                }
            },
            error: function (error) {
                // Optionally handle error, e.g., show error message
                console.error('Message send failed:', error);
            }
        });
    };
    reader.readAsDataURL(file);
}





function emojiSelection(button) {
    // Remove any existing picker
    $('.custom-emoji-picker').remove();

    // Create emoji picker container
    const $emojiPicker = $('<div>')
        .addClass('custom-emoji-picker absolute bg-white border rounded-lg shadow-lg p-2 z-10 emoji_options')
        .css({
            position: 'fixed',
            zIndex: 10000
        });

    const emojis = [
        '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😜', '😝', '😛', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '☠️', '🤡', '👹', '👺', '👻', '👽', '👾', '🤖',
        '👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🫰', '🤟', '🤘', '🤙', '🫵', '🫱', '🫲', '🫳', '🫴', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '🫶', '👐', '🤲', '🙏', '✍️', '💅', '🤳', '💪', '🦾', '🦵', '🦿', '🦶', '👣', '👂', '🦻', '👃', '🧠', '🦷', '🦴', '👀', '👁️', '👅', '👄', '🫦', '🫦', '🫦',
        '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐻‍❄️', '🐨', '🐯', '🦁', '🐮', '🐷', '🐽', '🐸', '🐵', '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🪱', '🐛', '🦋', '🐌', '🐞', '🐜', '🪰', '🪲', '🪳', '🦟', '🦗', '🕷️', '🕸️', '🦂', '🐢', '🐍', '🦎', '🦖', '🦕', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊', '🐅', '🐆', '🦓', '🦍', '🦧', '🐘', '🦣', '🦛', '🦏', '🐪', '🐫', '🦒', '🦘', '🦬', '🐃', '🐂', '🐄', '🐎', '🐖', '🐏', '🐑', '🦙', '🐐', '🦌', '🐕', '🐩', '🦮', '🐕‍🦺', '🐈', '🐈‍⬛', '🪶', '🐓', '🦃', '🦤', '🦚', '🦜', '🦢', '🦩', '🕊️', '🐇', '🦝', '🦨', '🦡', '🦫', '🦦', '🦥', '🐁', '🐀', '🐿️', '🦔',
        '🍏', '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🫑', '🌽', '🥕', '🫒', '🧄', '🧅', '🥔', '🍠', '🥐', '🥯', '🍞', '🥖', '🥨', '🥞', '🧇', '🧀', '🍖', '🍗', '🥩', '🥓', '🍔', '🍟', '🍕', '🌭', '🥪', '🌮', '🌯', '🫔', '🥙', '🧆', '🥚', '🍳', '🥘', '🍲', '🫕', '🥣', '🥗', '🍿', '🧈', '🧂', '🥫', '🍱', '🍘', '🍙', '🍚', '🍛', '🍜', '🍝', '🍠', '🍢', '🍣', '🍤', '🍥', '🥮', '🍡', '🥟', '🥠', '🥡', '🦪', '🍦', '🍧', '🍨', '🍩', '🍪', '🎂', '🍰', '🧁', '🥧', '🍫', '🍬', '🍭', '🍮', '🍯', '🍼', '🥛', '☕', '🫖', '🍵', '🍶', '🍾', '🍷', '🍸', '🍹', '🍺', '🍻', '🥂', '🥃', '🫗', '🥤', '🧋', '🧃', '🧉', '🧊',
        '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🥅', '🏒', '🏑', '🥍', '🏏', '🪃', '🥌', '🛷', '⛸️', '🥊', '🥋', '🥇', '🥈', '🥉', '🏆', '🎽', '🏅', '🎖️', '🏵️', '🎗️', '🎫', '🎟️', '🎪', '🤹', '🤹‍♂️', '🤹‍♀️', '🎭', '🩰', '🎨', '🎬', '🎤', '🎧', '🎼', '🎹', '🥁', '🪘', '🪗', '🎷', '🎺', '🪗', '🎸', '🪕', '🎻', '🎲', '♟️', '🎯', '🎳', '🎮', '🎰',
        '🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒', '🚐', '🛻', '🚚', '🚛', '🚜', '🦽', '🦼', '🛴', '🚲', '🛵', '🏍️', '🛺', '🚨', '🚔', '🚍', '🚘', '🚖', '🚡', '🚠', '🚟', '🚃', '🚋', '🚞', '🚝', '🚄', '🚅', '🚈', '🚂', '🚆', '🚇', '🚊', '🚉', '✈️', '🛫', '🛬', '🛩️', '💺', '🛰️', '🚀', '🛸', '🚁', '🛶', '⛵', '🚤', '🛥️', '🛳️', '⛴️', '🚢', '⚓', '🪝', '⛽', '🚧', '🚦', '🚥', '🚏', '🗺️', '🗿', '🗽', '🗼', '🏰', '🏯', '🏟️', '🎡', '🎢', '🎠', '⛲', '⛱️', '🏖️', '🏝️', '🏜️', '🌋', '⛰️', '🏔️', '🗻', '🏕️', '⛺', '🏠', '🏡', '🏘️', '🏚️', '🏗️', '🏭', '🏢', '🏬', '🏣', '🏤', '🏥', '🏦', '🏨', '🏩', '🏪', '🏫', '🏩', '💒', '🏛️', '⛪', '🕌', '🛕', '🕍', '🕋', '⛩️', '🛤️', '🛣️', '🗾', '🎑', '🏞️', '🌅', '🌄', '🌠', '🎇', '🎆', '🌇', '🌆', '🏙️', '🌃', '🌌', '🌉', '🌁',
        '⌚', '📱', '📲', '💻', '⌨️', '🖥️', '🖨️', '🖱️', '🖲️', '🕹️', '🗜️', '💽', '💾', '💿', '📀', '📼', '📷', '📸', '📹', '🎥', '📽️', '🎞️', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙️', '🎚️', '🎛️', '⏱️', '⏲️', '⏰', '🕰️', '⌛', '⏳', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '💸', '💵', '💴', '💶', '💷', '🪙', '💰', '💳', '🧾', '💎', '⚖️', '🪜', '🧰', '🧲', '🛠️', '🗡️', '⚔️', '🔫', '🪃', '🏹', '🛡️', '🔧', '🔨', '⚒️', '🛠️', '⛏️', '🪓', '🔩', '⚙️', '🗜️', '⚗️', '🧪', '🧫', '🧬', '🔬', '🔭', '📡', '💉', '🩸', '💊', '🩹', '🩺', '🚪', '🛗', '🪞', '🪟', '🛏️', '🛋️', '🪑', '🚽', '🚿', '🛁', '🪠', '🧴', '🧷', '🧹', '🧺', '🧻', '🪣', '🧼', '🪥', '🧽', '🧯', '🛒', '🚬', '⚰️', '🪦', '⚱️', '🗿',
        '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳', '🈶', '🈚', '🈸', '🈺', '🈷️', '✴️', '🆚', '💮', '🉐', '㊙️', '㊗️', '🈴', '🈵', '🈹', '🈲', '🅰️', '🅱️', '🆎', '🆑', '🅾️', '🆘', '❌', '⭕', '🛑', '⛔', '📛', '🚫', '💯', '💢', '♨️', '🚷', '🚯', '🚳', '🚱', '🔞', '📵', '🚭', '❗', '❕', '❓', '❔', '‼️', '⁉️', '🔅', '🔆', '〽️', '⚠️', '🚸', '🔱', '⚜️', '🔰', '♻️', '✅', '🈯', '💹', '❇️', '✳️', '❎', '🌐', '💠', 'Ⓜ️', '🌀', '💤', '🏧', '🚾', '♿', '🅿️', '🛗', '🈳', '🈂️', '🛂', '🛃', '🛄', '🛅', '🚹', '🚺', '🚼', '🚻', '🚮', '🎦', '📶', '🈁', '🔣', 'ℹ️', '🔤', '🔡', '🔠', '🆖', '🆗', '🆙', '🆒', '🆕', '🆓', '0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟', '🔢', '#️⃣', '*️⃣', '⏏️', '▶️', '⏸️', '⏯️', '⏹️', '⏺️', '⏭️', '⏮️', '⏩', '⏪', '⏫', '⏬', '⏸️', '⏹️', '⏺️', '⏭️', '⏮️', '⏩', '⏪', '⏫', '⏬', '◀️', '🔼', '🔽', '➡️', '⬅️', '⬆️', '⬇️', '↗️', '↘️', '↙️', '↖️', '↕️', '↔️', '↩️', '↪️', '⤴️', '⤵️', '🔀', '🔁', '🔂', '🔄', '🔃', '🎵', '🎶', '➕', '➖', '➗', '✖️', '💲', '💱', '™️', '©️', '®️', '〰️', '➰', '➿', '🔚', '🔙', '🔛', '🔝', '🔜', '✔️', '☑️', '🔘', '⚪', '⚫', '🔴', '🔵', '🔸', '🔹', '🔶', '🔷', '🔺', '🔻', '🔳', '🔲', '▪️', '▫️', '◾', '◽', '◼️', '◻️', '⬛', '⬜', '🔈', '🔇', '🔉', '🔊', '🔔', '🔕', '📣', '📢', '👁️‍🗨️', '💬', '💭', '🗯️', '♠️', '♣️', '♥️', '♦️', '🃏', '🎴', '🀄', '🕐', '🕑', '🕒', '🕓', '🕔', '🕕', '🕖', '🕗', '🕘', '🕙', '🕚', '🕛', '🕜', '🕝', '🕞', '🕟', '🕠', '🕡', '🕢', '🕣', '🕤', '🕥', '🕦', '🕧'
    ];

    // Create emoji buttons
    emojis.forEach(emoji => {
        const $emojiBtn = $('<button>')
            .addClass('text-2xl p-2 hover:bg-gray-100 rounded')
            .attr('type', 'button')
            .text(emoji)
            .on('click', function (e) {
                e.preventDefault();
                const $textarea = $('.message_text');
                const textarea = $textarea[0];
                if (!textarea) return;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const newText = $textarea.val().substring(0, start) +
                    emoji +
                    $textarea.val().substring(end);
                $textarea.val(newText);
                // Move cursor after inserted emoji
                textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
                $textarea.focus();
                // Do NOT close the picker, allow multiple selections
            });
        $emojiPicker.append($emojiBtn);
    });

    // Add close button
    const $closeBtn = $('<button>')
        .addClass('text-sm text-gray-500 hover:text-gray-700 p-1 block w-full text-left mt-2')
        .attr('type', 'button')
        .text('Close')
        .on('click', function () {
            $emojiPicker.remove();
            $(document).off('mousedown.emojiPicker');
        });
    $emojiPicker.append($closeBtn);

    // Position the picker near the emoji button
    const buttonRect = $(button)[0].getBoundingClientRect();
    $emojiPicker.css({
        left: buttonRect.left,
        bottom: (window.innerHeight - buttonRect.top + 10)
    });

    // Add to document
    $('body').append($emojiPicker);

    // Close picker when clicking outside (but not on emoji buttons)
    setTimeout(() => {
        $(document).on('mousedown.emojiPicker', function (e) {
            if (!$emojiPicker.is(e.target) && $emojiPicker.has(e.target).length === 0 && e.target !== button) {
                $emojiPicker.remove();
                $(document).off('mousedown.emojiPicker');
            }
        });
    }, 0);
}





function scrollToBottom() {
    const chatContainer = $(".all_chats")[0];
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}




function toggleDate(passedThis) {
    $(passedThis).children('.msg_date').toggleClass('hidden');
}






function updateMessageSeenStatus(data) {
    $(".unseen_icons").removeClass("text-[#000000]").addClass("text-blue-500");
    let token = $(".token").val();
    // update to database by ajax.
    $.ajax({
        url: linkFour,
        type: 'POST',
        data: {
            _token: token,
            senderId: data.senderId,
            receiverId: data.receiverId
        },
        success: function (response) {
            // console.log(response.type);
        },
        error: function (error) {
            // Optionally handle error, e.g., show error message
            console.error('Message send failed:', error);
        }
    });
}



// Add typing indicator handler
let typingTimeout;
$(".message_text").on('input', function () {
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





function goToChatList() {
    $(".message-container").addClass("make_hide");
    $(".chat-list-container").removeClass("make_hide");
    $(".first_part").removeClass("make_hide");
}