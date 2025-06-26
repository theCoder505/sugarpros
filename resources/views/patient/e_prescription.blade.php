@extends('layouts.patient_portal')

@section('title', 'E-Prescription')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .prescription-header {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
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

        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            display: inline-block;
            margin-top: 60px;
        }

        .prescription-card {
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .prescription-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }

        .status-active {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-completed {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .status-expired {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .search-box {
            transition: all 0.3s ease;
        }

        .search-box:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .stats-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')

    @include('layouts.patient_header')
    <div class="bg-gray-50 min-h-screen p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        <i class="fas fa-prescription-bottle-alt text-blue-500 mr-2"></i> My E-Prescriptions
                    </h1>
                    <p class="text-gray-600 mt-1">View and manage your digital prescriptions</p>
                </div>
                <div class="flex gap-3">
                    <div class="relative">
                        <input type="text" name="search_appointment_uid" id="search_appointment_uid"
                            class="search-box pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200"
                            placeholder="Search by Appointment ID" onkeyup="searchByAppointment(this)">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="stats-card bg-white p-4 rounded-lg shadow-sm border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Prescriptions</p>
                            <h3 class="text-2xl font-bold">{{ count($finalPrescription) }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                </div>
                <div class="stats-card bg-white p-4 rounded-lg shadow-sm border-l-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Active</p>
                            <h3 class="text-2xl font-bold">
                                {{ count(
                                    $finalPrescription->filter(function ($item) {
                                        return \Carbon\Carbon::parse($item->start_date)->addDays($item->time_duration)->isFuture();
                                    }),
                                ) }}
                            </h3>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="stats-card bg-white p-4 rounded-lg shadow-sm border-l-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Providers</p>
                            <h3 class="text-2xl font-bold">
                                {{ count(array_unique($finalPrescription->pluck('note_by_provider_id')->toArray())) }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescriptions List -->
            @forelse ($finalPrescription as $prescription)
                <div class="prescription-card bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300 result_line"
                    data-appointmentid="{{ $prescription->appointment_uid }}">
                    <!-- Prescription Header -->
                    <div class="prescription-header p-4 md:p-6 flex justify-between items-center cursor-pointer"
                        onclick="toggleView(this)">
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-xl md:text-2xl font-bold">E-PRESCRIPTION</h1>
                                @if (\Carbon\Carbon::parse($prescription->start_date)->addDays($prescription->time_duration)->isFuture())
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="status-badge status-expired">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Expired
                                    </span>
                                @endif
                            </div>
                            <p class="text-blue-100 text-sm mt-1">Digital Prescription • Valid Without Signature</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">Rx #{{ $prescription->id }}</div>
                            <div class="text-blue-100 text-sm">
                                {{ \Carbon\Carbon::parse($prescription->created_at)->format('M j, Y') }}</div>
                        </div>
                    </div>

                    <div class="details hidden">
                        <!-- Watermark -->
                        <div class="watermark text-gray-400 font-bold">VALID</div>

                        <!-- Prescription Body -->
                        <div class="prescription-body p-6 md:p-8 space-y-6 md:space-y-8">
                            <!-- Patient Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-700 mb-2">PATIENT INFORMATION</h2>
                                    <p class="text-gray-800">{{ $prescription->patient_name }}</p>
                                    <p class="text-gray-600">Age: {{ $prescription->age }} • Gender:
                                        {{ $prescription->gender == 'f' ? 'Female' : 'Male' }}</p>
                                    <p class="text-gray-600">Patient ID: {{ $prescription->patient_id }}</p>
                                    <p class="text-gray-600">Allergies: {{ $prescription->allergies }}</p>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-700 mb-2">PRESCRIBER</h2>
                                    <p class="text-gray-800 font-medium">Dr. {{ $prescription->note_by_provider_id }}</p>
                                    <p class="text-gray-600">Appointment ID: {{ $prescription->appointment_uid }}</p>
                                </div>
                            </div>

                            <!-- Prescription Details -->
                            <div class="border-t border-b border-gray-200 py-6">
                                <div class="flex items-start gap-4">
                                    <div class="text-4xl font-serif text-blue-500">℞</div>
                                    <div class="flex-1">
                                        <div class="mb-4">
                                            <p class="text-xl font-semibold text-gray-800">{{ $prescription->drug_name }}
                                            </p>
                                            <p class="text-gray-600">{{ $prescription->strength }} •
                                                {{ $prescription->form_manufacturer }}</p>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Dosage</p>
                                                <p class="font-medium">{{ $prescription->dose_amount }} per dose</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Frequency</p>
                                                <p class="font-medium">{{ $prescription->frequency }} times daily</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Duration</p>
                                                <p class="font-medium">{{ $prescription->time_duration }} Days</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-500">Start Date</p>
                                            <p class="font-medium">
                                                {{ \Carbon\Carbon::parse($prescription->start_date)->format('M j, Y') }}
                                            </p>
                                        </div>
                                        <div class="mt-4 grid grid-cols-2 gap-4">
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
                            </div>

                            <!-- Additional Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-700 mb-2">PHARMACY</h2>
                                    <p class="text-gray-600">Any pharmacy of your choice</p>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-700 mb-2">VALIDITY</h2>
                                    <p class="text-gray-800">Expires:
                                        {{ \Carbon\Carbon::parse($prescription->start_date)->addDays($prescription->time_duration)->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 mt-6">
                                <a href="/patient/show-appointment/{{ $prescription->appointment_uid }}"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-calendar-alt"></i> View Appointment
                                </a>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 p-4 border-t border-gray-200 text-center text-sm text-gray-500">
                            <p>This is an electronically generated prescription. No physical signature required.</p>
                            <p class="mt-1">For questions, please contact your healthcare provider.</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-prescription-bottle-alt text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-1">No Prescriptions Found</h3>
                    <p class="text-gray-500">You don't have any e-prescriptions yet. Prescriptions from your provider will
                        appear here after your appointments.</p>
                </div>
            @endforelse

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                            <i class="fas fa-question-circle text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">Need help with your prescriptions?</h3>
                        <p class="text-sm text-gray-600 mb-2">If you have questions about your medications or need
                            assistance with your prescriptions, our team is here to help.</p>
                        <div class="flex gap-3">
                            <a href="/chats"
                                class="text-sm text-blue-600 font-medium hover:underline flex items-center gap-1">
                                <i class="fas fa-message"></i> Chat with Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        // Toggle prescription details
        function toggleView(passedThis) {
            $(passedThis).closest('.prescription-card').find('.details').toggleClass('hidden');
        }

        // Search functionality
        window.searchByAppointment = function(passedThis) {
            let appointmentID = $(passedThis).val().trim();
            if (appointmentID === "") {
                $('.result_line').removeClass('hidden');
            } else {
                $('.result_line').each(function() {
                    if ($(this).data('appointmentid').toString().toLowerCase().includes(
                            appointmentID.toLowerCase())) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-expand if only one prescription
            if ($('.prescription-card').length === 1) {
                $('.prescription-card .details').removeClass('hidden');
            }
        });
    </script>
@endsection
