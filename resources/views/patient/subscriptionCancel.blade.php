@extends('layouts.patient_portal')
@section('title', 'Subscription Cancelled')
@section('content')
    @include('layouts.patient_header')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4">
        <div class="bg-white shadow-lg rounded-2xl p-8 max-w-lg w-full">
            <!-- Error Icon -->
            <div class="flex justify-center mb-6">
                <div class="bg-[#DE474E] rounded-full w-20 h-20 flex items-center justify-center">
                    <i class="fas fa-times text-white text-4xl"></i>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Payment Failed</h2>

            <!-- Description -->
            <p class="text-gray-600 text-center mb-8">
                We were unable to process your payment. Please review the details below and try again.
            </p>

            <!-- Error Notice -->
            <div class="bg-[#FEF2F2] border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-[#DE474E] mt-0.5 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-[#DE474E] mb-1">Payment Declined</h4>
                        <p class="text-sm text-gray-700">Your card was declined. This could be due to insufficient funds, an
                            incorrect card number, or your bank blocking the transaction.</p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 space-y-4">
                <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                    <span class="text-gray-600">Plan</span>
                    <span class="text-gray-900 font-semibold">Premium Care Plan</span>
                </div>
                <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                    <span class="text-gray-600">Billing Period</span>
                    <span class="text-gray-900 font-semibold">Monthly</span>
                </div>
                <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                    <span class="text-gray-600">Amount</span>
                    <span class="text-gray-900 font-bold text-lg">${{ $amount }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Transaction ID</span>
                    <span class="text-gray-900 font-mono text-sm">{{ $transaction_id }}</span>
                </div>
            </div>

            <!-- What You Can Do Section -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">What You Can Do</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                        <span class="text-gray-700">Verify your card details and try again</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                        <span class="text-gray-700">Try using a different payment method</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                        <span class="text-gray-700">Contact your bank to authorize the transaction</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                        <span class="text-gray-700">Reach out to our support team if you need assistance</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <a href="/subscriptions"
                    class="flex-1 bg-[#298AAB] hover:bg-[#247a99] text-white font-semibold px-6 py-3 rounded-lg text-center transition-colors duration-200">
                    Try Again
                </a>
                <a href="mailto:{{ $contact_email }}"
                    class="flex-1 bg-white hover:bg-gray-50 text-gray-800 font-semibold px-6 py-3 rounded-lg border border-gray-300 text-center transition-colors duration-200">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
@endsection
