@extends('layouts.provider')

@section('title', 'Your Chat Record')

@section('link')

@endsection

@section('style')
    <link rel="stylesheet" href="/assets/css/chat.css">
@endsection


@section('content')
    @include('layouts.provider_header')


    <div class="min-h-screen p-4 font-sans bg-gray-100 lg:p-6">
        {{-- max-w-6xl --}}
        <div class="grid grid-cols-1 p-4 mx-auto bg-white rounded-xl lg:grid-cols-3 chating_section relative">
            <div class="flex flex-col col-span-1 px-4 mb-4 border rounded-md lg:border-none lg:mb-0 lg:rounded-none">
                <div class="flex flex-col col-span-1 first_part">
                    <h1 class="text-[24px] font-bold text-[#000000] mb-6">SugarPros Chats</h1>
                    <a href="/provider/sugarpros-ai"
                        class="relative rounded-xl p-4 text-[#FF6400] text-center bg-gray-100 mb-3 font-bold">
                        Talk to {{ $brandname }} AI
                    </a>
                    <div class="relative w-full mb-3">
                        <i
                            class="absolute text-lg text-gray-900 transform -translate-y-1/2 bg-gray-100 fas fa-search left-3 top-1/2"></i>
                        <input type="text" placeholder="Search here..." onkeyup="searchList(this)"
                            class="w-full placeholder:text-gray-900  pl-10 py-4 pr-3 bg-gray-100 text-sm rounded-[12px] focus:outline-none">
                    </div>
                    <div class="flex justify-start gap-5 py-2">
                        <button class="message_tab px-8 all_list active_tab" onclick="showAllList(this)">All</button>
                        <button onclick="showUnreadFilter(this)" class="message_tab px-4 unread_list">
                            Unread Messages
                            <span
                                class="px-2 text-sm text-white bg-orange-500 rounded-full unread_tol">{{ $total_unread }}</span>
                        </button>
                    </div>
                </div>


                <div class="flex-grow chat-list-container">
                    <h2 class="text-[16px] text-[#000000] font-semibold my-2">Your Patients</h2>

                    <p class="text-gray-500 font-semibold text-center py-4 my-4 no_match hidden">
                        No Match Found
                    </p>

                    <div class="overflow-y-auto py-2 users_list">
                        @forelse ($releted_patients as $patient)
                            <div class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item @if ($patient->message_status == 'delivered' && $patient->is_sender != Auth::guard('provider')->user()->provider_id) unread @endif"
                                data-id="{{ $patient->patient_id }}" onclick="showMessage(this)">

                                <div class="flex items-center gap-3 provider_details">
                                    <div class="relative w-10 h-10 overflow-hidden image_section">
                                        @if (!empty($patient->profile_picture))
                                            <img src="{{ asset($patient->profile_picture) }}" class="w-full rounded-full" />
                                            <img src="{{ asset('assets/image/act.png') }}"
                                                class="absolute bottom-0 right-0" />
                                        @else
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="name_section">
                                        <p class="font-semibold text-[16px] provider_name">
                                            <span class="naming">{{ $patient->name }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600 patient_message">
                                            @if ($patient->latest_message)
                                                @if ($patient->is_sender)
                                                    You:
                                                @endif
                                                @if ($patient->message_type == 'image')
                                                    sent a picture
                                                @else
                                                    {{ Str::limit($patient->latest_message, 20, '...') }}
                                                @endif
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end timeandseen">
                                    @if ($patient->message_time)
                                        <span class="text-xs text-gray-400 msg_time">
                                            {{ \Carbon\Carbon::parse($patient->message_time)->format('g:i A') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center mt-1">
                                        @if ($patient->message_status == 'seen')
                                            <i class="fas fa-check-double text-[#2889AA] text-xs"></i>
                                        @elseif($patient->is_sender)
                                            <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                                        @endif
                                    </span>
                                    @if ($patient->unread_count > 0 && !$patient->is_sender)
                                        <span
                                            class="flex items-center justify-center w-5 h-5 mt-1 text-xs text-white bg-orange-500 rounded-full related_unread">
                                            {{ $patient->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="px-2 py-5 text-gray-500">No patients found in your pod</p>
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="col-span-2 flex flex-col bg-gray-100 rounded-t-[16px] message-container make_hide">
                <div class="justify-between py-2 truncate bg-gray-200 rounded-t-[16px] message_topbar hidden">
                    <div class="flex items-center gap-3 mx-4">
                        <div class="relative w-10 h-10 overflow-hidden img_div">

                        </div>

                        <div>
                            <p class="font-semibold picked_user_name"></p>
                            <p class="text-sm text-[#00000099] activity_status"></p>
                        </div>
                    </div>


                    <div class="flex items-center justify-center h-10 w-10 mr-2 bg-white rounded-md cursor-pointer lg:hidden"
                        onclick="goToChatList(this)">
                        <i class="fas fa-chevron-left text-2xl text-gray-700"></i>
                    </div>
                    {{-- <div class="flex items-center justify-center p-1 mr-2 bg-white rounded-md cursor-pointer">
                        <img src="{{ asset('assets/image/dots.png') }}" alt="" class="w-6 h-6 ">
                    </div> --}}
                </div>

                <div class="p-4 overflow-y-auto all_chats hidden"></div>

                <div class="px-4 mb-4 message_sending relative hidden">
                    <span class="text-sm text-[#6E6E70] typing mb-2 typing-indicator absolute left-8 top-2 hidden">
                        <span class="bubble"></span>
                        <span class="bubble"></span>
                        <span class="bubble"></span>
                    </span>

                    <div class="p-4 rounded-[10px] bg-white gap-2">

                        <textarea class="w-full px-4 py-2 rounded-md resize-none focus:outline-none message_text" id=""
                            placeholder="Type here something..." cols="30" rows="1" onkeydown="sendOnEnter(event, this)"></textarea>

                        <div class="flex items-center justify-between w-full">

                            <input type="hidden" class="token" value="{{ csrf_token() }}">
                            <input type="hidden" class="send_text_to" value="">
                            <input type="hidden" class="user_type" value="provider">
                            <input type="hidden" class="provider_id"
                                value="{{ Auth::guard('provider')->user()->provider_id ?? '' }}">


                            <div class="flex items-center ml-2 space-x-1">
                                <button class="p-2 text-gray-500 hover:text-gray-700" onclick="boldText(this)">
                                    <i class="text-sm fa-solid fa-bold"></i>
                                </button>
                                <button class="p-2 text-gray-500 hover:text-gray-700" onclick="italicText(this)">
                                    <i class="text-sm fa-solid fa-italic"></i>
                                </button>
                                <label class="p-2 text-gray-500 hover:text-gray-700 cursor-pointer" for="openfile">
                                    <i class="text-sm fa-solid fa-paperclip"></i>
                                </label>
                                <input type="file" id="openfile" class="hidden"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    onchange="selectingImage(this)">

                                <button class="px-3 text-gray-500 hover:text-gray-700" onclick="emojiSelection(this)">
                                    <i class="text-sm fa-regular fa-face-smile"></i>
                                </button>
                            </div>
                            <button class="ml-2 px-4 py-2 bg-[#2889AA] text-white rounded-lg hover:bg-opacity-80"
                                onclick="sendMessage(this)">
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
    <script src="/assets/js/chat_works.js"></script>

    <script>
        @if (session('send_to'))
            document.addEventListener('DOMContentLoaded', function() {
                var sendTo = "{{ session('send_to') }}";
                var chatItem = document.querySelector('.chat-item[data-id="' + sendTo + '"]');
                if (chatItem) {
                    chatItem.click();
                }
            });
        @endif
    </script>
@endsection
