@extends('layouts.patient_portal')

@section('title', 'Join Meeting')

@section('link')

@endsection

@section('style')


@endsection


@section('content')

    @include('layouts.patient_header')


    <div class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-7xl mx-auto bg-white p-6 md:flex justify-between min-h-screen rounded-md">


            <div class="w-full md:w-[400px]">
                <h2 class="text-xl font-semibold text-[#000000] mb-1">Join Meeting</h2>
                <p class="text-[16px] text-[#4E4E5099]/60 mb-6">Lorem Ipsum Dolor Sit Amet</p>

                <label for="meetingId" class="block text-sm font-semibold text-[#000000] mb-1">Meeting ID or Personal Link
                    Name</label>
                <input type="text" id="meetingId" placeholder="Meeting ID or Personal Link Name"
                    class="w-full p-4 mb-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm placeholder:text-[#A3A3A3] " />

                <div class="flex gap-3">
                    <button
                        class="bg-[#2889AA] text-white px-5 py-2 rounded hover:bg-cyan-700 transition text-sm font-medium">
                        Cancel
                    </button>
                    <button id="applyBtn"
                        class="border border-[#2889AA] text-[#2889AA] px-5 py-2 rounded hover:bg-cyan-50 transition text-sm font-medium">
                        Apply
                    </button>
                </div>
            </div>


            <!-- Popup Modal -->
            <div id="popupModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-xl px-6 py-6 w-full max-w-md mx-4">
                    <div class="flex justify-between">
                        <h3 class="text-[24px] font-semibold mb-4">Heres your joining info</h3>
                        <img src="{{ asset('assets/image/close.png') }}" id="cancelBtn" class=" cursor-pointer w-6 h-6"
                            alt="">
                    </div>
                    <p class="text-[#4E4E5099]/60 text-[16px] mb-4">
                        Send this to people you want to meet with. Be sure to see it so you can us e it later too.
                    </p>

                    <div class="flex items-center border border-gray-300  p-3 rounded-lg mb-6">
                        <input id="meetingLink" type="text" value="most.go:gibomn/not-get-liv"
                            class="bg-transparent flex-1 text-sm text-[#A3A3A3] outline-none" readonly>

                        <button id="copyBtn" class="ml-2 p-2 text-gray-600 hover:text[#2889AA]">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>

                    <button class="w-full py-3 bg-[#2889AA] text-white rounded-lg hover:bg-opacity-90">
                        Join Meeting
                    </button>
                </div>
            </div>

            <div class="w-full mt-12 md:mt-0 md:w-1/2 flex justify-center">
                <img src="{{ asset('assets/image/join.png') }}" alt="Join Meeting" class="max-w-[400px] max-h-[400px]" />
            </div>
        </div>
    </div>







@endsection

@section('script')

    <script>
        document.getElementById('applyBtn').addEventListener('click', function() {
            document.getElementById('popupModal').classList.remove('hidden');
        });

        document.getElementById('copyBtn').addEventListener('click', function() {
            const linkInput = document.getElementById('meetingLink');
            linkInput.select();
            document.execCommand('copy');


            const copyBtn = document.getElementById('copyBtn');
            copyBtn.innerHTML = '<i class="fas fa-check"></i>';
            copyBtn.classList.add('text-green-500');

            setTimeout(() => {
                copyBtn.innerHTML = '<i class="far fa-copy"></i>';
                copyBtn.classList.remove('text-green-500');
            }, 2000);
        });


        function closePopup() {
            document.getElementById('popupModal').classList.add('hidden');
        }

        document.getElementById('cancelBtn').addEventListener('click', closePopup);
        // document.getElementById('joinMeetingBtn').addEventListener('click', closePopup);
    </script>
@endsection
