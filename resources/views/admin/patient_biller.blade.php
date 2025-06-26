@extends('layouts.admin_app')

@section('title', 'Patient Claims Biller')

@section('link')
    <link rel="stylesheet" href="https://cdn.claim.md/sdk/latest/css/claimmd.css">
@endsection


@section('styles')
    <style>
        .claim-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">
        <div class="bg-gray-100 min-h-screen p-6">
            <div class="container mx-auto">
                <div class="claim-container">
                    <h1 class="text-2xl font-bold mb-6">Patient Claims Biller</h1>

                    <div id="claimmd-container"></div>

                    <div class="mt-6">
                        <p class="text-gray-600">Use the interface above to submit and track insurance claims.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(".patinet_biller").addClass("font-semibold");

        document.addEventListener('DOMContentLoaded', function() {
            var script = document.createElement('script');
            script.src = "https://cdn.claim.md/sdk/latest/js/claimmd.js";
            script.onload = function() {
                if (typeof claimmd !== 'undefined') {
                    claimmd.init({
                        clientId: '{{ $CLAIM_MD_CLIENT_ID }}',
                        environment: '{{ $CLAIM_MD_ENV }}',
                        container: 'claimmd-container',
                    });
                }
            };
            document.body.appendChild(script);
        });
    </script>
@endsection
