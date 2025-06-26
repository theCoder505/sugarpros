@extends('layouts.patient_portal')

@section('title', 'Dexcom')

@section('link')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.1.0"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
@endsection

@section('style')
    <style>
        .dexom {
            font-weight: 500;
            color: #000000;
        }
        
        .glucose-circle {
            border-radius: 60% 275% 298% 244%;
            box-shadow: 0px 4px 48px 0px #00000021;
        }
        
        .glucose-inner {
            box-shadow: 0px 4px 48px 0px #00000012;
        }
        
        /* Trend arrows */
        .trend-up { transform: rotate(-45deg); }
        .trend-down { transform: rotate(45deg); }
        .trend-flat { transform: rotate(0deg); }
        
        /* Alert for API errors */
        .dexcom-error {
            background-color: #FEE2E2;
            border-left: 4px solid #DC2626;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #DC2626;
        }
    </style>
@endsection

@section('content')

    @include('layouts.patient_header')

    <div class="py-4 bg-gray-100">
        @if($errors->has('dexcom_error'))
            <div class="max-w-6xl mx-auto dexcom-error">
                {{ $errors->first('dexcom_error') }}
                <a href="{{ route('connect.dexcom') }}" class="font-semibold underline">Reconnect Dexcom</a>
            </div>
        @endif
        
        <div class="max-w-6xl mx-auto space-y-6">
            <h2 class="px-5 text-xl font-semibold ">Dexcom/Libre</h2>
            <div class="grid grid-cols-1 gap-6 p-3 bg-white md:grid-cols-3 rounded-xl">
                <!-- Left Card -->
                <div class="max-w-sm p-4 mx-auto">
                    <div class="rounded-[12px] border border-[#D1D5DB] bg-[#FFFFFF] shadow-sm px-6 py-8 flex flex-col items-center">
                        <!-- Glucose Circle with Pointer -->
                        <div class="relative flex items-center justify-center pr-5 bg-white border shadow-xl w-52 h-52 glucose-circle">
                            <div class="flex flex-col items-center justify-center bg-white rounded-full w-36 h-36 glucose-inner">
                                <div class="text-[44px] font-bold text-black">{{ number_format($latestReading->value, 1) }}</div>
                                <div class="text-sm text-gray-500">mmol/L</div>
                            </div>

                            <div class="absolute transform -translate-y-1/2 w-0 h-0 border-y-[14px] border-y-transparent border-l-[20px] border-l-black"
                                style="top: 22px;left: 18px; rotate: -12deg;">
                            </div>
                        </div>

                        <div class="mt-6 text-[16px] leading-snug text-[#141414] text-center">
                            Manage Your<br />
                            Glucose Reading
                        </div>
                    </div>
                </div>

                <!-- Right Chart -->
                <div class="p-6 bg-white md:col-span-2 rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold">Glucose Reading (Last 12 Hours)</h3>
                        <div class="flex gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                Your Readings
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                Target Range
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
                            <i class="absolute text-lg text-gray-900 transform -translate-y-1/2 fas fa-search left-3 top-1/2"></i>
                            <input type="text" placeholder="Search here..."
                                class="w-full placeholder:text-gray-900 pl-10 py-4 pr-3 bg-white text-sm rounded-[12px] focus:outline-none">
                        </div>
                        <button style="display: flex;padding: 14px 35px;gap: 6px;border: 1px solid #e2e8f0;border-radius: 12px;">
                            <img src="{{ asset('assets/image/fill.png') }}" alt="" class="w-5">
                            Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[#00000080] border-l border-r border-slate-300 uppercase font-[400] bg-[#F7F9FB]">
                            <tr>
                                <th class="px-1 py-2">Time</th>
                                <th class="px-1 py-2">Glucose Level</th>
                                <th class="px-1 py-2">Trend</th>
                                <th class="px-1 py-2">Trend Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $reading)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $reading->time }}</td>
                                <td class="px-4 py-2">{{ number_format($reading->value, 1) }} mmol/L</td>
                                <td class="flex items-center px-4 py-2 {{ $reading->trend === 'rising' ? 'text-red-500' : 'text-green-500' }}">
                                    @if($reading->trend === 'rising')
                                        <svg class="w-4 h-4 trend-up" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                        Rising
                                    @else
                                        <svg class="w-4 h-4 trend-down" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                        Falling
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ number_format($reading->trend_rate, 1) }} mmol/L/min</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Connections Panel -->
            <div class="p-6 bg-white shadow-md rounded-xl">
                <h2 class="mb-4 text-xl font-semibold">Device Information</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Dexcom</span>
                        <span class="font-semibold text-[#FF6400]">Connected</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Last Sync</span>
                        <span class="text-[#000000] font-medium">{{ $deviceInfo->last_sync }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Time</span>
                        <span class="text-[#000000] font-medium">{{ $deviceInfo->sync_time }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor ID</span>
                        <span class="text-[#000000] font-medium">{{ $deviceInfo->sensor_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Battery Level</span>
                        <span class="text-[#000000] font-medium">{{ ucfirst($deviceInfo->battery) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor Start Date</span>
                        <span class="text-[#000000] font-medium">{{ $deviceInfo->start_date }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#00000099]">Sensor End Date</span>
                        <span class="text-[#000000] font-medium">{{ $deviceInfo->end_date }}</span>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('connect.dexcom') }}" class="px-4 py-2 text-white transition rounded-md bg-[#2889AA] hover:bg-cyan-700">
                            Reconnect Device
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const ctx = document.getElementById("glucoseChart").getContext("2d");
        
        // Target range annotations
        const targetRange = {
            type: 'box',
            xScaleID: 'x',
            yScaleID: 'y',
            xMin: 0,
            xMax: '100%',
            yMin: 4.0,
            yMax: 7.8,
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderColor: 'rgba(239, 68, 68, 0.5)',
            borderWidth: 1
        };

        const glucoseChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: "Your Readings",
                    data: @json($chartData['values']),
                    borderColor: "#22c55e",
                    backgroundColor: "transparent",
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    annotation: {
                        annotations: {
                            targetRange
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'mmol/L'
                        },
                        min: 2,
                        max: 12
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                }
            }
        });
    </script>
@endsection