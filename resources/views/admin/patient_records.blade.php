@extends('layouts.admin_app')

@section('title', 'All Providers')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

@section('styles')

@endsection

@section('content')
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">

        <div class="min-h-screen p-4 bg-gray-100 md:p-6">
            <div class="space-y-6 rounded-md">

                @include('includes.patient_management_record')

            </div>
        </div>



    </main>
@endsection


@section('scripts')
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script src="/assets/js/patient_records.js"></script>

    <script>
        $(".patients").addClass("font-semibold");
    </script>
@endsection
