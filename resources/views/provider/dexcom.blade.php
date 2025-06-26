@extends('layouts.provider')

@section('title', 'Dexcom')

@section('link')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.1.0"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@endsection

@section('style')

    <style>
        .flex.justify-between.items-center.mt-4.px-2 {
            display: none;
        }


        .dashboard {
            font-weight: 500;
            color: #000000;
        }


        table.dataTable thead th:first-child {
            border-left: 2px solid #CBD5E1;
        }

        table.dataTable thead th:last-child {
            border-right: 2px solid #CBD5E1;
        }



        table.dataTable.display>tbody>tr.odd>.sorting_1,
        table.dataTable.order-column.stripe>tbody>tr.odd>.sorting_1 {
            box-shadow: none !important;
        }

        table.dataTable.display tbody tr:hover>.sorting_1,
        table.dataTable.order-column.hover tbody tr:hover>.sorting_1 {
            box-shadow: none !important;
        }

        table.dataTable.hover>tbody>tr:hover>*,
        table.dataTable.display>tbody>tr:hover>* {
            box-shadow: none !important;
        }

        table.dataTable.stripe>tbody>tr.odd>*,
        table.dataTable.display>tbody>tr.odd>* {
            box-shadow: none !important;
        }


        table.dataTable.display>tbody>tr.even>.sorting_1,
        table.dataTable.order-column.stripe>tbody>tr.even>.sorting_1 {
            box-shadow: none !important;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            padding: 16px !important;
            border-bottom: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2889AA;
            color: white !important;
            border: 1px solid #2889AA;
        }

        table.dataTable thead th,
        table.dataTable thead td,
        table.dataTable tfoot th,
        table.dataTable tfoot td {
            text-align: unset;
            font-weight: initial;
            text-transform: none !important;
        }



        
        table.dataTable.display>tbody>tr>td {
            padding: 1rem !important;
        }
    </style>

@endsection

@section('content')

    @include('layouts.provider_header')


    <div class="py-4 bg-gray-100">
        <div class="max-w-6xl mx-auto space-y-6">
            <h2 class="px-5 text-xl font-semibold ">Dexcom/Libre</h2>
            <div class="grid grid-cols-1 gap-6 p-3 bg-white md:grid-cols-3 rounded-xl">
                <!-- Left Card -->
                <div class="max-w-sm p-4 mx-auto">
                    <div
                        class="rounded-[12px] border border-[#D1D5DB] bg-[#FFFFFF] shadow-sm px-6 py-8 flex flex-col items-center">

                        <!-- Glucose Circle with Pointer -->
                        <div class="relative flex items-center justify-center pr-5 bg-[#FFFFFF] border shadow-xl w-52 h-52"
                            style="border-radius: 60% 275% 298% 244%; box-shadow: 0px 4px 48px 0px #00000021;">
                            <div class="flex flex-col items-center justify-center bg-[#FFFFFF]rounded-full w-36 h-36"
                                style="box-shadow: 0px 4px 48px 0px #00000012; border-radius: 50%;">
                                <div class="text-[44px] font-bold text-black">6.1</div>
                                <div class="text-sm text-gray-500">mmol/L</div>
                            </div>

                            <div class="absolute transform -translate-y-1/2 w-0 h-0 border-y-[14px] border-y-transparent border-l-[20px] border-l-black"
                                style="top: 22px;left: 18px; rotate: -12deg;">
                            </div>
                        </div>

                        <div class="mt-6 text-[16px]  leading-snug text-[#141414] text-center ">
                            Manage Your<br />
                            Glucose Reading
                        </div>
                    </div>
                </div>




                <!-- Right Chart -->
                <div class="p-6 bg-white md:col-span-2 rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold">Glucose Reading</h3>
                        <div class="flex gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                Name here
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                Name here
                            </div>
                        </div>
                    </div>
                    <canvas id="glucoseChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="grid max-w-6xl grid-cols-1 gap-6 p-3 mx-auto mt-10 md:p-0 lg:grid-cols-3">


            <div class="col-span-2 bg-white shadow-md rounded-xl">
                <div class="flex items-center justify-between gap-1 p-3 mb-4 md:p-6">
                    <h2 class="text-xl text-[#000000] font-semibold">History</h2>
                    <div class="flex space-x-2">
                        <div class="relative border rounded-[12px] border-slate-200">
                            <i
                                class="absolute text-lg text-gray-900 transform -translate-y-1/2 fas fa-search left-3 top-1/2"></i>
                            <input id="customSearch" type="text" placeholder="Search here..."
                                class="w-full placeholder:text-gray-900 pl-10 py-4 pr-3 bg-white text-sm rounded-[12px] focus:outline-none">
                        </div>

                        <button
                            style="display: flex;padding: 14px 35px;gap: 6px;border: 1px solid #e2e8f0;border-radius: 12px;">
                            <img src="{{ asset('assets/image/fill.png') }}" alt="" class="w-5 ">
                            Filter</button>
                    </div>
                </div>

                <div class="px-4 overflow-x-auto">
                    <table id="historyTable" class="w-full text-sm text-left display">
                        <thead
                            class="text-[#00000080] border-l border-r border-slate-300 uppercase font-[400] bg-[#F7F9FB]">
                            <th class="px-1 py-2">Patient Name</th>
                            <th class="px-1 py-2">Start Date</th>
                            <th class="px-1 py-2">End Date</th>
                            <th class="px-1 py-2">Time</th>
                            <th class="px-1 py-2">Glucose Level</th>
                            <th class="px-1 py-2">Trend</th>
                        </thead>
                        <tbody>

                            <tr class="border-b">
                                <td class="px-4 py-4 ">William John</td>
                                <td class="px-4 py-4">02 July, 2024</td>
                                <td class="px-4 py-4">05 July, 2024</td>
                                <td class="px-4 py-4">12:48 PM</td>
                                <td class="px-4 py-4">110 mg</td>
                                <td class="flex items-center px-4 py-4 text-red-500">
                                    <img src="{{ asset('assets/image/top.png') }}" class="w-4 " alt="">
                                    Rising
                                </td>
                            </tr>

                            <tr class="border-b">
                                <td class="px-4 py-4 ">William John</td>
                                <td class="px-4 py-4">02 July, 2024</td>
                                <td class="px-4 py-4">05 July, 2024</td>
                                <td class="px-4 py-4">12:48 PM</td>
                                <td class="px-4 py-4">110 mg</td>
                                <td class="flex items-center px-4 py-4 text-green-500">
                                    <img src="{{ asset('assets/image/bottom.png') }}" class="w-4 " alt="">
                                    Falling
                                </td>
                            </tr>

                            <tr class="border-b">
                                <td class="px-4 py-4 ">William John</td>
                                <td class="px-4 py-4">02 July, 2024</td>
                                <td class="px-4 py-4">05 July, 2024</td>
                                <td class="px-4 py-4">12:48 PM</td>
                                <td class="px-4 py-4">110 mg</td>
                                <td class="flex items-center px-4 py-4 text-red-500">
                                    <img src="{{ asset('assets/image/top.png') }}" class="w-4 " alt="">
                                    Rising
                                </td>
                            </tr>

                            <tr class="border-b">
                                <td class="px-4 py-4 ">William John</td>
                                <td class="px-4 py-4">02 July, 2024</td>
                                <td class="px-4 py-4">05 July, 2024</td>
                                <td class="px-4 py-4">12:48 PM</td>
                                <td class="px-4 py-4">110 mg</td>
                                <td class="flex items-center px-4 py-4 text-green-500">
                                    <img src="{{ asset('assets/image/bottom.png') }}" class="w-4 " alt="">
                                    Falling
                                </td>
                            </tr>

                            <tr class="border-b">
                                <td class="px-4 py-4 ">William John</td>
                                <td class="px-4 py-4">02 July, 2024</td>
                                <td class="px-4 py-4">05 July, 2024</td>
                                <td class="px-4 py-4">12:48 PM</td>
                                <td class="px-4 py-4">110 mg</td>
                                <td class="flex items-center px-4 py-4 text-red-500">
                                    <img src="{{ asset('assets/image/top.png') }}" class="w-4 " alt="">
                                    Rising
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Connections Panel -->
            <div class="p-6 bg-white shadow-md rounded-xl">
                <h2 class="mb-4 text-xl font-semibold">Connections</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Dexcom</span>
                        <span class="font-semibold text-[#FF6400]">Connected</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Last Sync</span>
                        <span class="text-[#000000] font-medium">April 25, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Time</span>
                        <span class="text-[#000000] font-medium">10:42 AM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor ID</span>
                        <span class="text-[#000000] font-medium">L687BD</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Battery Level</span>
                        <span class="text-[#000000] font-medium">Low</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor Start Date</span>
                        <span class="text-[#000000] font-medium">April 25, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor End Date</span>
                        <span class="text-[#000000] font-medium">April 25, 2025</span>
                    </div>
                    <div class="pt-4">
                        <button class="px-4 py-2 text-white transition rounded-md bg-[#2889AA] hover:bg-cyan-700">
                            Connect New Device
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>









