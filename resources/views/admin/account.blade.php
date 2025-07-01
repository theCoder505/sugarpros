@extends('layouts.admin_app')

@section('title', 'Modify Admin Account')

@section('link')

@endsection

@section('styles')
    <style>
        .loader {
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3498db;
            width: 1.5rem;
            height: 1.5rem;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
            margin: 0px auto;
        }

        .active_nav_tab {
            font-weight: 500;
            color: #000000;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto mt-8 space-y-6 md:max-w-6xl">
        <div class="flex-1 bg-white border-l rounded-r-lg border-[#00000A]/10 p-4">
            <h3 class="text-xl font-bold ">Change Account Information</h3>


            <div class="email_changing_form border mt-4 shadow-xl p-6 rounded-lg">
                <h3 class="text-center text-[18px] border-b-2 pb-2 font-bold mb-4">Change Email</h3>

                <input type="hidden" value="{{ csrf_token() }}" class="token">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2 curr_email_part">
                        <label for="First" class="block text-sm font-medium text-gray-700 mb-1">
                            Current Email
                        </label>
                        <input type="email" id="First" placeholder="Current Email" name="curr_email" required
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none curr_email">
                    </div>

                    <div class="col-span-2 otp_part hidden">
                        <label for="First" class="block text-sm font-medium text-gray-700 mb-1">
                            Enter OTP
                        </label>
                        <input type="email" id="First" placeholder="Enter the 6 digit OTP sent to your account email"
                            name="otp" required maxlength="6"
                            class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none otp">
                    </div>

                    <div class="email_setup col-span-2 grid-cols-1 md:grid-cols-2 gap-4 hidden">
                        <div>
                            <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">
                                New Email
                            </label>
                            <input type="email" placeholder="New Email" name="new_email" required value=""
                                class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none new_email">
                        </div>
                        <div class="relative">
                            <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none current_password"
                                    placeholder="Enter your password" value="" />

                                <img id="togglePassword" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                    class="absolute right-4 top-4 w-5 h-5 cursor-pointer" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90 text-white px-4 py-2 text-sm rounded-lg request_otp"
                        onclick="requestEmailVerification(this)">
                        Request OTP
                    </button>

                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2 text-sm rounded-lg request_otp_verification hidden"
                        onclick="requestOTPVerification(this)">
                        Verify OTP
                    </button>

                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2 text-sm rounded-lg change_email hidden"
                        onclick="changeEmail(this)">
                        Change Email
                    </button>
                </div>
            </div>


            <div class="passwords_changing_form border mt-20 shadow-xl p-6 rounded-lg">
                <h3 class="text-center text-[18px] border-b-2 pb-2 font-bold mb-4">Reset Password</h3>

                <input type="hidden" value="{{ csrf_token() }}" class="token_password">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2 curr_email_part_password">
                        <label for="passwordCurrentEmail" class="block text-sm font-medium text-gray-700 mb-1">
                            Current Email
                        </label>
                        <input type="email" id="passwordCurrentEmail" placeholder="Current Email"
                            name="curr_email_password" required
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none curr_email_password">
                    </div>

                    <div class="col-span-2 otp_part_password hidden">
                        <label for="passwordOTP" class="block text-sm font-medium text-gray-700 mb-1">
                            Enter OTP
                        </label>
                        <input type="text" id="passwordOTP"
                            placeholder="Enter the 6 digit OTP sent to your account email" name="otp_password" required
                            maxlength="6"
                            class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none otp_password">
                    </div>

                    <div class="password_setup col-span-2 grid-cols-1 md:grid-cols-2 gap-4 hidden">
                        <div class="relative">
                            <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">
                                Current Password
                            </label>
                            <div class="relative">
                                <input type="password" id="currentPassword" name="current_password"
                                    class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none change_pass_current_password"
                                    placeholder="Enter your current password" />
                                <img src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                    class="absolute right-4 top-4 w-5 h-5 cursor-pointer toggleCurrentPassword" />
                            </div>
                        </div>

                        <div class="relative">
                            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="newPassword" name="new_password"
                                    class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none new_password"
                                    placeholder="Enter new password" />
                                <img src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                    class="absolute right-4 top-4 w-5 h-5 cursor-pointer toggleNewPassword" />
                            </div>
                            <div class="password-requirements mt-2 text-xs text-gray-500">
                                <p>Password must:</p>
                                <ul class="list-disc pl-5">
                                    <li class="length">Be at least 8 characters</li>
                                    <li class="uppercase">Contain an uppercase letter</li>
                                    <li class="lowercase">Contain a lowercase letter</li>
                                    <li class="number">Contain a number</li>
                                    <li class="special">Contain a special character</li>
                                    <li class="not-common">Not contain common words</li>
                                    <li class="not-personal">Not contain your name or email</li>
                                    <li class="not-old">Be different from current password</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90 text-white px-4 py-2 text-sm rounded-lg request_otp_password"
                        onclick="requestPasswordVerification(this)">
                        Request OTP
                    </button>

                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90 text-white px-4 py-2 text-sm rounded-lg request_otp_verification_password hidden"
                        onclick="verifyPasswordOTP(this)">
                        Verify OTP
                    </button>

                    <button type="button"
                        class="w-[150px] mt-4 bg-[#2889AA] hover:bg-opacity-90 text-white px-4 py-2 text-sm rounded-lg change_password hidden"
                        onclick="changePassword(this)">
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection



@section('scripts')
    <script src="/assets/js/admin.js"></script>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    </script>
@endsection
