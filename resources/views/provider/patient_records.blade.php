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
        .patients_records {
            font-weight: 500;
            color: #000000;
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

        .no-patients-message {
            display: block;
            width: 100%;
            padding: 2rem;
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .patients_records {
            font-weight: 500;
            color: #000000;
        }
    </style>
@endsection


@section('content')
    @include('layouts.provider_header')



    <div class="min-h-screen p-4 bg-gray-100 md:p-6">
        <div class="mb-6 space-y-6 rounded-md">

            <div class="bg-gray-100 rounded-lg shadow relative pt-2">
                <div style="margin: 1rem;">
                    <div class="text-xl font-semibold">Patient Records</div>
                </div>


                <div class="overflow-x-auto">
                    <table id="appointmentsTable" class="w-full text-sm text-left display">
                        <thead class="bg-[#F7F9FB] font-normal text-[#00000080]">
                            <tr>
                                <th class="px-1 py-4 font-normal">Patient Name</th>
                                <th class="px-1 py-4 font-normal">Unique ID</th>
                                <th class="px-1 py-4 font-normal">DOB</th>
                                <th class="px-1 py-4 font-normal">Age</th>
                                <th class="px-1 py-4 font-normal">Gender</th>
                                <th class="px-1 py-4 font-normal">Patient Results</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-[#000000]">
                            @forelse ($patients as $patient)
                                <tr class="border-b border-[#000000]/10 mb-3">
                                    <td class="px-1 py-4 w-[160px]">{{ $patient->name }}</td>
                                    <td class="px-1 py-4 w-[120px]">{{ $patient->patient_id }}</td>

                                    @php
                                        $found = false;
                                    @endphp
                                    @foreach ($userdetails as $details)
                                        @if ($details->user_id == $patient->id)
                                            @php $found = true; @endphp
                                            <td class="px-1 py-4 w-[140px]">
                                                {{ \Carbon\Carbon::parse($details->dob)->format('jS F, Y') }}
                                            </td>
                                            <td class="px-1 py-4 w-[80px] capitalize">
                                                {{ \Carbon\Carbon::parse($details->dob)->age }}
                                            </td>
                                            <td class="px-1 py-4 w-[100px] capitalize">{{ $details->gender }}</td>
                                            @break
                                        @endif
                                    @endforeach
                                    @if (!$found)
                                        <td class="px-4 py-4 text-gray-400">&mdash;</td>
                                        <td class="px-4 py-4 text-gray-400">&mdash;</td>
                                        <td class="px-4 py-4 text-gray-400">&mdash;</td>
                                    @endif

                                    <td class="px-1 py-4 w-[160px]">
                                        <select name="patient_result"
                                            class="flex items-center gap-2 text-[#2889AA] font-semibold bg-transparent px-2"
                                            onchange="if(this.value) window.location.href=this.value">
                                            <option disabled selected>View More</option>
                                            <option value="/provider/dexcom">Dexcom/Libre</option>
                                            <option value="/provider/passio-ai">Passio AI</option>
                                            <option value="/patient/{{ $patient->patient_id }}/results/virtual-notes">
                                                Virtual notes</option>
                                            <option value="/patient/{{ $patient->patient_id }}/results/clinical-notes">
                                                Clinical notes</option>
                                            <option value="/patient/{{ $patient->patient_id }}/results/quest-lab">Quest Lab
                                            </option>
                                            <option value="/patient/{{ $patient->patient_id }}/results/e-prescription">
                                                E-Prescriptions</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <center class="text-gray-500 text-lg">
                                            No Patient Been Added To Your POD Yet!
                                        </center>
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
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
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
    </script>




    <script>
        $(document).ready(function() {
            $('#appointmentsTable').DataTable({
                "pagingType": "simple_numbers",
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search something...",
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



        function copyProviderId(element) {
            const providerId = element.getAttribute('data-provider-id');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(providerId).then(function() {
                    showCopyFeedback(element);
                });
            } else {
                // fallback for older browsers
                const tempInput = document.createElement('input');
                tempInput.value = providerId;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                showCopyFeedback(element);
            }
        }

        function showCopyFeedback(element) {
            $('.copy_icon').addClass('text-green-500');
            setTimeout(() => {
                $('.copy_icon').removeClass('text-green-500');
            }, 500);
        }
    </script>
@endsection
