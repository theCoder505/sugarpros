@extends('layouts.provider')

@section('title', 'Audio Transcript')

@section('link')
@endsection

@section('style')
<style>
    html, body{
        background: #ffffff!important;
    }
</style>
@endsection


@section('content')
    <div class=" py-6 px-12 h-screen md:max-h-screen">
        <a href="/" class="w-full flex justify-center">
            <img src="{{ asset('assets/image/logo.png') }}" class="w-[130px]" alt="">
        </a>
        <div class="bg-white rounded-lg p-10 text-center  mt-8 w-full">

            <div class="w-full">
                <div class="flex w-full justify-center mb-8">
                    <img src="{{ asset('assets/image/er.png') }}" class="w-full md:max-w-[500px]" alt="">
                </div>

                <p class="mt-4 mb-12 text-[24px] font-semibold max-w-[550px] mx-auto">
                    This hair is as lost as this page! Return to the home page before it gets even more lost.
                </p>

                <a href="/" class="bg-[#2889AA] text-white text-[16px] p-4 rounded hover:bg-opacity-90 font-bold transition">
                    Return To Home Page
                </a>
            </div>

        </div>
    </div>
@endsection

@section('script')

@endsection
