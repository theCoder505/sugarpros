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

        .formatting-toolbar {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .formatting-btn {
            background: #f0f0f0;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
        }

        .formatting-btn:hover {
            background: #e0e0e0;
        }

        #file-name {
            font-size: 12px;
            margin-left: 10px;
            color: #666;
        }

        .attachment-preview {
            max-width: 100px;
            margin-top: 5px;
            display: none;
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
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="min-h-screen p-4 font-sans bg-gray-100 md:p-6">
        <div class="grid max-w-6xl grid-cols-1 p-4 mx-auto bg-white rounded-xl md:grid-cols-3">
            <!-- Sidebar: Chat History -->
            <div class="flex flex-col col-span-1 px-4 mb-4 border rounded-md md:border-none md:mb-0 md:rounded-none">
                <!-- New Chat Button -->
                <button class="text-sm text-gray-900 bg-gray-100 py-5 md:mt-0 mt-4 rounded-[12px]" onclick="resetChat()">
                    <i class="fa-solid fa-plus"></i> New Chat
                </button>

                <!-- Chat History List -->
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-gray-500">Recent Chats</h3>
                    <ul id="chat-history-list" class="mt-2 space-y-2">
                        <!-- Chat history items will be loaded here -->
                    </ul>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="col-span-2 flex flex-col bg-gray-100 rounded-t-[16px] chat_system_holder">
                <!-- Message Display Area -->
                <div id="chat-box" class="p-4 space-y-4 chat_system_container">
                    <!-- Messages will be appended here -->
                </div>

                <!-- Message Input -->
                <div class="bottom_message_container w-full">
                    <div class="bg-white rounded-[10px] gap-2 px-3">
                        <!-- Formatting Toolbar -->
                        <div class="formatting-toolbar">
                            <span id="file-name"></span>
                            <img id="attachment-preview" class="attachment-preview" src="" alt="Attachment preview">
                        </div>

                        <textarea id="chat-input" class="w-full rounded-md resize-none focus:outline-none" placeholder="Ask us anything..."
                            rows="1"></textarea>

                        <div class="flex items-center w-full gap-2 justify-between">
                            <div class="flex items-center gap-2">
                                <input type="file" id="fileInput" class="hidden" onchange="handleFileSelect(event)">
                                <button type="button" onclick="document.getElementById('fileInput').click()"
                                    class="flex items-center text-sm px-3 py-3 rounded-md border bg-white border-[#E5E5E5] space-x-1">
                                    <img src="{{ asset('assets/image/atta.png') }}" alt="">
                                    Add attachment
                                </button>
                                <button id="clear-btn"
                                    class="px-3 py-3 text-sm rounded-md border bg-white border-[#E5E5E5]">
                                    Clear
                                </button>
                            </div>
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
        // Current chat session ID
        let currentChatId = 'chat_' + Date.now();
        let fileAttachment = null;

        // Initialize chat history
        function loadChatHistory() {
            // Load from localStorage or server
            const history = JSON.parse(localStorage.getItem('chatHistory') || '[]');
            const historyList = $('#chat-history-list');
            historyList.empty();

            history.forEach(chat => {
                historyList.append(`
                    <li class="cursor-pointer p-2 hover:bg-gray-100 rounded-md" onclick="loadChat('${chat.id}')">
                        <p class="text-sm truncate">${chat.preview || 'New Chat'}</p>
                        <small class="text-xs text-gray-500">${new Date(chat.timestamp).toLocaleString()}</small>
                    </li>
                `);
            });
        }

        // Load a specific chat
        function loadChat(chatId) {
            currentChatId = chatId;
            const chatData = JSON.parse(localStorage.getItem(chatId) || '{"messages":[]}');
            $('#chat-box').empty();

            chatData.messages.forEach(msg => {
                if (msg.role === 'user') {
                    appendUserMessage(msg.content);
                } else {
                    appendAiMessage(msg.content);
                }
            });
        }

        // Reset chat to a new session
        function resetChat() {
            currentChatId = 'chat_' + Date.now();
            $('#chat-box').empty();
            $('#chat-input').val('');
            fileAttachment = null;
            $('#file-name').text('');
            $('.attachment-preview').hide();

            // Clear the session on server side if needed
            $.post("{{ route('clearChatSession') }}", {
                _token: '{{ csrf_token() }}'
            });
        }

        // Clear current input
        $('#clear-btn').on('click', function() {
            $('#chat-input').val('');
            fileAttachment = null;
            $('#file-name').text('');
            $('.attachment-preview').hide();
        });

        // Handle file selection
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            fileAttachment = file;
            $('#file-name').text(file.name);

            // Show preview if image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#attachment-preview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            } else {
                $('.attachment-preview').hide();
            }
        }


        // Append user message to chat
        function appendUserMessage(message) {
            $('#chat-box').append(`
                <div class="flex flex-col items-end gap-1">
                    <div class="bg-[#ffffff] text-[#262626] px-4 py-2 rounded-lg max-w-[440px] chat-message">
                        ${formatMarkdown(message)}
                    </div>
                    ${fileAttachment ? `<small class="text-xs text-gray-500">Attachment: ${fileAttachment.name}</small>` : ''}
                </div>
            `);
        }

        // Append AI message to chat
        function appendAiMessage(message) {
            $('#chat-box').append(`
                <div class="flex gap-3">
                    <img src="{{ asset('assets/image/icon.png') }}" class="border rounded-full w-12 h-12">
                    <div class="bg-white text-black px-4 py-2 rounded-lg max-w-[440px] chat-message">
                        ${formatMarkdown(message)}
                    </div>
                </div>
            `);
        }

        // Basic markdown formatting
        function formatMarkdown(text) {
            // Bold: **text**
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // Italic: *text*
            text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
            // Line breaks
            text = text.replace(/\n/g, '<br>');
            return text;
        }

        // Save chat to history
        function saveChatToHistory(message) {
            const chatData = {
                id: currentChatId,
                preview: message.substring(0, 30) + (message.length > 30 ? '...' : ''),
                timestamp: new Date().toISOString()
            };

            let history = JSON.parse(localStorage.getItem('chatHistory') || '[]');
            // Remove if already exists
            history = history.filter(chat => chat.id !== currentChatId);
            // Add to beginning
            history.unshift(chatData);
            localStorage.setItem('chatHistory', JSON.stringify(history));

            loadChatHistory();
        }

        // Send message to server
        $('#send-btn').on('click', function() {
            let userMessage = $('#chat-input').val().trim();
            if (userMessage === '' && !fileAttachment) return;

            // Show processing status
            $('#chat-box').append(`<div class="flex flex-col items-end gap-1">
                                        <div class="bg-[#ffffff] text-[#262626] px-4 py-2 rounded-lg max-w-[440px]">
                                            ${userMessage || '[Attachment only]'}
                                            ${fileAttachment ? `<div class="mt-2 text-xs text-gray-500">Processing ${fileAttachment.name}...</div>` : ''}
                                        </div>
                                    </div>`);

            const formData = new FormData();
            formData.append('message', userMessage);
            formData.append('_token', '{{ csrf_token() }}');
            if (fileAttachment) {
                formData.append('attachment', fileAttachment);
            }

            // Clear input
            $('#chat-input').val('');
            fileAttachment = null;
            $('#file-name').text('');
            $('.attachment-preview').hide();

            $.ajax({
                url: "{{ route('chatgptResponse') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#chat-box').append(
                        `<p id="typing" class="text-sm text-gray-400">Analyzing attachment (${fileAttachment ? fileAttachment.name : 'text'})...</p>`
                        );
                },
                success: function(response) {
                    $('#typing').remove();
                    if (response.message.includes('[ERROR PROCESSING ATTACHMENT]')) {
                        $('#chat-box').append(`
                    <div class="text-red-500 text-sm">
                        ${response.message}
                    </div>
                `);
                    } else {
                        appendAiMessage(response.message);
                        saveChatToHistory(userMessage);
                    }
                },
                error: function(xhr) {
                    $('#typing').remove();
                    let errorMsg = xhr.responseJSON?.message || 'Failed to process attachment';
                    if (xhr.status === 413) {
                        errorMsg = "File too large (max 2MB)";
                    }
                    $('#chat-box').append(`
                <div class="text-red-500 text-sm">
                    Error: ${errorMsg}
                </div>
            `);
                }
            });
        });

        // Allow Enter key to send message (Shift+Enter for new line)
        $('#chat-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#send-btn').click();
            }
        });

        // Initialize on page load
        $(document).ready(function() {
            loadChatHistory();
            // Load the latest chat if exists
            const history = JSON.parse(localStorage.getItem('chatHistory') || '[]');
            if (history.length > 0) {
                loadChat(history[0].id);
            }
        });
    </script>
