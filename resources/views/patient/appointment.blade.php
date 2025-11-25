@extends('layouts.patient_portal')

@section('title', 'Book An Appointment Now')

@section('link')

@endsection

@section('style')
    <style>
        .book {
            font-weight: 500;
            color: #000000;
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .object-contain {
            object-fit: contain;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .hidden {
            display: none;
        }

        .border-dashed {
            border-style: dashed;
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

            <form action="/book-new-appoinment" method="POST" enctype="multipart/form-data" class="space-y-6">
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


                    <div>
                        <label for="insurance_company" class="block text-sm font-medium text-gray-700 mb-1">Primary
                            Insurance Company</label>
                        <input type="text" id="insurance_company" required name="insurance_company"
                            placeholder="e.g., UnitedHealthcare, Blue Cross Blue Shield"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="policyholder_name" class="block text-sm font-medium text-gray-700 mb-1">Policyholder’s
                            Name (if different)</label>
                        <input type="text" id="policyholder_name" name="policyholder_name"
                            placeholder="Policyholder's Name"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="policy_id" class="block text-sm font-medium text-gray-700 mb-1">Policy/ID Number</label>
                        <input type="text" id="policy_id" required name="policy_id" placeholder="Policy/ID Number"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="group_number" class="block text-sm font-medium text-gray-700 mb-1">Group Number (if
                            applicable)</label>
                        <input type="text" id="group_number" name="group_number" placeholder="Group Number"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="insurance_plan_type" class="block text-sm font-medium text-gray-700 mb-1">Insurance
                            Plan Type</label>
                        <select id="insurance_plan_type" required name="insurance_plan_type"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                            <option value="">Select Plan Type</option>
                            <option value="HMO">HMO</option>
                            <option value="PPO">PPO</option>
                            <option value="Medicare">Medicare</option>
                            <option value="Medicaid">Medicaid</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="insurance_card_front" class="block text-sm font-medium text-gray-700 mb-1">Upload
                                Insurance Card (Front)</label>
                            <div class="relative">
                                <input type="file" id="insurance_card_front" name="insurance_card_front"
                                    accept="image/*" class="hidden" required onchange="showImage(this)">
                                <label for="insurance_card_front" class="cursor-pointer">
                                    <div id="front-preview"
                                        class="border-2 border-dashed border-gray-300 rounded-md p-4 flex items-center justify-center h-40 bg-gray-50 hover:bg-gray-100 transition">
                                        <span class="text-gray-500">Click to upload front image</span>
                                    </div>
                                </label>
                                <img id="front-preview-image"
                                    class="hidden max-h-[160px] inset-0 max-w-full h-full object-contain rounded-md cursor-pointer"
                                    onclick="document.getElementById('insurance_card_front').click()">
                            </div>
                        </div>
                        <div>
                            <label for="insurance_card_back" class="block text-sm font-medium text-gray-700 mb-1">Upload
                                Insurance Card (Back)</label>
                            <div class="relative">
                                <input type="file" id="insurance_card_back" name="insurance_card_back"
                                    accept="image/*" class="hidden" required onchange="showImage(this)">
                                <label for="insurance_card_back" class="cursor-pointer">
                                    <div id="back-preview"
                                        class="border-2 border-dashed border-gray-300 rounded-md p-4 flex items-center justify-center h-40 bg-gray-50 hover:bg-gray-100 transition">
                                        <span class="text-gray-500">Click to upload back image</span>
                                    </div>
                                </label>
                                <img id="back-preview-image"
                                    class="hidden max-h-[160px] inset-0 max-w-full h-full object-contain rounded-md cursor-pointer"
                                    onclick="document.getElementById('insurance_card_back').click()">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="chief_complaint" class="block text-sm font-medium text-gray-700 mb-1">Chief
                            Complaint/Reason for Visit</label>
                        <input type="text" id="chief_complaint" required name="chief_complaint"
                            placeholder="Reason for visit"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="symptom_onset" class="block text-sm font-medium text-gray-700 mb-1">Symptom
                            Onset/Duration</label>
                        <input type="text" id="symptom_onset" required name="symptom_onset"
                            placeholder="e.g., 2 weeks, 3 months"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="prior_diagnoses" class="block text-sm font-medium text-gray-700 mb-1">Prior Diagnoses
                            (if applicable)</label>
                        <textarea id="prior_diagnoses" name="prior_diagnoses" rows="2" placeholder="List any prior diagnoses"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="current_medications" class="block text-sm font-medium text-gray-700 mb-1">Current
                            Medications (include dosages)</label>
                        <textarea id="current_medications" required name="current_medications" rows="2"
                            placeholder="List medications and dosages"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-1">Allergies (drugs,
                            environmental, etc.)</label>
                        <textarea id="allergies" required name="allergies" rows="2" placeholder="List any allergies"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="past_surgical_history" class="block text-sm font-medium text-gray-700 mb-1">Past
                            Surgical History</label>
                        <textarea id="past_surgical_history" required name="past_surgical_history" rows="2"
                            placeholder="List any past surgeries" class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="family_medical_history" class="block text-sm font-medium text-gray-700 mb-1">Family
                            Medical History (if relevant)</label>
                        <textarea id="family_medical_history" name="family_medical_history" rows="2"
                            placeholder="Relevant family medical history"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="grid gap-0">
                        <div class="block text-sm font-medium text-gray-700 mb-1">
                            Select your Plan
                        </div>
                        <label class="inline-flex items-center cursor-pointer plan-label mt-0" id="label-medicare">
                            <input type="radio" name="plan" value="medicare"
                                class="form-radio text-[#2889AA] border-gray-300" onchange="handlePlanChange(this)"
                                {{ (isset($plan) ? $plan : 'cash') == 'medicare' ? 'checked' : '' }}>
                            <span class="ml-1">Medicare</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer plan-label" id="label-cash">
                            <input type="radio" name="plan" value="cash"
                                class="form-radio text-[#2889AA] border-gray-300" onchange="handlePlanChange(this)"
                                {{ (isset($plan) ? $plan : 'cash') == 'cash' ? 'checked' : '' }}>
                            <span class="ml-1">Cash Payment</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer plan-label mt-0" id="label-subscription">
                            <input type="radio" name="plan" value="subscription"
                                class="form-radio text-[#2889AA] border-gray-300" onchange="handlePlanChange(this)"
                                {{ (isset($plan) ? $plan : 'cash') == 'subscription' ? 'checked' : '' }}>
                            <span class="ml-1">Use Subscription</span>
                        </label>
                    </div>
                </div>



                <button type="submit"
                    class="max-w-[150px] bg-[#2889AA] hover:bg-opacity-90 text-white py-3 px-7 text-sm rounded-lg transition duration-200">
                    Book Now
                </button>
            </form>
        </div>
    </div>
















    <div id="showMedicare" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-5 w-full max-w-xl relative">
            <div class="absolute right-2 top-2 flex justify-end">
                <button onclick="closePopUp(this)" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="text-left mb-6">
                <h2 class="text-2xl font-semibold text-[#133a59] mb-2">For Medicare Patients</h2>
                <p class="text-md text-[#133a59] mb-4">$0 OUT-OF-POCKET FOR CORE SERVICES</p>

                <div class="text-left mb-4">
                    <p class="font-medium text-gray-700 mb-2">Your complete diabetes care covered by Medicare:</p>
                    <ul class="list-disc pl-5 space-y-1 text-gray-600">
                        <li>Monthly virtual visits with endocrinologists</li>
                        <li>Continuous glucose monitors (CGMs)</li>
                        <li>Full access to SugarPros AI</li>
                        <li>Annual comprehensive diabetes evaluation</li>
                    </ul>
                </div>

                <div class="border-t border-gray-200 pt-3 mt-8 text-[#1e2939] flex justify-between items-center">
                    <p class="font-bold text-xl">Standard Service Fee</p>
                    <p class="">
                        <span class="font-semibold text-xl text-[#163c5a]">$50</span>
                        <span class="text-md text-slate-700">/m</span>
                    </p>
                </div>
            </div>

            <button onclick="closePopUp(this)"
                class="w-full py-3 bg-[#2889AA] hover:bg-opacity-90 text-white font-medium rounded-lg transition duration-200">
                Continue with plan
            </button>
        </div>
    </div>


@endsection

@section('script')
    <script defer>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }

        function handlePlanChange(passedThis) {
            let val = $(passedThis).val();
            if (val == 'medicare') {
                $("#showMedicare").addClass("show");
                $("#showMedicare").removeClass("hidden");
            } else {
                $("#showMedicare").removeClass("show");
                $("#showMedicare").addClass("hidden");
            }
        }


        function closePopUp(passedThis) {
            $("#showMedicare").removeClass("show");
            $("#showMedicare").addClass("hidden");
        }


        function showImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const isFront = input.id === 'insurance_card_front';
                    const previewId = isFront ? 'front-preview' : 'back-preview';
                    const previewImageId = isFront ? 'front-preview-image' : 'back-preview-image';

                    document.getElementById(previewId).classList.add('hidden');
                    const previewImage = document.getElementById(previewImageId);
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
