@extends('layouts.provider')

@section('title', 'Clinical Notes')

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
                    Clinical Notes
                </h1>
            </div>

            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <form class="space-y-6" action="/provider/update-clinical-notes" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                            <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">
                            <input type="hidden" name="note_id" value="{{ $note_id }}">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Chief Complaint </label>
                                <input type="text" name="chief_complaint" value="{{ $chief_complaint }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">History of Present
                                    Illness</label>
                                <input type="text" name="history_of_present_illness"
                                    value="{{ $history_of_present_illness }}" required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Past Medical History</label>
                                <input type="text" name="past_medical_history" value="{{ $past_medical_history }}"
                                    required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Medications</label>
                                <input type="text" name="medications" value="{{ $medications }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Family History</label>
                                <input type="text" name="family_history" value="{{ $family_history }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Social History</label>
                                <input type="text" name="social_history" value="{{ $social_history }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Physical Examination</label>
                                <input type="text" name="physical_examination" value="{{ $physical_examination }}"
                                    required placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Assessment & Plan</label>
                                <input type="text" name="assessment_plan" value="{{ $assessment_plan }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Progress Notes</label>
                                <input type="text" name="progress_notes" value="{{ $progress_notes }}" required
                                    placeholder="Type here"
                                    class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Provider Information</label>
                            <textarea name="provider_information"
                                class="w-full  bg-white placeholder:text-[#A3A3A3]  px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none resize-none"
                                id="" placeholder="Type here" cols="30" rows="6" required>{{ $provider_information }}</textarea>
                        </div>

                        <div class="flex gap-4 justify-end">
                            <button type="submit"
                                class="bg-blue-500 py-2 px-4 text-white w-full rounded-lg text-md max-w-[100px]">
                                Save
                            </button>
                            <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this lab?')){ this.form.action='/provider/delete-clinical-notes/{{ $note_id }}'; this.form.submit(); }"
                                class="bg-red-500 py-2 px-4 text-white w-full rounded-lg text-md max-w-[100px]">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>





        </div>



    @endsection

    @section('script')



    @endsection
