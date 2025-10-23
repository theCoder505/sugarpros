@extends('layouts.patient_portal')

@section('title', 'Payment')

@section('link')

@endsection

@section('style')
    <style>
        .account {
            font-weight: bold;
            color: #2889AA !important;
        }

        /* Stripe Element styling */
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background-color: white;
            color: #A3A3A3;
        }

        .StripeElement--focus {
            border-color: #2889AA;
            box-shadow: 0 0 0 1px #2889AA;
        }

        .StripeElement--invalid {
            border-color: #ef4444;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
@endsection


@section('content')

    @include('layouts.patient_header')

    <div class="bg-gray-100 p-8">
        <h2 class="my-3 text-[32px] font-semibold">Payment</h2>
        <div class="w-6xl bg-[#ffffff] mx-auto p-8 rounded-md">
            <div class="bg-[#5469D4]  text-[#fff] rounded-xl text-center p-10">
                <p class="text-[16px] font-semibold uppercase">Payment integrated with </p>
                <img src="{{ asset('assets/image/stipe.png') }}" alt="" class="max-w-[90px] max-h-[40px] mx-auto mt-4">
            </div>

            <form method="POST" action="/complete-booking" class="space-y-6 mt-8" id="payment-form">
                @csrf
                <div class="my-5">
                    <h2 class="font-bold text-[20px] text-[#292524]">Payment info {{$stripe_client_id}} </h2>
                    <p class="text-sm text-[#57534E]">
                        Share the specific details below and complete the booking process.
                        You will be charged <span class="text-[#5469D4] font-semibold">{{ $amount . $currency }}</span> here.
                    </p>
                </div>

                <input type="hidden" name="fname" value="{{ $fname }}">
                <input type="hidden" name="lname" value="{{ $lname }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="patient_id" value="{{ $patient_id }}">
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="time" value="{{ $time }}">
                <input type="hidden" name="insurance_company" value="{{ $insurance_company }}">
                <input type="hidden" name="policyholder_name" value="{{ $policyholder_name }}">
                <input type="hidden" name="policy_id" value="{{ $policy_id }}">
                <input type="hidden" name="group_number" value="{{ $group_number }}">
                <input type="hidden" name="insurance_plan_type" value="{{ $insurance_plan_type }}">
                <input type="hidden" name="chief_complaint" value="{{ $chief_complaint }}">
                <input type="hidden" name="symptom_onset" value="{{ $symptom_onset }}">
                <input type="hidden" name="prior_diagnoses" value="{{ $prior_diagnoses }}">
                <input type="hidden" name="current_medications" value="{{ $current_medications }}">
                <input type="hidden" name="allergies" value="{{ $allergies }}">
                <input type="hidden" name="past_surgical_history" value="{{ $past_surgical_history }}">
                <input type="hidden" name="family_medical_history" value="{{ $family_medical_history }}">
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label for="First" class="block text-sm font-medium text-gray-700 mb-1">Full
                            Name</label>
                        <input type="text" id="Full" placeholder="Layla" name="users_full_name" required
                            class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                    </div>

                    <div>
                        <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="middle" placeholder="Barcelona, Spain" name="users_address" required
                            class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" placeholder="samantha.of@example.com" name="users_email"
                            required
                            class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                    </div>



                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <div class="relative flex items-center w-full">
                            <input type="text" id="phone" placeholder="(555) 687-9455" name="users_phone" required
                                class="w-full bg-white text-[#A3A3A3] px-3 py-2 border border-gray-300 rounded-md pr-16 outline-none" />
                            <select
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-transparent text-gray-700 text-sm focus:outline-none"
                                name="country_code" required>
                                @php
                                    $options = json_decode($prefixcodes, true);
                                @endphp
                                @if (is_array($options))
                                    @foreach ($options as $prefixcode)
                                        <option value="{{ $prefixcode }}">{{ $prefixcode }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <div id="card-element"></div>
                    </div>



                    <button type="submit" id="applyBtn"
                        class="max-w-[150px] mt-5 bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2  text-[18px] font-semibold rounded-lg transition duration-200 float-right">
                        Apply
                    </button>



                </div>
                <p class="text-[12px] text-[#4E4E5099]/60 py-4">By clicking this box, I have read and agree to the
                    payment and subscription terms set forth in our
                    <a href="/terms-of-use">Terms of Use</a> I understand that I will be charged the rate [above/displayed
                    on an
                    order form/some other place that indicates the price charged] [monthly.] as part of my subscription
                    to the [SugarPros] services. Such rates are subject to change and will be charged to my account
                    ending in [four digits] or such other account as I may place on file. My subscription to the
                    services is continuous and will be automatically renewed at the end of the applicable subscription
                    period, unless I cancel your subscription before the end of the then-current subscription period. I
                    may cancel my subscription at any time. I may cancel my subscription through my user settings or by
                    calling SugarPros phone line.
                </p>


                <div class="flex gap-4 items-center">
                    <input type="checkbox" id="checked" required>
                    <label for="checked" class="text-[#4E4E50] text-[16px]">
                        Consent for Recurring Credit or Debit Card Payments.
                    </label>
                </div>


            </form>
        </div>
    </div>



    <!-- Popup Modal -->
    <div id="popupModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
        <div class="bg-white rounded-xl px-6 py-6 w-full max-w-md mx-4">
            <div class="flex justify-end">
                <img src="{{ asset('assets/image/close.png') }}" id="closeBtn"
                    class=" cursor-pointer float-right w-6 h-6" alt="">
            </div>

            <div class="w-full flex justify-center items-center">
                <img src="{{ asset('assets/image/mark.png') }}" id="" class="w-[90px] h-[90px]"
                    alt="">
            </div>

            <div class="text-[20px] mx-auto text-center">
                <h3 class="font-semibold text-[#1E1E1E]">Payment Completed Successfully</h3>
                <p class="text-[#5D5E5E] text-sm ">Thank you for your purchase. A confirmation email has been sent to
                    your
                    inbox.</p>
            </div>

            <button id="completeCheckout"
                class="w-full py-3 mt-5 max-w-[150px] mx-auto block bg-[#2889AA] text-white rounded-lg hover:bg-opacity-90">
                Continue
            </button>
        </div>
    </div>







@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ $stripe_client_id }}");
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount("#card-element");
        const loader = `<div class="loader"></div>`;

        $(document).ready(function() {
            $("#payment-form").on("submit", async function(e) {
                e.preventDefault();

                const {
                    token,
                    error
                } = await stripe.createToken(card);
                if (error) {
                    alert(error.message);
                } else {
                    const formData = new FormData(this);
                    formData.append('stripeToken', token.id);

                    // Show loader on button
                    const $applyBtn = $('#applyBtn');
                    if ($applyBtn.length) {
                        $applyBtn.prop('disabled', true).html(loader);
                    }

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        dataType: 'json',
                        success: function(data) {
                            if ($applyBtn.length) {
                                $applyBtn.prop('disabled', false).html('Apply');
                            }
                            if (data.success) {
                                $('#payment-form')[0].reset();
                                $('#popupModal').removeClass('hidden').addClass('flex');
                            } else {
                                alert(data.message || 'Payment failed.');
                            }
                        },
                        error: function() {
                            if ($applyBtn.length) {
                                $applyBtn.prop('disabled', false).html('Apply');
                            }
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Close popup
            $('#closeBtn, #completeCheckout').on('click', function() {
                $('#popupModal').addClass('hidden').removeClass('flex');
            });
        });


        $(document).on('click', '#closeBtn, #completeCheckout', function() {
            window.location.href = '/appointments';
        });
    </script>
@endsection
