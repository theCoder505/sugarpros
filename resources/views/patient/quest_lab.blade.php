@extends('layouts.patient_portal')

@section('title', 'Quest Lab')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .lab-card {
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
        }

        .lab-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-reviewed {
            background-color: #dbeafe;
            color: #2563eb;
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
                        <i class="fas fa-flask text-blue-500 mr-2"></i> My QuestLab Results
                    </h1>
                    <p class="text-gray-600 mt-1">View and manage your laboratory test results</p>
                </div>
                <div class="flex gap-3">
                    <div class="relative">
                        <input type="text" name="search_appointment_uid" id="search_appointment_uid"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200"
                            placeholder="Search by Appointment UID" onkeyup="searchByAppointment(this)">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Tests</p>
                            <h3 class="text-2xl font-bold">{{ count($finalQuestlab) }}</h3>
                        </div>
                        <div class="test-icon">
                            <i class="fas fa-vial"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Completed</p>
                            <h3 class="text-2xl font-bold">{{ count($finalQuestlab) }}</h3>
                        </div>
                        <div class="test-icon" style="background-color: #dcfce7; color: #16a34a;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lab Results Section -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">My QuestLabs</h2>
                </div>

                @forelse ($finalQuestlab as $item)
                    <div class="lab-card bg-white p-4 mb-4 cursor-pointer border-b last:border-b-0 result_line"
                        data-appointmentid="{{ $item->appointment_uid }}">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="test-icon mt-1">
                                    <i class="fas fa-x-ray"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800">{{ $item->test_name }}</h3>
                                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }} at
                                                {{ $item->time }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ $item->preferred_lab_location }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>${{ $item->estimated_cost }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <a href="/patient/show-appointment/{{$item->appointment_uid}}" class="status-badge status-reviewed bg-slate-700 text-white">
                                    <i class="fas fa-eye mr-1"></i> View Appointment
                                </a>
                                <span class="status-badge status-reviewed">
                                    <i class="fas fa-eye mr-1"></i> Reviewed by Provider
                                </span>
                            </div>
                        </div>

                        <!-- Expanded Details (can be toggled) -->
                        <div class="mt-4 pl-14 pt-4 border-t hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">TEST DETAILS</h4>
                                    <p class="text-sm"><span class="font-medium">Code:</span> {{ $item->test_code }}</p>
                                    <p class="text-sm"><span class="font-medium">Category:</span> {{ $item->category }}</p>
                                    <p class="text-sm"><span class="font-medium">Specimen:</span>
                                        {{ $item->specimen_type }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">APPOINTMENT INFO</h4>
                                    <p class="text-sm"><span class="font-medium">ID:</span> {{ $item->appointment_uid }}
                                    </p>
                                    <p class="text-sm"><span class="font-medium">Provider:</span>
                                        {{ $item->note_by_provider_id }}</p>
                                    <p class="text-sm"><span class="font-medium">Urgency:</span> {{ $item->urgency }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">PATIENT INFO</h4>
                                    <p class="text-sm"><span class="font-medium">Name:</span> {{ $item->patient_name }}</p>
                                    <p class="text-sm"><span class="font-medium">ID:</span> {{ $item->patient_id }}</p>
                                    <p class="text-sm"><span class="font-medium">Phone:</span>
                                        {{ $item->patient_phone_no }}</p>
                                </div>
                            </div>

                            <!-- Clinical Notes Section -->
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">CLINICAL NOTES</h4>
                                <div class="bg-gray-50 p-3 rounded text-sm">
                                    {{ $item->clinical_notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-flask text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-1">No Lab Results Found</h3>
                        <p class="text-gray-500">You don't have any lab test results yet. Any completed tests will appear
                            here.</p>
                    </div>
                @endforelse
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                            <i class="fas fa-question-circle text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">Need help with your lab results?</h3>
                        <p class="text-sm text-gray-600 mb-2">If you have questions about your test results or need further
                            clarification, please contact with provider.</p>
                        <a href="recent-chat"
                            class="text-sm text-blue-600 font-medium hover:underline flex items-center gap-1">
                            <i class="fas fa-message mr-2"></i>Chat Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labCards = document.querySelectorAll('.lab-card');

            labCards.forEach(card => {
                const detailsSection = card.querySelector('.border-t');
                if (detailsSection) {
                    card.addEventListener('click', function(e) {
                        // Don't toggle if clicking on a button
                        if (!e.target.closest('button')) {
                            detailsSection.classList.toggle('hidden');
                        }
                    });
                }
            });
        });




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
    </script>
@endsection
