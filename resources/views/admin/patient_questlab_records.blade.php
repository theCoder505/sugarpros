@extends('layouts.admin_app')

@section('title', 'QuestLab Records')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('styles')
    <style>
        .questlab-container {
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

        .tests-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .test-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .test-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 18px;
        }

        .test-code {
            font-size: 14px;
            color: #7f8c8d;
            background: #f5f5f5;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .test-section {
            margin-bottom: 12px;
        }

        .test-section-title {
            font-weight: 600;
            color: #3498db;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .test-section-content {
            font-size: 14px;
            color: #34495e;
            line-height: 1.5;
        }

        .test-datetime {
            display: flex;
            gap: 15px;
            margin-top: 10px;
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

        .test-content {
            flex-grow: 1;
        }

        .cost-badge {
            background: #2ecc71;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
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

        .test-card.hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="questlab-container">
        <h1 class="page-title">
            <i class="fas fa-flask"></i> QuestLab Records
        </h1>

        <input type="text" class="searching_appointments border-2 border-gray-300 rounded-lg px-4 py-2 mb-6"
            onkeyup="searchByAppointment(this)" placeholder="Search by Appointment UID...">

        @if (count($questlab_records) > 0)
            <div class="tests-list">
                @foreach ($questlab_records as $record)
                    <div class="test-card" data-appointmentid="{{ $record->appointment_uid }}">
                        <div class="test-content">
                            <div class="note-header">
                                <span class="note-appointment-id">Appointment #{{ $record->appointment_uid }}</span>
                                @if ($record->updated_at)
                                    <span class="note-date">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($record->updated_at)->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>

                            <div class="test-header">
                                <span class="test-name">Test: {{ $record->test_name }}</span>
                                <span class="test-code">Code: {{ $record->test_code }}</span>
                            </div>

                            <div class="test-section">
                                <div class="test-section-title">Category</div>
                                <div class="test-section-content">{{ $record->category }}</div>
                            </div>

                            <div class="test-section">
                                <div class="test-section-title">Specimen Type</div>
                                <div class="test-section-content">{{ $record->specimen_type }}</div>
                            </div>

                            <div class="test-section">
                                <div class="test-section-title">Preferred Lab Location</div>
                                <div class="test-section-content">{{ $record->preferred_lab_location }}</div>
                            </div>

                            <div class="test-datetime">
                                <div class="test-section">
                                    <div class="test-section-title">Date</div>
                                    <div class="test-section-content">{{ $record->date }}</div>
                                </div>
                                <div class="test-section">
                                    <div class="test-section-title">Time</div>
                                    <div class="test-section-content">{{ $record->time }}</div>
                                </div>
                            </div>

                            <div class="test-section">
                                <div class="test-section-title">Estimated Cost</div>
                                <div class="cost-badge">${{ $record->estimated_cost }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ url('/admin/view-appointment/' . $record->appointment_uid) }}"
                                class="view-details-btn bg-slate-800 w-full">
                                <i class="fas fa-eye"></i> Appointment
                            </a>
                            <a href="{{ url('/admin/quest-lab/' . $record->appointment_uid . '/' . $record->id) }}"
                                class="view-details-btn w-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-flask"></i>
                <h3>No Lab Tests Found</h3>
                <p>You haven't ordered any lab tests for this patient yet.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function searchByAppointment(passedThis) {
            let appointmentID = $(passedThis).val().trim();
            if (appointmentID === "") {
                $('.test-card').removeClass('hidden');
            } else {
                $('.test-card').each(function() {
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
