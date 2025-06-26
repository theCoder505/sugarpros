@extends('layouts.admin_app')

@section('title', 'Quest Lab Setup')

@section('link')


@endsection

@section('style')


@endsection


@section('content')
    <div class="min-h-screen p-6 bg-gray-100">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    QuestLab | {{ $appointment_uid }}
                </h1>
            </div>

            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Test Name</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $test_name }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Test Code</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $test_code }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $category }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Specimen Type</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $specimen_type }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Urgency</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $urgency }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Preferred Lab Location</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $preferred_lab_location }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Date</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $date }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Time</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $time }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Patient Name</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $patient_name }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Patient ID</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $patient_id }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Clinical Notes</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $clinical_notes }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Patient Phone No</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $patient_phone_no }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Insurance Provider</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $insurance_provider }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Estimated Cost</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-200 rounded-md text-gray-800">
                                {{ $estimated_cost }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    @endsection

    @section('script')



    @endsection
