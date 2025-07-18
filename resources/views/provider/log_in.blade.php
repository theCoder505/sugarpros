@extends('layouts.app')

@section('title', 'provider Login')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('style')

@endsection

@section('content')
    <section class="min-h-screen flex items-center justify-center bg-white px-4">
        <div class="px-2 lg:px-8 w-full my-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex flex-col justify-center md:px-9">
                <h2 class="text-[28px] text-[#121212] font-semibold mb-1">Provider Log In </h2>
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

                @if (session('email'))
                    @php
                        $email = session('email');
                        $password = session('password');
                    @endphp
                @else
                    @php
                        $email = '';
                        $password = '';
                    @endphp
                @endif

                <form method="POST" action="/provider/sign-in" class="space-y-8">
                    @csrf
                    <div>
                        <label for="email" class="block text-[16px] font-semibold text-[#000000]">Email</label>
                        <input type="email" id="email" name="email"
                            class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your email" value="{{ $email }}" />
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-[16px] font-semibold text-[#000000]">Password</label>
                        <input type="password" id="password" name="password"
                            class="block w-full px-3 py-4 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter your password" value="{{ $password }}" />

                        <img id="togglePassword" src="{{ asset('assets/image/eye.png') }}" alt="Toggle Password"
                            class="absolute right-4 top-[42px] w-5 h-5 cursor-pointer" />

                        <div class="mt-2 text-right">
                            <a href="#" class="text-sm text-[#3D3D3D] underline hover:text-button">Forgot
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
                    <a href="/provider/sign-up" class="text-[#2889AA] font-bold hover:underline">Sign Up</a>
                </p>
            </div>

            <div class="hidden md:block">
                <img src="{{ asset('assets/image/l1.jpg') }}" alt="Doctor"
                    class="w-full h-full object-cover rounded-lg shadow-md" />
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
    </script>

@endsection
