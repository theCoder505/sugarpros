@extends('layouts.app')

@section('title', 'About-us')

@section('link')

@endsection

@section('style')
    <style>
        @media (min-width: 768px) {
            .dot_rounded {
                right: 13px;
                top: 175px;
            }
        }

        .About {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="">
                <h1 class="mb-2 text-[40px] text-[#133A59]">About Us</h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="#" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>About Us</span>
                </div>
            </div>
        </div>
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



    <section class="px-4 py-16 bg-[#298AAB]/10">
        <div class="mx-auto text-center max-w-7xl">
            <span class="inline-block px-4 py-1 mb-4 text-xs  text-font_color bg-[#DE474E33]/20 rounded-full">
                OUR CARE REVOLUTION
            </span>
            <h2 class="mb-12 text-[40px] font-bold text-font_color  md:text-4xl">5 Radical Differences</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-6 text-left bg-white shadow rounded-xl">
                    <div
                        class="mb-4 border bg-zinc-50 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-zinc-200 ">
                        <img src="{{ asset('assets/image/h1.png') }}" class=" w-7 h-7" alt="">
                    </div>
                    <h3 class="mb-2 font-semibold text-[15px] text-[#000]">Time as Treatment</h3>
                    <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                        <li>60-minute new patient visits (industry standard: 15 mins)</li>
                        <li>30+ minute follow-ups with your actual doctor (not assistants)</li>
                    </ul>
                </div>

                <div class="p-6 text-left bg-white shadow rounded-xl">
                    <div
                        class="mb-4 border bg-zinc-50 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-zinc-200 ">
                        <img src="{{ asset('assets/image/h1.png') }}" class=" w-7 h-7" alt="">
                    </div>
                    <h3 class="mb-2 font-semibold text-[15px] text-[#000]">Tech That Cares</h3>
                    <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                        <li>AI that spots patterns humans miss</li>
                        <li>Interact with custom AI for all your solutions prior to Doctor’s appointment</li>
                    </ul>
                </div>

                <div class="p-6 text-left bg-white shadow rounded-xl">
                    <div
                        class="mb-4 border bg-zinc-50 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-zinc-200 ">
                        <img src="{{ asset('assets/image/h1.png') }}" class=" w-7 h-7" alt="">
                    </div>
                    <h3 class="mb-2 font-semibold text-[15px] text-[#000]">Always-On Support 24/7 access to</h3>
                    <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                        <li>Your care team via secure chat</li>
                        <li>Emergency diabetes specialists</li>
                    </ul>
                </div>

                <div class="gap-5 ">
                    <div class="flex items-center justify-center gap-4 px-4 py-5 mb-2 text-left bg-white shadow rounded-xl">
                        <div
                            class="mb-4 border bg-zinc-50 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-zinc-200 ">
                            <img src="/assets/image/h1.png" class=" w-7 h-7" alt="">
                        </div>
                        <h3 class="mb-2 font-semibold text-[15px] text-[#000]">Video Conferencing and Tele-healthcare</h3>
                    </div>

                    <div class="flex items-center justify-center gap-4 px-4 py-5 text-left bg-white shadow rounded-xl">
                        <div
                            class="mb-4 border bg-zinc-50 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-zinc-200 ">
                            <img src="{{ asset('assets/image/h1.png') }}" class=" w-7 h-7" alt="">
                        </div>
                        <h3 class="mb-2 font-semibold text-[15px] text-[#000]">Phone chat with Doctors</h3>
                    </div>
                </div>


            </div>
        </div>
    </section>




    <section class="bg-[#0e3757] py-12 px-4 rounded-2xl mx-4 my-16 sm:mx-8 md:m-16">
        <div class="grid max-w-6xl grid-cols-1 gap-8 mx-auto text-center text-white md:grid-cols-3">
            <div>
                <center>
                    <div
                        class="mb-4 border bg-[#FFFFFF21]/19 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-[#FFFFFF21]/13 ">
                        <img src="{{ asset('assets/image/i2.png') }}" class=" w-7 h-7" alt="">
                    </div>
                </center>
                <h3 class="mb-1 font-semibold text-[#ffffff] text-[18px]">The 80/20 Rule</h3>
                <p class="text-[14px] text-gray-300">We focus on the 20% of interventions that<br>drive 80% of results</p>
            </div>

            <div>
                <center>
                    <div
                        class="mb-4 border bg-[#FFFFFF21]/19 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-[#FFFFFF21]/13 ">
                        <img src="{{ asset('assets/image/i2.png') }}" class=" w-7 h-7" alt="">
                    </div>
                </center>
                <h3 class="mb-1 font-semibold text-[#ffffff] text-[18px]">Transparency First</h3>
                <p class="text-[14px] text-gray-300">All pricing visible before treatment begins</p>
            </div>

            <div>
                <center>
                    <div
                        class="mb-4 border bg-[#FFFFFF21]/19 w-[60px] h-[60px] flex justify-center items-center rounded-xl border-[#FFFFFF21]/13 ">
                        <img src="{{ asset('assets/image/i2.png') }}" class=" w-7 h-7" alt="">
                    </div>
                </center>
                <h3 class="mb-1 font-semibold text-[#ffffff] text-[18px]">Progress Over Perfection</h3>
                <p class="text-[14px] text-gray-300">Celebrating small wins builds lasting change</p>
            </div>
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

            <a href="#"
                class="hidden px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90">
                View all reviews
            </a>
        </div>


        <div class="grid gap-8 mt-16 md:grid-cols-3">
            <div class="p-6 ">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('assets/image/r1.png') }}" alt="Terrie Moore"
                        class="w-12 h-12 mr-3 rounded-full">
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

                    <a href="#"
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

    <section class="bg-[#0e3757] md:rounded-2xl  sm:mx-8 md:mx-16 md:my-12 overflow-hidden">
        <div class="grid items-center grid-cols-1 gap-8 mx-auto max-w-7xl md:grid-cols-2">

            <div class="p-8 space-y-4 text-white">
                <h2 class="text-[40px] font-bold leading-tight">Join the<br>Movement</h2>
                <p class="text-[16px] text-gray-100">We’re hiring mission-driven</p>
                <ul class="list-disc list-inside text-[15px] space-y-1 text-gray-100">
                    <li>Endocrinologists tired of quotas</li>
                    <li>Developers who want to fix healthcare</li>
                    <li>Care coordinators with heart</li>
                </ul>
                <a href="#"
                    class="inline-block mt-4 bg-button_lite hover:bg-button text-white font-semibold text-[16px] px-6 py-2 rounded-lg transition">
                    Get Started Now
                </a>

            </div>

            <div class="relative flex justify-center md:justify-end">
                <div class="absolute hidden md:block">
                    <div class="w-[400px] h-[400px] border-[4px] border-[#298AAB]/40 rounded-full absolute  dot_rounded"
                        style="top: 185px;right: 0px;">
                    </div>
                    <div class="w-[280px] h-[280px] border-[4px] border-[#298AAB]/40 rounded-full absolute"
                        style="right: 68px;top: 237px;">
                    </div>
                </div>
                <div class="absolute md:hidden">
                    <div class="w-[350px] h-[350px] border-[4px] border-[#298AAB]/40 rounded-full absolute  dot_rounded"
                        style="top: 151px; right: -163px;">
                    </div>
                    <div class="w-[280px] h-[280px] border-[4px] border-[#298AAB]/40 rounded-full absolute"
                        style="right: -118px; top: 208px;">
                    </div>
                </div>

                <img src="{{ asset('assets/image/fm1.png') }}" alt="Doctor"
                    class="relative z-10 w-auto max-h-[400px]">
            </div>

        </div>
    </section>





@endsection

@section('script')

@endsection
