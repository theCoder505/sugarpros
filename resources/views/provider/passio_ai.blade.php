@extends('layouts.provider')

@section('title', 'Passio Ai')

@section('link')

@endsection

@section('style')


@endsection


@section('content')
    @include('layouts.provider_header')


    <div class="bg-gray-100 min-h-screen p-6">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <h1 class="text-xl font-semibold text-[#000000]">
                    Passio AI
                </h1>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-1">
                        <input type="text" placeholder="Search..."
                            class="md:w-[350px] pl-10 text-white  bg-[#133A59] pr-4 py-4 rounded-xl  text-sm focus:outline-none placeholder:text-white" />
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                    </div>

                    <select
                        class="w-full sm:w-auto px-4 py-2 text-sm border bg-white border-gray-300 rounded focus:outline-none  text-black">
                        <option>Breakfast</option>
                        <option>Lunch</option>
                        <option>Dinner</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 space-y-5 md:space-y-0 bg-white px-4  py-12 rounded-md">
                {{--  item  --}}
                
                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4">
                    <img src="{{ asset('assets/image/a1.jpg') }}" alt="Food"
                        class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                    <div class="flex-1 px-2 w-full">
                        <h3 class="text-[18px] font-semibold">Roasted Chicken Soup</h3>

                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt=""> 23g
                            </div>
                            <div
                                class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt=""> 23g
                            </div>
                        </div>

                        <p class="text-xs text-[#1C1917] mt-2">
                            1200 <span class="text-[12px] text-[#737373]">kcal</span>
                        </p>
                    </div>

                    <div class="relative w-10 h-10">
                        <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                            <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-green-500" stroke="currentColor" stroke-width="4" stroke-dasharray="80, 100"
                                fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                            80%
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>






@endsection

@section('script')


@endsection
