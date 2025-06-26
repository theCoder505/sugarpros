@extends('layouts.patient_portal')

@section('title', 'agreement-form')

@section('link')

@endsection

@section('style')


@endsection

@section('content')


    @forelse ($page_data as $data)
        <form action="/self-payment-form" method="post">
            @csrf
            <div class="bg-gray-100">
                <div class=" flex justify-center items-center mb-5 pt-3">
                    <img src="{{ $brandlogo }}" class="max-w-[128pc] h-[44px]" alt="">
                </div>
                <div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow">
                    <!-- Heading -->
                    <div>
                        <h2 class="text-[32px] font-semibold text-[#121212] mb-2">
                            Agreement for Self-Payment of Services
                        </h2>
                        <p class="text-[18px] text-[#4E4E50] mb-8">
                            [SugarPros] and affiliated medical groups (collectively, [SugarPros]) is committed to providing
                            the best
                            quality healthcare services. We do not participate in any insurance plans, including Medicare or
                            Medicaid, and we do not accept any health insurance whatsoever. Our services are 100% self-pay
                            by our
                            patients. By signing this form, you acknowledge that
                        </p>
                    </div>


                    <ul class="list-decimal list-inside space-y-2 text-[18px] text-[#4E4E50] mb-8">
                        <li>
                            You do not have any health insurance through a PPO, HMO, Medicaid or Medicare or any other
                            insurance
                            plan</li>
                        <li>
                            You have health insurance but you do not want to us e any insurance benefit for these services,
                            acknowledging that [SugarPros] does not accept any health insurance. Your insurance policy is a
                            contract
                            between you and your insurance company. It is your responsibility to know your benefits, and how
                            they
                            will apply to your benefit payments, and we take no responsibility to understand or be bound by
                            the
                            terms and conditions of such insurance.
                        </li>
                    </ul>

                    <!-- More Info -->
                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        By signing this form, you are electing to purchase services that may or may not be covered by your
                        insurance
                        if you obtained those services from a different provider. You have selected services for purchase
                        from us on
                        a self-pay basis. In other words, you have directed us to treat your purchase of these services as
                        if you
                        are an uninsured patient and you agree to be 100% responsible for full payment of the listed price
                        of the
                        services. There is no guarantee your insurance company will make any payment on the cost of the
                        services you
                        have purchased
                    </p>
                    <p class="text-[18px] text-[#4E4E50] mb-8">
                        [SugarPros] has provided you with the charges, in advance, for the services you have requested. By
                        signing
                        below, you agree to pay these charges in full as a self-pay patient, electing not to us e an
                        insurance
                        policy benefit. You have been given a choice of different services, along with their costs. You have
                        selected the services and are willing to accept full financial responsibility for payment. I have
                        read the
                        Agreement for Self-Payment of Services.
                    </p>

                    <!-- Checkbox Agreement -->
                    <div class="flex items-start gap-2 my-6 t">
                        <input type="checkbox" id="agree"
                            class="mt-1 accent-blue-600 w-4 h-4 rounded-[4px] border border-[#4E4E50]" required>
                        <label for="agree" class="text-[16px] ext-[#4E4E50]">
                            I understand and agree to this Agreement.
                        </label>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="flex border-b border-[#000000]/20">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold"> Name</label>
                            <input type="text" id="patientName"
                                class=" border-none outline-none  rounded-md p-2 text-sm max-w-1/2 mt-[-1rem]" name="user_name"
                                required value="{{ $data->user_name }}">
                        </div>
                        <div class="flex border-b border-[#000000]/20">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold">Print Patient
                                Name</label>
                            <input type="text" id="patientName"
                                class=" border-none outline-none  rounded-md p-2 text-sm max-w-1/2 mt-[-1rem]" name="patients_name"
                                required value="{{ $data->patients_name }}">
                        </div>
                        <div class="flex border-b border-[#000000]/20 justify-between">
                            <label for="patientName" class="text-sm text-gray-700 block mb-4 font-semibold">Patient
                                Signature
                                Date</label>
                            <input type="date" id="patientName"
                                class=" border-none outline-none  rounded-md p-2 text-sm max-w-1/2 mt-[-1rem]"
                                name="patients_signature_date" required value="{{ $data->patients_signature_date }}">
                        </div>

                    </div>

                    <!-- Button -->
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
