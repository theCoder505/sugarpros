@extends('layouts.app')

@section('title', 'Service')

@section('link')

@endsection

@section('style')
    <style>
        .Services {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Our Services</h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="/" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Our Services</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 mx-auto bg-white">
        <div class="text-center max-w-[530px] mx-auto mb-12">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                OUR SERVICES
            </span>
            <h2 class="mt-4 text-3xl font-semibold md:text-5xl text-font_color">Your Diabetes Care, Simplified</h2>
            <p class="mt-3 text-gray-800 text-[15px]">Comprehensive virtual care designed for real life—because managing
                diabetes shouldn't feel like a second job.</p>
        </div>

        <div class="space-y-16">
            @forelse ($allServices as $key => $service)
                @php
                    $headings = json_decode($service->service_points);
                    $shortnotes = json_decode($service->service_point_details);
                @endphp
                
                @if($key % 2 == 0)
                    <div class="md:py-8 while_odd">
                        <div class="flex flex-col items-center max-w-5xl gap-6 p-6 mx-auto md:flex-row">
                            <img src="{{ asset($service->service_image) }}" alt="{{ $service->service_title }}"
                                class="w-full md:max-h-[400px] md:w-1/2 rounded-xl shadow-sm">
                            <div class="md:w-1/2">
                                <h3 class="text-[35px] font-semibold text-font_color">{{ $service->service_title }}</h3>
                                <p class="text-[15px] text-gray-500">Stay ahead with seamless data tracking</p>
                                <ul class="list-disc pl-5 mt-3 space-y-4 text-gray-600 text-[15px]">
                                    @if($headings && $shortnotes)
                                        @for($i = 0; $i < count($headings); $i++)
                                            <li>
                                                <strong>{{ $headings[$i] ?? '' }}:</strong> 
                                                {{ $shortnotes[$i] ?? '' }}
                                            </li>
                                        @endfor
                                    @endif
                                </ul>
                                <div class="mt-10">
                                    <a href="/sign-up"
                                        class="mt-4 px-4 py-4 text-white rounded-lg bg-button_lite hover:opacity-90 text-[16px]">
                                        Get Started
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-[#FF6500]/10 md:py-8 while_even">
                        <div class="flex flex-col-reverse items-center max-w-5xl gap-6 p-6 mx-auto md:flex-row rounded-xl">
                            <div class="md:w-1/2">
                                <h3 class="text-[35px] font-semibold text-font_color">{{ $service->service_title }}</h3>
                                <p class="text-[15px] text-gray-500">Specialist care from your couch</p>
                                <ul class="list-disc pl-5 mt-3 space-y-4 text-gray-600 text-[15px]">
                                    @if($headings && $shortnotes)
                                        @for($i = 0; $i < count($headings); $i++)
                                            <li>
                                                <strong>{{ $headings[$i] ?? '' }}:</strong> 
                                                {{ $shortnotes[$i] ?? '' }}
                                            </li>
                                        @endfor
                                    @endif
                                </ul>
                                <div class="mt-10">
                                    <a href="#"
                                        class="mt-4 px-4 py-4 text-white rounded-lg bg-button_lite hover:opacity-90 text-[16px]">
                                        Get Started
                                    </a>
                                </div>
                            </div>
                            <img src="{{ asset($service->service_image) }}" alt="{{ $service->service_title }}"
                                class="w-full rounded-xl shadow-sm md:max-h-[400px] md:w-1/2">
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500">No services available at the moment.</p>
                </div>
            @endforelse
        </div>
    </section>

    @include('includes.patient_reviews')

    @include('includes.faq')

    <section class="bg-[#0e3757] md:rounded-2xl md:mx-16 md:my-12 overflow-hidden">
        <div class="grid items-center grid-cols-1 gap-8 mx-auto max-w-7xl md:grid-cols-2">
            <div class="p-4 text-white max-w-1/2 md:p-8">
                <h2 class="text-[40px] font-bold leading-tight md:max-w-[320px]">Let's Get Started</h2>
                <a href="#"
                    class="inline-block mt-4 bg-button_lite hover:bg-opacity-90 text-white font-semibold text-[18px] px-6 py-2 rounded-lg transition">
                    Sign Up Now
                </a>
            </div>
            <div class="max-w-1/2">
                <img src="{{ asset('assets/image/doctor.png') }}" alt="Doctor" class="relative z-10 w-auto max-h-[300px]">
            </div>
        </div>
    </section>

@endsection

@section('script')

@endsection