@extends('layouts.app')

@section('title', 'provider Login')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('style')

@endsection

@section('content')
    <section class="flex justify-center my-24 mx-auto">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
            <div class="flex flex-col items-center mb-6">
                <div class="bg-blue-700 rounded-full p-3 mb-3">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
            </div>
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2" for="password">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="w-full px-4 py-2 pr-12 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-eye togglingIcon absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500" onclick="showPassword(this)"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox text-blue-600">
                        <span class="ml-2 text-gray-600 text-sm">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
                </div>
                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 rounded-lg transition duration-200">
                    Login
                </button>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script>
        function showPassword(passedThis){
            var passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passedThis.classList.remove('fa-eye');
                passedThis.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passedThis.classList.remove('fa-eye-slash');
                passedThis.classList.add('fa-eye');
            }
        }
    </script>

@endsection
