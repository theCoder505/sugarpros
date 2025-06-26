@extends('layouts.patient_portal')

@section('title', 'privacy-policy')

@section('link')

@endsection

@section('style')


@endsection

@section('content')

    @forelse ($page_data as $data)
        <form action="/privacy-form" method="post">
            @csrf
            <div class="bg-gray-100">
                <div class=" flex justify-center items-center mb-5 pt-3">
                    <img src="{{ $brandlogo }}" class="max-w-[128pc] h-[44px]" alt="">
                </div>
                <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow">
                    <div class=" mb-8">

                        <h2 class="text-[32px] font-semibold text-[#121212] mt-4">
                            Privacy Form and Consent Form
                        </h2>
                    </div>

                    <div class="space-y-6 text-sm text-gray-800">
                        <p class=" text-[18px] text-[#4E4E50]">
                            This Notice of Privacy Practices (the “Notice”) describes how [SugarPros] (“we” or “our”) may us
                            and
                            disclose your protected health information to carry out treatment, payment, or business
                            operations
                            and
                            for other purposes that are permitted or required by law. “Protected health information” or
                            “PHI” is
                            information about you, including demographic information, that may identify you and that relates
                            to
                            your
                            past, present or future physical health or condition, treatment, or payment for health care
                            services.
                            This Notice also describes your rights to access and control your protected health information.
                        </p>

                        <h3 class="font-semibold text-[20px]">
                            us eS AND DISCLOSURES OF PROTECTED HEALTH INFORMATION:
                        </h3>
                        <p class=" text-[18px] text-[#4E4E50]">
                            Your protected health information may be us ed and disclosed
                            by our health care providers...
                        </p>

                        <h4 class="font-semibold">TREATMENT:</h4>
                        <p class=" text-[18px] text-[#4E4E50]">
                            We will ue and disclose your protected health information
                            to provide, coordinate, or manage your health care...
                        </p>

                        <h4 class="font-semibold">PAYMENT:</h4>
                        <p class=" text-[18px] text-[#4E4E50]">
                            Your protected health information may be us ed to bill or
                            obtain payment for your health care services...
                        </p>

                        <h4 class="font-semibold">HEALTH CARE OPERATIONS:</h4>
                        <p class=" text-[18px] text-[#4E4E50]">
                            We may us e or disclose, as needed, your protected health
                            information in order to support the business activities...
                        </p>

                        <h3 class="font-semibold text-[20px]">
                            us eS AND DISCLOSURES THAT DO NOT REQUIRE YOUR AUTHORIZATION:
                        </h3>
                        <p class=" text-[18px] text-[#4E4E50]">
                            We may us e or disclose your protected health information in
                            certain situations without your authorization...
                        </p>

                        <h3 class="font-semibold text-[20px]">
                            us eS AND DISCLOSURES THAT REQUIRE YOUR AUTHORIZATION:
                        </h3>
                        <p class=" text-[18px] text-[#4E4E50]">
                            Other permitted and required us es and disclosures will be
                            made only with your consent, authorization or opportunity to
                            object unless permitted or required by law...
                        </p>

                        <h3 class="font-semibold text-[20px]">
                            YOUR RIGHTS WITH RESPECT TO YOUR PROTECTED HEALTH
                            INFORMATION:
                        </h3>
                        <ul class=" list-decimal list-inside space-y-2 px-2">
                            <li>
                                You have the right to request a restriction on the us e
                                or disclosure of your protected health information...
                            </li>
                            <li>
                                You have the right to request to receive confidential
                                communications from us...
                            </li>
                            <li>
                                You have the right to access, inspect, and copy your
                                protected health information...
                            </li>
                            <li>
                                You have the right to request an amendment of your
                                protected health information...
                            </li>
                            <li>
                                You have the right to receive an accounting of
                                disclosures...
                            </li>
                            <li>
                                You have the right to obtain a paper copy of this
                                Notice...
                            </li>
                            <li>
                                We will notify you if a breach of your unsecured
                                protected health information is discovered.
                            </li>
                        </ul>

                        <h3 class="font-semibold text-[20px]">
                            REVISIONS TO THIS NOTICE:
                        </h3>
                        <p class=" text-[18px] text-[#4E4E50]">We reserve the right to revise this Notice...</p>

                        <h3 class="font-semibold text-[20px]">COMPLAINTS:</h3>
                        <p class=" text-[18px] text-[#4E4E50]">
                            Complaints about this Notice or how we handle your protected
                            health information should be directed to our HIPAA Privacy
                            Officer...
                        </p>

                        <p class=" text-gray-600">
                            This Notice was originally published and effective on
                            <strong>1st May 2025</strong>.
                        </p>
                    </div>

                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold mb-4">
                            Acknowledgement of Receipt of Notice of Privacy Practices
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-8">

                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First
                                    Name</label>
                                <input type="text" id="firstName" placeholder="Patient First Name" name="fname"
                                    value="{{ $data->fname }}" required
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Last
                                    Name</label>
                                <input type="text" id="firstName" placeholder="Patient Last Name" name="lname" required
                                    value="{{ $data->lname }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="firstName" placeholder="Date" name="date" required
                                    value="{{ $data->date }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                        </div>

                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Your massage</label>

                            <textarea placeholder="Your Message" rows="4" name="users_message" required
                                class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">{{ $data->users_message }}</textarea>
                        </div>


                        <div class="space-y-4 my-6">
                            {{-- <label class="flex items-start space-x-2">
                            <input type="checkbox" class="mt-1" />
                            <span>I have received or been given an opportunity to
                                receive <strong>SugarPros’s</strong> Notice of
                                Privacy Practices.</span>
                        </label> --}}
                            <label class="flex items-start space-x-2">
                                <input type="checkbox" class="mt-1" name="notice_of_privacy_practice"
                                    @if ($data->notice_of_privacy_practice == 'on') checked @endif />
                                <span>I have received or been given an opportunity to
                                    receive <strong>SugarPros’s</strong> Notice of
                                    Privacy Practices on behalf of:</span>
                            </label>
                        </div>



                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Patient’s Name</label>
                                <input type="text" placeholder="Patient's Name" name="patients_name"
                                    value="{{ $data->patients_name }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Personal Representative’s
                                    Name</label>
                                <input type="text" placeholder="Personal Representative’s Name"
                                    name="representatives_name" value="{{ $data->representatives_name }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" placeholder="Date" name="service_taken_date"
                                    value="{{ $data->service_taken_date }}"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>

                        </div>



                        <div class="my-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">State the nature of your
                                relationship
                                with
                                the Patient and describe your authority to act for the Patient.</label>

                            <textarea placeholder="State your authority to act for the Patient" rows="3" name="relation_with_patient"
                                class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">{{ $data->relation_with_patient }}</textarea>
                        </div>



                        <button
                            class="px-8 py-3 font-semibold text-white transition-colors rounded-lg bg-button_lite hover:bg-button">
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
