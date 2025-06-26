@extends('layouts.app')

@section('title', 'Pricing')

@section('link')

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

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Pricing
                </h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="#" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Pricing</span>
                </div>
            </div>
        </div>
    </section>






    <section class="px-6 py-16 bg-white md:px-20">
        <div class="mx-auto mb-12 ">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
                PRICING
            </span>
            <h2 class="text-[35px]  font-bold text-font_color mt-8">
                We believe world-class_diabetes care should be accessible and affordable.
            </h2>
            <p class="mt-3 text-gray-600 text-[20px]">
                No hidden fees. No surprise bills. Just straightforward pricing that puts your health first.
            </p>
        </div>

        <div class="gap-4 mx-auto md:flex max-w-8xl">
            <div class="bg-slate-50 mx-auto max-w-[320px] rounded-xl p-6 flex flex-col justify-between"
                style="box-shadow: 0px 14px 84px 0px #0000001F;">
                <div>
                    <h3 class="mb-2 text-2xl font-semibold text-font_color">For Medicare Patients</h3>
                    <p class="mb-4 text-sm text-gray-800">$0 OUT-OF-POCKET FOR CORE SERVICES</p>
                    <p class="text-[16px] text-gray-800 mb-4">Your complete diabetes care covered by Medicare:
                    </p>

                    <ul class="list-disc pl-5 text-[16px] pb-6 text-gray-600 space-y-4 border-b border-b-gray-300">

                        <li>Monthly virtual visits with endocrinologists</li>
                        <li>Continuous glucose monitors (CGMs)</li>
                        <li>Full access to SugarPros AI</li>
                        <li>Annual comprehensive diabetes evaluation</li>
                    </ul>

                    <p class="mt-4 text-[16px] text-gray-800">Optional wellness add-ons available at $50/month</p>
                </div>


                <div class="mt-5">
                    <a href="#"
                        class="px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90 w-full text-center text-[16px] font-bold">
                        Continue with plan
                    </a>
                </div>
            </div>

            <div class="hidden max-w-5xl overflow-hidden bg-white shadow rounded-xl md:block"
                style="border: 1px solid #D1D5DC;">
                <div class="p-6 border-b border-gray-300">
                    <h3 class="text-[24px] font-semibold text-font_color">Subscription Plans (Self-Pay)</h3>
                    <p class="mt-1 text-sm text-gray-800">CHOOSE THE PLAN THAT FITS YOUR NEEDS:</p>
                </div>
                <div class="mx-4 overflow-x-auto">
                    <table class="min-w-full text-[16px] text-left">
                        <thead class="border-b border-gray-300 text-slate-700">
                            <tr>
                                <th class="px-4 py-3 font-medium border-r border-gray-300">Plan</th>
                                <th class="px-4 py-3 font-medium border-r border-gray-300">Basic</th>
                                <th class="px-4 py-3 font-medium border-r border-gray-300">Premium</th>
                                <th class="px-4 py-3 font-medium">VIP</th>
                            </tr>
                        </thead>

                        <tbody class="border-gray-300 text-slate-600">
                            <tr class="border-b border-gray-300">
                                <td class="px-4 py-3 font-medium border-r border-gray-300">Monthly</td>
                                <td class="px-4 py-3 border-r border-gray-300">$99</td>
                                <td class="px-4 py-3 border-r border-gray-300">$149</td>
                                <td class="px-4 py-3 ">$199</td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="px-4 py-3 font-medium border-r border-gray-300">Annual (Save 10%)</td>
                                <td class="px-4 py-3 border-r border-gray-300">$1,069</td>
                                <td class="px-4 py-3 border-r border-gray-300">$1,609</td>
                                <td class="px-4 py-3">$2,149</td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="px-4 py-3 font-medium border-r border-gray-300">Best For</td>
                                <td class="px-4 py-3 border-r border-gray-300">Maintenance care</td>
                                <td class="px-4 py-3 border-r border-gray-300">Advanced needs</td>
                                <td class="px-4 py-3">Comprehensive care</td>
                            </tr>



                            <tr class="align-top border-gray-200">
                                <td class="px-4 py-3 font-medium border-r border-gray-300">
                                    Whats Included
                                </td>

                                <td class="px-4 py-3 align-top border-r border-gray-300">
                                    <div class="flex flex-col justify-between h-full min-h-[240px]">
                                        <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                            <li>1 visit/month</li>
                                            <li>Medication management</li>
                                            <li>Basic lab review</li>
                                        </ul>
                                        <div class="mt-4">
                                            <button
                                                class="w-full bg-[#298AAB1A]/10 text-[#133A59] px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                                                Continue with Basic
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top border-r border-gray-300">
                                    <div class="flex flex-col justify-between h-full min-h-[240px]">
                                        <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                            <li>Everything in Basic</li>
                                            <li>CGM data analysis</li>
                                            <li>Weight loss support</li>
                                            <li>Urgent care messaging</li>
                                        </ul>
                                        <div class="mt-4">
                                            <button
                                                class="w-full bg-[#298AAB1A]/10 text-[#133A59] px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                                                Continue with Premium
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col justify-between h-full min-h-[240px]">
                                        <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                            <li>Everything in Premium</li>
                                            <li>Unlimited provider messaging</li>
                                            <li>Advanced testing</li>
                                            <li>Dedicated care coordinator</li>
                                        </ul>
                                        <div class="mt-4">
                                            <button
                                                class="w-full bg-[#298AAB1A]/10 text-[#133A59] px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                                                Continue with VIP
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>




                        </tbody>
                    </table>
                </div>
            </div>

            <section class="block p-4 space-y-4 md:hidden">
                <div class="p-4 bg-white border border-gray-300 shadow-sm rounded-xl">
                    <div class="">
                        <div class="flex justify-between">
                            <h3 class="text-[20px] text-font_color font-semibold">Basic</h3>
                            <p class="text-[20px] text-font_color font-bold">$99 <span
                                    class="text-sm font-normal text-gray-500">/m</span></p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-800">Annual<br>(Save 10%)</p>
                            <p class="text-[16px] text-gray-600">$1,069</p>
                        </div>
                    </div>
                    <p class="mt-4 text-[16px] font-medium text-gray-500">Best For</p>
                    <p class="text-[16px] text-gray-600">Maintenance care</p>
                    <ul class="mt-4 space-y-1 text-[16px] text-gray-600 list-disc list-inside">
                        <li>1 visit/month</li>
                        <li>Medication management</li>
                        <li>Basic lab review</li>
                    </ul>
                    <div class="mt-8">
                        <button
                            class="w-full bg-[#298AAB1A]/10 text-[#133A59]  px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                            Continue with basic
                        </button>
                    </div>
                </div>




                <div class="p-4 bg-white border border-gray-300 shadow-sm rounded-xl">
                    <div class="">
                        <div class="flex justify-between">
                            <h3 class="text-[20px] text-font_color font-semibold">Premium</h3>
                            <p class="text-[20px] text-font_color font-bold">$99 <span
                                    class="text-sm font-normal text-gray-500">/m</span></p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-800">Annual<br>(Save 10%)</p>
                            <p class="text-[16px] text-gray-600">$1,069</p>
                        </div>
                    </div>
                    <p class="mt-4 text-[16px] font-medium text-gray-500">Best For</p>
                    <p class="text-[16px] text-gray-600">Maintenance care</p>
                    <ul class="mt-4 space-y-1 text-[16px] text-gray-600 list-disc list-inside">
                        <li>1 visit/month</li>
                        <li>Medication management</li>
                        <li>Basic lab review</li>
                    </ul>
                    <div class="mt-8">
                        <button
                            class="w-full bg-[#298AAB1A]/10 text-[#133A59]  px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                            Continue with basic
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-white border border-gray-300 shadow-sm rounded-xl">
                    <div class="">
                        <div class="flex justify-between">
                            <h3 class="text-[20px] text-font_color font-semibold">VIP</h3>
                            <p class="text-[20px] text-font_color font-bold">$99 <span
                                    class="text-sm font-normal text-gray-500">/m</span></p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-800">Annual<br>(Save 10%)</p>
                            <p class="text-[16px] text-gray-600">$1,069</p>
                        </div>
                    </div>
                    <p class="mt-4 text-[16px] font-medium text-gray-500">Best For</p>
                    <p class="text-[16px] text-gray-600">Maintenance care</p>
                    <ul class="mt-4 space-y-1 text-[16px] text-gray-600 list-disc list-inside">
                        <li>1 visit/month</li>
                        <li>Medication management</li>
                        <li>Basic lab review</li>
                    </ul>

                    <div class="mt-8">
                        <button
                            class="w-full bg-[#298AAB1A]/10 text-[#133A59]  px-2 py-2 rounded-md border border-[#298AAB4D]/30 text-[15px]">
                            Continue with basic
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </section>



    <section class="px-6 py-12 bg-white md:px-20">
        <div class="mb-6">
            <span class="text-font_color bg-[#298AAB33]/20 px-2 py-1 rounded">FAQs</span>
            <h2 class="text-[45px] font-semibold mt-4 text-font_color">Frequently asked questions.</h2>
        </div>

        <div class="space-y-2" id="faq-container">
            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">01</span>How is SugarPros
                        different from other
                        telemedicine diabetes services?</span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p class="mb-2">We provide <strong>endocrinologist-led care</strong> (not general practitioners)
                        with:</p>
                    <ul class="ml-6 space-y-1 list-disc text-slate-600">
                        <li>3–5× longer appointment times</li>
                        <li>AI-powered care</li>
                        <li>Remote Appointment</li>
                        <li>Chatbot and telemedicine</li>
                        <li class="text-slate-400">Transparent pricing with no surprise bills</li>
                    </ul>
                </div>
            </div>

            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">02</span>What insurance do you
                        accept?</span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p>We accept most major insurance providers. Please contact support for a full list.</p>
                </div>
            </div>

            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">03</span>How quickly can I get
                        an
                        appointment?</span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p>Appointments are usually available within 24–48 hours.</p>
                </div>
            </div>
            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">04</span>How does the CGM
                        program work?
                    </span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p>Appointments are usually available within 24–48 hours.</p>
                </div>
            </div>
            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">05</span>Can you help with
                        diabetes-related mental health issues?
                    </span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p>Appointments are usually available within 24–48 hours.</p>
                </div>
            </div>
            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">06</span>What if I need
                        in-person care?
                    </span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-[18px] text-gray-600">
                    <p>Appointments are usually available within 24–48 hours.</p>
                </div>
            </div>

        </div>
    </section>




    <section class="bg-[#0e3757] md:rounded-2xl   md:mx-16 md:my-12 overflow-hidden">
        <div class="grid items-center grid-cols-1 gap-8 py-8 mx-auto max-w-7xl md:py-0 md:grid-cols-2">

            <div class="p-4 text-white max-w-1/2 md:p-8">
                <h2 class="text-[40px] font-bold leading-tight md:max-w-[320px]">Let’s Get Started</h2>
                <a href="#"
                    class="inline-block mt-4 bg-button_lite hover:bg-opacity-90 text-white font-semibold text-[18px] px-6 py-2 rounded-lg transition">
                    Sign Up Now
                </a>

            </div>

            <div class="max-w-1/2">
                <img src="{{ asset('assets/image/doctor.png') }}" alt="Doctor" class="w-auto max-h-[300px]">
            </div>

        </div>
    </section>







@endsection

@section('script')

@endsection
