@extends('layouts.provider')

@section('title', 'provider dashboard')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

@section('style')
    <style>
        .dashboard {
            font-weight: 500;
            color: #000000;
        }

        .odd td {
            padding: 1rem 0.5rem !important;
        }

        .even td {
            padding: 1rem 0.5rem !important;
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

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .appointment_type_dropdown {
            position: absolute;
            top: 3.8rem;
            left: 1rem;
            z-index: 1;
        }

        .dataTables_empty {
            padding: 2rem !important;
        }

        @media (max-width: 600px) {
            .appointment_type_dropdown {
                position: relative;
                top: 0;
                left: unset;
                z-index: 1;
                width: 100%;
                display: flex;
                justify-content: center;
                padding-top: 1.5rem;
            }

            #appointmentsTable_filter i {
                position: relative;
                top: 0.7rem;
                left: 1.7rem;
            }
        }
    </style>
@endsection


@section('content')
    @include('layouts.provider_header')

    <div class="min-h-screen p-4 bg-gray-100 md:p-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <!-- Left Sidebar -->
            <div class="space-y-6 lg:col-span-1">
                <div class="mb-6 text-xl font-medium">Provider Dashboard</div>
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="mb-6 font-medium text-20px">Profile</h3>
                    <div class="flex flex-col items-center text-center">
                        @php
                            if (Auth::guard('provider')->user()->profile_picture == null) {
                                $src = '/assets/image/dummy_user.png';
                            } else {
                                $src = '/' . Auth::guard('provider')->user()->profile_picture;
                            }
                        @endphp

                        <img src="{{ $src }}" class="w-[130px] h-[130px] mb-2 rounded-full" alt="Profile">

                        <h2 class="text-2xl font-semibold">{{ Auth::guard('provider')->user()->name }}</h2>
                        <h2 class="text-lg font-semibold text-gray-500">
                            <span class="text-sm text-gray-500 group relative cursor-pointer provider-id-copy"
                                data-provider-id="{{ Auth::guard('provider')->user()->provider_id }}"
                                onclick="copyProviderId(this)">
                                <span
                                    class="absolute left-[-20px] top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white">
                                    <i class="far fa-copy copy_icon text-gray-400"></i>
                                </span>
                                <span class="provider-id-text">{{ Auth::guard('provider')->user()->provider_id }}</span>
                            </span>
                            | POD {{ Auth::guard('provider')->user()->pod_name }}
                        </h2>


                        <div class="w-full">
                            <a href="/provider/account"
                                class="w-full block text-center mt-4 px-4 py-2 bg-[#2889AA] text-white rounded hover:bg-cyan-800 text-[16px]">
                                View Profile
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Chat Inbox -->
                <div class="bg-white p-4 rounded-lg shadow">

                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-20px mb-6 unseen_mesages">Inbox ( <span
                                class="">{{ $total_unread }}</span> Unread)</h3>
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
                        @forelse ($releted_patients as $patient)
                            <a href="/send-to-chats/patient/{{ $patient->patient_id }}"
                                class="flex items-center justify-between px-2 py-5 mb-2 bg-gray-100 rounded-lg cursor-pointer chat-item @if ($patient->message_status != 'seen' && $patient->is_sender != Auth::guard('provider')->user()->provider_id) unread @endif"
                                data-id="{{ $patient->patient_id }}" onclick="showMessage(this)">

                                <div class="flex items-center gap-3 provider_details">
                                    <div class="relative w-10 h-10 overflow-hidden image_section">
                                        @if (!empty($patient->profile_picture))
                                            <img src="{{ asset($patient->profile_picture) }}" class="w-full rounded-full" />
                                            <img src="{{ asset('assets/image/act.png') }}"
                                                class="absolute bottom-0 right-0" />
                                        @else
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold text-lg">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="name_section">
                                        <p class="font-semibold text-[16px] provider_name">
                                            <span class="naming">{{ $patient->name }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600 patient_message">
                                            @if ($patient->latest_message)
                                                @if ($patient->is_sender)
                                                    You:
                                                @endif
                                                @if ($patient->message_type == 'image')
                                                    sent a picture
                                                @else
                                                    {{ Str::limit($patient->latest_message, 20, '...') }}
                                                @endif
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end timeandseen">
                                    @if ($patient->message_time)
                                        <span class="text-xs text-gray-400 msg_time">
                                            {{ \Carbon\Carbon::parse($patient->message_time)->format('H:i') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center mt-1">
                                        @if ($patient->message_status == 'seen')
                                            <i class="fas fa-check-double text-[#2889AA] text-xs"></i>
                                        @elseif($patient->is_sender)
                                            <i class="fas fa-check-double text-md text-[#6c7683]"></i>
                                        @endif
                                    </span>
                                    @if ($patient->unread_count > 0 && !$patient->is_sender)
                                        <span
                                            class="flex items-center justify-center w-5 h-5 mt-1 text-xs text-white bg-orange-500 rounded-full related_unread">
                                            {{ $patient->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="px-2 py-5 text-gray-500">No patients found in your pod</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mb-6 lg:col-span-3">
                <div class="text-xl font-medium mb-6">Welcome, {{ Auth::guard('provider')->user()->name }}</div>

                <div class="space-y-6 p-6 bg-white rounded-md">
                    <div class="bg-gray-100 rounded-lg shadow relative">
                        <div class="space-y-6 bg-[#f3f4f6] rounded-lg lg:col-span-3 overflow-x-auto relative">
                            <div class="md:flex justify-between items-center mx-4 pt-4">
                                <h3 class="text-lg font-medium text-[#000000]">
                                    Appointments
                                </h3>
                            </div>

                            <div class="overflow-x-auto">
                                <div class="appointment_type_dropdown">
                                    <select name="appointment_type" id="appointmentTypeFilter"
                                        class="bg-white px-4 py-2 rounded-lg">
                                        <option id="all-count" value="all">All Appointments ({{ count($appointments) }})</option>
                                        <option id="active-count" value="active" selected>Active Appointments</option>
                                        <option id="upcoming-count" value="upcoming">Upcoming Appointments</option>
                                        <option id="missed-count" value="missed">Missed Appointments</option>
                                        <option id="unset-count" value="unset">Unset Appointments</option>
                                        <option id="complete-count" value="complete">Completed Appointments</option>
                                    </select>
                                </div>

                                <table id="appointmentsTable" class="w-full text-sm text-left">
                                    <thead class="bg-[#ffffff] text-[#00000080]/50 ">
                                        <tr>
                                            <th>ID</th>
                                            <th>Appointment UID</th>
                                            <th>Patient</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm text-[#000000]">
                                        @php
                                            $activeCount = 0;
                                            $upcomingCount = 0;
                                            $missedCount = 0;
                                            $unsetCount = 0;
                                            $completeCount = 0;
                                        @endphp
                                        
                                        @foreach ($appointments as $key => $item)
                                            @php
                                                $appointmentDateTime = \Carbon\Carbon::parse($item->date . ' ' . $item->time);
                                                $gracePeriodEnd = $appointmentDateTime->copy()->addHour();
                                                $currentDateTime = \Carbon\Carbon::now();
                                                $statusClass = '';
                                                $statusText = '';
                                                
                                                // Fixed status classification logic - same as appointments page
                                                if ($item->status == 5) {
                                                    $statusText = 'Completed';
                                                    $statusClass = 'complete';
                                                    $completeCount++;
                                                } elseif ($item->status == 1) {
                                                    $statusText = 'Started';
                                                    $statusClass = 'active';
                                                    $activeCount++;
                                                } elseif ($item->status == 0) {
                                                    if ($appointmentDateTime->isFuture()) {
                                                        $statusText = 'Upcoming';
                                                        $statusClass = 'upcoming';
                                                        $upcomingCount++;
                                                    } elseif ($gracePeriodEnd->isFuture()) {
                                                        // Within grace period (1 hour after appointment time)
                                                        if ($item->meet_link) {
                                                            $statusText = 'Waiting To Start';
                                                            $statusClass = 'active';
                                                            $activeCount++;
                                                        } else {
                                                            $statusText = 'Pending Approval meeting';
                                                            $statusClass = 'unset';
                                                            $unsetCount++;
                                                        }
                                                    } else {
                                                        // Past grace period
                                                        if ($item->meet_link) {
                                                            $statusText = 'You Absented';
                                                            $statusClass = 'missed';
                                                            $missedCount++;
                                                        } else {
                                                            $statusText = 'Pending Approval meeting';
                                                            $statusClass = 'unset';
                                                            $unsetCount++;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <tr class="border-b border-[#000000]/10 appointment-row"
                                                data-status="{{ $statusClass }}">
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>{{ $item->appointment_uid }}</td>
                                                @foreach ($allPatients as $patient)
                                                    @if ($patient->patient_id == $item->patient_id)
                                                        <td class="px-4 py-4">{{ $patient->name }}</td>
                                                    @endif
                                                @endforeach
                                                <td class="px-4 py-4">
                                                    {{ \Carbon\Carbon::parse($item->date)->format('jS F Y') }}
                                                </td>
                                                <td class="px-4 py-4">
                                                    {{ \Carbon\Carbon::parse($item->time)->format('g:i A') }}
                                                </td>
                                                <td class="px-4 py-4 max-w-[100px]">
                                                    @if ($statusClass == 'active')
                                                        <span
                                                            class="status-badge bg-blue-100 text-blue-800">{{ $statusText }}</span>
                                                    @elseif($statusClass == 'upcoming')
                                                        <span
                                                            class="status-badge bg-purple-100 text-purple-800">{{ $statusText }}</span>
                                                    @elseif($statusClass == 'missed')
                                                        <span
                                                            class="status-badge bg-red-100 text-red-800">{{ $statusText }}</span>
                                                    @elseif($statusClass == 'unset')
                                                        <span
                                                            class="status-badge bg-gray-100 text-gray-800">{{ $statusText }}</span>
                                                    @elseif($statusClass == 'complete')
                                                        <span
                                                            class="status-badge bg-green-100 text-green-800">{{ $statusText }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <p class="capitalize px-3">
                                                        @if ($item->plan == null)
                                                            Cash
                                                        @else
                                                            {{ $item->plan }}
                                                        @endif
                                                    </p>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <a href="/provider/view-appointment/{{ $item->appointment_uid }}"
                                                        class="px-4 py-1 bg-[#f6028b] text-white text-center max-w-[150px] rounded-full text-[0.70rem]">Click
                                                        Here</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP counts to JavaScript
        const appointmentCounts = {
            all: {{ count($appointments) }},
            active: {{ $activeCount }},
            upcoming: {{ $upcomingCount }},
            missed: {{ $missedCount }},
            unset: {{ $unsetCount }},
            complete: {{ $completeCount }}
        };
    </script>
@endsection

@section('script')
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="/assets/js/chat_works.js"></script>

    <script>
        function updateDropdownCounts() {
            // Always show the fixed counts from server-side calculation
            $('#all-count').text('All Appointments (' + appointmentCounts.all + ')');
            $('#active-count').text('Active Appointments (' + appointmentCounts.active + ')');
            $('#upcoming-count').text('Upcoming Appointments (' + appointmentCounts.upcoming + ')');
            $('#missed-count').text('Missed Appointments (' + appointmentCounts.missed + ')');
            $('#unset-count').text('Unset Appointments (' + appointmentCounts.unset + ')');
            $('#complete-count').text('Completed Appointments (' + appointmentCounts.complete + ')');
        }

        function filterAppointments(selectedType) {
            var table = $('#appointmentsTable').DataTable();
            
            // Clear any existing filters
            table.columns().search('').draw();
            
            if (selectedType === 'all') {
                // Show all appointments
                table.search('').draw();
            } else {
                // Filter by exact status text using regex
                var statusMap = {
                    'active': '^(Waiting To Start|Started|Grace Period \\(1hr\\))$',
                    'upcoming': '^Upcoming$',
                    'missed': '^You Absented$',
                    'unset': '^Pending Approval meeting$',
                    'complete': '^Completed$'
                };

                var searchText = statusMap[selectedType] || '';
                if (searchText) {
                    table.column(5).search(searchText, true, false, true).draw();
                }
            }
        }

        $(document).ready(function() {
            // Initialize the DataTable
            var table = $('#appointmentsTable').DataTable({
                "pagingType": "simple_numbers",
                "ordering": false,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search something...",
                    "paginate": {
                        "previous": "←",
                        "next": "→"
                    },
                    "emptyTable": "No appointments found for the selected filter."
                },
                "dom": '<"top"f>rt<"bottom"lip><"clear">',
                "initComplete": function() {
                    // Add the search icon to the search input
                    $('.dataTables_filter input').before(
                        '<i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>'
                    );
                    $('.dataTables_filter input').addClass('pl-8');
                    
                    // Set initial counts and show active appointments by default
                    updateDropdownCounts();
                    filterAppointments('active');
                }
            });

            // Handle dropdown change
            $('#appointmentTypeFilter').change(function() {
                filterAppointments(this.value);
            });

            // Override DataTables search to work with our custom filtering
            table.on('search.dt', function() {
                // If there's a search term, combine it with current filter
                var searchTerm = table.search();
                var currentFilter = $('#appointmentTypeFilter').val();
                
                if (searchTerm && currentFilter !== 'all') {
                    // Reapply the filter after search
                    setTimeout(function() {
                        filterAppointments(currentFilter);
                    }, 100);
                }
            });
        });

        // API Table initialization (if needed elsewhere on the page)
        $(document).ready(function() {
            const apiTable = $('#apiTable').DataTable({
                paging: true,
                searching: true,
                ordering: false, // This disables sorting
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
                apiTable.search(this.value).draw();
            });
        });

        function copyProviderId(element) {
            const providerId = element.getAttribute('data-provider-id');
            navigator.clipboard.writeText(providerId).then(function() {
                // Optional: Show a tooltip or notification that ID was copied
                console.log('Provider ID copied to clipboard: ' + providerId);
            }).catch(function(err) {
                console.error('Failed to copy Provider ID: ', err);
            });
        }
    </script>
@endsection