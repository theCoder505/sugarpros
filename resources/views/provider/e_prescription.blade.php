@extends('layouts.provider')

@section('title', 'E Prescriptions')

@section('link')

@endsection

@section('style')

@endsection

@section('content')

    @include('layouts.provider_header')

    <div class=" p-6 bg-gray-100">
        <div class=" bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    E-Prescription
                </h1>
            </div>
            <form class="space-y-6" action="/provider/add-e-prescription" method="POST">
                @csrf

                <div class="py-5 bg-gray-100 ">
                    <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                        <h2 class="font-semibold text-[20px] mb-4">Patient information</h2>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Name </label>
                                <input type="text" name="patient_name" required placeholder="Type here"
                                    value="{{ $patient_name }}"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Patient ID</label>
                                <input type="text" name="patient_id" required placeholder="Type here"
                                    value="{{ $patient_id }}"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Age</label>
                                <input type="text" name="age" required placeholder="Type here"
                                    value="{{ $age }}"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                    <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $gender == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Allergies</label>
                                <input type="text" name="allergies" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                        <h2 class="font-semibold text-[20px] mb-4">Drug Info</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Drug Name </label>
                                <input type="text" name="drug_name" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Strength </label>
                                <input type="text" name="strength" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Form Manufacturer</label>
                                <input type="text" name="form_manufacturer" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Dose Amount</label>
                                <input type="text" name="dose_amount" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Frequency</label>
                                <input type="text" name="frequency" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Time Duration</label>
                                <div class="relative">
                                    <input type="text" name="time_duration" required placeholder="Type here"
                                        min="1" max="365" maxlength="3"
                                        oninput="if(this.value === '' || this.value < 1) this.value='';"
                                        class="w-full bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none pr-16">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">Days</span>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Quantity</label>
                                <input type="text" name="quantity" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Refills</label>
                                <input type="text" name="refills" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" name="start_date" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <button class="bg-blue-500 py-4 px-4 text-white uppercase w-full rounded-lg text-2xl">Submit</button>
            </form>
        </div>
    </div>

@endsection

@section('script')

@endsection
