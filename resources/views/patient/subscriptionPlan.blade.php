@extends('layouts.patient_portal')

@section('title', 'Subscribe To Our ' . $recurring_option . ' ' . $plan . ' Plan')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .Pricing {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')





    <div class="bg-gray-100 p-8">
        <h2 class="my-3 text-[32px] font-semibold capitalize">{!! 'Subscribe To The <span class="text-[#2889AA]">' . $recurring_option . ' ' . $plan . '</span> Plan' !!}</h2>
        <div class="w-6xl bg-[#ffffff] mx-auto p-8 rounded-md">
            <div class="bg-[#5469D4]  text-[#fff] rounded-xl text-center p-10">
                <p class="text-[16px] font-semibold uppercase">Payment integrated with </p>
                <img src="{{ asset('assets/image/stipe.png') }}" alt="" class="max-w-[90px] max-h-[40px] mx-auto mt-4">
            </div>

            <form method="POST" action="/complete-subscription" class="space-y-6 mt-8" id="payment-form">
                @csrf

                <input type="hidden" name="recurring_option" value="{{ $recurring_option }}">
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="my-5">
                    <h2 class="font-bold text-[20px] text-[#292524]">Payment info</h2>
                    <p class="text-sm text-[#57534E] ">Share the specific details below and complete the booking process</p>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label for="First" class="block text-sm font-medium text-gray-700 mb-1">Full
                            Name</label>
                        <input type="text" id="Full" placeholder="Layla" name="users_full_name" required
                            class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                    </div>

                    <div>
                        <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="middle" placeholder="Barcelona, Spain" name="users_address" required
                            class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" placeholder="samantha.of@example.com" name="users_email"
                            required
                            class="w-full  bg-white placeholder:text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                    </div>



                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <div class="relative flex items-center w-full">
                            <input type="text" id="phone" placeholder="(555) 687-9455" name="users_phone" required
                                class="w-full bg-white placeholder:text-[#A3A3A3] px-3 py-2 border border-gray-300 rounded-md pr-16 outline-none" />
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

                    <div id="card-errors" class="text-red-500 text-sm mt-2"></div>


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

@endsection


@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ $stripe_client_id }}");
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
            },
        });

        card.mount("#card-element");

        // Handle real-time validation errors from the Card Element
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        $(document).ready(function() {
            $("#payment-form").on("submit", async function(e) {
                e.preventDefault();

                // Validate form
                if (!$('#checked').is(':checked')) {
                    alert('Please agree to the terms and conditions.');
                    return;
                }

                const $applyBtn = $('#applyBtn');

                $applyBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                try {
                    const {
                        token,
                        error
                    } = await stripe.createToken(card);

                    if (error) {
                        alert('Card error: ' + error.message);
                        $applyBtn.prop('disabled', false).html('Apply');
                        return;
                    }

                    const formData = new FormData(this);
                    formData.append('stripeToken', token.id);

                    const response = await fetch($(this).attr('action'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.success) {
                        window.location.href = "{{ route('subscription.success') }}";
                    } else {
                        alert(data.message || 'Payment failed. Please try again.');
                        $applyBtn.prop('disabled', false).html('Apply');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error. Please check your connection and try again.');
                    $applyBtn.prop('disabled', false).html('Apply');
                }
            });
        });
    </script>
@endsection
