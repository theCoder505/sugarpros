@extends('layouts.provider')

@section('title', 'Quest Lab Setup')

@section('link')


@endsection

@section('style')


@endsection


@section('content')

    @include('layouts.provider_header')


    <div class="min-h-screen p-6 bg-gray-100">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    QuestLab
                </h1>
            </div>

            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <form class="space-y-6" action="/provider/add-quest-lab" method="POST">
                        @csrf
                        {{-- <input type="hidden" name="on_appointment" value="{{ $appointment_id }}"> --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Test Name </label>
                                <input type="text" name="test_name" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Test Code</label>
                                <input type="text" name="test_code" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                                <input type="text" name="category" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Specimen Type</label>
                                <input type="text" name="specimen_type" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Urgency</label>
                                <input type="text" name="urgency" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Preferred Lab Location</label>
                                <input type="text" name="preferred_lab_location" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Date</label>
                                <input type="date" name="date" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Time</label>
                                <input type="time" name="time" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Patient Name</label>
                                <input type="text" name="patient_name" value="{{ $patient_name }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Patient ID</label>
                                <input type="text" name="patient_id" value="{{ $patient_id }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Clinical Notes</label>
                                <input type="text" name="clinical_notes" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Patient Phone No</label>
                                <input type="text" name="patient_phone_no" value="{{ $patient_phone_no }}" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Insurance Provider </label>
                                <input type="text" name="insurance_provider" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Estimated Cost</label>
                                <input type="text" name="estimated_cost" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>


                        <button
                            class="bg-blue-500 py-4 px-4 text-white uppercase w-full rounded-lg text-2xl">Submit</button>
                    </form>
                </div>
            </div>





        </div>



    @endsection

    @section('script')



    @endsection
