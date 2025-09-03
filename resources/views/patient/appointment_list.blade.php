@extends('layouts.patient_portal')

@section('title', 'appointment-list')

@section('link')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

@endsection

@section('style')
    <style>
        .appointment {
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


        /* Status badge styling */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .appointment_type_dropdown {
            position: absolute;
            top: 2.8rem;
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

    @include('layouts.patient_header')

    <div class="bg-gray-100 min-h-screen p-6">
        <h3 class="text-[#000000] appointments_null_text text-4xl font-bold text-center mt-6">
            Appointments
        </h3>

        <div class="max-w-7xl mx-auto bg-white p-6 rounded-md mt-10 mb-16">
            <div class="bg-gray-100 rounded-lg shadow relative p-6 pt-0">
                <div class="space-y-6 bg-[#f3f4f6] rounded-lg lg:col-span-3 overflow-x-auto relative">
                    <div class="appointment_type_dropdown">
                        <select name="appointment_type" id="appointmentTypeFilter" class="bg-white px-4 py-2 rounded-lg">
                            <option id="all-count" value="all">All Appointments</option>
                            <option id="active-count" value="active" selected>Active Appointments</option>
                            <option id="upcoming-count" value="upcoming">Upcoming Appointments</option>
                            <option id="missed-count" value="missed">Missed Appointments</option>
                            <option id="unset-count" value="unset">Unset Appointments</option>
                            <option id="complete-count" value="complete">Completed Appointments</option>
                        </select>
                    </div>

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
                                    $appointmentDateTime = \Carbon\Carbon::parse($item->date . ' ' . $item->time);
                                    $gracePeriodEnd = $appointmentDateTime->copy()->addHour();
                                    $statusClass = '';

                                    if ($item->status == 0) {
                                        if ($appointmentDateTime->isFuture()) {
                                            $statusText = 'Upcoming';
                                            $statusClass = 'upcoming';
                                        } elseif ($gracePeriodEnd->isFuture()) {
                                            if ($item->meet_link) {
                                                $statusText = 'Waiting To Start';
                                                $statusClass = 'active';
                                            } else {
                                                $statusText = 'Grace Period (1hr)';
                                                $statusClass = 'active';
                                            }
                                        } else {
                                            if ($item->meet_link) {
                                                $statusText = 'You Absented';
                                                $statusClass = 'missed';
                                            } else {
                                                $statusText = 'Pending Approval';
                                                $statusClass = 'unset';
                                            }
                                        }
                                    } elseif ($item->status == 1) {
                                        $statusText = 'Started';
                                        $statusClass = 'active';
                                    } elseif ($item->status == 5) {
                                        $statusText = 'Completed';
                                        $statusClass = 'complete';
                                    }
                                @endphp

                                <tr class="border-b border-[#000000]/10 appointment-row" data-status="{{ $statusClass }}">
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
                                                        Pending Approval
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
                                        <a href="/appointments/{{ $item->appointment_uid }}"
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
        </div>
    </div>




@endsection

@section('script')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
        function showSelectedType(selectElement) {
            var selected = typeof selectElement === 'string' ? selectElement : selectElement.value;
            var table = $('#appointmentsTable').DataTable();

            if (selected === 'all') {
                table.columns(5).search('').draw();
            } else {
                // Map filter value to status text in table
                var statusMap = {
                    'active': 'Waiting To Start|Started|Grace Period \\(1hr\\)',
                    'upcoming': 'Upcoming',
                    'missed': 'You Absented',
                    'unset': 'Pending Approval',
                    'complete': 'Completed'
                };

                var searchText = statusMap[selected] || '';
                table.columns(5).search(searchText, true, false, true).draw();
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
                    updateCounts();

                    // Show only active appointments by default
                    showSelectedType('active');
                }
            });

            // Function to update counts
            function updateCounts() {
                const allCount = $('.appointment-row').length;
                const activeCount = $('.appointment-row[data-status="active"]').length;
                const upcomingCount = $('.appointment-row[data-status="upcoming"]').length;
                const missedCount = $('.appointment-row[data-status="missed"]').length;
                const unsetCount = $('.appointment-row[data-status="unset"]').length;
                const completeCount = $('.appointment-row[data-status="complete"]').length;

                $('#all-count').text('All Appointments (' + allCount + ')');
                $('#active-count').text('Active Appointments (' + activeCount + ')');
                $('#upcoming-count').text('Upcoming Appointments (' + upcomingCount + ')');
                $('#missed-count').text('Missed Appointments (' + missedCount + ')');
                $('#unset-count').text('Unset Appointments (' + unsetCount + ')');
                $('#complete-count').text('Completed Appointments (' + completeCount + ')');
            }

            $('#appointmentTypeFilter').change(function() {
                showSelectedType(this);
            });
        });
    </script>
@endsection
