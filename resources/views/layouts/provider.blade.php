<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugarPros - @yield('title')</title>


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="shortcut icon" href="{{ $brandicon }}" type="image/x-icon">

    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">

    @yield('link')
    @yield('style')
</head>

<body>
    @yield('content')



    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/provider.js') }}"></script>


    <script>
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif
    </script>

    @yield('script')






    <script>
        let socket;
        const userId = "{{ Auth::guard('provider')->user()->provider_id }}";

        function connectWebSocket() {
            socket = new WebSocket(`{{ $socketURL }}/?userId=${userId}`);

            socket.onopen = function(e) {
                console.log('Connected to WebSocket server');
            };

            socket.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);

                    if ($(".message_topbar").length === 0) {
                        if (data.type == 'message' || data.type == 'image') {
                            if ($(".dashboard_message_list").length != 0) {
                                handleIncomingMessage(data);
                            }
                            showNewMessageAppeared('data');
                        }
                    } else {
                        switch (data.type) {
                            case 'message':
                            case 'image':
                                handleIncomingMessage(data);
                                showNewMessageAppeared(data);
                                break;

                            case 'typing':
                                handleTypingIndicator(data);
                                break;

                            case 'seen':
                                updateMessageSeenStatus(data);
                                break;

                            case 'onlineStatus':
                                handleOnlineStatus(data);
                                break;
                        }
                    }
                } catch (error) {
                    console.error('Error parsing WebSocket message:', error);
                }
            };

            socket.onclose = function(event) {
                if (event.wasClean) {
                    console.log(`Connection closed cleanly, code=${event.code}, reason=${event.reason}`);
                } else {
                    console.log('Connection died');
                    // Attempt to reconnect after a delay
                    setTimeout(connectWebSocket, 5000);
                }
            };

            socket.onerror = function(error) {
                console.log(`WebSocket error: ${error.message}`);
            };
        }

        connectWebSocket();



        function showNewMessageAppeared(data) {
            if ($(".message_topbar").length === 0 || $(".message_topbar").hasClass("hidden")) {
                let $span = $(".unseen_mesages span");
                let unseen_mesages = 0;
                if ($span.length) {
                    unseen_mesages = Number($span.html());
                } else {
                    $(".unseen_mesages").append(
                        '<span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">0</span>'
                    );
                    $span = $(".unseen_mesages span");
                }
                let new_tot = unseen_mesages + 1;
                $span.html(new_tot);
                toastr.info(
                    '<a href="/provider/chats" style="color:#fff;text-decoration:underline;">Message From Patient</a>');
            }


            if ($(".chating_section").length) {
                let unread_total = Number($(".unread_list span").html());
                let new_unread_total = (unread_total + 1);
                $(".unread_list span").html(new_unread_total);
                if (data && data.receiverId) {
                    $(`.chat-item[data-id='${data.receiverId}']`).addClass('unread');
                }
            }
        }
    </script>
</body>

</html>
