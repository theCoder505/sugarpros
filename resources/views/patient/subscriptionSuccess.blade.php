@extends('layouts.patient_portal')

@section('title', 'Subscription Success')

@section('content')
    @include('layouts.patient_header')

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-green-50 py-12 px-4">
        <div class="bg-white shadow-2xl rounded-3xl p-10 max-w-lg w-full text-center border border-gray-100">
            <div class="flex justify-center mb-6">
                <div class="bg-green-100 rounded-full p-4 shadow-lg">
                    <img src="{{ asset('assets/image/mark.png') }}" class="w-20 h-20" alt="Success">
                </div>
            </div>
            <h2 class="text-3xl font-extrabold text-green-600 mb-3 tracking-tight drop-shadow">Subscription Successful!</h2>
            <p class="text-gray-600 mb-6 text-lg">Thank you for subscribing to our service.<br>Your payment has been processed successfully.</p>
            <a href="/dashboard" class="inline-block bg-gradient-to-r from-[#2889AA] to-green-500 hover:from-green-500 hover:to-[#2889AA] text-white font-semibold px-8 py-3 rounded-xl shadow-md transition-all duration-200 transform hover:scale-105">
                Go to Dashboard
            </a>
            <div class="mt-8 flex justify-center space-x-4">
                <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                <span class="inline-block w-2 h-2 bg-blue-400 rounded-full"></span>
                <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full"></span>
            </div>
        </div>
    </div>
@endsection