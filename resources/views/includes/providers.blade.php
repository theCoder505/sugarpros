@section('link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
@endsection


@section('style')
    <style>
        /* Owl Carousel Navigation Styles */
        .owl-carousel .owl-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            margin-top: 0;
        }

        /* For desktop - keep nav buttons at top right */
        @media (min-width: 768px) {
            .owl-carousel .owl-nav {
                position: absolute;
                top: -80px;
                right: 0;
                width: auto;
                transform: none;
                justify-content: flex-end;
            }
        }

        .owl-carousel .owl-nav button {
            background: #298AAB !important;
            color: white !important;
            width: 40px;
            height: 40px;
            border-radius: 50% !important;
            margin: 0 5px !important;
            position: relative;
        }

        /* Position arrows on sides for mobile */
        @media (max-width: 767px) {
            .owl-carousel .owl-nav button {
                position: absolute;
                margin: 0 !important;
            }

            .owl-carousel .owl-nav button.owl-prev {
                left: -40px;
            }

            .owl-carousel .owl-nav button.owl-next {
                right: 15px;
            }
        }

        .owl-carousel .owl-nav button span {
            font-size: 24px;
            line-height: 1;
            display: block;
            position: relative;
            top: -2px;
        }

        .owl-carousel .owl-dots {
            margin-top: 20px;
        }

        .owl-carousel .owl-dots button.owl-dot span {
            background: #298AAB;
            opacity: 0.3;
        }

        .owl-carousel .owl-dots button.owl-dot.active span {
            opacity: 1;
        }

        /* Make sure carousel doesn't overlap with arrows on mobile */
        @media (max-width: 767px) {
            .providers-carousel {
                padding: 0 30px;
            }
        }
    </style>
@endsection



<section>
    <div class="w-full mx-auto overflow-hidden px-6 gap-4 md:grid grid-cols-3 py-16 md:py-24 bg-[#298AAB]/10 relative">
        <div class="mb-12 ml-8 col-span-1">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                Our Team
            </span>
            <h1 class="text-[45px] mt-4 font-semibold text-font_color">
                Meet our<br>
                Providers
            </h1>
        </div>
        <div class="col-span-2 relative">
            <div class="owl-carousel providers-carousel">
                @forelse ($providers as $provider)
                    <div class="item">
                        <div class="max-w-[300px] mx-auto">
                            <a href="">
                                <img src="/{{ $provider->profile_picture }}" alt="Dr. Alexis John"
                                    class="rounded-md h-[300px] md:h-[400px] w-full object-cover">
                                <div class="mt-2 rounded-lg">
                                    <h3 class="text-xl font-bold text-font_color md:text-2xl">
                                        {{ $provider->first_name . ' ' . $provider->last_name }}
                                    </h3>
                                    <p class="mb-4 text-[#6A7282]">
                                        @switch($provider->provider_role)
                                            @case('doctor')
                                                Doctor
                                            @break

                                            @case('nurse')
                                                Nurse
                                            @break

                                            @case('mental_health_specialist')
                                                Mental Health Specialist
                                            @break

                                            @case('dietician')
                                                Dietician
                                            @break

                                            @case('medical_assistant')
                                                Medical Assistant
                                            @break
                                        @endswitch
                                    </p>
                                    <ul class="space-y-3 text-[#4A5565] list-disc pl-5">
                                        @forelse(json_decode($provider->about_me ?? '[]', true) ?? [] as $about)
                                            <li><span>{{ $about }}</span></li>
                                        @empty
                                            <li>No information available</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="item">
                        <p>No providers found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hidden md:block">
            <div class="w-[500px] h-[500px] absolute left-[100px] bottom-[115px] border-[4px] border-[#298AAB]/40 rounded-full"
                style="bottom: -200px; left: -170px;">
            </div>
            <div class="w-[400px] h-[400px] absolute border-[4px] border-[#298AAB]/40 rounded-full"
                style="bottom: -170px; left: -145px;">
            </div>
            <div class="w-[300px] h-[300px] absolute border-[4px] border-[#298AAB]/40 rounded-full"
                style="bottom: -130px; left: -120px;">
            </div>
        </div>
    </div>
</section>




@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".providers-carousel").owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    },
                    1600: {
                        items: 4
                    },
                }
            });
        });
    </script>
@endsection
