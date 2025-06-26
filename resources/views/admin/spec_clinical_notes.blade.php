@extends('layouts.admin_app')

@section('title', 'Clinical Notes')

@section('link')


@endsection

@section('style')


@endsection


@section('content')

    <div class="min-h-screen p-6 bg-gray-100">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    Clinical Notes | {{ $appointment_uid }}
                </h1>
            </div>

            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Chief Complaint</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $chief_complaint }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">History of Present Illness</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $history_of_present_illness }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Past Medical History</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $past_medical_history }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Medications</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $medications }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Family History</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $family_history }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Social History</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $social_history }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Physical Examination</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $physical_examination }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Assessment & Plan</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $assessment_plan }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Progress Notes</label>
                            <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md">
                                {{ $progress_notes }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Provider Information</label>
                        <div class="w-full bg-gray-100 px-3 py-2 mt-1 border border-gray-300 rounded-md min-h-[96px]">
                            {{ $provider_information }}
                        </div>
                    </div>
                </div>
            </div>





        </div>



    @endsection

    @section('script')



    @endsection
