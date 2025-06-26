@extends('layouts.admin_app')

@section('title', 'E Prescriptions')

@section('link')

@endsection

@section('style')

@endsection

@section('content')

    <div class=" p-6 bg-gray-100">
        <div class=" bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    E-Prescription | {{ $appointment_uid }}
                </h1>
            </div>
            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <h2 class="font-semibold text-[20px] mb-4">Patient information</h2>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Name </label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $patient_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Patient ID</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $patient_id }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Age</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $age }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Gender</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md capitalize">
                                @if ($gender == 'm')
                                    Male
                                @elseif($gender == 'f')
                                    Female
                                @else
                                    Other
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Allergies</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $allergies }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <h2 class="font-semibold text-[20px] mb-4">Drug Info</h2>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Drug Name </label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $drug_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Strength </label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $strength }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Form Manufacturer</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $form_manufacturer }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Dose Amount</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $dose_amount }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Frequency</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $frequency }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Time Duration</label>
                            <div
                                class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md flex items-center">
                                {{ $time_duration }} <span class="ml-2 text-gray-500">Days</span>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Quantity</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $quantity }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Refills</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $refills }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Start Date</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md">
                                {{ $start_date }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection
