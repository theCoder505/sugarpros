@extends('layouts.provider')

@section('title', 'ai chat')

@section('link')

@endsection

@section('style')

    <style>
        .ai {
            font-weight: 500;
            color: #000000;
        }
    </style>

@endsection


@section('content')

    @include('layouts.provider_header')


    <div class="min-h-screen p-4  bg-gray-100 md:p-6">
        <div class="grid max-w-6xl grid-cols-1 p-4 mx-auto bg-white rounded-xl md:grid-cols-3">

            <div class="flex flex-col col-span-1 px-4 mb-4 border rounded-md md:border-none md:mb-0 md:rounded-none">

                <button class="text-sm text-gray-900 bg-gray-100 py-5 md:mt-0 mt-4 rounded-[12px]">
                    <i class="fa-solid fa-plus"></i> New Chat
                </button>





                <div class="flex-grow ">
                    <h2 class="text-[16px] text-zinc-600 font-semibold my-3">Recent</h2>

                    <div class="overflow-y-auto h-[160px] md:h-[320px] py-4 px-2">
                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item unread">

                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>

                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>

                            </div>
                        </div>


                    </div>

                </div>



                <div class="flex-grow ">
                    <h2 class="text-[16px] text-zinc-600 font-semibold my-3">Last Month</h2>

                    <div class="overflow-y-auto h-[250px] md:h-[420px] py-4 px-2">
                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item unread">

                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between px-5 py-3 mb-2 rounded-lg cursor-pointer chat-item unread hover:bg-gray-100">
                            <div class="flex items-center justify-start gap-3">
                                <img src="{{ asset('assets/image/sms.png') }}" class="w-5 h-5 " alt="">
                                <p class="font-semibold text-[16px] ">Lorem Ipsum Dolor sit Amet</p>
                            </div>
                        </div>


                    </div>

                </div>







            </div>

            <div class="col-span-2 flex flex-col bg-gray-100 rounded-t-[16px] relative">

                <div class=" p-4 overflow-y-auto space-y-4 h-[800px] md:h-[635px]">


                    <div class="flex flex-col items-end gap-1">
                        <p class="bg-[#ffffff] text-[#262626] px-4 py-2 rounded-lg max-w-[440px]">
                            I need testimonials section heading for this website
                        </p>

                    </div>

                    <div class=" max-w-[500px]">

                        <div class="flex gap-3 ">
                            <img src="{{ asset('assets/image/aim.png') }}" class="rounded-full w-9 h-9">
                            <h2 class="text-[16px] text-[#000000]">Here are some suggestions for a testimonials section
                                heading for the Kingdom Culture Mindset
                                course website:</h2>
                        </div>

                        <div class="mx-5">



                            <ul class="px-10 py-2 mt-2 list-decimal font-semibold text-[16px]">

                                <li>"What Our Students Are Saying"</li>
                                <li>"Hear From Our Kingdom Community"</li>
                                <li>"Real Stories of Mindset Transformation"</li>
                                <li>"Life-Changing Experiences"</li>
                                <li>"Voices of Transformation"</li>
                                <li>"Discover the Impact of Kingdom Culture"</li>
                                <li>"Faith-Driven Transformations"</li>
                                <li>"Empowered Minds, Changed Lives"</li>
                                <li>"Testimonials from Our Kingdom Family"</li>
                            </ul>

                            <p class="ml-4 text-[16px]">Let me know if you d like a more specific tone or focus!</p>

                            <div class="flex mt-3 ml-5 space-x-4">
                                <div class="cursor-pointer ">
                                    <img src="{{ asset('assets/image/spiker.png') }}" alt="">
                                </div>
                                <div class="cursor-pointer ">
                                    <img src="{{ asset('assets/image/copy.png') }}" alt="">
                                </div>

                                <div class="cursor-pointer ">
                                    <img src="{{ asset('assets/image/like.png') }}" alt="">
                                </div>

                                <div class="cursor-pointer ">
                                    <img src="{{ asset('assets/image/dislike.png') }}" alt="">
                                </div>

                                <div class="cursor-pointer ">
                                    <img src="{{ asset('assets/image/Frame(1).png') }}" alt="">
                                </div>
                            </div>




                        </div>

                    </div>




                </div>

                <div class="absolute bottom-0 w-full px-4 mb-4 sm:px-8">
                    <div class="p-4  rounded-[10px] bg-white gap-2">

                        <textarea class="w-full px-4 py-2 rounded-md resize-none focus:outline-none" id=""
                            placeholder="Ask as anything here..." cols="30" rows="6"></textarea>

                        <div class="flex items-center justify-between w-full">


                            <input type="file" id="fileInput" class="hidden">


                            <button type="button" onclick="document.getElementById('fileInput').click()"
                                class="flex items-center text-sm ml-2 px-3 py-3 rounded-md border bg-white border-[#E5E5E5] space-x-1">
                                <img src="{{ asset('assets/image/atta.png') }}" alt="">
                                Add attachment
                            </button>


                            <button class="ml-2 px-4 py-2 bg-[#2889AA] text-white rounded-lg hover:bg-opacity-80">

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

@endsection
