@extends('layouts.admin_app')

@section('title', 'All Providers')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

@section('styles')
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

        #providersTable_filter {
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

        #providersTable_paginate {
            display: flex;
            justify-content: end;
            align-items: center;
            position: relative;
            margin-bottom: 1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 100%;
            max-width: 1000px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .modal-body {
            padding: 20px 0;
        }

        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-footer {
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">

        <div class="min-h-screen p-4 bg-gray-100 md:p-6">
            <div class="space-y-6 rounded-md">

                <div class="bg-gray-100 rounded-lg shadow relative pt-2">
                    <div style="margin: 1rem;">
                        <div class="text-2xl font-semibold">Provider Records</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="providersTable" class="w-full text-sm text-left display">
                            <thead class="bg-[#F7F9FB] font-normal text-[#00000080]">
                                <tr>
                                    <th class="px-1 py-4 font-normal">Serial</th>
                                    <th class="px-1 py-4 font-normal">Provider</th>
                                    <th class="px-1 py-4 font-normal">Email</th>
                                    <th class="px-1 py-4 font-normal">POD</th>
                                    <th class="px-1 py-4 font-normal">Verification</th>
                                    <th class="px-1 py-4 font-normal">Since</th>
                                    <th class="px-1 py-4 font-normal">
                                        <div class="text-end"> Action </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-[#000000] w-full">
                                @php
                                    $roleLabels = [
                                        'doctor' => 'Doctor',
                                        'nurse' => 'Nurse',
                                        'mental_health_specialist' => 'Mental Health Specialist',
                                        'dietician' => 'Dietician',
                                        'medical_assistant' => 'Medical Assistant',
                                    ];
                                @endphp
                                @forelse ($providers as $key => $provider)
                                    <tr class="border-b border-[#000000]/10 mb-3">
                                        <form action="/admin/change-pod" method="post">
                                            @csrf
                                            <input type="hidden" name="provider_id" value="{{ $provider->provider_id }}">
                                            <td class="px-1 py-4 w-[50px] text-center">{{ $key + 1 }}</td>
                                            <td class="px-1 py-4 lg:w-[200px]">
                                                <span class="text-wrap capitalize text-black font-semibold">{{ $provider->name }}</span> <br>
                                                <span class="text-wrap italic text-gray-700">{{ $provider->provider_id ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-1 py-4 lg:w-[120px]">
                                                <a href="mailto:{{ $provider->email }}"
                                                    class="text-blue-500 text-wrap">{{ $provider->email }}</a>
                                            </td>
                                            <td class="px-1 py-4 w-[170px]">
                                                <input type="text" class="border-1 rounded px-2 py-1 w-full text-center"
                                                    name="new_pod" value="{{ $provider->pod_name }}">
                                            </td>
                                            <td class="px-1 py-4 w-[170px]">
                                                <select name="activity_status" class="border-1 rounded px-2 py-1 w-full text-center bg-white">
                                                    <option disabled>Change Verification Status</option>
                                                    <option value="0" {{ $provider->activity_status == 0 ? 'selected' : '' }}>Not Verified</option>
                                                    <option value="1" {{ $provider->activity_status == 1 ? 'selected' : '' }}>Verified</option>
                                                </select>
                                            </td>
                                            <td class="px-1 py-4 w-[150px]">
                                                {{ \Carbon\Carbon::parse($provider->created_at)->format('jS M, Y') }}
                                            </td>
                                            <td class="px-1 py-4">
                                                <div class="flex gap-2 justify-center items-center">
                                                    <button class="bg-[#133a59] text-white rounded text-sm px-4 py-1 block">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="bg-blue-400 text-white rounded text-sm px-4 py-1 block view-provider-btn"
                                                        data-provider-id="{{ $provider->provider_id }}"
                                                        data-provider-name="{{ $provider->name }}"
                                                        data-provider-data="{{ json_encode([
                                                            'provider_id' => $provider->provider_id,
                                                            'pod_name' => $provider->pod_name,
                                                            'name' => $provider->name,
                                                            'first_name' => $provider->first_name,
                                                            'last_name' => $provider->last_name,
                                                            'provider_role' => $provider->provider_role,
                                                            'prefix_code' => $provider->prefix_code,
                                                            'mobile' => $provider->mobile,
                                                            'email' => $provider->email,
                                                            'language' => $provider->language,
                                                            'profile_picture' => $provider->profile_picture,
                                                            'created_at' => \Carbon\Carbon::parse($provider->created_at)->format('jS M, Y'),
                                                            'last_logged_in' => $provider->last_logged_in
                                                                ? \Carbon\Carbon::parse($provider->last_logged_in)->format('g.iA jS M, Y')
                                                                : 'N/A',
                                                            'last_activity' => $provider->last_activity
                                                                ? \Carbon\Carbon::parse($provider->last_activity)->format('g.iA jS M, Y')
                                                                : 'N/A',
                                                            'upcoming_appointments' => $appointments->where('provider_id', $provider->provider_id)->where('status', 0)->filter(function ($appt) {
                                                                    return \Carbon\Carbon::parse($appt->date)->isFuture();
                                                                })->count(),
                                                            'ongoing_appointments' => $appointments->where('provider_id', $provider->provider_id)->where('status', 1)->count(),
                                                            'missed_appointments' => $appointments->where('provider_id', $provider->provider_id)->where('status', 0)->filter(function ($appt) {
                                                                    return \Carbon\Carbon::parse($appt->date)->isPast();
                                                                })->count(),
                                                            'completed_appointments' => $appointments->where('provider_id', $provider->provider_id)->where('status', 5)->count(),
                                                            'virtual_notes' => $virtual_notes->where('note_by_provider_id', $provider->provider_id)->count(),
                                                            'clinical_notes' => $clinical_notes->where('note_by_provider_id', $provider->provider_id)->count(),
                                                            'eprescriptions' => $eprescriptions->where('note_by_provider_id', $provider->provider_id)->count(),
                                                            'questlabs' => $questlabs->where('note_by_provider_id', $provider->provider_id)->count(),
                                                            'unread_messages' => $unread_messages->where('received_by', $provider->provider_id)->count(),
                                                        ]) }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <center class="text-gray-500 text-lg">
                                                No Provider Joined The Platform Yet!
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

        <!-- The Modal -->
        <div id="providerModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2 class="text-xl font-semibold">Provider Details</h2>
                </div>
                <div class="modal-body" id="modalProviderContent">
                    <!-- Content will be loaded here dynamically -->
                </div>
            </div>
        </div>

    </main>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#providersTable').DataTable({
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

            // Get the modal
            const modal = document.getElementById("providerModal");

            // Get the <span> element that closes the modal
            const span = document.getElementsByClassName("close")[0];
            const closeBtn = document.getElementsByClassName("close-btn")[0];

            // When the user clicks on the button, open the modal
            $(document).on('click', '.view-provider-btn', function() {
                const providerData = JSON.parse($(this).attr('data-provider-data'));
                const providerName = $(this).attr('data-provider-name');

                let content = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1">
                            ${providerData.profile_picture ? `<img src="/${providerData.profile_picture}" alt="Profile Picture" class="w-full mb-4 mx-auto block rounded-md">` : '<p class="text-gray-500">No profile picture available</p>'}
                        </div>
                        <div class="col-span-2">
                            <div class="border shadow-md rounded-md p-4">
                                <h3 class="font-semibold mb-3">Activity Summary</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="bg-orange-50 p-3 rounded text-center">
                                        <p class="text-xs text-orange-600">Upcoming Appointments</p>
                                        <p class="text-xl font-bold">${providerData.upcoming_appointments.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded text-center">
                                        <p class="text-xs text-blue-600">Ongoing Appointments</p>
                                        <p class="text-xl font-bold">${providerData.ongoing_appointments.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-red-50 p-3 rounded text-center">
                                        <p class="text-xs text-red-600">Missed Appointments</p>
                                        <p class="text-xl font-bold">${providerData.missed_appointments.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded text-center">
                                        <p class="text-xs text-green-600">Completed Appointments</p>
                                        <p class="text-xl font-bold">${providerData.completed_appointments.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded text-center">
                                        <p class="text-xs text-gray-600">Virtual Notes</p>
                                        <p class="text-xl font-bold">${providerData.virtual_notes.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded text-center">
                                        <p class="text-xs text-gray-600">Clinical Notes</p>
                                        <p class="text-xl font-bold">${providerData.clinical_notes.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded text-center">
                                        <p class="text-xs text-gray-600">E-Prescriptions</p>
                                        <p class="text-xl font-bold">${providerData.eprescriptions.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded text-center">
                                        <p class="text-xs text-gray-600">QuestLabs</p>
                                        <p class="text-xl font-bold">${providerData.questlabs.toString().padStart(2, '0')}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded text-center">
                                        <p class="text-xs text-gray-600">Unread Messages</p>
                                        <p class="text-xl font-bold">${providerData.unread_messages.toString().padStart(2, '0')}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1 border rounded-lg p-4 shadow-md">
                            <h3 class="font-semibold mb-2">Basic Information</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Full Name:</span> <span class="capitalize">${providerData.first_name} ${providerData.last_name}</span></p>
                            <p class="text-sm mb-2"><span class="font-semibold">Provider ID:</span> ${providerData.provider_id}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">POD:</span> ${providerData.pod_name}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Role:</span> ${getRoleLabel(providerData.provider_role)}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Member Since:</span> ${providerData.created_at}</p>
                            <p class="text-sm mb-2 capitalize"><span class="font-semibold">Language:</span> ${providerData.language}</p>
                        </div>
                        
                        <div class="col-span-1 border rounded-lg p-4 shadow-md">
                            <h3 class="font-semibold mb-2">Contact Information</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Phone:</span> ${providerData.prefix_code} ${providerData.mobile}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Email:</span> <a class="text-blue-500" href="mailto:${providerData.email}">${providerData.email}</a></p>
                        </div>
                    </div>
                
                    <div class="mt-6">
                        <p class="text-sm mb-2"><span class="font-semibold">Last Login:</span> ${providerData.last_logged_in}</p>
                        <p class="text-sm mb-2"><span class="font-semibold">Last Activity:</span> ${providerData.last_activity}</p>
                    </div>
                `;

                $('#modalProviderContent').html(content);
                modal.style.display = "block";
            });

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            function getRoleLabel(roleCode) {
                const roles = {
                    'doctor': 'Doctor',
                    'nurse': 'Nurse',
                    'mental_health_specialist': 'Mental Health Specialist',
                    'dietician': 'Dietician',
                    'medical_assistant': 'Medical Assistant'
                };
                return roles[roleCode] || roleCode.charAt(0).toUpperCase() + roleCode.slice(1);
            }
        });

        $(".providers").addClass("font-semibold");
    </script>
@endsection