@endsection

@section('script')
    <script>
        const ctx = document
            .getElementById("glucoseChart")
            .getContext("2d");

        const glucoseChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: [
                    "3 hrs",
                    "4 hrs",
                    "5 hrs",
                    "6 hrs",
                    "7 hrs",
                    "8 hrs",
                    "9 hrs",
                    "10 hrs",
                    "11 hrs",
                    "12 hrs",
                ],
                datasets: [{
                        label: "Name here",
                        data: [
                            3.5, 4, 4.8, 5.3, 5.2, 5.5, 5.4, 5.3, 5.2, 5,
                        ],
                        borderColor: "#22c55e",
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        tension: 0.4,
                    },
                    {
                        label: "Name here",
                        data: [
                            3, 3.6, 4.5, 5.2, 5.3, 6, 7.8, 8.5, 7.9, 8.8,
                        ],
                        borderColor: "#ef4444",
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                    },
                ],
            },
            // options: {
            //     responsive: true,
            //     maintainAspectRatio: false,

            //     animation: {
            //         duration: 0,
            //     },
            //     transitions: {
            //         active: {
            //             animation: {
            //                 duration: 0,
            //             },
            //         },
            //         show: {
            //             animations: {
            //                 x: { duration: 0 },
            //                 y: { duration: 0 },
            //             },
            //         },
            //         hide: {
            //             animations: {
            //                 x: { duration: 0 },
            //                 y: { duration: 0 },
            //             },
            //         },
            //     },

            //     scales: {
            //         y: {
            //             min: 2,
            //             max: 9,
            //             title: {
            //                 display: true,
            //                 text: "mmol/L",
            //             },
            //         },
            //     },
            //     plugins: {
            //         tooltip: {
            //             animation: false,
            //             callbacks: {
            //                 label: (ctx) => `${ctx.raw} mmol/L`,
            //             },
            //         },
            //     },
            // },
        });
    </script>



    <script>
        $(document).ready(function() {
            var table = $('#historyTable').DataTable({
                paging: true,
                info: false,
                searching: true, // will be overridden by custom
                language: {
                    search: "", // remove built-in label
                    searchPlaceholder: "Search...",
                },
                dom: 't<"flex justify-between items-center mt-4 px-2"p>',
            });

            // Connect custom search box
            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>


@endsection
