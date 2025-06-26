@extends('layouts.app')

@section('title', 'Review')

@section('link')

@endsection

@section('style')
   <style>
        .Reviews {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Patient Reviews 
                </h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="#" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Customer Reviews 
                    </span>
                </div>
            </div>
        </div>
    </section>



    <section class="px-4 py-12 mx-auto my-16 bg-white max-w-8xl md:px-12 lg:px-24">
        <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
            PATIENT REVIEWS
        </span>
        <div class="flex flex-col items-end justify-between mt-8 md:flex-row">
            <h2 class="text-[35px]  font-bold text-font_color mb-4 md:mb-6 max-w-[950px]">
                We have served in love and our patients have poured endless testimonials for our work.
            </h2>

           
        </div>


        <div class="grid gap-8 mt-16 md:grid-cols-3">
            <div class="p-6 ">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r1.png') }}" alt="Terrie Moore" class="w-12 h-12 mr-3 rounded-full">
                    <div>
                        <p class="text-lg font-semibold text-[#1E2939]">Terrie Moore</p>
                        <p class="text-sm text-gray-600">4 Days ago</p>
                    </div>
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4">
                    My doctor’s visit today was nothing less than awesome. Upon entering the office I was professionally
                    greeted by the office staff. Wait time was minimal. Nursing staff provided excellent service. Upon
                    enterin...
                </p>
                <a href="#" class="inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90  ">See
                    More ↓</a>
            </div>

            <div class="p-6">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r2.png') }}" alt="Mel Smith" class="w-12 h-12 mr-3 rounded-full">
                    <div>
                        <p class="text-lg font-semibold text-[#1E2939]">Mel Smith</p>
                        <p class="text-sm text-gray-600">4 Days ago</p>
                    </div>
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4">
                    I have been a patient in that practice for a while and I love the care I receive there. The office
                    staff, clerical, medical, phlebotomy, and physician are phenomenal. Has a family type atmosphere in
                    a clean and professio...
                </p>
                <a href="#" class="inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90  ">See
                    More ↓</a>
            </div>

            <div class="p-6 ">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r3.png') }}" alt="Lorraine White" class="w-12 h-12 mr-3 rounded-full">
                    <div>
                        <p class="text-lg font-semibold text-[#1E2939]">Lorraine White</p>
                        <p class="text-sm text-gray-600">4 Days ago</p>
                    </div>
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4">
                    The service I received was outstanding. Everyone, those working as the Nurse Practitioner and Dr
                    Ogwo, were kind and courteous. Their friendly nature made the visit comfortable and pleasant...
                </p>
                <a href="#" class="inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90  ">See
                    More ↓</a>
            </div>

            <div class="p-6 ">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r1.png') }}" alt="Terrie Moore" class="w-12 h-12 mr-3 rounded-full">
                    <div>
                        <p class="text-lg font-semibold text-[#1E2939]">Terrie Moore</p>
                        <p class="text-sm text-gray-600">4 Days ago</p>
                    </div>
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4">
                    My doctor’s visit today was nothing less than awesome. Upon entering the office I was professionally
                    greeted by the office staff. Wait time was minimal. Nursing staff provided excellent service. Upon
                    enterin...
                </p>
                <a href="#" class="inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90  ">See
                    More ↓</a>
            </div>

            <div class="p-6">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r2.png') }}" alt="Mel Smith" class="w-12 h-12 mr-3 rounded-full">
                    <div>
                        <p class="text-lg font-semibold text-[#1E2939]">Mel Smith</p>
                        <p class="text-sm text-gray-600">4 Days ago</p>
                    </div>
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4">
                    I have been a patient in that practice for a while and I love the care I receive there. The office
                    staff, clerical, medical, phlebotomy, and physician are phenomenal. Has a family type atmosphere in
                    a clean and professio...
                </p>
                <a href="#" class="inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90  ">See
                    More ↓</a>
            </div>
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
        <div class="max-w-7xl mx-auto grid py-8 md:py-0 grid-cols-1 md:grid-cols-2 items-center gap-8">

            <div class="text-white max-w-1/2 p-4 md:p-8">
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
