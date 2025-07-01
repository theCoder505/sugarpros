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
                    <a href="/" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>FAQs</span>
                </div>
            </div>
        </div>
    </section>










    @include('includes.faq')




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
