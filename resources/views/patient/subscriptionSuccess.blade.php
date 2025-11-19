@extends('layouts.patient_portal')
@section('title', 'Subscription Success')
@section('content')
    @include('layouts.patient_header')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4">
        <div class="bg-white shadow-lg rounded-2xl max-w-lg w-full">
            <div id="receipt" class="p-8">
                <div class="flex justify-center mb-6">
                    <div class="bg-[#298AAB] rounded-full w-20 h-20 flex items-center justify-center">
                        <i class="fas fa-check text-white text-4xl"></i>
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Payment Successful!</h2>

                <!-- Description -->
                <p class="text-gray-600 text-center mb-8">
                    Thank you for subscribing to the {{ $plan }} Plan. Your payment has been processed successfully.
                </p>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                        <span class="text-gray-600">Plan</span>
                        <span class="text-gray-900 font-semibold">{{ $plan }} Plan</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                        <span class="text-gray-600">Billing Period</span>
                        <span class="text-gray-900 font-semibold capitalize">{{ $recurring_option }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                        <span class="text-gray-600">Amount Paid</span>
                        <span class="text-gray-900 font-bold text-lg">${{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-b-gray-300 pb-1">
                        <span class="text-gray-600">Valid Until</span>
                        <span class="text-gray-900 font-semibold">{{ $expires_at }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Transaction ID</span>
                        <span class="text-gray-900 font-mono text-xs break-all">{{ $transaction_id }}</span>
                    </div>
                </div>

                <!-- What's Next Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">What's Next?</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2">
                            <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                            <span class="text-gray-700">Check your email for a confirmation receipt</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                            <span class="text-gray-700">Your account is now active with full access to all premium
                                features</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-2 h-2 bg-[#6A7282] rounded-sm flex-shrink-0 mt-2"></div>
                            <span class="text-gray-700">Schedule your first appointment with your care team</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 p-8 pt-0 no-print">
                <a href="/dashboard"
                    class="flex-1 bg-[#298AAB] hover:bg-[#247a99] text-white font-semibold px-6 py-3 rounded-lg text-center transition-colors duration-200">
                    Go to Dashboard
                </a>
                <button id="printReceipt"
                    class="flex-1 bg-white hover:bg-gray-50 text-gray-800 font-semibold px-6 py-3 rounded-lg border border-gray-300 text-center transition-colors duration-200">
                    Download Receipt
                </button>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {

            /* Hide everything except receipt */
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }

            /* Hide buttons and non-printable elements */
            .no-print {
                display: none !important;
            }

            /* Remove background colors for print */
            body {
                background: white !important;
            }

            /* Ensure proper page breaks */
            #receipt {
                page-break-inside: avoid;
            }

            /* Optimize colors for print */
            .bg-gray-50 {
                background-color: #f9fafb !important;
                border: 1px solid #e5e7eb;
            }

            /* Make sure icons print properly */
            .fa-check {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Ensure the success icon background prints */
            .bg-\[\#298AAB\] {
                background-color: #298AAB !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('script')
    <script>
        document.getElementById('printReceipt').addEventListener('click', function() {
            // Simply trigger the browser's print dialog
            window.print();
        });
    </script>
@endsection