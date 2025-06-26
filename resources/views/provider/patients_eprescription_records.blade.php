@extends('layouts.provider')

@section('title', 'E-Prescription Records')

@section('link')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .eprescription-container {
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

        .prescriptions-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .prescription-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .prescription-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
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

        .drug-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 18px;
        }

        .prescription-date {
            font-size: 13px;
            color: #95a5a6;
        }

        .prescription-section {
            margin-bottom: 12px;
        }

        .prescription-section-title {
            font-weight: 600;
            color: #3498db;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .prescription-section-content {
            font-size: 14px;
            color: #34495e;
            line-height: 1.5;
        }

        .prescription-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
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

        .prescription-content {
            flex-grow: 1;
        }

        .refill-badge {
            background: #e67e22;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
        }

        .prescription-card.hidden {
            display: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.provider_header')

    <div class="eprescription-container">
        <h1 class="page-title">
            <i class="fas fa-prescription-bottle-alt"></i> E-Prescription Records
        </h1>

        <input type="text" class="searching_appointments border-2 border-gray-300 rounded-lg px-4 py-2 mb-6"
            onkeyup="searchByAppointment(this)" placeholder="Search by Appointment UID...">

        @if (count($eprescription_records) > 0)
            <div class="prescriptions-list">
                @foreach ($eprescription_records as $record)
                    <div class="prescription-card" data-appointmentid="{{ $record->appointment_uid }}">
                        <div class="prescription-content">
                            <div class="drug-name mb-2">{{ $record->drug_name }}</div>

                            <div class="prescription-header">
                                <div class="prescription-date">
                                    Start:
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($record->start_date)->format('M d, Y') }}
                                </div>

                                @if ($record->updated_at)
                                    <div class="note-date">
                                        Modified:
                                        <i class="far fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($record->updated_at)->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>

                            <div class="prescription-details">
                                <div class="prescription-section">
                                    <div class="prescription-section-title">Strength</div>
                                    <div class="prescription-section-content">{{ $record->strength }}</div>
                                </div>
                                <div class="prescription-section">
                                    <div class="prescription-section-title">Dosage</div>
                                    <div class="prescription-section-content">{{ $record->dose_amount }}</div>
                                </div>
                                <div class="prescription-section">
                                    <div class="prescription-section-title">Frequency</div>
                                    <div class="prescription-section-content">{{ $record->frequency }} times daily</div>
                                </div>
                                <div class="prescription-section">
                                    <div class="prescription-section-title">Duration</div>
                                    <div class="prescription-section-content">{{ $record->time_duration }}</div>
                                </div>
                            </div>

                            <div class="prescription-section">
                                <div class="prescription-section-title">Refills</div>
                                <div class="refill-badge">{{ $record->refills }}</div>
                            </div>

                            <div class="note-header">
                                <span class="note-appointment-id mx-auto">Appointment #{{ $record->appointment_uid }}</span>
                            </div>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ url('/provider/view-appointment/' . $record->appointment_uid) }}"
                                class="view-details-btn bg-slate-800 w-full">
                                <i class="fas fa-eye"></i> Appointment
                            </a>
                            <a href="{{ url('/provider/e-prescription/' . $record->appointment_uid . '/' . $record->id) }}"
                                class="view-details-btn w-full">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-prescription-bottle-alt"></i>
                <h3>No Prescriptions Found</h3>
                <p>You haven't prescribed any medications for this patient yet.</p>
            </div>
        @endif
    </div>
@endsection

@section('script')
    <script>
        function searchByAppointment(passedThis) {
            let appointmentID = $(passedThis).val().trim();
            if (appointmentID === "") {
                $('.prescription-card').removeClass('hidden');
            } else {
                $('.prescription-card').each(function() {
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
