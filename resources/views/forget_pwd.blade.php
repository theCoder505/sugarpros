@extends('layouts.app')

@section('title', 'Forget Password')

@section('link')

@endsection

@section('style')

@endsection

@section('content')
    <section class="min-h-screen flex items-center justify-center bg-white px-4">
        <div class="px-2 lg:px-8 w-full my-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col justify-center md:px-9">
                <h2 class="text-[28px] text-[#121212] font-semibold mb-1">Patient Account Retriving </h2>
                <p class="text-[16px] text-gray-500 mb-6">
                    Send an OTP to your account email & retrive your account 
                </p>

                @php
                    if (session('email')) {
                        $email = session('email');
                    } else {
                        $email = '';
                    }
                @endphp

                <form method="POST" action="/reset-account-password" class="space-y-8">
                    @csrf

                    <input type="hidden" class="token" value="{{csrf_token()}}">

                    <div class="email_part">
                        <label for="email" class="block text-[16px] font-semibold text-[#000000]">Email</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm email"
                            placeholder="Enter your email" value="{{ $email }}" />
                    </div>

                    <div class="otp_part hidden">
                        <label for="otp" class="block text-[16px] font-semibold text-[#000000]">Submit OTP</label>
                        <input type="text" id="otp" name="otp" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm otp"
                            placeholder="Enter the 6 digit OTP sent to your email" maxlength="6"/>
                    </div>

                    <div class="set_new_password hidden">
                        <div class="relative">
                            <label for="password" class="block text-[16px] font-semibold text-[#000000]">Password</label>
                            <input type="password" id="password" name="password" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm password"
                                placeholder="Enter your password" />

                            <img id="togglePassword" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />
                        </div>

                        <div class="relative mt-4">
                            <label for="password" class="block text-[16px] font-semibold text-[#000000]">Confirm
                                Password</label>
                            <input type="password" id="passwordOne" name="confirm_password" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm confirm_password"
                                placeholder="Enter your password" />

                            <img id="togglePasswordOne" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                                class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />
                        </div>
                    </div>


                    <button type="button"
                        class="w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5 send_otp" onclick="sendForgetOTP(this)">
                        Proceed
                    </button>
                    <button type="button"
                        class="w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5 verify_otp hidden" onclick="submitOTP(this)">
                        Verify OTP
                    </button>
                    <button type="button"
                        class="w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5 reset_password hidden" onclick="finalForgetSubmit(this)">
                        SUBMIT
                    </button>
                </form>

               
                <p class="mt-8 text-sm text-center text-[#3e3e3e]">
                    Don’t have an account?
                    <a href="/sign-up" class="text-[#2889AA] font-bold hover:underline">Sign Up</a>
                </p>
            </div>

            <div class="hidden md:block">
                <img src="{{ asset('assets/image/pa.jpg') }}" alt="Doctor"
                    class="w-full h-full object-cover rounded-lg shadow-md" />
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const togglePasswordOne = document.getElementById('togglePasswordOne');
        const passwordInputOne = document.getElementById('passwordOne');


        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });

        togglePasswordOne.addEventListener('click', function() {
            const type = passwordInputOne.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInputOne.setAttribute('type', type);
        });
    </script>

@endsection
