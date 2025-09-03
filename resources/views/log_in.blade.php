@extends('layouts.app')

@section('title', 'Login')

@section('link')

@endsection

@section('style')

@endsection

@section('content')
    <section class="min-h-screen flex items-center justify-center bg-white px-4">
        <div class="px-2 lg:px-8 w-full my-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col justify-center md:px-9">
                <h2 class="text-[28px] text-[#121212] font-semibold mb-1">Patient Log In </h2>
                <p class="text-[16px] text-gray-500 mb-6">
                    Enter your credentials to access your account
                </p>

                @php
                    if (session('email')) {
                        $email = session('email');
                        $password = session('password');
                    } else {
                        $email = '';
                        $password = '';
                    }
                @endphp

                <form method="POST" action="/login-existing-user" class="space-y-8">
                    @csrf
                    <div>
                        <label for="email" class="block text-[16px] font-semibold text-[#000000]">Email</label>
                        <input type="email" id="email" name="email"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your email" value="{{ $email }}" />
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-[16px] font-semibold text-[#000000]">Password</label>
                        <input type="password" id="password" name="password"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-4 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your password" value="{{ $password }}" />

                        <img id="togglePassword" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                            class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />

                        <div class="text-right mt-2">
                            <a href="/forgot-password" class="text-sm text-[#3D3D3D] underline hover:text-button">Forgot
                                Password?</a>
                        </div>
                    </div>


                    <button type="submit"
                        class="w-full bg-[#2889AA] text-[18px] hover:bg-opacity-90 text-white py-4 px-4 rounded-md font-bold mt-5">
                        Log In
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

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    </script>

@endsection
