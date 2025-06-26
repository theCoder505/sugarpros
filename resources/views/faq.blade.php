@extends('layouts.app')

@section('title', 'FAQs')

@section('link')

@endsection

@section('style')
    <style>
        .FAQs {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">FAQs
                </h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="#" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>FAQs</span>
                </div>
            </div>
        </div>
    </section>










    <section class="px-6 py-12 bg-white md:px-20">
        <div class="mb-12 text-center">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
                FAQS
            </span>
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
