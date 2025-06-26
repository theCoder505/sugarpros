@extends('layouts.patient_portal')

@section('title', 'Meeting Room')

@section('link')

@endsection

@section('style')


@endsection


@section('content')

    @include('layouts.patient_header')


    <div class="bg-gray-100 min-h-screen p-6">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <h2 class="text-[24px] sm:text-[28px] md:text-[32px] font-semibold text-[#000000] mb-3">
                SugarPros Meeting Room
            </h2>

            <div class="bg-[#FF640033]/20 rounded-xl h-[80vh] w-full flex flex-col justify-between p-4">
                <!-- Placeholder for video or content -->
                <div class="flex-1"></div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                    <div class="text-sm text-gray-700 flex items-center gap-2">
                        <p class="font-medium">5:07 PM</p><span>|</span>
                        <p class="text-xs text-gray-500">str-2-k9x-oh</p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 items-center">
                        <button
                            class="w-10 h-10 rounded-full bg-[#0f2940] text-white flex items-center justify-center hover:bg-[#193c5b]">
                            <i class="fa-solid fa-microphone"></i>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-[#0f2940] text-white flex items-center justify-center hover:bg-[#193c5b]">
                            <i class="fa-solid fa-video"></i>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-white text-gray-700 border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                            <i class="fa-regular fa-image"></i>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-[#f36565] text-white flex items-center justify-center hover:bg-red-600">
                            <i class="fa-solid fa-record-vinyl"></i>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-white text-gray-700 border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                            <i class="fa-regular fa-comment-dots"></i>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-white text-gray-700 border border-gray-300 flex items-center justify-center hover:bg-gray-100">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <button
                            class="px-4 py-2 rounded-full bg-[#DD464D] hover:bg-[#e74c3c] text-white text-sm font-semibold">
                            End Call
                        </button>
                    </div>

                    <div class="w-16 hidden sm:block"></div>
                </div>
            </div>
        </div>

    </div>







@endsection

@section('script')


@endsection
