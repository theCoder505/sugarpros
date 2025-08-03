@extends('layouts.patient_portal')

@section('title', 'Subscription Cancelled')

@section('content')
    @include('layouts.patient_header')

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-green-50 py-12 px-4">
        <div class="bg-white shadow-2xl rounded-3xl p-10 max-w-lg w-full text-center border border-gray-100">
            <div class="flex justify-center mb-6">
                <div class="bg-red-100 rounded-full w-[100px] h-[100px] flex justify-center items-center shadow-lg">
                    <i class="fas fa-times text-red-500 text-5xl"></i>
                </div>
            </div>
            <h2 class="text-3xl font-extrabold text-red-600 mb-3 tracking-tight drop-shadow">Subscription Cancelled</h2>
            <p class="text-gray-600 mb-6 text-lg">Your subscription process was not completed.<br>No charges have been made to your account.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/subscriptions" class="inline-block bg-gradient-to-r from-[#2889AA] to-red-400 hover:from-red-400 hover:to-[#2889AA] text-white font-semibold px-8 py-3 rounded-xl shadow-md transition-all duration-200 transform hover:scale-105">
                    Try Again
                </a>
                <a href="/dashboard" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-semibold px-8 py-3 rounded-xl shadow-md transition-all duration-200 transform hover:scale-105">
                    Go to Dashboard
                </a>
            </div>
            <div class="mt-8 flex justify-center space-x-4">
                <span class="inline-block w-2 h-2 bg-red-400 rounded-full"></span>
                <span class="inline-block w-2 h-2 bg-blue-400 rounded-full"></span>
                <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full"></span>
            </div>
        </div>
    </div>
@endsection