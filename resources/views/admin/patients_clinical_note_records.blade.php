@extends('layouts.admin_app')

@section('title', 'Clinical Note Records')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('styles')
    <style>
        .clinical-notes-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notes-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .note-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .note-appointment-id {
            font-size: 14px;
            color: #7f8c8d;
            background: #f5f5f5;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .note-date {
            font-size: 13px;
            color: #95a5a6;
        }

        .note-section {
            margin-bottom: 15px;
        }

        .note-section-title {
            font-weight: 600;
            color: #3498db;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .note-section-content {
            font-size: 14px;
            color: #34495e;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .empty-state h3 {
            font-weight: 500;
            margin-bottom: 10px;
        }

        .view-details-btn {
            margin-top: auto;
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: fit-content;
            align-self: flex-end;
        }

        .view-details-btn:hover {
            background: #2980b9;
        }

        .note-content {
            flex-grow: 1;
        }

        .note-card.hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="clinical-notes-container">
        <h1 class="page-title">
            <i class="fas fa-notes-medical"></i> Clinical Note Records
        </h1>

        <input type="text" class="searching_appointments border-2 border-gray-300 rounded-lg px-4 py-2 mb-6"
            onkeyup="searchByAppointment(this)" placeholder="Search by Appointment UID...">

        @if (count($all_my_clinical_notes) > 0)
            <div class="notes-list">
                @foreach ($all_my_clinical_notes as $note)
                    <div class="note-card" data-appointmentid="{{ $note->appointment_uid }}">
                        <div class="note-content">
                            <div class="note-header">
                                <span class="note-appointment-id">Appointment #{{ $note->appointment_uid }}</span>
                                @if ($note->updated_at)
                                    <span class="note-date">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($note->updated_at)->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>

                            @if ($note->chief_complaint)
                                <div class="note-section">
                                    <div class="note-section-title">Chief Complaint</div>
                                    <div class="note-section-content">{{ $note->chief_complaint }}</div>
                                </div>
                            @endif

                            @if ($note->history_of_present_illness)
                                <div class="note-section">
                                    <div class="note-section-title">History of Present Illness</div>
                                    <div class="note-section-content">{{ $note->history_of_present_illness }}</div>
                                </div>
                            @endif

                            @if ($note->assessment_plan)
                                <div class="note-section">
                                    <div class="note-section-title">Assessment & Plan</div>
                                    <div class="note-section-content">{{ $note->assessment_plan }}</div>
                                </div>
                            @endif
                        </div>



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ url('/admin/view-appointment/' . $note->appointment_uid) }}"
                                class="view-details-btn bg-slate-800 w-full">
                                <i class="fas fa-eye"></i> Appointment
                            </a>
                            <a href="{{ url('/admin/clinical-notes/' . $note->appointment_uid . '/' . $note->id) }}"
                                class="view-details-btn w-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard"></i>
                <h3>No Clinical Notes Found</h3>
                <p>You haven't created any clinical notes for this patient yet.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // You can add any JavaScript interactions here if needed
            // For example, expanding/collapsing note sections
            $('.note-section-title').click(function() {
                $(this).next('.note-section-content').slideToggle();
            });
        });


        function searchByAppointment(passedThis) {
            let appointmentID = $(passedThis).val().trim();
            if (appointmentID === "") {
                $('.note-card').removeClass('hidden');
            } else {
                $('.note-card').each(function() {
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
