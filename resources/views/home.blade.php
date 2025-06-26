@extends('layouts.app')

@section('title', 'Home')

@section('link')

@endsection

@section('style')
    <style>
        .Home {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')


    <section class="relative flex bg-[#e8ecef] lg:top-[-64px] z-1">
        <div class="w-full items-center justify-between px-6 py-12 pb-0 lg:pb-12 mx-auto max-w-7xl lg:flex heroItems relative z-10">
            <div class="max-w-xl mx-auto">
                <div class="mb-4">
                    <span class="inline-block px-4 py-1 text-xs font-semibold text-gray-700 bg-white rounded-full shadow">
                        TRUSTED BY 5,000+ PATIENTS ACROSS 3 STATES
                    </span>
                </div>

                <h1 class="mb-6 text-4xl font-medium leading-tight sm:text-5xl text-font_color">
                    Holistic Results-Driven <br />
                    Diabetes Care at your <br />
                    Finger-tips
                </h1>

                <p class="mb-6 text-font_normal">
                    Board-certified endocrinologists and specialty-trained providers. <br />
                    Personalized medical care. Covered by Medicare or affordable subscriptions.
                </p>

                <a href="/sign-up" class="px-6 py-3 mb-8 font-semibold text-white rounded-md shadow bg-button_lite hover:bg-button inline-block">
                    Get Started in 60 Seconds
                </a>

                <div class="relative max-w-sm p-8 space-x-4 overflow-hidden bg-white shadow-md rounded-xl">
                    <div
                        class="w-[200px] h-[200px] absolute right-[-130px] top-[-160px] border border-[#FF6500] rounded-full">
                    </div>
                    <div
                        class="w-[200px] h-[200px] absolute top-[-140px] right-[-109px]  border border-[#FF6500] rounded-full">
                    </div>
                    <div
                        class="w-[250px] h-[250px] absolute top-[-165px] right-[-126px] border border-[#FF6500] rounded-full">
                    </div>
                    <img src="https://i.pravatar.cc/40?img=1" alt="User" class="w-12 h-12 mb-5 rounded-full" />
                    <div style="margin: 0;">
                        <p class="text-[20px] font-semibold">“The service I received was absolutely amazing”</p>
                        <a href="/reviews" class="text-sm text-[#298AAB] underline mt-2 inline-block">See More Success
                            Stories</a>
                    </div>
                </div>
            </div>

            <div class="md:hidden flex-1 justify-end items-center relative top-[-1.5rem]">
                <img src="{{ asset('assets/image/just_girl.png') }}" alt="Doctor" class="max-w-md w-full h-auto object-contain" />
            </div>
        </div>

        <img src="{{ asset('assets/image/bg_frame.png') }}"  alt="HeroSection" class="h-full hidden lg:block"/>
    </section>



    <section>
        <div class="items-center justify-center px-4 py-4 mx-4 my-12 rounded-md max-w-7xl md:mx-auto bg-font_color md:flex">
            <div class="flex">
                <img src="{{ asset('assets/image/l1.png') }}" class="w-12 h-12" alt="">
                <div class="">
                    <h1 class="font-bold text-white ">
                        Tired of...?
                    </h1>
                    <p class="text-white">
                        Long waits, confusing treatment plans, unaffordable care
                    </p>
                </div>
            </div>
            <div class="w-16 mx-auto">
                <img src="{{ asset('assets/image/l3.png') }}" class="w-16 h-14" alt="">
            </div>
            <div class="flex">
                <img src="{{ asset('assets/image/l2.png') }}" class="w-12 h-12" alt="">
                <div class="">
                    <h1 class="font-bold text-white ">
                        We deliver...
                    </h1>
                    <p class="text-white">
                        Same-day appointments, clear action plans, $99/month starting
                    </p>
                </div>
            </div>
        </div>
    </section>


    <section>
        <div class="grid gap-2 px-4 mx-auto my-20 max-w-7xl md:grid-cols-2">

            <div class="order-1 md:order-2">
                <div class="mb-12">
                    <span class="text-[16px] text-font_color uppercase bg-[#DE474E33]/20 py-2 rounded-3xl px-4">
                        OUR STORY
                    </span>
                    <h1 class="mt-8 text-3xl font-bold md:text-4xl lg:text-5xl text-font_color">
                        Born out of Necessity<br>
                        Fueled by Innovation
                    </h1>
                </div>

                <div class="space-y-8">
                    <p class="text-lg md:text-xl text-[#4A5565] leading-relaxed">
                        In healthcare systems, patients often face long wait times for brief medical appointments.
                        During these short consultations, many struggle to understand complex treatment plans while also
                        worrying about financial pressures. These recurring situations reveal systemic failures in care
                        delivery that go beyond simple inefficiencies.
                    </p>

                    <div class="space-y-4">
                        <h3 class="text-xl md:text-xl text-[#1E2939]">
                            Today, were a team of:
                        </h3>
                        <ul class="space-y-3 pl-5 list-disc text-lg md:text-xl text-[#4A5565]">
                            <li>Endocrinologists tired of assembly-line medicine</li>
                            <li>Techies who believe healthcare should work like modern apps</li>
                            <li>Patient Advocates fighting for transparent, affordable care</li>
                        </ul>
                    </div>

                    <div class="pt-8">
                        <button
                            class="px-8 py-3 font-semibold text-white transition-colors rounded-lg bg-button_lite hover:bg-button">
                            Get Started
                        </button>
                    </div>
                </div>
            </div>

            <img src="{{ asset('assets/image/i1.png') }}"
                class="max-h-[42rem] order-2 mt-12 md:mt-0 mx-auto md:mx-0 md:order-1" alt="">

        </div>

    </section>


    <section>
        <div class="w-full mx-auto overflow-hidden px-6 gap-4 md:flex py-16 md:py-24 bg-[#298AAB]/10 relative">
            <div class="w-1/2 mb-12 ml-8">
                <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                    Our Team
                </span>
                <h1 class="text-[45px]  mt-4 font-semibold text-font_color">
                    Meet our<br>
                    Providers
                </h1>
            </div>
            <div class="flex w-full gap-6 pb-8 overflow-x-auto "
                style="scrollbar-color: #298AAB transparent; scrollbar-width: thin;">
                <div class="max-w-[300px] md:max-w-[300px] flex-shrink-0">
                    <a href="">
                        <img src="{{ asset('assets/image/m1.png') }}" alt="Dr. Alexis John"
                            class="rounded-md h-[300px] md:h-[400px] w-full object-cover">
                        <div class="mt-2 rounded-lg ">
                            <h3 class="text-xl font-bold text-font_color md:text-2xl">
                                Dr. Alexis John, MD
                            </h3>
                            <p class="mb-4 text-[#6A7282]">Medical Officer</p>
                            <ul class="space-y-3 text-[#4A5565] list-disc pl-5">
                                <li class="">
                                    <span>15 years at Music Clinic Endocrinology</span>
                                </li>
                                <li class="">
                                    <span>Developed this "Contrastum Care" model</span>
                                </li>
                                <li class="">
                                    <span>Personal mission: "No patient should ever feel disinclined with
                                        diabetes"</span>
                                </li>
                            </ul>
                        </div>
                    </a>
                </div>
                <div class="max-w-[300px] md:max-w-[300px] flex-shrink-0">
                    <img src="{{ asset('assets/image/m2.png') }}" alt=""
                        class="rounded-md h-[300px] md:h-[400px] w-full object-cover">
                    <div class="mt-2 rounded-lg">
                        <h3 class="text-xl font-bold text-font_color md:text-2xl">
                            Dr. Mariam Boutte, MD
                        </h3>
                        <p class="mb-4 text-[#6A7282]">Dietitian</p>
                        <ul class="space-y-3 text-[#4A5565] list-disc pl-5">
                            <li class="">
                                <span>15 years at Music Clinic Endocrinology</span>
                            </li>
                            <li class="">
                                <span>10 years in Dietetics and Nutrition</span>
                            </li>
                            <li class="">
                                <span>Personal mission: "No patient should ever feel disinclined with diabetes"</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="max-w-[300px] md:max-w-[300px] flex-shrink-0">
                    <img src="{{ asset('assets/image/m1.png') }}" alt="Dr. Alexis John"
                        class="rounded-md h-[300px] md:h-[400px] w-full object-cover">
                    <div class="mt-2 rounded-lg">
                        <h3 class="text-xl font-bold text-font_color md:text-2xl">
                            Dr. Alexis John, MD
                        </h3>
                        <p class="mb-4 text-[#6A7282]">Medical Officer</p>
                        <ul class="space-y-3 text-[#4A5565] list-disc pl-5">
                            <li class="">
                                <span>15 years at Music Clinic Endocrinology</span>
                            </li>
                            <li class="">
                                <span>Developed this "Contrastum Care" model</span>
                            </li>
                            <li class="">
                                <span>Personal mission: "No patient should ever feel disinclined with diabetes"</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="hidden md:block">
                <div class="w-[500px] h-[500px] absolute left-[100px] bottom-[115px] border-[4px] border-[#298AAB]/40 rounded-full"
                    style="bottom: -200px; left: -170px;">
                </div>
                <div class="w-[400px] h-[400px] absolute   border-[4px] border-[#298AAB]/40 rounded-full"
                    style="bottom: -170px; left: -145px;">
                </div>
                <div
                    class="w-[300px] h-[300px] absolute  border-[4px] border-[#298AAB]/40 rounded-full"style="bottom: -130px; left: -120px;">
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

            <a href="/reviews"
                class="hidden px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90">
                View all reviews
            </a>
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
                    <img src="{{ asset('assets/image/r3.png') }}" alt="Lorraine White"
                        class="w-12 h-12 mr-3 rounded-full">
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
        </div>
    </section>



    <section class="bg-[#fdf1eb] my-16 py-20 px-8 md:px-0">
        <div class="max-w-6xl mx-auto">
            <div class="items-end grid-cols-1 mb-10 lg:grid lg:grid-cols-2">
                <div class="max-w-xl">
                    <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
                        Our Services
                    </span>
                    <h2 class="mt-8 text-3xl font-bold md:text-4xl text-slate-800">Your Diabetes Care,<br> Simplified
                    </h2>
                </div>
                <div class="mt-8 lg:mt-0">
                    <p class="mb-4 text-gray-800 text-[18px]">
                        Comprehensive virtual care designed for real life—because managing diabetes shouldn’t feel like
                        a
                        second job.
                    </p>

                    <a href="/reviews"
                        class="px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90">
                        View all reviews
                    </a>
                </div>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="mt-8 md:mt-0">
                    <img src="{{ asset('assets/image/s1.png') }}" alt="Continuous Health Monitoring"
                        class="object-cover w-full mb-4 rounded-md">
                    <h3 class="mb-2 text-[24px] font-semibold text-font_color">Continuous Health Monitoring</h3>
                    <span class="text-[15px] text-gray-500">Stay ahead with seamless data tracking</span>
                    <ul class="space-y-2text-[15px] mt-5 list-disc list-inside text-gray-600">
                        <li><strong>CGM Integration:</strong> Real-time glucose monitoring with alerts.</li>
                        <li><strong>Lab Diagnostics:</strong> Automatic lab result updates in your dashboard.</li>
                        <li><strong>AI-Powered Trends:</strong> Spot patterns in your data with AI insights.</li>
                    </ul>
                </div>

                <div class="mt-8 md:mt-0">
                    <img src="{{ asset('assets/image/s2.png') }}" alt="Virtual Specialist Visits"
                        class="object-cover w-full mb-4 rounded-md">
                    <h3 class="mb-2 text-[24px] font-semibold text-font_color">Virtual Specialist Visits</h3>
                    <span class="text-[15px] text-gray-500">Stay ahead with seamless data tracking</span>
                    <ul class="space-y-2 mt-5 text-[15px] list-disc list-inside text-gray-600">
                        <li><strong>Video Consultations:</strong> HIPAA-secure Zoom visits with specialty-trained
                            providers.
                        </li>
                        <li><strong>Evidence-based cutting-edge therapy</strong></li>
                        <li><strong>Frequent Check-ins</strong></li>
                    </ul>
                </div>

                <div class="mt-8 md:mt-0">
                    <img src="{{ asset('assets/image/s3.png') }}" alt="SugarPros AI Assistant"
                        class="object-cover w-full mb-4 rounded-md">
                    <h3 class="mb-2 text-[24px] font-semibold text-font_color">SugarPros AI Assistant</h3>
                    <span class="text-[15px] text-gray-500">Stay ahead with seamless data tracking</span>
                    <ul class="space-y-2 mt-5 text-[15px] list-disc list-inside text-gray-600">
                        <li><strong>Symptom Checker:</strong> Type your concerns (e.g., “Why am I dizzy after meals?”).
                        </li>
                        <li><strong>Nutrition Guidance:</strong> Ask things like, “What’s a low-carb snack for work?”
                        </li>
                        <li><strong>Medication FAQs:</strong> Can I take metformin with coffee?</li>
                    </ul>
                </div>
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



@endsection

@section('script')

@endsection
