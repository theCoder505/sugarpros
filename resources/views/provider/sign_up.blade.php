@extends('layouts.app')

@section('title', 'provider SignUp')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('style')

@endsection

@section('content')
    <section class="min-h-screen flex items-center justify-center bg-white px-4">
        <div class="px-2 lg:px-8 w-full my-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col justify-center md:px-9">
                <h2 class="text-[28px] text-[#121212] font-semibold mb-1">Provider Sign Up</h2>
                <p class="text-[16px] text-gray-500 mb-6">
                    Enter your credentials to access your account
                </p>

                <div class="flex flex-col gap-4 mb-4 sm:flex-row">
                    <a href="#"
                        class="w-full flex items-center justify-center border h-[52px] rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <img src="{{ asset('assets/image/go.png') }}" alt="Google" class="h-5 mr-2" />
                        Log in with <span class="pl-1 font-bold">Google</span>
                    </a>
                    <a href="#"
                        class="w-full flex items-center justify-center border h-[52px] rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <img src="{{ asset('assets/image/i.png') }}" alt="Apple" class="h-5 mr-2" />
                        Log in with <span class="pl-1 font-bold">Apple</span>
                    </a>
                </div>


                <div class="flex items-center justify-center my-3">
                    <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                    <div class="text-center text-[#737373] text-sm">or</div>
                    <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                </div>

                <form method="POST" action="/add-new-provider" id="providerSignUpForm">
                    @csrf

                    <input type="hidden" class="token" value="{{ csrf_token() }}">

                    <div class="signup_form space-y-8">
                        <div>
                            <label for="username" class="block text-[16px] font-semibold text-[#000000]">Username</label>
                            <input type="text" id="username" name="username"
                                class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your name" required />
                        </div>

                        <div>
                            <label for="email" class="block text-[16px] font-semibold text-[#000000]">Email</label>
                            <input type="email" id="email" name="email"
                                class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your email" required />
                        </div>

                        <div class="">
                            <label for="mobile" class="block text-[16px] font-semibold text-[#000000] mb-1">Mobile
                                Number</label>
                            <div class="flex items-center px-2 py-3 border border-gray-300 rounded-md shadow-sm">
                                <select name="prefix_code" class="text-gray-700 bg-transparent focus:outline-none" required>
                                    @php
                                        $options = json_decode($prefixcode, true);
                                    @endphp
                                    @if (is_array($options))
                                        @foreach ($options as $prefix)
                                            <option value="{{ $prefix }}">{{ $prefix }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="tel" id="mobile" placeholder="(555) 135-7924" name="mobile"
                                    class="w-full ml-2 placeholder-gray-400 border-none focus:outline-none" required />
                            </div>
                        </div>

                        <div class="">
                            <label for="mobile" class="block text-[16px] font-semibold text-[#000000] mb-1">
                                Select your role
                            </label>
                            <div class="flex items-center px-2 py-3 border border-gray-300 rounded-md shadow-sm">
                                <select name="provider_role" class="text-gray-700 bg-transparent focus:outline-none w-full"
                                    required>
                                    <option value="doctor">Doctor</option>
                                    <option value="nurse">Nurse</option>
                                    <option value="mental_health_specialist">Mental Health Specialist</option>
                                    <option value="dietician">Dietician</option>
                                    <option value="medical_assistant">Medical Assistant</option>
                                </select>
                            </div>
                        </div>


                        <div class="relative">
                            <label for="password" class="block text-[16px] font-semibold text-[#000000]">Password</label>
                            <input type="password" id="password" name="password"
                                class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your password" required />
                            <img id="togglePassword" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />
                        </div>

                        <div class="relative">
                            <label for="password" class="block text-[16px] font-semibold text-[#000000]">Confirm
                                Password</label>
                            <input type="password" id="passwordOne" name="confirm_password"
                                class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter your password" required />

                            <img id="togglePasswordOne" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />
                        </div>
                        <div class="flex items-center space-x-2 mt-4">
                            <input id="terms" type="checkbox" class="w-4 h-4 border-gray-300 rounded "
                                style="border: 1.2px solid #BDBDBD" required>
                            <label for="terms" class="text-sm text-[#3D3D3D]">Agreement to Terms & Conditions</label>
                        </div>
                    </div>


                    <div class="otp_form hidden">
                        <label for="user_otp" class="block text-[16px] font-semibold text-[#000000]">OTP</label>
                        <input type="text" id="user_otp" name="user_otp" maxlength="6"
                            class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Submit 6 digit otp sent to your email" required />
                    </div>

                    <button type="button" onclick="checkSignUp(this)"
                        class="send_otp w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5">
                        Submit
                    </button>

                    <button type="button" onclick="verifyAndSignup(this)"
                        class="final_signup hidden w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5">
                        Verify and Sign Up
                    </button>
                </form>

                <p class="mt-8 text-sm text-center text-[#3e3e3e]">
                    Already have an account?
                    <a href="/provider/login" class="text-[#2889AA] font-bold hover:underline">Log in</a>
                </p>
            </div>

            <div class="hidden md:block">
                <img src="{{ asset('assets/image/pl2.png') }}" alt="Doctor"
                    class="object-cover w-full h-full rounded-lg shadow-md" />
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script src="/assets/js/provider.js"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });

        const togglePasswordOne = document.getElementById('togglePasswordOne');
        const passwordInputOne = document.getElementById('passwordOne');

        togglePasswordOne.addEventListener('click', function() {
            const type = passwordInputOne.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInputOne.setAttribute('type', type);
        });
    </script>

@endsection
