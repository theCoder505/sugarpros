@extends('layouts.patient_portal')

@section('title', 'Clinical Notes')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .note-card {
            transition: all 0.3s ease;
            border-left: 4px solid #4f46e5;
        }

        .note-card:hover {
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

        .note-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #e0e7ff;
            color: #4f46e5;
        }

        .section-title {
            color: #4f46e5;
            font-weight: 600;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #e0e7ff;
            padding-bottom: 0.25rem;
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

        .search-box {
            transition: all 0.3s ease;
        }

        .search-box:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
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
                        <i class="fas fa-file-medical text-indigo-600 mr-2"></i> My Clinical Notes
                    </h1>
                    <p class="text-gray-600 mt-1">Review your medical documentation and provider notes</p>
                </div>
                <div class="flex gap-3">
                    <div class="relative">
                        <input type="text" name="search_appointment_uid" id="search_appointment_uid"
                            class="search-box pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200"
                            placeholder="Search by Appointment ID" onkeyup="searchByAppointment(this)">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Notes</p>
                            <h3 class="text-2xl font-bold">{{ count($finalClinicalNotes) }}</h3>
                        </div>
                        <div class="note-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Reviewed</p>
                            <h3 class="text-2xl font-bold">{{ count($finalClinicalNotes) }}</h3>
                        </div>
                        <div class="note-icon" style="background-color: #dcfce7; color: #16a34a;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Providers</p>
                            <h3 class="text-2xl font-bold">
                                {{ count(array_unique($finalClinicalNotes->pluck('note_by_provider_id')->toArray())) }}</h3>
                        </div>
                        <div class="note-icon" style="background-color: #dbeafe; color: #2563eb;">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clinical Notes Section -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Medical Documentation</h2>
                </div>

                @forelse ($finalClinicalNotes as $note)
                    <div class="note-card bg-white p-4 border-b mb-4 last:border-b-0 result_line cursor-pointer"
                        data-appointmentid="{{ $note->appointment_uid }}">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="note-icon mt-1">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800">{{ $note->chief_complaint }}</h3>
                                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>{{ \Carbon\Carbon::parse($note->created_at)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-user-md"></i>
                                            <span>Provider: {{ $note->note_by_provider_id }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-id-card"></i>
                                            <span>Appointment: {{ $note->appointment_uid }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <a href="/appointments/{{ $note->appointment_uid }}"
                                    class="status-badge bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-calendar-check mr-1"></i> View Appointment
                                </a>
                                <span class="status-badge status-reviewed">
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                </span>
                            </div>
                        </div>

                        <!-- Expanded Details -->
                        <div class="mt-4 pl-14 pt-4 border-t hidden">
                            <!-- Chief Complaint -->
                            <div class="mb-6">
                                <h4 class="section-title">CHIEF COMPLAINT</h4>
                                <div class="note-content">
                                    {{ $note->chief_complaint }}
                                </div>
                            </div>

                            <!-- History of Present Illness -->
                            <div class="mb-6">
                                <h4 class="section-title">HISTORY OF PRESENT ILLNESS</h4>
                                <div class="note-content">
                                    {{ $note->history_of_present_illness }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Past Medical History -->
                                <div>
                                    <h4 class="section-title">PAST MEDICAL HISTORY</h4>
                                    <div class="note-content">
                                        {{ $note->past_medical_history }}
                                    </div>
                                </div>

                                <!-- Medications -->
                                <div>
                                    <h4 class="section-title">MEDICATIONS</h4>
                                    <div class="note-content">
                                        {{ $note->medications }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Family History -->
                                <div>
                                    <h4 class="section-title">FAMILY HISTORY</h4>
                                    <div class="note-content">
                                        {{ $note->family_history }}
                                    </div>
                                </div>

                                <!-- Social History -->
                                <div>
                                    <h4 class="section-title">SOCIAL HISTORY</h4>
                                    <div class="note-content">
                                        {{ $note->social_history }}
                                    </div>
                                </div>
                            </div>

                            <!-- Physical Examination -->
                            <div class="mb-6">
                                <h4 class="section-title">PHYSICAL EXAMINATION</h4>
                                <div class="note-content">
                                    {{ $note->physical_examination }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Assessment & Plan -->
                                <div>
                                    <h4 class="section-title">ASSESSMENT & PLAN</h4>
                                    <div class="note-content">
                                        {{ $note->assessment_plan }}
                                    </div>
                                </div>

                                <!-- Progress Notes -->
                                <div>
                                    <h4 class="section-title">PROGRESS NOTES</h4>
                                    <div class="note-content">
                                        {{ $note->progress_notes }}
                                    </div>
                                </div>
                            </div>

                            <!-- Provider Information -->
                            <div class="mt-6">
                                <h4 class="section-title">PROVIDER INFORMATION</h4>
                                <div class="note-content bg-indigo-50">
                                    {{ $note->provider_information }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-file-medical text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-1">No Clinical Notes Found</h3>
                        <p class="text-gray-500">You don't have any clinical notes yet. Notes from your provider will appear
                            here after your appointments.</p>
                    </div>
                @endforelse
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-indigo-50 rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                            <i class="fas fa-question-circle text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800 mb-1">Questions about your clinical notes?</h3>
                        <p class="text-sm text-gray-600 mb-2">If you have questions about your medical documentation or
                            need clarification, please contact your healthcare provider.</p>
                        <div class="flex gap-3">
                            <a href="/chats"
                                class="text-sm text-indigo-600 font-medium hover:underline flex items-center gap-1">
                                <i class="fas fa-message"></i> Message A Provider
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
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle note details when clicking on the card
            const noteCards = document.querySelectorAll('.note-card');

            noteCards.forEach(card => {
                const detailsSection = card.querySelector('.border-t');
                if (detailsSection) {
                    card.addEventListener('click', function(e) {
                        // Don't toggle if clicking on a button or link
                        if (!e.target.closest('button') && !e.target.closest('a')) {
                            detailsSection.classList.toggle('hidden');
                        }
                    });
                }
            });
        });

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
    </script>
@endsection
