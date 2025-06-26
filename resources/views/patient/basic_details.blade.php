@extends('layouts.patient_portal')

@section('title', 'Basic Details')

@section('link')

@endsection

@section('style')
    <style>
        .step-item {
            @apply flex items-center space-x-1 font-semibold;
        }

        .step-circle {
            @apply w-6 h-6 rounded-full border flex items-center justify-center text-sm;
        }

        .step-item.active .step-circle {
            background-color: #FF6400;
            border-color: #FF6400;
            color: white;

        }

        .step-item.active span:last-child {
            color: #FF6400;
            font-weight: bold;
        }


        .actived {
            border-color: #133a59 !important;
            font-weight: bold;
        }

        .actived .flex .stop {
            display: none;
        }

        .actived .flex .run {
            display: block;
        }

        /* This targets the calendar icon in WebKit browsers like Chrome and Safari */
        .no-date-icon::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        body {
            background: #f5f5f5;
        }
    </style>
@endsection

@section('content')
    <div class="h-full">
        <div class="w-full bg-[#f5f5f5] pt-4">

            <div class="flex mb-8 justify-around items-center max-w-7xl mx-auto">

                <a href="/dashboard">
                    <img src="{{ asset('assets/image/logo.png') }}" alt="" class="w-[128px] h-[44px]">
                </a>

                <div id="progressBar" class="flex flex-wrap justify-center items-center gap-2 text-sm text-[#A1A1A1]">
                    <div class="step-item active gap-2 flex justify-center items-center">
                        <span class="step-circle flex items-center justify-center w-7 h-7 border rounded-full">1</span>
                        <span>Basic Details</span>
                    </div>

                    <img src="{{ asset('assets/image/arrow.png') }}" class="w-5 h-5 " alt="">

                    <div class="step-item gap-2 flex justify-center items-center">
                        <span class="step-circle flex items-center justify-center w-7 h-7 border rounded-full">2</span>
                        <span>Contact & Safety</span>
                    </div>
                    <img src="{{ asset('assets/image/arrow.png') }}" class="w-5 h-5 " alt="">

                    <div class="step-item gap-2 flex justify-center items-center">
                        <span class="step-circle flex items-center justify-center w-7 h-7 border rounded-full">3</span>
                        <span>Home Address</span>
                    </div>
                    <img src="{{ asset('assets/image/arrow.png') }}" class="w-5 h-5 " alt="">
                    <div class="step-item gap-2 flex justify-center items-center">
                        <span class="step-circle flex items-center justify-center w-7 h-7 border rounded-full">4</span>
                        <span>Insurance & ID</span>
                    </div>
                    <img src="{{ asset('assets/image/arrow.png') }}" class="w-5 h-5 " alt="">
                    <div class="step-item gap-2 flex justify-center items-center">
                        <span class="step-circle flex items-center justify-center w-7 h-7 border rounded-full">5</span>
                        <span>Communication</span>
                    </div>
                </div>
            </div>

            <div class=" max-w-7xl mx-auto bg-[#ffffff] p-4 rounded-lg">
                <form action="/complete-user-details" method="post" enctype="multipart/form-data">
                    @csrf
                    <div id="formContainer" class="max-w-xl mx-auto">
                        <!-- Step 1: Name -->
                        <div class="form-step active_form">
                            <div class="max-w-md mx-auto mt-4">
                                <div class="mb-6 text-center">
                                    <h2 class="text-[30px] font-semibold text-[#FF6400] my-12">Welcome to SugarPros!</h2>
                                    <p class="mt-2 text-sm text-[#5C5A5A] my-8">Let's Get You Set Up.</p>
                                    <p class="mt-2 font-semibold text-[#121212] text-[24px]">
                                        First, can I know your full name?<br>
                                        (We need this exactly as it appears on your insurance card!)
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label for="first_name" class="block text-sm font-semibold text-gray-700">First
                                            Name</label>
                                        <input type="text" id="first_name" placeholder="Enter your first name"
                                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            name="fname" value="{{ old('fname', $fname) }}" required />
                                    </div>
                                    <div class="space-y-2">
                                        <label for="middle_name" class="block text-sm font-semibold text-gray-700">Middle
                                            Name</label>
                                        <input type="text" id="middle_name" placeholder="Enter your middle name"
                                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            name="mname" value="{{ old('mname', $mname) }}" />
                                    </div>
                                    <div class="space-y-2">
                                        <label for="last_name" class="block text-sm font-semibold text-gray-700">Last
                                            Name</label>
                                        <input type="text" id="last_name" placeholder="Enter your last name"
                                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            name="lname" value="{{ old('lname', $lname) }}" required />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Birthday -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    Can I get your birthday? (MM/DD/YYYY)
                                </h1>
                                <div class="space-y-2">
                                    <label for="birth_day" class="block text-sm font-semibold text-[#000000]">Your Birthday</label>
                                    <small class="text-orange-500">
                                        (You must be at least 18 years old to use this service)
                                    </small>
                                    <input type="date" id="birth_day" placeholder="MM/DD/YYYY" name="dob" required
                                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder-gray-300 no-date-icon"
                                        value="{{ old('dob', $dob) }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Gender -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    Great Job. What was your assigned gender at birth?
                                </h1>
                                <div class="space-y-2">
                                    <label for="gender" class="block text-sm font-semibold text-[#000000]">Your
                                        Gender</label>
                                    <select id="gender" name="gender" required
                                        class="w-full bg-white text-[#A3A3A3] px-3 py-2 mt-1 border placeholder-gray-300 border-gray-300 rounded-md">
                                        <option value="male" {{ old('gender', $gender) == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ old('gender', $gender) == 'female' ? 'selected' : '' }}>
                                            Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Contact Info -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    What's your contact email address and phone number?
                                </h1>
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-gray-700">Contact
                                        Email</label>
                                    <input type="email" id="email" placeholder="Enter your email"
                                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        name="contact_email" value="{{ old('contact_email', $email) }}" required />
                                </div>
                                <div class="space-y-2">
                                    <label for="phone" class="block text-sm font-semibold text-gray-700">Phone</label>
                                    <input type="tel" id="phone" placeholder="Enter your phone number"
                                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        name="phone_number" value="{{ old('phone_number', $phone) }}" required />
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Address -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    What's your current <br> address?
                                </h1>

                                <div class="space-y-2">
                                    <label for="street"
                                        class="block text-sm font-semibold text-[#000000]">Street</label>
                                    <select id="street" name="street" required
                                        class="w-full px-3 py-2 bg-white text-[#A3A3A3] mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($streets, true);
                                        @endphp
                                        @if (is_array($options))
                                            @foreach ($options as $prefixcode)
                                                <option value="{{ $prefixcode }}"
                                                    {{ old('street', $street) == $prefixcode ? 'selected' : '' }}>
                                                    {{ $prefixcode }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="city" class="block text-sm font-semibold text-[#000000]">City</label>
                                    <select id="city" name="city" required
                                        class="w-full bg-white text-[#A3A3A3] px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($cities, true);
                                        @endphp
                                        @if (is_array($options))
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}"
                                                    {{ old('city', $city) == $option ? 'selected' : '' }}>
                                                    {{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="state" class="block text-sm font-semibold text-[#000000]">State</label>
                                    <select id="state" name="state" required
                                        class="w-full bg-white text-[#A3A3A3] px-3 py-2 mt-1 border placeholder-gray-300 rounded-md">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($states, true);
                                        @endphp
                                        @if (is_array($options))
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}"
                                                    {{ old('state', $state) == $option ? 'selected' : '' }}>
                                                    {{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="zip_code" class="block text-sm font-semibold text-[#000000]">Zip
                                        Code</label>
                                    <select id="zip_code" name="zip_code" required
                                        class="w-full bg-white text-[#A3A3A3] px-3 py-2 mt-1 border placeholder-gray-300 rounded-md">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($zip_codes, true);
                                        @endphp
                                        @if (is_array($options))
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}"
                                                    {{ old('zip_code', $zip_code) == $option ? 'selected' : '' }}>
                                                    {{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 6: Medicare -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    For billing purposes, please enter your Medicare number.
                                </h1>

                                <div class="space-y-2">
                                    <label for="medicare" class="block text-sm font-semibold text-[#000000]">Medicare
                                        Number</label>
                                    <input type="text" id="medicare" name="medicare_number" required
                                        class="w-full px-3 py-2 bg-white text-[#A3A3A3] mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        value="{{ old('medicare_number', $medicare_number) }}" />
                                </div>

                                <div class="space-y-2">
                                    <label for="group_number" class="block text-sm font-semibold text-[#000000]">Group
                                        Number
                                        <span class="font-normal text-[#000000]/40">(If Applicable)</span></label>
                                    <input type="text" id="group_number" name="group_number"
                                        class="w-full bg-white text-[#A3A3A3] px-3 py-2 mt-1 border border-gray-300 rounded-md"
                                        value="{{ old('group_number', $group_number) }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Step 7: License Upload -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    To verify your identity, could you take a picture of your driver's license or state ID?
                                </h1>

                                <div class="bg-[#f5f5f5] flex items-center justify-center">
                                    <div class="w-full max-w-md">
                                        <label id="upload-box" for="file-upload"
                                            class="w-full h-[230px] bg-white rounded-lg border-2 border-dashed border-[#BDBDBD] flex flex-col items-center justify-center text-center cursor-pointer hover:border-[#FF6400] transition-colors duration-200">
                                            @if ($license)
                                                <img id="preview-image"
                                                    class="max-w-full h-full rounded-md border border-gray-300 object-cover"
                                                    src="{{ asset($license) }}" />
                                            @else
                                                <div
                                                    class="w-20 h-20 bg-gray-300 flex justify-center items-center rounded-full my-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </div>
                                                <p class="text-lg font-medium text-gray-700 mb-2">Upload your picture
                                                    here
                                                </p>
                                                <p class="text-sm text-gray-500 mb-6">Don't worry—this is stored securely!
                                                </p>
                                            @endif
                                        </label>
                                        <input type="file" class="hidden" name="license"
                                            {{ !$license ? 'required' : '' }} onchange="OnFileChange(this)"
                                            id="file-upload" accept="image/*" />
                                        <div id="file-name"
                                            class="mt-2 text-sm text-gray-600 {{ $license ? '' : 'hidden' }}">
                                            @if ($license)
                                                {{ basename($license) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 8: SSN -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    Lastly, we might need your Social Security Number for insurance claims. Is that okay?
                                </h1>
                                <div class="space-y-2">
                                    <label for="ssn" class="block text-sm font-semibold text-gray-700">SSN</label>
                                    <input type="text" id="ssn" placeholder="Enter your SSN number"
                                        name="ssn" required
                                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        value="{{ old('ssn', $ssn) }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Step 9: Communication Preferences -->
                        <div class="form-step hidden">
                            <div class="max-w-md mx-auto mt-4 space-y-4">
                                <h1 class="text-center text-[24px] font-semibold text-[#121212] my-12">
                                    Almost done! How would you like us to communicate with you?
                                </h1>

                                <div class="space-y-4">
                                    <label
                                        class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                        <input type="radio" name="communication" value="email" class="peer w-4 h-4"
                                            {{ old('communication', $notification_type) == 'email' ? 'checked' : '' }} />
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">Email</span>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                        <input type="radio" name="communication" value="text" class="peer w-4 h-4"
                                            {{ old('communication', $notification_type) == 'text' ? 'checked' : '' }} />
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">Text</span>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                        <input type="radio" name="communication" value="app" class="peer w-4 h-4"
                                            {{ old('communication', $notification_type) == 'app' ? 'checked' : '' }} />
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">App
                                                Notifications</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex grid-cols-3 justify-center gap-4 my-20 max-w-md mx-auto">
                            <a href="/basic"
                                class="w-full py-2 text-center text-gray-700 transition bg-gray-200 rounded-md hover:bg-gray-300 cancel_btn">
                                Cancel
                            </a>
                            <button
                                class="w-full py-2 text-center text-gray-100 transition bg-gray-800 rounded-md hover:bg-gray-300 hidden cancel_btn"
                                onclick="cancelToBack(this)">
                                Back
                            </button>
                            <button type="button" id="nextBtn"
                                class="w-full bg-[#21748f] hover:bg-[#1b5e70] text-white px-6 py-2 rounded-md transition"
                                onclick="proceedToNext(this)">
                                Next
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>





@endsection

@section('script')
    {{-- <script src="/assets/js/profile_complete.js"></script> --}}
@endsection
