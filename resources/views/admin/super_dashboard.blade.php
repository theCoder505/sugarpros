@extends('layouts.admin_app')

@section('title', 'Admin Dashboard')

@section('link')

@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    
    <style>
        @import url('https://fonts.cdnfonts.com/css/geist');

        body {
            font-family: 'Geist', sans-serif;
        }

        #apiTable_filter {
            margin-bottom: 2rem;
        }


        .odd,
        .even {
            background: black;
        }



        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply px-2 py-1 rounded bg-gray-100 mx-1 text-sm;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-blue-600 text-white;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto mt-8 space-y-6 md:max-w-6xl">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 px-2 md:px-0">


            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">

                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k1.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">Total Active Patients</span>

                        <div class="text-2xl font-bold">1,350</div>

                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs"><img
                                src="{{ asset('assets/image/itop.png') }}" class="w-[14px] h-14px" alt="">
                            20.9%</span>

                        <div class="">
                            <img src="{{ asset('assets/image/c1.png') }}" alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>



            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">

                <div class="flex justify-between">
                    <div class=" space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k2.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">Total Consultations</span>

                        <div class="text-2xl font-bold">20</div>

                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs"><img
                                src="{{ asset('assets/image/idwn.png') }}" class="w-[14px] h-14px" alt="">
                            20.9%</span>

                        <div class="">
                            <img src="{{ asset('assets/image/c2.png') }}" alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>



            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">

                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k3.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">AI Queries Processed</span>

                        <div class="text-2xl font-bold">590</div>

                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs"><img
                                src="{{ asset('assets/image/itop.png') }}" class="w-[14px] h-14px" alt="">
                            20.9%</span>

                        <div class="">
                            <img src="{{ asset('assets/image/c1.png') }}" alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>



            </div>

            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">

                <div class="flex justify-between">
                    <div class=" space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k4.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">New Proscription</span>

                        <div class="text-2xl font-bold">20</div>

                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs"><img
                                src="{{ asset('assets/image/idwn.png') }}" class="w-[14px] h-14px" alt="">
                            20.9%</span>

                        <div class="">
                            <img src="{{ asset('assets/image/c2.png') }}" alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>



            </div>


        </div>


        <!-- Chart + Appointments -->
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="">
                        <h3 class="font-bold text-[16px]">Total User Active</h3>
                        <p class="text-sm text-slate-600">Overview of User Active</p>
                    </div>
                    <select class="p-1 text-sm border rounded">
                        <option>Monthly</option>
                        <option>Weekly</option>
                    </select>
                </div>
                <canvas id="userChart"></canvas>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="">
                        <h3 class="font-bold text-[16px]">Upcoming Appointments</h3>
                        <p class="text-sm text-slate-600">Overview of Appointments</p>
                    </div>
                    <select class="p-1 text-sm border rounded">
                        <option>Monthly</option>
                        <option>Weekly</option>
                    </select>
                </div>
                <div class="mt-4 space-y-2">

                    <div class="p-4 bg-white border shadow-sm rounded-xl border-slate-300">

                        <div class="flex flex-col justify-between gap-2 mb-2 text-sm sm:flex-row">
                            <div>
                                <p class="text-slate-500">name</p>
                                <h2 class="text-[#000000] font-bold">Smith William</h2>
                            </div>

                            <span class="flex items-center gap-1 text-xs sm:text-sm">
                                <i class="fas fa-circle text-[#2889AA] text-[10px]"></i>
                                08:00 PM - 24 May 2024
                            </span>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                            <div
                                class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                <i class="text-gray-400 fas fa-envelope"></i>
                                <span class="truncate">username089@gmail.com</span>
                            </div>

                            <div
                                class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                <i class="text-gray-400 fas fa-phone"></i>
                                <span>+92 3306444299</span>
                            </div>
                        </div>


                        <div
                            class="flex items-center justify-between bg-[#DBEAFE] text-gray-800 px-3 sm:px-4 py-2 rounded-[42px] mt-3 text-xs sm:text-sm font-semibold">
                            <span class="truncate">
                                55 Water Street New York City, while 111 West 57th Street
                            </span>
                            <div
                                class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-white border shadow-sm rounded-xl border-slate-300">

                        <div class="flex flex-col justify-between gap-2 mb-2 text-sm sm:flex-row">
                            <div>
                                <p class="text-slate-500">name</p>
                                <h2 class="text-[#000000] font-bold">Smith William</h2>
                            </div>

                            <span class="flex items-center gap-1 text-xs sm:text-sm">
                                <i class="fas fa-circle text-[#2889AA] text-[10px]"></i>
                                08:00 PM - 24 May 2024
                            </span>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                            <div
                                class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                <i class="text-gray-400 fas fa-envelope"></i>
                                <span class="truncate">username089@gmail.com</span>
                            </div>

                            <div
                                class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                <i class="text-gray-400 fas fa-phone"></i>
                                <span>+92 3306444299</span>
                            </div>
                        </div>


                        <div
                            class="flex items-center justify-between bg-[#DBEAFE] text-gray-800 px-3 sm:px-4 py-2 rounded-[42px] mt-3 text-xs sm:text-sm font-semibold">
                            <span class="truncate">
                                55 Water Street New York City, while 111 West 57th Street
                            </span>
                            <div
                                class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-[16px]">Total User Active</h3>
                    </div>

                </div>
                <div class=" bg-white p-4 space-y-4 ">

                    <!-- Message from Sugarpros AI -->
                    <div class="flex items-start space-x-2">
                        <div class="w-8 h-8 rounded-full bg-[#E3E3E3]"></div>
                        <div>
                            <div class="text-sm text-[#000000] mb-3 font-semibold">Sugarpros AI <span
                                    class="ml-1 text-[12px] font-normal">11:00 PM</span></div>
                            <div
                                class="bg-[#00000012] text-[#000000] text-[16px] px-4 py-2 rounded-lg inline-block max-w-xs break-words">
                                Excepteur sint occaecat cupidatat non proident
                            </div>
                        </div>
                    </div>

                    <!-- Message from user -->
                    <div class="flex items-start justify-end space-x-2">
                        <div>
                            <div
                                class="bg-[#2889AA] text-white  text-[16px] px-4 py-2 rounded-lg inline-block max-w-xs break-words">
                                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                                mollit laborum.
                            </div>
                            <div class="text-[12px] text-[#000000] text-right mt-1">11:00 PM <span>✓✓</span></div>
                        </div>
                    </div>

                    <div class="flex items-start space-x-2">
                        <div class="w-8 h-8 rounded-full bg-[#E3E3E3]"></div>
                        <div>
                            <div class="text-sm text-[#000000] mb-3 font-semibold">Sugarpros AI <span
                                    class="ml-1 text-[12px] font-normal">11:00 PM</span></div>
                            <div
                                class="bg-[#00000012]  text-[16px] text-[#000000] px-4 py-2 rounded-lg inline-block max-w-xs break-words">
                                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center border rounded px-2 py-2 " style="margin-top: 10rem;">
                        <input type="text" placeholder="Type here something..." class="flex-grow outline-none px-2" />
                        <button class="bg-[#2889AA] text-white px-4 py-2 rounded">Send Message</button>
                    </div>

                </div>

            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="overflow-x-auto bg-white rounded-lg p-4">

                    <div class="mb-4">
                        <h2 class="text-xl font-semibold">Patient Management Record</h2>

                    </div>



                    <table id="pa_Table" class="display w-full text-sm text-left">
                        <thead class="pt-4 bg-[#F3F4F6] ">
                            <tr>
                                <th class="text-left">Patient Name</th>
                                <th class="text-left">Unique ID</th>
                                <th class="text-left">DOB</th>
                                <th class="text-left">Age</th>
                                <th class="text-left">Gender</th>
                                <th class="text-left">Patient Results</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-[#000000] bg-[#FFFFFF]">
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>William John</td>
                                <td>YTEJCMFG</td>
                                <td>24 July, 2025</td>
                                <td>40</td>
                                <td>Male</td>
                                <td>
                                    <div class="flex items-center font-bold gap-2 text-[#2889AA]">
                                        <span>View More</span>
                                        <img src="/assets/image/dwon.png" alt="icon" class="" />
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </section>



        <section>
            <div class="my-10 overflow-x-auto bg-white rounded-lg p-4">

                <div class="flex justify-between items-center mb-4 py-4">
                    <h2 class="text-xl font-semibold">API Integration Health</h2>
                    <div class="relative w-1/3">
                        <input type="text" id="tableSearch" placeholder="Search Something..."
                            class="w-full py-2 px-8 pr-10  rounded-lg border border-gray-300 focus:outline-none ">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>



                <table id="apiTable" class="display w-full text-sm text-left">
                    {{--  <h2 class="mb-4 text-xl font-semibold">API Integration Health</h2>  --}}
                    <thead class="pt-4 bg-gray-100">
                        <tr>
                            <th class="text-left">API Name</th>
                            <th class="text-left">Uptime (7D)</th>
                            <th class="text-left">Errors (24h)</th>
                            <th class="text-left">Last Sync</th>
                            <th class="text-left">Status</th>
                            <th class="text-left">Last Response Time</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-[#000000]">
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <!-- 6 more sample rows -->
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>William John</td>
                            <td>99.9%</td>
                            <td>23</td>
                            <td>5 min ago</td>
                            <td>
                                Slow
                            </td>
                            <td>45 min ago</td>
                            <td>
                                <button
                                    class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                                    View Logs
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection



@section('scripts')
    <script>
        $(".emr").addClass("font-semibold");

        
        const menuToggle = document.getElementById("menuToggle");
        const menu = document.getElementById("menu");
        let menuOpen = false;

        menuToggle.addEventListener("click", () => {
            menuOpen = !menuOpen;
            menu.classList.toggle("hidden");

            menuToggle.innerHTML = menuOpen ?
                '<i class="fas fa-times"></i>' :
                '<i class="fas fa-bars"></i>';
        });

        const ctx = document.getElementById("userChart").getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                ],
                datasets: [{
                    label: "Users",
                    data: [
                        0, 1000, 2500, 12000, 15000, 20000,
                        18000, 22000, 30000, 35000, 40000, 45000,
                    ],
                    borderColor: "#3b82f6",
                    backgroundColor: "rgba(59, 130, 246, 0.1)",
                    tension: 0.4,
                    fill: true,
                }, ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return value / 1000000 + "M";
                                if (value >= 1000) return value / 1000 + "k";
                                return value;
                            },
                        },
                    },
                },
            },
        });

        $(document).ready(function() {
            const table = $('#apiTable').DataTable({
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                pageLength: 10,
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                },
                dom: 't<"flex justify-center mt-4"p>',
            });

            $('#tableSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });

        $(document).ready(function() {
            const table = $('#pa_Table').DataTable({
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                pageLength: 10,
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                },
                dom: 't<"flex justify-center mt-4"p>',
            });

            $('#tableSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endsection
