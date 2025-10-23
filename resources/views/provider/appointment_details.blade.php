@extends('layouts.provider')

@section('title', 'Appointment Details')

@section('content')
    @include('layouts.provider_header')

    <div class="bg-gray-100 min-h-screen p-6">
        <!-- Insurance Card Modal -->
        <div id="insuranceCardModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Insurance Card</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="flex justify-center">
                    <img id="modalInsuranceCardImage" src="" alt="Insurance Card" class="max-h-[70vh] max-w-full">
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">Close</button>
                </div>
            </div>
        </div>


        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-sm">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Appointment Details</h1>
                    <p class="text-gray-600">UID: {{ $appointment['appointmentData'][0]->appointment_uid }}</p>
                    <p class="text-gray-600">
                        Appointment Time:
                        {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->date)->format('F j, Y') }} at
                        {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->time)->format('g:i A') }}
                    </p>
                    <div class="text-gray-600 my-3">
                        <span
                            class="uppercase bg-[#133a59] text-[#fff] rounded-lg px-4 py-2 text-sm font-semibold">{{ $appointment['appointmentData'][0]->plan }}</span>
                    </div>
                </div>

                @php
                    $appointmentDateTime = \Carbon\Carbon::parse(
                        $appointment['appointmentData'][0]->date . ' ' . $appointment['appointmentData'][0]->time,
                    );
                    $gracePeriodEnd = $appointmentDateTime->copy()->addHour();
                @endphp

                @if ($appointment['appointmentData'][0]->status == 0)
                    @if ($appointmentDateTime->isFuture())
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Upcoming</span>
                    @elseif ($gracePeriodEnd->isFuture())
                        @if ($appointment['appointmentData'][0]->meet_link)
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Waiting To
                                Start</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">Grace
                                Period (1hr)</span>
                        @endif
                    @else
                        @if ($appointment['appointmentData'][0]->meet_link)
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-[#f6028b] text-white">You
                                Absented</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">Pending
                                Approval
                                meeting</span>
                        @endif
                    @endif
                @elseif ($appointment['appointmentData'][0]->status == 1)
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Started</span>
                @elseif ($appointment['appointmentData'][0]->status == 5)
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Completed</span>
                @endif
            </div>

            <!-- Appointment Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Patient Information Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Patient Information</h2>
                    <div class="space-y-4">
                        @forelse ($patients as $patient)
                            @if ($patient->patient_id == $appointment['appointmentData'][0]->patient_id)
                                @forelse ($patient_details as $details)
                                    @if ($details->user_id == $patient->id)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Full Name</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $details->fname }}
                                                {{ $details->lname }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Patient ID</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $appointment['appointmentData'][0]->patient_id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Email</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $details->email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Address</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $details->street ?? 'N/A' }},
                                                {{ $details->city ?? 'N/A' }},
                                                {{ $details->state ?? 'N/A' }},
                                                {{ $details->zip_code ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Contact</p>
                                            <p class="font-medium text-gray-900">
                                                {{ $details->phone ?? 'N/A' }}</p>
                                        </div>
                                    @endif
                                @empty
                                @endforelse
                            @endif
                        @empty
                        @endforelse
                    </div>
                </div>

                <!-- Appointment Details Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Appointment Details</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Appointment UID</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->appointment_uid }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date & Time</p>
                            <p class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->date)->format('F j, Y') }} at
                                {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->time)->format('g:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Chief Complaint</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->chief_complaint ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Symptom Onset</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->symptom_onset ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Provider Information Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Provider Information</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Provider Name</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->users_full_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Provider ID</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->provider_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>




                <!-- Medical History Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Medical History</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Prior Diagnoses</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->prior_diagnoses ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Current Medications</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->current_medications ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Allergies</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->allergies ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Past Surgical History</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->past_surgical_history ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Family Medical History</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->family_medical_history ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Insurance Information Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Insurance Information</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Insurance Company</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->insurance_company ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Policyholder Name</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->policyholder_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Policy ID</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->policy_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Group Number</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->group_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Insurance Plan Type</p>
                            <p class="font-medium text-gray-900">
                                {{ $appointment['appointmentData'][0]->insurance_plan_type ?? 'N/A' }}</p>
                        </div>
                        @if ($appointment['appointmentData'][0]->insurance_card_front || $appointment['appointmentData'][0]->insurance_card_back)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Insurance Cards</p>
                                <div class="flex gap-2 mt-2">
                                    @if ($appointment['appointmentData'][0]->insurance_card_front)
                                        <button
                                            onclick="showInsuranceCard('{{ $appointment['appointmentData'][0]->insurance_card_front }}')"
                                            class="text-blue-600 hover:underline">View Front</button>
                                    @endif
                                    @if ($appointment['appointmentData'][0]->insurance_card_back)
                                        <button
                                            onclick="showInsuranceCard('{{ $appointment['appointmentData'][0]->insurance_card_back }}')"
                                            class="text-blue-600 hover:underline">View Back</button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Payment Information Card -->
                @if ($appointment['appointmentData'][0]->plan != 'subscription')
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Payment Information</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Payment Status</p>
                                <p class="font-medium text-gray-900 capitalize">
                                    {{ $appointment['appointmentData'][0]->payment_status }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Amount</p>
                                <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->currency }}
                                    {{ $appointment['appointmentData'][0]->amount }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Stripe Charge ID</p>
                                <p class="font-medium text-gray-900">
                                    {{ $appointment['appointmentData'][0]->stripe_charge_id }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Virtual Notes Section -->
            <div class="notes-section shadow border-2 mb-8 p-6 rounded-lg cursor-pointer" onclick="expandSection(this)">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-laptop-medical"></i>
                    Virtual Notes
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">
                        {{ count($appointment['virtual_notes']) }} notes
                    </span>
                </h3>

                <div class="expanded_section hidden mt-4">
                    @if (count($appointment['virtual_notes']) > 0)
                        <div class="all_notes my-4 space-y-4">
                            @foreach ($appointment['virtual_notes'] as $note)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $note->main_note }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Created:
                                                {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                        <a href="/provider/virtual-notes/{{ $appointment['appointmentData'][0]->appointment_uid }}/{{ $note->id }}"
                                            class="flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm my-4">No virtual notes added yet.</p>
                    @endif

                    <div class="flex gap-3">
                        <a href="/provider/virtual-notes/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                            class="flex items-center gap-2 px-3 py-1 text-xs justify-center bg-green-50 text-green-700 border border-green-700 rounded-full hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus"></i> Add Virtual Notes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Clinical Notes Section -->
            <div class="notes-section shadow border-2 mb-8 p-6 rounded-lg cursor-pointer" onclick="expandSection(this)">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-notes-medical"></i>
                    Clinical Notes
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">
                        {{ count($appointment['clinical_notes']) }} notes
                    </span>
                </h3>

                <div class="expanded_section hidden mt-4">
                    @if (count($appointment['clinical_notes']) > 0)
                        <div class="all_notes my-4 space-y-4">
                            @foreach ($appointment['clinical_notes'] as $note)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $note->notes }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Created:
                                                {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                        <a href="/provider/clinical-notes/{{ $appointment['appointmentData'][0]->appointment_uid }}/{{ $note->id }}"
                                            class="flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm my-4">No clinical notes added yet.</p>
                    @endif

                    <div class="flex gap-3">
                        <a href="/provider/clinical-notes/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                            class="flex items-center gap-2 px-3 py-1 text-xs justify-center bg-green-50 text-green-700 border border-green-700 rounded-full hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus"></i> Add Clinical Notes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quest Lab Section -->
            <div class="notes-section shadow border-2 mb-8 p-6 rounded-lg cursor-pointer" onclick="expandSection(this)">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-vials"></i>
                    Quest Lab
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">
                        {{ count($appointment['questlab_notes']) }} notes
                    </span>
                </h3>

                <div class="expanded_section hidden mt-4">
                    @if (count($appointment['questlab_notes']) > 0)
                        <div class="all_notes my-4 space-y-4">
                            @foreach ($appointment['questlab_notes'] as $note)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $note->notes }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Created:
                                                {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                        <a href="/provider/quest-lab/{{ $appointment['appointmentData'][0]->appointment_uid }}/{{ $note->id }}"
                                            class="flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm my-4">No quest lab notes added yet.</p>
                    @endif

                    <div class="flex gap-3">
                        <a href="/provider/quest-lab/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                            class="flex items-center gap-2 px-3 py-1 text-xs justify-center bg-green-50 text-green-700 border border-green-700 rounded-full hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus"></i> Add Quest Lab
                        </a>
                    </div>
                </div>
            </div>

            <!-- E-Prescriptions Section -->
            <div class="notes-section shadow border-2 mb-8 p-6 rounded-lg cursor-pointer" onclick="expandSection(this)">
                <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-prescription-bottle-alt"></i>
                    E-Prescriptions
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">
                        {{ count($appointment['eprescription_notes']) }} notes
                    </span>
                </h3>

                <div class="expanded_section hidden mt-4">
                    @if (count($appointment['eprescription_notes']) > 0)
                        <div class="all_notes my-4 space-y-4">
                            @foreach ($appointment['eprescription_notes'] as $note)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $note->notes }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Created:
                                                {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                        <a href="/provider/e-prescription/{{ $appointment['appointmentData'][0]->appointment_uid }}/{{ $note->id }}"
                                            class="flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm my-4">No e-prescriptions added yet.</p>
                    @endif

                    <div class="flex gap-3">
                        <a href="/provider/e-prescription/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                            class="flex items-center gap-2 px-3 py-1 text-xs justify-center bg-green-50 text-green-700 border border-green-700 rounded-full hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus"></i> Add E-Prescriptions
                        </a>
                    </div>
                </div>
            </div>


            @if ($appointment['appointmentData'][0]->plan == 'medicare')
                <!-- only if Doctor then can see -->
                @if (Auth::guard('provider')->user()->provider_role == 'doctor')
                    <div class="notes-section shadow border-2 mb-8 p-6 rounded-lg cursor-pointer"
                        onclick="expandSection(this)">
                        <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-[#133a59]"></i>
                            Patient Claims Biller MD
                        </h3>

                        <div class="expanded_section hidden mt-4">
                            <a href="/provider/patient-claims-biller/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                                target="_blank"
                                class="inline-block px-4 py-1.5 text-sm font-normal text-white rounded-full bg-blue-500 hover:bg-[#1a4b75] transition-colors">
                                Manage Patient Claims Biller
                            </a>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Meeting Management Section -->
            @if (
                ($appointment['appointmentData'][0]->plan == 'medicare' &&
                    $appointment['appointmentData'][0]->medicare_status != 'pending') ||
                    $appointment['appointmentData'][0]->plan == 'subscription' ||
                    $appointment['appointmentData'][0]->plan == null)
                <div class="mb-8 border-2 p-6 rounded-lg">
                    <h3 class="font-semibold mb-2 text-gray-700">Meeting Management</h3>
                    @if ($appointment['appointmentData'][0]->status == 5)
                        <div class="px-4 py-2 bg-[#00897B] text-white text-center max-w-[150px] rounded-md">
                            Meeting Ended
                        </div>
                    @else
                        <form action="/provider/set-meeting-link" method="post">
                            @csrf
                            <input type="hidden" name="appointment_id"
                                value="{{ $appointment['appointmentData'][0]->appointment_uid }}">
                            <div class="flex gap-2">
                                @if ($appointment['appointmentData'][0]->status == 0)
                                    @if ($appointmentDateTime->isFuture())
                                        @if ($appointment['appointmentData'][0]->meet_link)
                                            @if ($appointment['appointmentData'][0]->provider_id == Auth::guard('provider')->user()->provider_id)
                                                <div class="w-full">
                                                    <div
                                                        class="flex items-center gap-2 px-4 py-4 justify-center bg-blue-100 text-blue-800 rounded-md text-sm font-semibold border border-blue-300">
                                                        <i class="fa fa-info-circle"></i>
                                                        You already scheduled the meeting. You can start from here during
                                                        the
                                                        grace period.
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-full">
                                                    <div
                                                        class="flex items-center gap-2 px-4 py-4 justify-center bg-orange-100 text-orange-800 rounded-md text-sm font-semibold border border-orange-300">
                                                        <i class="fa fa-info-circle"></i>
                                                        This Appointment Already Scheduled By A Provider.
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <button type="submit"
                                                class="w-full flex justify-center gap-2 items-center text-center px-4 py-4 mx-auto bg-[#2889AA] text-white rounded hover:bg-cyan-800 text-lg uppercase">
                                                <i class="fa fa-calendar-alt"></i> Schedule The Meeting
                                            </button>
                                        @endif
                                    @elseif ($gracePeriodEnd->isFuture())
                                        <!-- In grace period -->
                                        @if ($appointment['appointmentData'][0]->meet_link)
                                            @if ($appointment['appointmentData'][0]->provider_id == Auth::guard('provider')->user()->provider_id)
                                                <a href="/provider/start-meeting/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                                                    class="w-full flex justify-center gap-2 items-center px-4 py-2 bg-[#f6028b] text-white rounded hover:bg-blue-800 text-[16px] font-bold transition-colors duration-200 hover:text-decoration-none">
                                                    <i class="fa fa-video-camera"></i> Start The Meeting
                                                </a>
                                            @else
                                                <div class="w-full">
                                                    <div
                                                        class="flex items-center gap-2 px-4 py-4 justify-center bg-orange-100 text-orange-800 rounded-md text-sm font-semibold border border-orange-300">
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        This Appointment Already Scheduled By A Provider!
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <button type="submit"
                                                class="w-full flex justify-center gap-2 items-center text-center px-4 py-4 mx-auto bg-[#2889AA] text-white rounded hover:bg-cyan-800 text-lg uppercase">
                                                <i class="fa fa-calendar-alt"></i> Schedule The Meeting
                                                <small class="capitalize text-sm text-yellow-300 flex items-center gap-1">
                                                    (<i class="fa fa-exclamation-triangle"></i> In Grace Period, Start
                                                    Soon! <i class="fa fa-exclamation-triangle"></i>)
                                                </small>
                                            </button>
                                        @endif
                                    @else
                                        <!-- Missed meeting -->
                                        @if ($appointment['appointmentData'][0]->meet_link)
                                            <div
                                                class="px-4 py-1 bg-[#f6028b] text-white text-center max-w-[150px] rounded-full text-[0.70rem]">
                                                Missed Meeting
                                            </div>
                                        @else
                                            <div
                                                class="px-4 py-1 bg-[#f6028b] text-white text-center max-w-[150px] rounded-full text-[0.70rem]">
                                                Did not schedule!
                                            </div>
                                        @endif
                                    @endif
                                @elseif ($appointment['appointmentData'][0]->status == 1)
                                    <a href="/provider/start-meeting/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                                        target="_blank"
                                        class="w-full flex justify-center gap-2 items-center px-4 py-4 bg-[#0274f6] text-white rounded hover:bg-blue-800 text-[16px] font-bold transition-colors duration-200 hover:text-decoration-none">
                                        <i class="fa fa-video-camera"></i> Join Meeting
                                    </a>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            @endif


            <!-- Timestamps -->
            <div class="text-sm text-gray-500 space-y-1">
                <p>Created:
                    {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->created_at)->format('M j, Y g:i A') }}</p>
                <p>Last Updated:
                    {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->updated_at)->format('M j, Y g:i A') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function expandSection(passedThis) {
            $(passedThis).toggleClass('border-blue-400');
            $(passedThis).find('.expanded_section').toggleClass('hidden');
        }



        function showInsuranceCard(imageUrl) {
            const modal = document.getElementById('insuranceCardModal');
            const imgElement = document.getElementById('modalInsuranceCardImage');

            // Ensure the URL has the correct path
            const fullImageUrl = imageUrl.startsWith('/') ? imageUrl : '/' + imageUrl;

            imgElement.src = fullImageUrl;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        }

        function closeModal() {
            const modal = document.getElementById('insuranceCardModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }

        // Close modal when clicking outside the content
        document.getElementById('insuranceCardModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endsection
