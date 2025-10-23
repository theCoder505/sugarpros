@extends('layouts.patient_portal')

@section('title', 'Appointment Details')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        /* Base Styles */
        .appointment-header {
            background: linear-gradient(135deg, #2d92b3, #133a59);
            color: white;
        }

        .section-card {
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            cursor: pointer;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-content {
            display: none;
            padding: 1.5rem;
        }

        .section-content.active {
            display: block;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }

        .badge-primary {
            background-color: #e0e7ff;
            color: #2889aa;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 1px;
            height: 100%;
            background-color: #e2e8f0;
        }

        .timeline-dot {
            position: absolute;
            left: -0.375rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background-color: #2889aa;
        }

        .note-content {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .note-label {
            font-weight: 500;
            color: #64748b;
            margin-right: 0.5rem;
        }

        .prescription-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #e0e7ff;
            color: #2889aa;
        }

        .test-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .watermark {
            position: absolute;
            opacity: 0.1;
            font-size: 120px;
            transform: rotate(-30deg);
            z-index: 0;
            pointer-events: none;
        }

        .prescription-body {
            position: relative;
            z-index: 1;
        }

        .rx-symbol {
            font-size: 2.5rem;
            color: #2889aa;
            /* font-family: serif; */
        }

        .text-indigo-600 {
            color: #2889aa;
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="bg-gray-50 min-h-screen p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Appointment Header -->
            <div class="appointment-header rounded-lg shadow-md mb-6 p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Appointment Details</h1>
                        <div class="flex items-center gap-2 mt-2">
                            @php
                                $status = $appointment['appointmentData'][0]->status;
                                $meetLink = $appointment['appointmentData'][0]->meet_link;

                                $appointmentDateTime = \Carbon\Carbon::parse(
                                    $appointment['appointmentData'][0]->date .
                                        ' ' .
                                        $appointment['appointmentData'][0]->time,
                                );
                                $gracePeriodEnd = $appointmentDateTime->copy()->addHour();
                            @endphp

                            @if ($status == 0)
                                @if ($appointmentDateTime->isFuture())
                                    <span class="badge badge-primary">
                                        <i class="fas fa-clock mr-1"></i> Upcoming
                                    </span>
                                @elseif ($gracePeriodEnd->isFuture())
                                    @if ($meetLink)
                                        <span class="badge badge-primary">
                                            <i class="fas fa-hourglass-half mr-1"></i> Waiting To Start
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-hourglass-end mr-1"></i> Grace Period (1hr)
                                        </span>
                                    @endif
                                @else
                                    @if ($meetLink)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-user-times mr-1"></i> Provider Absent
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle mr-1"></i> Pending Approval
                                        </span>
                                    @endif
                                @endif
                            @elseif ($status == 1)
                                <span class="badge badge-primary">
                                    <i class="fas fa-play mr-1"></i> Started
                                </span>
                            @elseif ($status == 5)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                </span>
                            @endif


                            <span class="text-blue-100">
                                {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->date)->format('F j, Y') }} at
                                {{ $appointment['appointmentData'][0]->time }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        @if ($appointment['appointmentData'][0]->provider_id == null)
                            <a href="/chats"
                                class="flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded hover:bg-indigo-50 transition">
                                <i class="fas fa-message"></i> Message A Provider
                            </a>
                        @else
                            <a href="/send-to-chats/provider/{{ $appointment['appointmentData'][0]->provider_id }}"
                                class="flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded hover:bg-indigo-50 transition">
                                <i class="fas fa-message"></i> Message Provider
                            </a>
                            @if (($appointment['appointmentData'][0]->plan == 'medicare' && $appointment['appointmentData'][0]->medicare_status != 'pending') || $appointment['appointmentData'][0]->plan == 'subscription')
                                @if ($appointment['appointmentData'][0]->status == 1)
                                    <a href="/join-meeting/{{ $appointment['appointmentData'][0]->appointment_uid }}"
                                        target="_blank"
                                        class="flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded hover:bg-indigo-50 transition">
                                        <i class="fas fa-video"></i> Join
                                    </a>
                                @endif
                            @endif
                            <button onclick="window.print()"
                                class="flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded hover:bg-indigo-50 transition">
                                <i class="fas fa-download"></i> Export
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Appointment Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Patient Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-indigo-600"></i> Your Patient Information
                    </h3>
                    <div class="space-y-2">
                        <p><span class="note-label">Name:</span> {{ $appointment['appointmentData'][0]->users_full_name }}
                        </p>
                        <p><span class="note-label">Patient ID:</span> {{ $appointment['appointmentData'][0]->patient_id }}
                        </p>
                        <p><span class="note-label">Email:</span> {{ $appointment['appointmentData'][0]->users_email }}</p>
                        <p><span class="note-label">Phone:</span> {{ $appointment['appointmentData'][0]->users_phone }}</p>
                        <p><span class="note-label">Address:</span> {{ $appointment['appointmentData'][0]->users_address }}
                        </p>
                    </div>
                </div>

                <!-- Provider Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-md text-indigo-600"></i> Provider Information
                    </h3>
                    @forelse ($all_providers as $provider)
                        @if ($provider->provider_id == $appointment['appointmentData'][0]->provider_id)
                            <div class="space-y-2">
                                <p><span class="note-label">Provider:</span> {{ $provider->first_name . ' ' . $provider->last_name }}</p>
                                <p><span class="note-label">Provider ID:</span>
                                    {{ $appointment['appointmentData'][0]->provider_id }}</p>
                                <p><span class="note-label">Appointment ID:</span>
                                    {{ $appointment['appointmentData'][0]->appointment_uid }}</p>
                            </div>
                        @endif
                    @empty
                    @endforelse
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-indigo-600"></i> Payment Information
                    </h3>
                    <div class="space-y-2">
                        <p><span class="note-label">Amount:</span> {{ $appointment['appointmentData'][0]->amount }}
                            {{ strtoupper($appointment['appointmentData'][0]->currency) }}</p>
                        <p><span class="note-label">Status:</span>
                            <span
                                class="badge {{ $appointment['appointmentData'][0]->payment_status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($appointment['appointmentData'][0]->payment_status) }}
                            </span>
                        </p>
                        <p><span class="note-label">Transaction ID:</span>
                            {{ $appointment['appointmentData'][0]->stripe_charge_id }}</p>
                        <p><span class="note-label">Booked On:</span>
                            {{ \Carbon\Carbon::parse($appointment['appointmentData'][0]->created_at)->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Virtual Notes Section -->
            @if (count($appointment['virtual_notes']) > 0)
                <div class="section-card bg-white shadow-sm">
                    <div class="section-header bg-indigo-50" onclick="toggleSection(this)">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-sticky-note text-indigo-600"></i> Virtual Notes
                            ({{ count($appointment['virtual_notes']) }})
                        </h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform"></i>
                    </div>
                    <div class="section-content">
                        <div class="timeline">
                            @foreach ($appointment['virtual_notes'] as $note)
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="bg-white rounded-lg p-4 shadow-xs">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-medium text-gray-800">Provider:
                                                    {{ $note->note_by_provider_id }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y \a\t g:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="note-content">
                                            {{ $note->main_note }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Clinical Notes Section -->
            @if (count($appointment['clinical_notes']) > 0)
                <div class="section-card bg-white shadow-sm">
                    <div class="section-header bg-indigo-50" onclick="toggleSection(this)">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-file-medical text-indigo-600"></i> Clinical Notes
                            ({{ count($appointment['clinical_notes']) }})
                        </h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform"></i>
                    </div>
                    <div class="section-content">
                        @foreach ($appointment['clinical_notes'] as $note)
                            <div class="mb-6 pb-6 border-b last:border-b-0">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $note->chief_complaint }}</h4>
                                        <p class="text-sm text-gray-500">Provider: {{ $note->note_by_provider_id }} •
                                            {{ \Carbon\Carbon::parse($note->created_at)->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <!-- History Sections -->
                                    <div>
                                        <h5 class="text-md font-medium text-gray-700 mb-2">History of Present Illness</h5>
                                        <div class="note-content">
                                            {{ $note->history_of_present_illness }}
                                        </div>

                                        <h5 class="text-md font-medium text-gray-700 mb-2 mt-4">Past Medical History</h5>
                                        <div class="note-content">
                                            {{ $note->past_medical_history }}
                                        </div>
                                    </div>

                                    <!-- Medication & Family History -->
                                    <div>
                                        <h5 class="text-md font-medium text-gray-700 mb-2">Medications</h5>
                                        <div class="note-content">
                                            {{ $note->medications }}
                                        </div>

                                        <h5 class="text-md font-medium text-gray-700 mb-2 mt-4">Family History</h5>
                                        <div class="note-content">
                                            {{ $note->family_history }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Physical Exam -->
                                <h5 class="text-md font-medium text-gray-700 mb-2">Physical Examination</h5>
                                <div class="note-content mb-6">
                                    {{ $note->physical_examination }}
                                </div>

                                <!-- Assessment & Progress -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h5 class="text-md font-medium text-gray-700 mb-2">Assessment & Plan</h5>
                                        <div class="note-content">
                                            {{ $note->assessment_plan }}
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-md font-medium text-gray-700 mb-2">Progress Notes</h5>
                                        <div class="note-content">
                                            {{ $note->progress_notes }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Provider Info -->
                                <h5 class="text-md font-medium text-gray-700 mb-2 mt-6">Provider Information</h5>
                                <div class="note-content bg-indigo-50">
                                    {{ $note->provider_information }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- QuestLab Section -->
            @if (count($appointment['questlab_notes']) > 0)
                <div class="section-card bg-white shadow-sm">
                    <div class="section-header bg-indigo-50" onclick="toggleSection(this)">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-flask text-indigo-600"></i> Lab Tests
                            ({{ count($appointment['questlab_notes']) }})
                        </h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform"></i>
                    </div>
                    <div class="section-content">
                        @foreach ($appointment['questlab_notes'] as $test)
                            <div class="mb-6 pb-6 border-b last:border-b-0">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                    <div class="flex items-start gap-4">
                                        <div class="test-icon">
                                            <i class="fas fa-vial"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $test->test_name }}</h4>
                                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span>{{ \Carbon\Carbon::parse($test->date)->format('M d, Y') }} at
                                                        {{ $test->time }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span>{{ $test->preferred_lab_location }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    <span>${{ $test->estimated_cost }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge badge-primary">
                                            <i class="fas fa-info-circle mr-1"></i> {{ $test->urgency }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500 mb-2">TEST DETAILS</h5>
                                        <p class="text-sm"><span class="note-label">Code:</span> {{ $test->test_code }}
                                        </p>
                                        <p class="text-sm"><span class="note-label">Category:</span>
                                            {{ $test->category }}</p>
                                        <p class="text-sm"><span class="note-label">Specimen:</span>
                                            {{ $test->specimen_type }}</p>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500 mb-2">PATIENT INFO</h5>
                                        <p class="text-sm"><span class="note-label">Name:</span>
                                            {{ $test->patient_name }}</p>
                                        <p class="text-sm"><span class="note-label">ID:</span> {{ $test->patient_id }}
                                        </p>
                                        <p class="text-sm"><span class="note-label">Phone:</span>
                                            {{ $test->patient_phone_no }}</p>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500 mb-2">INSURANCE</h5>
                                        <p class="text-sm"><span class="note-label">Provider:</span>
                                            {{ $test->insurance_provider }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-500 mb-2">CLINICAL NOTES</h5>
                                    <div class="note-content">
                                        {{ $test->clinical_notes }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- E-Prescriptions Section -->
            @if (count($appointment['eprescription_notes']) > 0)
                <div class="section-card bg-white shadow-sm">
                    <div class="section-header bg-indigo-50" onclick="toggleSection(this)">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-prescription-bottle-alt text-indigo-600"></i> E-Prescriptions
                            ({{ count($appointment['eprescription_notes']) }})
                        </h3>
                        <i class="fas fa-chevron-down text-indigo-600 transition-transform"></i>
                    </div>
                    <div class="section-content">
                        @foreach ($appointment['eprescription_notes'] as $prescription)
                            <div class="mb-6 pb-6 border-b last:border-b-0">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                    <div class="flex items-start gap-4">
                                        <div class="prescription-icon">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $prescription->drug_name }}</h4>
                                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span>Start:
                                                        {{ \Carbon\Carbon::parse($prescription->start_date)->format('M d, Y') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-industry"></i>
                                                    <span>{{ $prescription->form_manufacturer }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-bolt"></i>
                                                    <span>{{ $prescription->strength }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        @if (\Carbon\Carbon::parse($prescription->start_date)->addDays($prescription->time_duration)->isFuture())
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle mr-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Expired
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500 mb-2">DOSAGE</h5>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Amount</p>
                                                <p class="font-medium">{{ $prescription->dose_amount }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Frequency</p>
                                                <p class="font-medium">{{ $prescription->frequency }} times/day</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Duration</p>
                                                <p class="font-medium">{{ $prescription->time_duration }} days</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500 mb-2">DISPENSING</h5>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Quantity</p>
                                                <p class="font-medium">{{ $prescription->quantity }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Refills</p>
                                                <p class="font-medium">{{ $prescription->refills }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-500 mb-2">PATIENT INFORMATION</h5>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Name</p>
                                            <p class="font-medium">{{ $prescription->patient_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Age/Gender</p>
                                            <p class="font-medium">{{ $prescription->age }} /
                                                {{ $prescription->gender == 'f' ? 'Female' : 'Male' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Allergies</p>
                                            <p class="font-medium">{{ $prescription->allergies }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Patient ID</p>
                                            <p class="font-medium">{{ $prescription->patient_id }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State (if no data in any section) -->
            @if (count($appointment['virtual_notes']) == 0 &&
                    count($appointment['clinical_notes']) == 0 &&
                    count($appointment['questlab_notes']) == 0 &&
                    count($appointment['eprescription_notes']) == 0)
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-1">No Additional Details Available</h3>
                    <p class="text-gray-500">This appointment doesn't have any associated notes, prescriptions, or lab
                        tests yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Toggle section content
        function toggleSection(header) {
            const section = header.closest('.section-card');
            const content = section.querySelector('.section-content');
            const icon = header.querySelector('.fa-chevron-down');

            content.classList.toggle('active');
            icon.classList.toggle('rotate-180');
        }

        // Auto-expand first section with content
        document.addEventListener('DOMContentLoaded', function() {
            // Find the first section with content and expand it
            const sections = document.querySelectorAll('.section-card');
            for (let section of sections) {
                if (section.querySelector('.section-content')) {
                    section.querySelector('.section-content').classList.add('active');
                    break;
                }
            }
        });
    </script>
@endsection
