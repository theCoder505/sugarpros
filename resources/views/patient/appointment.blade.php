@extends('layouts.patient_portal')

@section('title', 'Basic Details')

@section('link')

@endsection

@section('style')
    <style>
        .book {
            font-weight: 500;
            color: #000000;
        }
    </style>
@endsection

@section('content')

    @include('layouts.patient_header')

    <div class=" bg-gray-100 py-5 px-5">
        <div class="max-w-7xl mx-auto bg-white min-h-screen rounded-xl shadow-md overflow-hidden p-8">
            <h1 class="text-[32px] font-bold text-[#000000] mb-8">Book an Appointment</h1>

            @php
                if (session('date')) {
                    $date = session('date');
                    $time = session('time');
                } else {
                    $date = '';
                    $time = '';
                }
            @endphp

            <form action="/book-new-appoinment" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="firstName" placeholder="Enter your first name" name="fname" required
                            value="{{ $fname }}"
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="lastName" placeholder="Enter your last name" name="lname" required
                            value="{{ $lname }}"
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" placeholder="Enter your email" name="email" required
                            value="{{ $email }}"
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="patientId" class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                        <input type="text" id="patientId" placeholder="Enter your unique patient ID" name="patient_id"
                            required value="{{ $patient_id }}" disabled
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="date" placeholder="MM/DD/YYYY" name="date" required
                            value="{{ $date }}"
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <select id="time" name="time" required
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md">
                            <option value="" disabled {{ empty($time) ? 'selected' : '' }}>Select from here</option>
                            <option value="00:00" {{ $time == '00:00' ? 'selected' : '' }}>12:00 AM</option>
                            <option value="01:00" {{ $time == '01:00' ? 'selected' : '' }}>1:00 AM</option>
                            <option value="02:00" {{ $time == '02:00' ? 'selected' : '' }}>2:00 AM</option>
                            <option value="03:00" {{ $time == '03:00' ? 'selected' : '' }}>3:00 AM</option>
                            <option value="04:00" {{ $time == '04:00' ? 'selected' : '' }}>4:00 AM</option>
                            <option value="05:00" {{ $time == '05:00' ? 'selected' : '' }}>5:00 AM</option>
                            <option value="06:00" {{ $time == '06:00' ? 'selected' : '' }}>6:00 AM</option>
                            <option value="07:00" {{ $time == '07:00' ? 'selected' : '' }}>7:00 AM</option>
                            <option value="08:00" {{ $time == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                            <option value="09:00" {{ $time == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                            <option value="10:00" {{ $time == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                            <option value="11:00" {{ $time == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                            <option value="12:00" {{ $time == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                            <option value="13:00" {{ $time == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                            <option value="14:00" {{ $time == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                            <option value="15:00" {{ $time == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                            <option value="16:00" {{ $time == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                            <option value="17:00" {{ $time == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                            <option value="18:00" {{ $time == '18:00' ? 'selected' : '' }}>6:00 PM</option>
                            <option value="19:00" {{ $time == '19:00' ? 'selected' : '' }}>7:00 PM</option>
                            <option value="20:00" {{ $time == '20:00' ? 'selected' : '' }}>8:00 PM</option>
                            <option value="21:00" {{ $time == '21:00' ? 'selected' : '' }}>9:00 PM</option>
                            <option value="22:00" {{ $time == '22:00' ? 'selected' : '' }}>10:00 PM</option>
                            <option value="23:00" {{ $time == '23:00' ? 'selected' : '' }}>11:00 PM</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                    class="max-w-[150px] bg-[#2889AA] hover:bg-opacity-90 text-white py-3 px-7 text-sm rounded-lg transition duration-200">
                    Book Now
                </button>
            </form>
        </div>
    </div>





@endsection

@section('script')
    <script defer>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }
    </script>



@endsection
