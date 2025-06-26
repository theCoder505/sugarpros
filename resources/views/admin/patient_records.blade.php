@extends('layouts.admin_app')

@section('title', 'All Providers')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            padding: 1rem;
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

        .document-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #2889AA;
        }

        .document-section h3 {
            margin-top: 0;
            color: #2889AA;
        }

        .document-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .document-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
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
                                <div class="mb-4">
                                    <h2 class="text-xl font-semibold">Patient Management Record</h2>
                                </div>

                                <div id="pa_Table_wrapper" class="dataTables_wrapper no-footer">
                                    <table id="pa_Table" class="display w-full text-sm text-left dataTable no-footer">
                                        <thead class="pt-4 bg-[#F3F4F6] ">
                                            <tr>
                                                <th class="text-left cursor-pointer sorting sorting_asc">Serial</th>
                                                <th class="text-left cursor-pointer sorting sorting_asc">POD</th>
                                                <th class="text-left cursor-pointer sorting sorting_asc">Patient Name</th>
                                                <th class="text-left cursor-pointer sorting">Unique ID</th>
                                                <th class="text-left cursor-pointer sorting">DOB</th>
                                                <th class="text-left cursor-pointer sorting">Age</th>
                                                <th class="text-left cursor-pointer sorting">Gender</th>
                                                <th class="px-1 py-4 cursor-pointer sorting font-normal">Since</th>
                                                <th class="text-left cursor-pointer sorting">
                                                    <div class="text-center">Details</div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm text-[#000000] bg-[#FFFFFF]">
                                            @php
                                                if (!function_exists('getPodCode')) {
                                                    function getPodCode($index)
                                                    {
                                                        $letters = '';
                                                        do {
                                                            $letters = chr(65 + ($index % 26)) . $letters;
                                                            $index = intdiv($index, 26) - 1;
                                                        } while ($index >= 0);
                                                        return $letters;
                                                    }
                                                }
                                            @endphp
                                            @forelse ($patients as $key => $patient)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @php
                                                            $id = $patient->id;
                                                            $podIndex = intdiv($id, 500);
                                                            $podCode = getPodCode($podIndex);
                                                        @endphp
                                                        {{ $podCode }}
                                                    </td>
                                                    <td>{{ $patient->name }}</td>
                                                    <td>{{ $patient->patient_id }}</td>
                                                    @forelse ($patientDetails as $details)
                                                        @if ($details->user_id == $patient->id)
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($details->dob)->format('d/m/Y') }}
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($details->dob)->age }}
                                                            </td>
                                                            <td>
                                                                <span class="capitalize">{{ $details->gender }}</span>
                                                            </td>
                                                            <td class="px-1 py-4 w-[120px]">
                                                                {{ \Carbon\Carbon::parse($patient->created_at)->format('jS M, Y') }}
                                                            </td>
                                                            <td>
                                                                <button
                                                                    class="bg-blue-400 text-white rounded-full w-[80px] text-sm px-4 py-1 mx-auto block view-details-btn"
                                                                    data-patient-id="{{ $patient->id }}"
                                                                    data-patient-name="{{ $patient->name }}"
                                                                    data-patient-data="{{ json_encode([
                                                                        'patient_id' => $patient->patient_id,
                                                                        'dob' => \Carbon\Carbon::parse($details->dob)->format('d/m/Y'),
                                                                        'age' => \Carbon\Carbon::parse($details->dob)->age,
                                                                        'gender' => $details->gender,
                                                                        'created_at' => \Carbon\Carbon::parse($patient->created_at)->format('jS M, Y'),
                                                                        'profile_picture' => $patient->profile_picture,
                                                                        'fname' => $details->fname,
                                                                        'mname' => $details->mname,
                                                                        'lname' => $details->lname,
                                                                        'zip_code' => $details->zip_code,
                                                                        'street' => $details->street,
                                                                        'city' => $details->city,
                                                                        'state' => $details->state,
                                                                        'phone' => $details->phone,
                                                                        'email' => $details->email,
                                                                        'medicare_number' => $details->medicare_number,
                                                                        'group_number' => $details->group_number,
                                                                        'ssn' => $details->ssn,
                                                                        'language' => $patient->language,
                                                                        'emmergency_name' => $details->emmergency_name,
                                                                        'emmergency_relationship' => $details->emmergency_relationship,
                                                                        'emmergency_phone' => $details->emmergency_phone,
                                                                        'insurance_provider' => $details->insurance_provider,
                                                                        'insurance_plan_number' => $details->insurance_plan_number,
                                                                        'insurance_group_number' => $details->insurance_group_number,
                                                                        'license' => $details->license,
                                                                        'last_logged_in' => $patient->last_logged_in
                                                                            ? \Carbon\Carbon::parse($patient->last_logged_in)->format('g.iA jS M, Y')
                                                                            : 'N/A',
                                                                        'upcoming_appointments' => $appointments->where('patient_id', $patient->patient_id)->where('status', 0)->filter(function ($appt) {
                                                                                return \Carbon\Carbon::parse($appt->date)->isFuture();
                                                                            })->count(),
                                                                        'ongoing_appointments' => $appointments->where('patient_id', $patient->patient_id)->where('status', 1)->count(),
                                                                        'missed_appointments' => $appointments->where('patient_id', $patient->patient_id)->where('status', 0)->filter(function ($appt) {
                                                                                return \Carbon\Carbon::parse($appt->date)->isPast();
                                                                            })->count(),
                                                                        'completed_appointments' => $appointments->where('patient_id', $patient->patient_id)->where('status', 5)->count(),
                                                                        'virtual_notes' => $virtual_notes->where('patient_id', $patient->patient_id)->count(),
                                                                        'clinical_notes' => $clinical_notes->where('patient_id', $patient->patient_id)->count(),
                                                                        'eprescriptions' => $eprescriptions->where('patient_id', $patient->patient_id)->count(),
                                                                        'questlabs' => $questlabs->where('patient_id', $patient->patient_id)->count(),
                                                                        'financial_agreements' => $financilas->where('user_id', $patient->id)->map(function ($item) {
                                                                                return [
                                                                                    'user_name' => $item->user_name,
                                                                                    'patients_name' => $item->patients_name,
                                                                                    'patients_signature_date' => $item->patients_signature_date
                                                                                        ? \Carbon\Carbon::parse($item->patients_signature_date)->format('jS M, Y')
                                                                                        : 'N/A',
                                                                                ];
                                                                            })->toArray(),
                                                                        'sle_payments' => $slepayments->where('user_id', $patient->id)->map(function ($item) {
                                                                                return [
                                                                                    'user_name' => $item->user_name,
                                                                                    'patients_name' => $item->patients_name,
                                                                                    'patients_signature_date' => $item->patients_signature_date
                                                                                        ? \Carbon\Carbon::parse($item->patients_signature_date)->format('jS M, Y')
                                                                                        : 'N/A',
                                                                                ];
                                                                            })->toArray(),
                                                                        'compliance_forms' => $complianceform->where('user_id', $patient->id)->map(function ($item) {
                                                                                return [
                                                                                    'patients_name' => $item->patients_name,
                                                                                    'dob' => $item->dob ? \Carbon\Carbon::parse($item->dob)->format('jS M, Y') : 'N/A',
                                                                                    'patients_signature' => $item->patients_signature,
                                                                                    'patients_dob' => $item->patients_dob
                                                                                        ? \Carbon\Carbon::parse($item->patients_dob)->format('jS M, Y')
                                                                                        : 'N/A',
                                                                                    'representative_signature' => $item->representative_signature,
                                                                                    'representative_dob' => $item->representative_dob
                                                                                        ? \Carbon\Carbon::parse($item->representative_dob)->format('jS M, Y')
                                                                                        : 'N/A',
                                                                                    'nature_with_patient' => $item->nature_with_patient,
                                                                                ];
                                                                            })->toArray(),
                                                                    ]) }}">
                                                                    View
                                                                </button>
                                                            </td>
                                                        @endif
                                                    @empty
                                                    @endforelse
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center no-patients-message">
                                                        No patients found.
                                                    </td>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal -->
        <div id="patientModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2 class="text-xl font-semibold">Patient Details</h2>
                </div>
                <div class="modal-body" id="modalPatientContent">
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
            const table = $('#pa_Table').DataTable({
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

            // Get the modal
            const modal = document.getElementById("patientModal");
            const modalContent = document.getElementById("modalPatientContent");

            // Get the <span> element that closes the modal
            const span = document.getElementsByClassName("close")[0];

            // Function to safely parse JSON
            function safeParseJSON(jsonString) {
                try {
                    return JSON.parse(jsonString);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    return null;
                }
            }

            // Function to render document sections
            const renderDocumentSection = function(title, items, fields) {
                // Ensure items is an array, if null/undefined create empty array
                const safeItems = Array.isArray(items) ? items : [];
                
                if (safeItems.length === 0) {
                    return `<div class="document-section">
                        <h3>${title}</h3>
                        <p>No ${title.toLowerCase()} found.</p>
                    </div>`;
                }
                
                let content = `<div class="document-section">
                    <h3>${title}</h3>`;
                
                safeItems.forEach(function(item) {
                    content += `<div class="document-item">`;
                    fields.forEach(function(field) {
                        if (item[field] !== undefined && item[field] !== null) {
                            const fieldName = field.split('_').map(function(word) {
                                return word.charAt(0).toUpperCase() + word.slice(1);
                            }).join(' ');
                            content += `<p class="text-sm mb-1"><span class="font-semibold">${fieldName}:</span> ${item[field]}</p>`;
                        }
                    });
                    content += `</div>`;
                });
                
                content += `</div>`;
                return content;
            };

            // When the user clicks on the button, open the modal
            $(document).on('click', '.view-details-btn', function() {
                const patientDataString = $(this).attr('data-patient-data');
                const patientName = $(this).attr('data-patient-name');
                
                // Safely parse the JSON data
                const patientData = safeParseJSON(patientDataString);
                
                if (!patientData) {
                    console.error("Invalid patient data");
                    return;
                }

                let content = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1">
                            ${patientData.profile_picture ? `<img src="/${patientData.profile_picture}" alt="Profile Picture" class="w-full mb-4 mx-auto block rounded-md">` : '<p class="text-gray-500">No profile picture available</p>'}
                        </div>
                        <div class="col-span-2">
                            <div class="border shadow-md rounded-md p-4">
                                <h3 class="font-semibold mb-3">Activity Summary</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div class="bg-orange-50 p-3 rounded text-center">
                                        <p class="text-xs text-orange-600">Upcoming Appointments</p>
                                        <p class="text-xl font-bold">${patientData.upcoming_appointments ? patientData.upcoming_appointments.toString().padStart(2, '0') : '00'}</p>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded text-center">
                                        <p class="text-xs text-blue-600">Ongoing Appointments</p>
                                        <p class="text-xl font-bold">${patientData.ongoing_appointments ? patientData.ongoing_appointments.toString().padStart(2, '0') : '00'}</p>
                                    </div>
                                    <div class="bg-red-50 p-3 rounded text-center">
                                        <p class="text-xs text-red-600">Missed Appointments</p>
                                        <p class="text-xl font-bold">${patientData.missed_appointments ? patientData.missed_appointments.toString().padStart(2, '0') : '00'}</p>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded text-center">
                                        <p class="text-xs text-green-600">Completed Appointments</p>
                                        <p class="text-xl font-bold">${patientData.completed_appointments ? patientData.completed_appointments.toString().padStart(2, '0') : '00'}</p>
                                    </div>
                                    <a href="/admin/view-results/${patientData.patient_id}/virtual-notes" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                                        <p class="text-xs text-gray-600">Total Virtual Notes</p>
                                        <p class="text-xl font-bold">${patientData.virtual_notes ? patientData.virtual_notes.toString().padStart(2, '0') : '00'}</p>
                                    </a>
                                    <a href="/admin/view-results/${patientData.patient_id}/clinical-notes" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                                        <p class="text-xs text-gray-600">Total Clinical Notes</p>
                                        <p class="text-xl font-bold">${patientData.clinical_notes ? patientData.clinical_notes.toString().padStart(2, '0') : '00'}</p>
                                    </a>
                                    <a href="/admin/view-results/${patientData.patient_id}/e-prescription" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                                        <p class="text-xs text-gray-600">Total E-Prescriptions</p>
                                        <p class="text-xl font-bold">${patientData.eprescriptions ? patientData.eprescriptions.toString().padStart(2, '0') : '00'}</p>
                                    </a>
                                    <a href="/admin/view-results/${patientData.patient_id}/quest-lab" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                                        <p class="text-xs text-gray-600">Total QuestLabs</p>
                                        <p class="text-xl font-bold">${patientData.questlabs ? patientData.questlabs.toString().padStart(2, '0') : '00'}</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1 border rounded-lg p-4 shadow-md">
                            <h3 class="font-semibold mb-2">Basic Information</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Full Name:</span> <span class="capitalize">${patientData.fname || ''} ${patientData.mname || ''} ${patientData.lname || ''}</span></p>
                            <p class="text-sm mb-2"><span class="font-semibold">Patient ID:</span> ${patientData.patient_id || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">DOB:</span> ${patientData.dob || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Age:</span> ${patientData.age || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Gender:</span> <span class="capitalize">${patientData.gender || 'N/A'}</span></p>
                            <p class="text-sm mb-2"><span class="font-semibold">Member Since:</span> ${patientData.created_at || 'N/A'}</p>
                            <p class="text-sm mb-2 capitalize"><span class="font-semibold">Language:</span> ${patientData.language || 'N/A'}</p>

                            <h3 class="font-semibold mb-2 border-t-2 border-gray-400 mt-4 pt-4">Contact Information</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Address:</span> ${patientData.zip_code || ''}, ${patientData.street || ''}, ${patientData.city || ''}, ${patientData.state || ''}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Phone:</span> ${patientData.phone || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Email:</span> <a class="text-blue-500" href="mailto:${patientData.email || ''}">${patientData.email || 'N/A'}</a></p>
                        </div>
                        
                        <div class="col-span-1 border rounded-lg p-4 shadow-md">
                            <h3 class="font-semibold mb-2">Insurance Information</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Medicare:</span> ${patientData.medicare_number || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Group:</span> ${patientData.group_number || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">SSN:</span> ${patientData.ssn || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Insurance Provider:</span> ${patientData.insurance_provider || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Insurance Plan Number:</span> ${patientData.insurance_plan_number || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Insurance Group:</span> ${patientData.insurance_group_number || 'N/A'}</p>

                            <h3 class="font-semibold mb-2 border-t-2 border-gray-400 mt-4 pt-4">Emergency Contact</h3>
                            <p class="text-sm mb-2"><span class="font-semibold">Name:</span> ${patientData.emmergency_name || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Relationship:</span> ${patientData.emmergency_relationship || 'N/A'}</p>
                            <p class="text-sm mb-2"><span class="font-semibold">Phone:</span> ${patientData.emmergency_phone || 'N/A'}</p>
                        </div>
                    </div>
                    
                    ${renderDocumentSection('Financial Agreements', patientData.financial_agreements, ['user_name', 'patients_name', 'patients_signature_date'])}
                    
                    ${renderDocumentSection('SLE Payments', patientData.sle_payments, ['user_name', 'patients_name', 'patients_signature_date'])}
                    
                    ${renderDocumentSection('Compliance Forms', patientData.compliance_forms, ['patients_name', 'dob', 'patients_signature', 'patients_dob', 'representative_signature', 'representative_dob', 'nature_with_patient'])}
                    
                    <div class="mt-6">
                        <p class="text-sm mb-2"><span class="font-semibold">Last Activity:</span> ${patientData.last_logged_in || 'N/A'}</p>
                        ${patientData.license ? `<a target="_blank" href="/${patientData.license}" class="bg-slate-700 hover:bg-slate-900 text-white text-sm py-2 px-4 rounded inline-block mt-2">View License</a>` : ''}
                    </div>
                `;

                modalContent.innerHTML = content;
                modal.style.display = "block";
            });

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            };

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };
        });

        $(".patients").addClass("font-semibold");
    </script>
@endsection