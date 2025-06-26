@extends('layouts.patient_portal')

@section('title', 'terms-Conditions')

@section('link')

@endsection

@section('style')
    <style>
        .custom_align {
            align-items: center;
        }
    </style>

@endsection

@section('content')
    @forelse ($page_data as $data)
        <form action="/compliance-form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="bg-gray-100">
                <div class=" flex justify-center items-center mb-5 pt-3">
                    <img src="{{ $brandlogo }}" class="max-w-[128pc] h-[44px]" alt="">
                </div>
                <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow">
                    <!-- Heading -->
                    <div>
                        <h2 class="text-[32px] font-semibold text-[#121212] mb-2">
                            Compliance Form
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
                            <div>
                                <label for="First" class="block text-sm font-medium text-gray-700 mb-1">Patient
                                    Name</label>
                                <input type="text" id="Full" placeholder="Enter your first name"
                                    name="patients_name" required value="{{ $data->patients_name }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                            </div>

                            <div>
                                <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">DOB</label>
                                <input type="date" id="middle" placeholder="Enter your last name" name="dob"
                                    required value="{{ $data->dob }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>

                    </div>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        I hereby authorize (“Sugar Pros”) to us e my image, video recording, audio recording, demographic
                        information, medical information, and personal testimony in articles, films, videotapes, books,
                        portfolios,
                        presentations, marketing materials and similar documents for Sugar Pros’ marketing, promotion and
                        advertising activities. I hereby consent to the storage and sharing of my image, video, and personal
                        testimony for Sugar Pros’ marketing, promotional, and advertising purposes. I understand this
                        information
                        will be, without limitation, released to the general public worldwide and/or posted online on the
                        Internet.
                    </p>

                    <!-- More Info -->
                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        I understand that I have the right to revoke this Marketing Authorization, in writing, at any time
                        by
                        sending such written notification to Sugar Pros at <b> [insert address] </b> Attn: <b> [insert
                            appropriate
                            internal department; e.g., Legal Department or Compliance Office] </b> , except to the extent
                        that
                        action has been taken in reliance upon my Authorization. I understand that since the disclosure of
                        my
                        medical information will be made to the public, it is possible, and even likely, that my medical
                        information
                        will be redisclosed and no longer protected by health care privacy laws. Sugar Pros will not
                        condition my
                        treatment on whether I provide authorization for the requested us e or disclosure.
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        This Marketing Authorization is valid for five (5) years from the date this Authorization is signed,
                        or the
                        period provided under applicable state law, whichever is earlier.
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        I have read the above information and authorize Sugar Pros to us e or disclose the identified
                        information
                        for the purposes described herein. <br>
                        <b> By signing your name below, you acknowledge that you have read and agree to the terms of this
                            Authorization.</b>
                    </p>

                    <div class="grid sm:grid-cols-2 gap-4 my-8">
                        <div class="align-baseline flex justify-start border-b border-[#000000]/20 custom_align">
                            <label class="text-sm text-gray-700 block font-semibold cursor-pointer" for="signature">
                                Patient Signature
                                @if (isset($data->patients_signature) && $data->patients_signature)
                                    <img id="patientSignaturePreview" src="{{ asset($data->patients_signature) }}"
                                        class="signature-preview"
                                        style="max-width: 100px; max-height: 50px; cursor: pointer; margin: 10px 1rem;">
                                @else
                                    <img id="patientSignaturePreview" class="signature-preview"
                                        style="max-width: 100px; max-height: 50px; cursor: pointer; margin: 10px 1rem;">
                                @endif
                            </label>
                            <input type="file" id="signature"
                                class="border-none outline-none rounded-md text-sm p-2 sm:w-1/2 hidden"
                                name="patients_signature" accept="image/*" onchange="showSignature(this)">
                        </div>

                        <div class="align-baseline flex justify-start border-b border-[#000000]/20 custom_align">
                            <label class="text-sm text-gray-700 block  font-semibold">DOB</label>
                            <input type="date" class=" border-none outline-none rounded-md text-sm p-2 w-full"
                                name="patients_dob" value="{{ $data->patients_dob }}">
                        </div>
                    </div>



                    <p class="text-[20px] text-[#121212] font-semibold my-4">
                        Or
                    </p>
                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        If you are the parent or personal representative of the Patient, by signing below, you acknowledge
                        that you
                        have read and agree to the terms of this Authorization.
                    </p>



                    <div class="grid sm:grid-cols-2 gap-4 my-8">


                        <div class="align-baseline flex justify-start border-b border-[#000000]/20 custom_align">
                            <label for="representative"
                                class="text-sm text-gray-700 block font-semibold cursor-pointer">Personal
                                Representative's
                                Signature
                                @if (isset($data->representative_signature) && $data->representative_signature)
                                    <img id="representativeSignaturePreview"
                                        src="{{ asset($data->representative_signature) }}" class="signature-preview"
                                        style="max-width: 100px; max-height: 50px; cursor: pointer; margin: 10px 1rem;">
                                @else
                                    <img id="representativeSignaturePreview" class="signature-preview"
                                        style="max-width: 100px; max-height: 50px; cursor: pointer; margin: 10px 1rem;">
                                @endif
                            </label>
                            <input type="file" id="representative"
                                class="border-none outline-none rounded-md text-sm p-2 sm:w-1/2 hidden"
                                name="representative_signature" accept="image/*" onchange="showSignature(this)">
                        </div>





                        <div class="align-baseline flex justify-start border-b border-[#000000]/20 custom_align">
                            <label for="patientName" class="text-sm text-gray-700 block  font-semibold">DOB</label>
                            <input type="date" id="patientName"
                                class=" border-none outline-none rounded-md text-sm p-2 w-full" name="representative_dob"
                                value="{{ $data->representative_dob }}">
                        </div>
                    </div>

                    <div class="align-baseline flex justify-start border-b border-[#000000]/20 custom_align mt-8">
                        <label for="patientName" class="text-sm text-gray-700 block  font-semibold">State the nature of your
                            relationship with the Patient and describe your authority to act for the Patient.</label>
                        <input type="text" id="patientName"
                            class="border-none outline-none rounded-md text-sm p-2 sm:w-1/2 mt-[-1rem]"
                            name="nature_with_patient" value="{{ $data->nature_with_patient }}" placeholder="Type here...">
                    </div>

                    <!-- Button -->
                    <div class="pt-6">
                        <button
                            class="px-8 py-3 font-semibold text-white transition-colors rounded-lg bg-button_lite hover:bg-opacity-90">
                            I Accept
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @empty
    @endforelse




@endsection

@section('script')

@endsection
