@extends('layouts.patient_portal')

@section('title', 'appointment-list')

@section('link')

@endsection

@section('style')
    <style>
        .appointment {
            font-weight: 500;
            color: #000000;
        }


        .litepicker {
            top: 160px !important;
            right: 160px !important;
            left: unset !important;
        }

        @media (max-width: 720px) {

            .litepicker {
                top: 252px !important;
                right: 49px !important;
                left: unset !important;
            }

        }


        .loader {
            border: 3px solid #999;
            border-radius: 50%;
            border-top: 3px solid #fff;
            width: 3.5rem;
            height: 3.5rem;
            -webkit-animation: spin 1s linear infinite;
            /* Safari */
            animation: spin 1s linear infinite;
            margin: 0px auto;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

@endsection


@section('content')

    @include('layouts.patient_header')

    <div class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-7xl mx-auto bg-white p-6 rounded-md">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="text-xl font-semibold">Appointments</div>

                <div class="w-full md:w-auto flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">
                    <button
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700 w-full sm:w-auto">
                        <i class="fa-solid fa-sliders"></i>
                        Filter
                    </button>

                    <div class="relative w-full sm:w-auto">
                        <input type="text" id="dateRangePicker" class="hidden" />
                        <button id="datePickerBtn"
                            class="flex items-center justify-center gap-2 px-4 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700 w-full sm:w-auto">
                            <i class="far fa-calendar-alt"></i>
                            <span id="selectedDate">Select Date Range</span>
                        </button>
                    </div>

                    <input type="hidden" value="{{ csrf_token() }}" class="token">

                    <div class="relative w-full sm:w-auto">
                        <select
                            class="appearance-none pr-8 pl-4 py-2 text-sm border border-gray-300 rounded focus:outline-none  text-gray-700 w-full sm:w-auto cursor-pointer"
                            onchange="appointmentsByMonth(this)">
                            <option value="" selected disabled>Select Month</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="spin_items hidden">
                <div class="loader"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5" id="appointmentList">
                @forelse ($appointments as $item)
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
                                    <a href="{{ '/join-meeting/' . $item->appointment_uid }}"
                                        class="truncate hover:underline" target="_blank">
                                        {{ url('/join-meeting/' . $item->appointment_uid) }}
                                    </a>
                                </p>
                                <div
                                    class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                                    <i class="fa-solid fa-video text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                                </div>
                            </div>
                        @else
                            <div class="absolute w-full left-0 bottom-0 p-4">
                                <div
                                    class="text-center bg-[#DBEAFE] px-3 sm:px-4 py-4 rounded-[42px] mt-3 text-lg sm:text-sm font-semibold text-blue-500">
                                    <i class="fas fa-exclamation-triangle"></i> Meeting not scheduled yet!
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-xl text-red-500 font-semibold text-center welcome col-span-1 md:col-span-2">No
                        Appointments Yet!</p>
                @endforelse

            </div>

        </div>
    </div>




@endsection

@section('script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>
    <script src="{{ asset('assets/js/appointment.js') }}"></script>
@endsection