@endsection












The related controllers:
    public function chatgptResponse(Request $request)
    {
        $OPENAI_API_KEY = Settings::where('id', 1)->value('OPENAI_API_KEY');
        $userMessage = $request->input('message');

        // Initialize attachment text
        $attachmentText = '';

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentText = $this->processAttachment($file);
        }

        // $request->validate([
        //     'attachment' => 'nullable|file|max:2048|mimes:pdf,txt,jpg,jpeg,png,docx,xml,csv,json',
        // ]);

        // Get chat history
        $chatHistory = session('chat_history', []);

        // System message explaining attachment handling
        if (empty($chatHistory)) {
            $chatHistory[] = [
                'role' => 'system',
                'content' => 'When users attach files, you will receive extracted text from these files. ' .
                    'You can analyze prescription PDFs, medical reports (text portions), ' .
                    'and other documents. For images, you will receive any available text.'
            ];
        }

        // Combine user message with attachment content
        $fullMessage = $userMessage;
        if (!empty($attachmentText)) {
            $fullMessage .= "\n\n[ATTACHMENT CONTENT BELOW]\n" . $attachmentText;
        }

        $chatHistory[] = ['role' => 'user', 'content' => $fullMessage];

        // Send to OpenAI
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $OPENAI_API_KEY,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o',
                'messages' => $chatHistory,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        $aiReply = $data['choices'][0]['message']['content'];

        $chatHistory[] = ['role' => 'assistant', 'content' => $aiReply];
        session(['chat_history' => $chatHistory]);

        return response()->json(['message' => $aiReply]);
    }


    private function processAttachment($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = $file->getPathname();

        try {
            // Text-based files
            if (in_array($extension, ['txt', 'csv', 'json', 'xml'])) {
                return file_get_contents($fileName);
            }

            // PDF files
            if ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($fileName);
                return $pdf->getText();
            }

            // DOCX files
            if ($extension === 'docx') {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileName);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($element->getElements() as $textElement) {
                                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                    $text .= $textElement->getText();
                                }
                            }
                        }
                    }
                }
                return $text;
            }

            // Images with text (OCR)
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                // Check if Tesseract is installed
                if (!shell_exec('which tesseract')) {
                    throw new \Exception("Tesseract OCR is not installed on server");
                }

                $tesseract = new TesseractOCR($fileName);
                $tesseract->setTempDir(storage_path('app/temp'));
                return $tesseract->run();
            }
        } catch (\Exception $e) {
            Log::error("Attachment processing failed: " . $e->getMessage());
            return "[ERROR PROCESSING ATTACHMENT: " . $e->getMessage() . "]";
        }

        return "[Unsupported file type: .$extension]";
    }




    public function clearChatSession(Request $request)
    {
        $request->session()->forget('chat_history');
        return response()->json(['success' => true]);
    }



Routes:
Route::post('/chatgpt-response', [HomeController::class, 'chatgptResponse'])->name('chatgptResponse')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/clear-chat-session', [HomeController::class, 'clearChatSession'])->name('clearChatSession')->middleware('patient_loggedin', 'check_if_forms_filled');

