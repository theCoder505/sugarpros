@extends('layouts.provider')

@section('title', 'Appointment Details')

@section('content')
    @include('layouts.provider_header')

    <div class="bg-gray-100 min-h-screen p-6">
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
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">Did not set
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
                        <div>
                            <p class="text-sm font-medium text-gray-500">Full Name</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->fname }}
                                {{ $appointment['appointmentData'][0]->lname }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Patient ID</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->patient_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->email }}</p>
                        </div>
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
                            <p class="text-sm font-medium text-gray-500">Type</p>
                            <p class="font-medium text-gray-900 capitalize">
                                {{ $appointment['appointmentData'][0]->type }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Provider Information Card -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Provider Information</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Provider Name</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->users_full_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Provider ID</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->provider_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->users_address }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Contact</p>
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->users_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information Card -->
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
                            <p class="font-medium text-gray-900">{{ $appointment['appointmentData'][0]->stripe_charge_id }}
                            </p>
                        </div>
                    </div>
                </div>
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

            <!-- Meeting Management Section -->
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
                                                    You already scheduled the meeting. You can start from here during the
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
                                                (<i class="fa fa-exclamation-triangle"></i> In Grace Period, Start Soon! <i
                                                    class="fa fa-exclamation-triangle"></i>)
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
                                <a href="/provider/start-meeting/{{ $appointment['appointmentData'][0]->appointment_uid }}" target="_blank"
                                    class="w-full flex justify-center gap-2 items-center px-4 py-4 bg-[#0274f6] text-white rounded hover:bg-blue-800 text-[16px] font-bold transition-colors duration-200 hover:text-decoration-none">
                                    <i class="fa fa-video-camera"></i> Join Meeting
                                </a>
                            @endif
                        </div>
                    </form>
                @endif
            </div>

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
    </script>
@endsection
