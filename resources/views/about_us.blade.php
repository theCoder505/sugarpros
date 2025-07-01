@extends('layouts.app')

@section('title', 'About-us')



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
                    <a href="/" class="hover:text-[#133A59]">Home</a>
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



    @include('includes.our_story')



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





    @include('includes.providers')

    @include('includes.patient_reviews')

    @include('includes.services')


    @include('includes.faq')


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


