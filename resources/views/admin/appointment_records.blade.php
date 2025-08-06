@extends('layouts.admin_app')

@section('title', 'All Appointments')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

@section('styles')
    <style>
        /* Your existing styles remain the same */
        .appoint {
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

        table.dataTable no-footer {
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
            float: right;
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

        .meeting-select {
            min-width: 180px;
            background: #fff;
            transition: border-color 0.2s;
        }

        .meeting-select:focus {
            border-color: #2889AA;
            box-shadow: 0 0 0 2px #2889aa33;
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

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            text-align: right;
            padding-top: .25em;
            margin-bottom: 2rem;
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

            #appointmentsTable_filter {
                float: unset;
                margin-top: -10px;
            }

            #appointmentsTable_filter label {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
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
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">

        <div class="min-h-screen p-4 bg-gray-100 md:p-6">
            <div class="space-y-6 rounded-md">

                <div class="bg-gray-100 rounded-lg shadow relative overflow-hidden">
                    <div class="overflow-x-auto">
                        <div class="p-4 bg-white rounded shadow">
                            <div class="overflow-x-auto bg-white rounded-lg p-4">
                                <div
                                    class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                                    <div class="text-xl font-semibold">Appointments</div>
                                </div>

                                <div class="space-y-6 bg-[#f3f4f6] rounded-lg lg:col-span-3 overflow-x-auto relative">
                                    <div class="appointment_type_dropdown">
                                        <select name="appointment_type" id="appointmentTypeFilter"
                                            class="bg-white px-4 py-2 rounded-lg">
                                            <option id="all-count" value="all">All Appointments</option>
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
                                            @foreach ($appointments as $key => $item)
                                                @php
                                                    $appointmentDateTime = \Carbon\Carbon::parse(
                                                        $item->date . ' ' . $item->time,
                                                    );
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
                                                                $statusText = 'Pending Approval meeting';
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
                                                <tr class="border-b border-[#000000]/10 appointment-row"
                                                    data-status="{{ $statusClass }}">
                                                    <td class="text-center">{{ $key + 1 }}</td>
                                                    <td>{{ $item->appointment_uid }}</td>
                                                    @foreach ($patients as $patient)
                                                        @if ($patient->patient_id == $item->patient_id)
                                                            <td class="px-4 py-4">{{ $patient->name }}</td>
                                                        @endif
                                                    @endforeach
                                                    <td class="px-4 py-4">
                                                        {{ \Carbon\Carbon::parse($item->date)->format('jS F Y') }}</td>
                                                    <td class="px-4 py-4">
                                                        {{ \Carbon\Carbon::parse($item->time)->format('g:i A') }}</td>
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
                                                        <a href="/admin/view-appointment/{{ $item->appointment_uid }}"
                                                            class="px-4 py-1 bg-[#f6028b] text-white text-center w-[150px] rounded-full text-[0.70rem] block">
                                                            Click Here
                                                        </a>
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
    </main>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
        $(".appointments").addClass("font-semibold");


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
                    'unset': 'Pending Approval meeting',
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
