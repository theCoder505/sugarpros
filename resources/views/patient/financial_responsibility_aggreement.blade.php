@extends('layouts.patient_portal')

@section('title', 'privacy-Consent')

@section('link')

@endsection

@section('style')

@endsection

@section('content')

    @forelse ($page_data as $data)
        <form action="/financial-form" method="post">
            @csrf
            <div class="bg-gray-100">
                <div class=" flex justify-center items-center mb-5 pt-3">
                    <img src="{{ $brandlogo }}" class="max-w-[128pc] h-[44px]" alt="">
                </div>
                <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow">

                    <div>
                        <h2 class="text-[32px] font-semibold text-[#121212] mb-2">
                            Patient Financial Responsibility Agreement
                        </h2>
                        <p class="text-[18px] text-[#4E4E50] mb-8">
                            [SugarPros] and affiliated medical groups (collectively, [SugarPros]) and its affiliated medical
                            group(s) (collectively, “Group”) are committed to providing the best quality medical services
                            (the
                            “Services”)
                            . This Patient Financial Responsibility Agreement (“Agreement”) outlines
                            yourfinancial
                            responsibility in relation to receipt of the Services from Group accepts certain insurance
                            plans,
                            including commercial payors and Medicare, etc. Please let Group know if you have medical
                            insurance
                            that
                            you plan to us e for payment of the Services. Group also offers a self-pay option for the
                            Services.
                            Please see the “Self-Payment of Services” section below for information on self-pay options
                            INSURANCE.
                            As a courtesy to its patients, Group is pleased to assist in the submission of medical insurance
                            claims
                            to insurance companies for payment.To the extent you have insurance that is accepted by Group,
                            you
                            understand and acknowledge that:
                        </p>
                    </div>




                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        Your medical insurance policy, if any, is a contract between you and your insurance company. It is
                        your
                        responsibility to know your benefits, and how they will apply to payment for the Services.
                    </p>
                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        t is your responsibility to confirm that the provider that you see at Group is aparticipating
                        provider
                        under
                        your medical insurance policy.
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        Your insurance company, including Medicare, may not cover 100% of the costs and fees associated with
                        the
                        Services, and you will be responsible for payment of any remainingbalance due for the Services,
                        including
                        without limitation, for paying co-payments, deductibles, and any other costs and fees associated
                        with
                        the
                        Services you receive that are not fully (or at all) covered by your insurance company. It is your
                        responsibility to provide Group with appropriate and current medical insurance information, and to
                        notify
                        Group immediately upon any change in your medical insurance coverage to ensure efficient claims
                        billing
                        and
                        payment. In the event that you fail to provide all necessary and current medical insurance
                        information,
                        you
                        understand that your insurance company may deny payment of claims relating to the Services, and you
                        understand that you may be 100% responsible for the costs and fees associated withthe Services.
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">

                        It is your responsibility to have obtained any and all necessary referrals and authorizations
                        required
                        prior
                        to receiving the Services from Group. If your insurance company requires a referral and you do not
                        have
                        one,
                        then you understand that you will be responsible for all the costs and fees associated with the
                        Services
                        you
                        receive.
                    </p>


                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        If your medical insurance requires a co-pay, the co-pay is required at the time the Service is
                        rendered.
                        To
                        the extent you have insurance, you further hereby authorize payment of all medical insurance
                        benefits
                        which
                        are payable to you under the terms of your medical insurance policy to be paid directly to Group for
                        the
                        Services rendered. SELF-PAYMENT OF SERVICES. Services provided by Group that are not covered by
                        medical
                        insurance or that you request not be submitted to your medical insurance are 100% self-pay by our
                        patients.
                        If you notify Group that you wish to purchase the Services on a self-pay basis either because you do
                        not
                        have medical insurance, or because you have medical insurance but you request that Group not submit
                        bills to
                        such medical insurance, you agree to be 100% responsible for full payment of the Services as set
                        forth
                        in
                        the Fee Schedule. You understand and acknowledge that if you do not have medical insurance, or you
                        communicate a preference to self-pay without using medical insurance, you will be responsible for
                        100%
                        of
                        the payment for your services.
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        *****
                    </p>

                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        If your medical insurance requires a co-pay, the co-pay is required at the time the Service is
                        rendered.
                        To
                        the extent you have insurance, you further hereby authorize payment of all medical insurance
                        benefits
                        which
                        are payable to you under the terms of your medical insurance policy to be paid directly to Group for
                        the
                        Services rendered. SELF-PAYMENT OF SERVICES. Services provided by Group that are not covered by
                        medical
                        insurance or that you request not be submitted to your medical insurance are 100% self-pay by our
                        patients.
                        If you notify Group that you wish to purchase the Services on a self-pay basis either because you do
                        not
                        have medical insurance, or because you have medical insurance but you request that Group not submit
                        bills to
                        such medical insurance, you agree to be 100% responsible for full payment of the Services as set
                        forth
                        in
                        the Fee Schedule. You understand and acknowledge that if you do not have medical insurance, or you
                        communicate a preference to self-pay without using medical insurance, you will be responsible for
                        100%
                        of
                        the payment for your services.
                    </p>




                    <div class="flex items-start gap-2 my-6 t">
                        <input type="checkbox" id="agree"
                            class="mt-1 accent-blue-600 w-4 h-4 rounded-[4px] border border-[#4E4E50]" required>
                        <label for="agree" class="text-[16px] text-[#4E4E50]">
                            I Agree
                        </label>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="flex border-b border-[#000000]/20">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold"> Name</label>
                            <input type="text" id="patientName"
                                class=" border-none outline-none  rounded-md p-2 text-sm mt-[-1rem] w-full" name="user_name"
                                required value="{{ $data->user_name }}">
                        </div>
                        <div class="flex border-b border-[#000000]/20">
                            <label for="patientName"
                                class="text-sm text-gray-700 block mb-4 font-semibold min-w-[130px]">Print Patient
                                Name</label>
                            <input type="text" id="patientName"
                                class=" border-none outline-none mt-[-1rem] rounded-md p-2 text-sm w-full"
                                name="patients_name" required value="{{ $data->patients_name }}">
                        </div>
                        <div class="flex border-b border-[#000000]/20 justify-between">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold">Patient
                                Signature
                                Date</label>
                            <input type="date" id="patientName"
                                class=" border-none outline-none mt-[-1rem] rounded-md p-2 text-sm"
                                name="patients_signature_date" value="{{ $data->patients_signature_date }}">
                        </div>
                        <div class="flex border-b border-[#000000]/20">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold">If not signed by
                                the
                                patient, please indicate relationship:</label>
                            <input type="text" id="patientName"
                                class=" border-none outline-none mt-[-1rem] rounded-md p-2 text-sm" name="relationship"
                                value="{{ $data->relationship }}">
                        </div>

                    </div>

                    <div class="pt-4">
                        <button
                            class="px-8 py-3 font-semibold text-white transition-colors rounded-lg bg-button_lite hover:bg-opacity-90">
                            Next
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
