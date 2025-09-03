@extends('layouts.app')

@section('title', 'Home')



<style>
    .Home {
        font-weight: bold;
        color: #298AAB;
    }
</style>

@section('content')


    <section class="relative flex bg-[#e8ecef] lg:top-[-80px] z-1">
        <div
            class="w-full items-center justify-between px-6 py-12 pb-0 lg:pb-12 mx-auto max-w-7xl lg:flex heroItems relative z-10">
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

                <a href="/sign-up"
                    class="px-6 py-3 mb-8 font-semibold text-white rounded-md shadow bg-button_lite hover:bg-button inline-block">
                    Get Started in 60 Seconds
                </a>

                @forelse ($allReviews as $key1 => $review)
                    @if ($key1 == 0)
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

                            <div class="flex items-center mb-4">
                                @forelse ($users as $user)
                                    @if ($user->patient_id == $review->reviewed_by)
                                        <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}"
                                            class="w-12 h-12 mb-5 rounded-full">
                                    @endif
                                @empty
                                @endforelse
                            </div>
                            <div style="margin: 0;">
                                <p class="text-[20px] font-semibold">“{{  \Illuminate\Support\Str::limit($review->main_review, 65, '...') }}”</p>
                                <a href="/reviews" class="text-sm text-[#298AAB] underline mt-2 inline-block">
                                    See More Success Stories
                                </a>
                            </div>
                        </div>
                    @endif
                @empty
                @endforelse
            </div>

            <div class="md:hidden flex-1 justify-end items-center relative top-[-1.5rem]">
                <img src="{{ asset('assets/image/just_girl.png') }}" alt="Doctor"
                    class="max-w-md w-full h-auto object-contain" />
            </div>
        </div>

        <img src="{{ asset('assets/image/bg_frame.png') }}" alt="HeroSection" class="h-full hidden lg:block" />
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

    @include('includes.providers')

    @include('includes.patient_reviews')

    @include('includes.services')

    @include('includes.faq')





@endsection
