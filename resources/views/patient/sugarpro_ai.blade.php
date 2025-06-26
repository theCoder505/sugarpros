@extends('layouts.patient_portal')

@section('title', 'AI Chat')

@section('style')
    <style>
        .sugarpro_ai {
            font-weight: 500;
            color: #000000;
        }

        .chat-message p {
            margin-bottom: 0.5rem;
        }

        .chat-message strong {
            font-weight: bold;
        }

        .chat-message em {
            font-style: italic;
        }

        .chat_system_holder {
            position: relative;
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
        }

        .chat_system_container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .bottom_message_container {
            background: white;
            position: sticky;
            bottom: 0;
        }

        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 1rem;
        }

        #chat-input {
            max-height: 150px;
            min-height: 60px;
        }

        .chat_history {
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .chat-session.active {
            background-color: #f0f0f0;
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="min-h-screen p-4 font-sans bg-gray-100 md:p-6">
        <div class="grid max-w-6xl grid-cols-1 p-4 mx-auto bg-white rounded-xl md:grid-cols-3">
            <!-- Sidebar: Chat History -->
            <div class="flex flex-col col-span-1 px-4 mb-4 border rounded-md md:border-none md:mb-0 md:rounded-none">
                <!-- New Chat Button -->
                <button class="text-sm text-gray-900 bg-gray-100 py-5 md:mt-0 mt-4 rounded-[12px]" onclick="addNewChat()">
                    <i class="fa-solid fa-plus"></i> New Chat
                </button>

                <!-- Chat History List -->
                <div class="mt-4">
                    <h2 class="text-[16px] text-zinc-600 font-semibold my-3">Chat With SugarPros AI</h2>
                    <ul id="chat-history-list" class="mt-2 space-y-2 chat_history">
                        @forelse ($allChats as $key => $item)
                            @php
                                if ($key == 0) {
                                    $chatactivity = 'active';
                                } else {
                                    $chatactivity = 'inactive';
                                }
                            @endphp
                            <li class="cursor-pointer p-2 hover:bg-gray-100 rounded-md chat-session {{ $chatactivity }}"
                                data-chatuid="{{ $item->chatuid }}">
                                <p class="text-sm truncate">
                                    {{ $item->message }}
                                </p>
                                <small class="text-xs text-gray-500">
                                    {{ $item->created_at->format('g:i A d/m/Y') }}
                                </small>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="col-span-2 flex flex-col bg-gray-100 rounded-t-[16px] chat_system_holder">
                <!-- Message Display Area -->
                <div id="chat-box" class="p-4 space-y-4 chat_system_container">
                    @foreach ($chats as $chat)
                        @if ($chat->requested_to === 'AI')
                            <div class="flex flex-col items-end gap-1">
                                <div class="bg-[#ffffff] text-[#262626] px-4 py-2 rounded-lg max-w-[440px] chat-message">
                                    {!! nl2br(e($chat->message)) !!}
                                </div>
                            </div>
                        @else
                            <div class="flex gap-3">
                                <img src="{{ asset('assets/image/icon.png') }}" class="h-12">
                                <div class="bg-white text-black px-4 py-2 rounded-lg max-w-[440px] chat-message">
                                    @php
                                        $message = e($chat->message);
                                        $message = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $message);
                                        $message = preg_replace(
                                            '/(?<!\*)\*(?!\*)([^*]+)(?<!\*)\*(?!\*)/',
                                            '<em>$1</em>',
                                            $message,
                                        );
                                        echo nl2br($message);
                                    @endphp
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="bottom_message_container w-full">
                    <div class="bg-white rounded-[10px] gap-2 px-3">
                        <input type="hidden" class="chatuid" value="{{ $currentChatUid }}">
                        <textarea id="chat-input" class="pt-4 w-full rounded-md resize-none focus:outline-none" placeholder="Ask us anything..."
                            rows="1"></textarea>
                        <div class="flex items-center w-full gap-2 justify-between">
                            <button id="clear-btn" class="px-3 py-3 text-sm rounded-md border bg-white border-[#E5E5E5]">
                                Clear
                            </button>
                            <button id="send-btn" class="px-4 py-2 bg-[#2889AA] text-white rounded-lg hover:bg-opacity-80">
                                Send Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(".ai").addClass("active_nav_tab");
    </script>
    <script>
        // Append user message to chat
        function appendUserMessage(message) {
            $('#chat-box').append(`
                <div class="flex flex-col items-end gap-1">
                    <div class="bg-[#ffffff] text-[#262626] px-4 py-2 rounded-lg max-w-[440px] chat-message">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `);


            let chatuid = $('.chatuid').val().trim();
            if ($('.chat_history li[data-chatuid="' + chatuid + '"]').length === 0) {
                let shortMsg = message.length > 50 ? message.substring(0, 50) + '...' : message;
                let now = new Date();
                let hours = now.getHours() % 12 || 12;
                let minutes = now.getMinutes().toString().padStart(2, '0');
                let ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                let day = now.getDate().toString().padStart(2, '0');
                let month = (now.getMonth() + 1).toString().padStart(2, '0');
                let year = now.getFullYear();
                let formattedTime = `${hours}:${minutes} ${ampm} ${day}/${month}/${year}`;

                let newList = `<li class="cursor-pointer p-2 hover:bg-gray-100 rounded-md chat-session" data-chatuid="${chatuid}">
                    <p class="text-sm truncate">
                        ${shortMsg}
                    </p>
                    <small class="text-xs text-gray-500">
                        ${formattedTime}
                    </small>
                </li>`;
                $(".chat_history").prepend(newList);
            }

            scrollToBottom();
        }

        // Append AI message to chat
        function appendAiMessage(message) {
            $('#chat-box').append(`
                <div class="flex gap-3">
                    <img src="{{ asset('assets/image/icon.png') }}" class="h-12">
                    <div class="bg-white text-black px-4 py-2 rounded-lg max-w-[440px] chat-message">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `);

            scrollToBottom();
        }

        // Reset chat to a new session
        function addNewChat() {
            $.post("{{ route('clearChatSession') }}", {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    $('.chatuid').val(response.newChatUid);
                    $('#chat-box').empty();
                    $('.chat-session').removeClass('active');
                }
            });
        }

        // Clear current input
        $('#clear-btn').on('click', function() {
            $('#chat-input').val('');
        });

        // Send message to server
        $('#send-btn').on('click', function() {
            let userMessage = $('#chat-input').val().trim();
            let chatuid = $('.chatuid').val().trim();
            if (userMessage === '') return;

            appendUserMessage(userMessage);
            scrollToBottom();

            // Send to server
            $.ajax({
                url: "{{ route('chatgptResponse') }}",
                method: 'POST',
                data: {
                    chatuid: chatuid,
                    message: userMessage,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#chat-input').val('');
                    $('#chat-box').append(
                        `<p id="typing" class="text-sm text-gray-400">SugarPros AI is typing...</p>`
                    );
                    scrollToBottom();
                },
                success: function(response) {
                    $('#typing').remove();
                    // Format: **text** => <strong>text</strong>, *text* => <em>text</em>
                    let formattedMsg = response.message
                        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.+?)\*/g, '<em>$1</em>');
                    appendAiMessage(formattedMsg);
                    scrollToBottom();

                    // If this is the first message in a new chat, reload to update sidebar
                    if ($('#chat-box').children().length === 2) { // User message + AI response
                        // location.reload();
                    }
                },
                error: function(xhr) {
                    $('#typing').remove();
                    $('#chat-box').append(`
                        <div class="text-red-500 text-sm">
                            Error: ${xhr.responseJSON?.message || 'Failed to get response'}
                        </div>
                    `);
                    scrollToBottom();
                }
            });
        });

        // Load chat session when clicked in sidebar
        $(document).on('click', '.chat-session', function() {
            let chatuid = $(this).data('chatuid');
            $('.chatuid').val(chatuid);
            $('.chat-session').removeClass('active');
            $(this).addClass('active');

            // Load messages for this chat session
            $.get("{{ route('sugarpro_ai') }}?chatuid=" + chatuid, function(data) {
                let newDoc = (new DOMParser()).parseFromString(data, 'text/html');
                let messages = $(newDoc).find('#chat-box').html();
                $('#chat-box').html(messages);
                scrollToBottom();
            });
        });

        // Allow Enter key to send message (Shift+Enter for new line)
        $('#chat-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#send-btn').click();
            }
        });

        // Auto-scroll to bottom of chat
        function scrollToBottom() {
            const chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Initialize on page load
        $(document).ready(function() {
            scrollToBottom();
        });
    </script>
@endsection
