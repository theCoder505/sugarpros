@extends('layouts.admin_app')

@section('title', 'Admin Dashboard')

@section('link')

@endsection

@section('styles')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <!-- Patients Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k1.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">Total Active Patients</span>
                        <div class="text-2xl font-bold">{{ $totPatients }}</div>
                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs">
                            <img src="{{ asset('assets/image/' . ($patientsChange >= 0 ? 'itop.png' : 'idwn.png')) }}"
                                class="w-[14px] h-14px" alt="">
                            {{ number_format(abs($patientsChange), 1) }}%
                        </span>
                        <div>
                            <img src="{{ asset('assets/image/c' . ($patientsChange >= 0 ? '1' : '2') . '.png') }}"
                                alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultations Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k2.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">Total Consultations</span>
                        <div class="text-2xl font-bold">{{ $totConsultations }}</div>
                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs">
                            <img src="{{ asset('assets/image/' . ($consultationsChange >= 0 ? 'itop.png' : 'idwn.png')) }}"
                                class="w-[14px] h-14px" alt="">
                            {{ number_format(abs($consultationsChange), 1) }}%
                        </span>
                        <div>
                            <img src="{{ asset('assets/image/c' . ($consultationsChange >= 0 ? '1' : '2') . '.png') }}"
                                alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Queries Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k3.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">AI Queries Processed</span>
                        <div class="text-2xl font-bold">{{ $totAIQueries }}</div>
                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs">
                            <img src="{{ asset('assets/image/' . ($aiQueriesChange >= 0 ? 'itop.png' : 'idwn.png')) }}"
                                class="w-[14px] h-14px" alt="">
                            {{ number_format(abs($aiQueriesChange), 1) }}%
                        </span>
                        <div>
                            <img src="{{ asset('assets/image/c' . ($aiQueriesChange >= 0 ? '1' : '2') . '.png') }}"
                                alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescriptions Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between">
                    <div class="space-y-3">
                        <div class="bg-slate-100 w-[45px] h-[45px] p-2 rounded-md">
                            <img src="{{ asset('assets/image/k4.png') }}" class="w-[28px] h-[28px]" alt="">
                        </div>
                        <span class="text-gray-500 text-sm">Total E-Prescription</span>
                        <div class="text-2xl font-bold">{{ $totPrescriptions }}</div>
                    </div>
                    <div class="space-y-8">
                        <span class="flex py-1 px-2 rounded-md bg-slate-100 text-xs">
                            <img src="{{ asset('assets/image/' . ($prescriptionsChange >= 0 ? 'itop.png' : 'idwn.png')) }}"
                                class="w-[14px] h-14px" alt="">
                            {{ number_format(abs($prescriptionsChange), 1) }}%
                        </span>
                        <div>
                            <img src="{{ asset('assets/image/c' . ($prescriptionsChange >= 0 ? '1' : '2') . '.png') }}"
                                alt="Chart" class="max-w-[105px] h-[60px]">
                        </div>
                    </div>
                </div>
            </div>


            <!-- Last Activity Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="space-y-3">
                        <div class="bg-cyan-50 w-[45px] h-[45px] p-2 rounded-md flex items-center justify-center">
                            <i class="fas fa-bolt text-cyan-500 text-xl"></i>
                        </div>
                        <span class="text-gray-500 text-sm">Last Activity</span>
                        <div class="text-sm font-normal text-gray-700 mt-1">
                            {{ Auth::guard('admin')->user()->last_activity
                                ? \Carbon\Carbon::parse(Auth::guard('admin')->user()->last_activity)->diffForHumans()
                                : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100">
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-info-circle text-cyan-400 mr-2"></i>
                        <span>Recent activity for your account</span>
                    </div>
                </div>
            </div>


            <!-- Last Login Card -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="space-y-3">
                        <div class="bg-blue-50 w-[45px] h-[45px] p-2 rounded-md flex items-center justify-center">
                            <i class="fas fa-user-clock text-blue-500 text-xl"></i>
                        </div>
                        <span class="text-gray-500 text-sm">Your Last Login</span>
                        <div class="text-sm font-normal text-gray-700 mt-1">
                            {{ Auth::guard('admin')->user()->last_login_time
                                ? \Carbon\Carbon::parse(Auth::guard('admin')->user()->last_login_time)->format('jS F Y, g:i A')
                                : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-2 border-t border-gray-100">
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        <span>Last login time for your account</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Chart + Appointments -->
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-[16px]">User Growth Overview</h3>
                        <p class="text-sm text-slate-600">New user registrations over time</p>
                    </div>
                    <select id="chartPeriod" class="p-1 text-sm border rounded">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div class="relative h-64">
                    <canvas id="userChart"></canvas>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Total Users: {{ $totPatients }}</span>
                    <span>Last 30 Days: {{ $last30DaysUsers }}</span>
                    <span>Active Today: {{ $activeTodayUsers }}</span>
                </div>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="">
                        <h3 class="font-bold text-[16px]">Upcoming Appointments</h3>
                        <p class="text-sm text-slate-600">Overview of Appointments</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2 max-h-[350px] overflow-y-auto">
                    @forelse ($upcomingAppointments as $item)
                        <div class="bg-white rounded-xl border border-slate-300 p-4 shadow-sm relative">
                            <div class="flex flex-col sm:flex-row justify-between text-sm mb-2 gap-2">
                                <div>
                                    <p class="text-slate-500">Name</p>
                                    @forelse ($all_providers as $provider)
                                        @if ($item->provider_id == $provider->provider_id)
                                            <h2 class="text-[#000000] font-bold">{{ $provider->name }}</h2>
                                        @endif
                                    @empty
                                    @endforelse

                                    <div class="my-4">
                                        <p class="text-slate-500">Patient ID: {{ $item->patient_id }} </p>
                                        <p class="text-slate-500">Provider ID: {{ $item->provider_id }} </p>
                                    </div>
                                </div>

                                <span class="flex items-center gap-1 text-xs sm:text-sm">
                                    <i class="fas fa-circle text-[#2889AA] text-[10px]"></i>
                                    {{ \Carbon\Carbon::parse($item->time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($item->date)->format('jS F Y') }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                                @forelse ($all_providers as $provider)
                                    @if ($item->provider_id == $provider->provider_id)
                                        <div
                                            class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                            <span class="truncate">{{ $provider->email }}</span>
                                        </div>

                                        <div
                                            class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                                            <i class="fas fa-phone text-gray-400"></i>
                                            <span>{{ $provider->prefix_code }} {{ $provider->mobile }}</span>
                                        </div>
                                    @endif
                                @empty
                                @endforelse

                            </div>
                            @if ($item->meet_link == 'scheduled')
                                <div
                                    class="flex items-center justify-between bg-[#DBEAFE] text-gray-800 px-3 sm:px-4 py-2 rounded-[42px] mt-3 text-xs sm:text-sm font-semibold">
                                    <p class="text-blue-600">
                                        Join Meeting:
                                        <a href="{{ $meeting_web_root_url . '/room/' . $item->appointment_uid }}"
                                            class="truncate hover:underline" target="_blank">
                                            {{ $meeting_web_root_url . '/room/' . $item->appointment_uid }}
                                        </a>
                                    </p>
                                    <div
                                        class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                                        <i class="fa-solid fa-video text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                                    </div>
                                </div>
                            @else
                                <div class="relative w-full left-0 bottom-0 p-4">
                                    <div
                                        class="text-center bg-[#DBEAFE] px-3 sm:px-4 py-4 rounded-[42px] mt-3 text-lg sm:text-sm font-semibold text-blue-500">
                                        <i class="fas fa-exclamation-triangle"></i> Meeting not scheduled yet!
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <h3 class="text-center text-gray-500 font-medium text-2xl h-48 flex items-center justify-center">
                            No Upcoming Appointment!
                        </h3>
                    @endforelse
                </div>
            </div>
        </section>


        <section class="grid grid-cols-1 gap-4 md:grid-cols-1">
            {{-- <div class="p-4 bg-white rounded shadow">
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

            </div> --}}

            @include('includes.patient_management_record')
        </section>


        {{-- @include('includes.api_health_integration') --}}


        <div class="h-12"></div>

    </main>
@endsection



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/patient_records.js"></script>


    <script>
        $(".emr").addClass("font-semibold");


        $(document).ready(function() {
            // Initialize chart with monthly data
            const ctx = document.getElementById('userChart').getContext('2d');
            const userChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'New Users',
                        data: @json($chartData),
                        borderColor: '#2889AA',
                        backgroundColor: 'rgba(40, 137, 170, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#2889AA',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1E293B',
                            titleFont: {
                                size: 12,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Handle period change
            $('#chartPeriod').change(function() {
                const period = $(this).val();

                // Show loading state
                userChart.data.labels = ['Loading...'];
                userChart.data.datasets[0].data = [0];
                userChart.update();

                // AJAX call to get new data
                $.ajax({
                    url: '{{ route('admin.getUserChartData') }}',
                    method: 'GET',
                    data: {
                        period: period
                    },
                    success: function(response) {
                        userChart.data.labels = response.labels;
                        userChart.data.datasets[0].data = response.data;
                        userChart.update();
                    },
                    error: function() {
                        alert('Failed to load chart data');
                    }
                });
            });
        });
    </script>
@endsection
