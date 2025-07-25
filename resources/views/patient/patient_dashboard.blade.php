@extends('layouts.patient_portal')

@section('title', 'patient dashboard')

@section('link')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        .dashboard {
            font-weight: 500;
            color: #000000;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding-left: 32px;
            margin-left: 0;
            border: none;
            border-radius: 10px;
            height: 40px;
            background: white;
            min-width: 250px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info {
            padding: 10px 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 2px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2889AA;
            color: white !important;
            border: 1px solid #2889AA;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            padding: 20px;
            border-bottom: 0px;
            font-weight: 500;
            font-size: 1rem;
            background: #ffffff;
        }

        table.dataTable tbody td {
            padding: 1rem;
            background: #f3f4f6;
            border-bottom: 1px solid #ddd;
        }

        table.dataTable.no-footer {
            border-bottom: none !important;
        }

        .bottom {
            padding: 10px 1rem;
        }

        #appointmentsTable_filter {
            position: relative;
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: none;
            margin-right: 1rem;
        }

        .appointments_text {
            position: absolute;
            top: 1.3rem;
            left: 1rem;
        }

        .appointments_null_text {
            top: 1.3rem;
            left: 1rem;
        }

        /* Status badge styling */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="min-h-screen bg-gray-100 p-4 md:p-6 font-sans">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Sidebar -->
            <div class="space-y-6 lg:col-span-1">
                <div class="text-xl font-semibold mb-6">Patient Dashboard</div>

                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="font-semibold text-20px mb-6">Profile</h3>
                    <div class="flex flex-col items-center text-center">
                        @php
                            $src = Auth::user()->profile_picture
                                ? '/' . Auth::user()->profile_picture
                                : '/assets/image/dummy_user.png';
                        @endphp

                        <img src="{{ $src }}" class="w-[170px] h-[170px] rounded-full mb-2" alt="Profile">
                        <h2 class="text-2xl font-semibold">{{ Auth::user()->name }}</h2>
                        <p class="text-sm text-gray-500">{{ Auth::user()->patient_id }}</p>

                        <div class="mt-[3rem] w-full">
                            <a href="/account"
                                class="block w-full text-center px-4 py-2 bg-[#2889AA] text-white rounded hover:bg-cyan-800 text-[16px] font-bold">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sugar Overview -->
                <div class="bg-white p-4 rounded-lg shadow space-y-2">
                    <h3 class="text-[20px] font-semibold text-[#000000] mb-4 border-b border-[#0000001A]/10 pb-2">
                        Sugar Overview
                    </h3>
                    <div class="text-gray-700 text-base mb-8">
                        Monitoring your blood sugar is essential for managing diabetes. Devices like Dexcom provide real-time glucose readings, helping you track trends and make informed decisions about your health. Connect your Dexcom or Libre device to view detailed sugar level progress and insights here.
                    </div>

                    <div class="mt-[3rem]">
                        <a href="/dexcom"
                            class="block w-full text-center px-4 py-2 bg-[#2889AA] text-white rounded hover:bg-cyan-800 text-[16px] font-bold">
                            View Progress On Dexcom/Libre
                        </a>
                    </div>
                </div>

                <!-- Chat Inbox -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-20px mb-6 unseen_mesages">Inbox ( <span class="">{{$total_unread}}</span> Unread)</h3>
                    </div>

                    <div class="relative w-full mb-3">
                        <i
                            class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Search here..."
                            class="w-full pl-10 pr-3 bg-gray-100 py-2 text-sm border rounded-md border-gray-300 focus:outline-none"
                            onkeyup="searchList(this)" />
                    </div>

                    <p class="text-gray-500 font-semibold text-center py-4 my-4 no_match hidden">
                        No Match Found
                    </p>

                    <div class="dashboard_message_list users_list space-y-2">
                        @forelse ($related_providers as $provider)
                            <a href="/send-to-chats/provider/{{$provider->provider_id}}" class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item
                                @if ($provider->message_status != 'seen' && $provider->is_sender != Auth::user()->patient_id) unread @endif"
                                data-id="{{ $provider->provider_id }}">

                                <div class="flex items-center gap-3 provider_details">
                                    <div class="relative w-10 h-10 overflow-hidden image_section">
                                        @if (!empty($provider->profile_picture))
                                            <img src="{{ asset($provider->profile_picture) }}"
                                                class="w-full rounded-full" />
                                            <img src="{{ asset('assets/image/act.png') }}"
                                                class="absolute bottom-0 right-0" />
                                        @else
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                                                {{ strtoupper(substr($provider->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="name_section">
                                        <p class="font-semibold text-[16px] provider_name">
                                            <span class="naming">{{ $provider->name }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600 patient_message">
                                            @if ($provider->latest_message)
                                                @if ($provider->is_sender)
                                                    You:
                                                @endif
                                                @if ($provider->message_type == 'image')
                                                    sent a picture
                                                @else
                                                    {{ Str::limit($provider->latest_message, 20, '...') }}
                                                @endif
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end timeandseen">
                                    @if ($provider->message_time)
                                        <span class="text-xs text-gray-400 msg_time">
                                            {{ \Carbon\Carbon::parse($provider->message_time)->format('g:i A') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center mt-1">
                                        @if ($provider->message_status == 'seen')
                                            <i class="fas fa-check-double text-[#2889AA] text-xs"></i>
                                        @elseif($provider->is_sender)
                                            <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                                        @endif
                                    </span>
                                    @if ($provider->unread_count > 0 && !$provider->is_sender)
                                        <span
                                            class="flex items-center justify-center w-5 h-5 mt-1 text-xs text-white bg-orange-500 rounded-full related_unread">
                                            {{ $provider->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="px-2 py-5 text-gray-500">No providers found in your pod</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Appointments + Preferences -->
            <div class="lg:col-span-3">
                <div class="text-xl font-semibold mb-6">Welcome, {{ Auth::user()->name }}</div>

                <div class="space-y-6 p-6 bg-white rounded-md">
                    <div class="bg-gray-100 rounded-lg shadow relative">
                        @if ($appointments->isEmpty())
                            <div class="md:flex justify-between items-center">
                                <h3 class="text-[20px] font-semibold text-[#000000] appointments_null_text">
                                    Appointments
                                </h3>
                            </div>
                        @else
                            <div class="md:flex justify-between items-center appointments_text">
                                <h3 class="text-[20px] font-semibold text-[#000000]">
                                    Appointments
                                </h3>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table id="appointmentsTable" class="w-full text-sm text-left">
                                <thead class="bg-[#ffffff] text-[#00000080]/50">
                                    <tr>
                                        <th class="px-4 py-4">#</th>
                                        <th class="px-4 py-4">Appointment UID</th>
                                        <th class="px-4 py-4">Provider</th>
                                        <th class="px-4 py-4">Date</th>
                                        <th class="px-4 py-4">Time</th>
                                        <th class="px-4 py-4">Status</th>
                                        <th class="px-4 py-4">View Details</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-[#000000]">
                                    @forelse ($appointments as $key => $item)
                                        @php
                                            $appointmentDateTime = \Carbon\Carbon::parse(
                                                $item->date . ' ' . $item->time,
                                            );
                                            $gracePeriodEnd = $appointmentDateTime->copy()->addHour();
                                        @endphp
                                        <tr class="border-b border-[#000000]/10">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->appointment_uid }}</td>
                                            <td class="px-4 py-4">
                                                @if ($item->provider_id == null)
                                                    <span class="text-gray-400">&mdash;</span>
                                                @else
                                                    @forelse ($all_providers as $provider)
                                                        @if ($item->provider_id == $provider->provider_id)
                                                            <span class="text-[#000000]">{{ $provider->name }}</span>
                                                            @break
                                                        @endif
                                                    @empty
                                                        <span class="text-gray-400">Provider Not Found</span>
                                                    @endforelse
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">{{ $appointmentDateTime->format('jS F Y') }}</td>
                                            <td class="px-4 py-4">{{ $appointmentDateTime->format('g:i A') }}</td>
                                            <td class="px-4 py-4">
                                                @if ($item->status == 0)
                                                    @if ($appointmentDateTime->isFuture())
                                                        <span class="status-badge bg-blue-100 text-blue-800">Upcoming</span>
                                                    @elseif ($gracePeriodEnd->isFuture())
                                                        @if ($item->meet_link)
                                                            <span class="status-badge bg-blue-100 text-blue-800">Waiting To
                                                                Start</span>
                                                        @else
                                                            <span class="status-badge bg-yellow-100 text-yellow-800">Grace
                                                                Period (1hr)</span>
                                                        @endif
                                                    @else
                                                        @if ($item->meet_link)
                                                            <span class="status-badge bg-red-100 text-red-800">
                                                                Provider Absent
                                                            </span>
                                                        @else
                                                            <span class="status-badge bg-red-100 text-red-800">
                                                                Did Not Set
                                                            </span>
                                                        @endif
                                                    @endif
                                                @elseif ($item->status == 1)
                                                    <span class="status-badge bg-blue-100 text-blue-800">Started</span>
                                                @elseif ($item->status == 5)
                                                    <span class="status-badge bg-green-100 text-green-800">Completed</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <a href="/patient/show-appointment/{{ $item->appointment_uid }}"
                                                    class="px-4 py-1 bg-[#f6028b] text-white text-center max-w-[150px] rounded-full text-[0.70rem]">Click
                                                    Here</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-400">
                                                No Appointments Yet!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- User Preferences -->
                    <div class="bg-gray-100 rounded-md p-6">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-[20px] font-semibold text-[#000000]">
                                User Profile & Preferences Appointments
                            </h3>
                            <input type="hidden" name="token" class="token" value="{{ csrf_token() }}">
                            <div class="space-y-4 mt-4 text-sm">
                                <div class="flex justify-between items-center border-b pb-2 border-[#000000]/10">
                                    <span>HIPAA Consent</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" value="" class="sr-only peer"
                                            @if ($Consent == true) checked @endif
                                            onchange="hippaConsent(this)" />
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-300 peer-checked:bg-green-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:left-[6px] after:top-[4px] after:bg-white after:border-gray-300 after:rounded-full after:h-4 after:w-4 after:transition-all">
                                        </div>
                                    </label>
                                </div>
                                <div class="flex justify-between items-center border-b pb-2 border-[#000000]/10">
                                    <span>Notification Settings</span>
                                    <span class="text-sm text-gray-600 capitalize">{{ $notificationMethod }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Language Selection</span>
                                    <select class="text-sm border rounded-md px-2 py-1" onchange="changeLanguage(this)">
                                        @php
                                            $options = json_decode($languages, true);
                                        @endphp
                                        @if (is_array($options))
                                            @foreach ($options as $language)
                                                <option value="{{ $language }}"
                                                    {{ $userLang == $language ? 'selected' : '' }}>{{ $language }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable({
                "pagingType": "simple_numbers",
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search appointments...",
                    "paginate": {
                        "previous": "←",
                        "next": "→"
                    }
                },
                "dom": '<"top"f>rt<"bottom"lip><"clear">',
                "initComplete": function() {
                    // Add the search icon to the search input
                    $('.dataTables_filter input').before(
                        '<i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>'
                    );
                    $('.dataTables_filter input').addClass('pl-8');
                }
            });
        });

        function hippaConsent(checkbox) {
            const isChecked = checkbox.checked;
            const token = document.querySelector('.token').value;

            fetch('/hippa-consent-prefference', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        consent: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.type == 'success') {
                        toastr.success(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    checkbox.checked = !isChecked;
                });
        }

        function changeLanguage(select) {
            const language = select.value;
            const token = document.querySelector('.token').value;

            fetch('/change-language-prefference', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        language: language
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.type == 'success') {
                        toastr.success(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

    <script src="/assets/js/chat_works.js"></script>
@endsection
