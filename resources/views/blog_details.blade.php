@extends('layouts.app')

@section('title', 'Blog-details')

@section('link')

@endsection

@section('style')
    <style>
         .Blog{
            font-weight: bold;
            color: #298AAB;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #298AAB #f1f1f1;
        }
    </style>
@endsection

@section('content')

    <section class="px-4 py-10 mx-auto max-w-7xl">
        <h1 class="text-[45px] font-bold text-font_color leading-tight max-w-[50rem] my-8">
            5 Simple Daily Habits to Better Manage Your Diabetes
        </h1>

        <div class="flex flex-col mb-6 text-sm sm:flex-row sm:items-center sm:justify-between text-slate-500">
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/image/auth.jpg') }}" alt="" class="rounded-full w-14 h-14">
                <div class="">
                    <span class="font-medium text-[18px] text-font_color">Cheri Opowo</span>
                    <div class="flex mt-2 text-sm">
                        <span>• April 22, 2025</span>
                        <span>• 7 Min Read</span>
                    </div>
                </div>

            </div>

            <div class="flex gap-4 mt-2 sm:mt-0 text-slate-600">
                <a href="{{ route('blog_details') }}"><i class="fab fa-facebook-f hover:text-font_color"></i></a>
                <a href="{{ route('blog_details') }}"><i class="fab fa-linkedin-in hover:text-font_color"></i></a>
                <a href="{{ route('blog_details') }}"><i class="fab fa-twitter hover:text-font_color"></i></a>
                <a href="{{ route('blog_details') }}"><img src="{{ asset('assets/image/attach.png') }}" alt=""
                        class="w-5 h-5 "></a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg md:mb-6">
            <img src="{{ asset('assets/image/de.png') }}" alt="Featured Image" class="w-full  md:h-[400px] object-contain">

            <p class="block mt-4 md:hidden text-gray-600 text-[16px] mt-4">
                Living with diabetes doesn’t have to feel overwhelming. Small, consistent changes can make a big
                difference in your blood sugar control and overall well-being. Here are five practical habits to
                help
                you stay on track—without drastic lifestyle overhauls.
            </p>
        </div>

        <div class="gap-8 mx-auto md:flex md:justify-between">
            <aside
                class="self-start px-2 mt-5 md:mt-0 space-y-4 md:col-span-1 md:sticky md:top-10 md:max-w-[400px] md:h-[300px]">
                <p class="text-sm font-bold uppercase text-zinc-600">Information</p>
                <ul
                    class=" custom-scrollbar space-y-2 text-[16px] overflow-y-auto h-[300px] text-[#0A0A0A] shadow-sm px-1 rounded-lg">
                    <li>
                        <a href="#protein" class="hover:underline ">Start Your Day with a Protein-Rich
                            Breakfast</a>
                    </li>
                    <li>
                        <a href="#walks" class="hover:underline">Take Short Walks After Meals</a>
                    </li>
                    <li>
                        <a href="#hydrate" class="hover:underline">Hydrate Smartly</a>
                    </li>
                    <li>
                        <a href="#check" class="hover:underline">Check Blood Sugar at Consistent Times</a>
                    </li>
                    <li>
                        <a href="#stress" class="hover:underline">Prioritize Stress Relief</a>
                    </li>

                </ul>
            </aside>

            <article class="space-y-6 leading-relaxed md:max-w-[800px] md:col-span-3 mt-10 md:mt-0">

                <div>
                    <p class="hidden md:block">
                        Living with diabetes doesn’t have to feel overwhelming. Small, consistent changes can make a big
                        difference in your blood sugar control and overall well-being. Here are five practical habits to
                        help
                        you stay on track—without drastic lifestyle overhauls.
                    </p>

                    <h2 id="protein" class="text-[30px] font-semibold text-font_color mt-8">Start Your Day with a
                        Protein-Rich
                        Breakfast</h2>
                    <p class="text-[20px] text-gray-600">
                        Skipping breakfast or grabbing a sugary pastry can cause blood sugar spikes. Instead, opt for a
                        balanced
                        meal with protein (like eggs, Greek yogurt, or nut butter) and fiber (whole grains, veggies). This
                        combo
                        keeps you full and stabilizes glucose levels all morning.
                    </p>
                    <img src="{{ asset('assets/image/configuration.jpg') }}" alt=""
                        class="max-h-[400px] w-full object-cover rounded-xl">
                </div>
                <div class="mt-6">


                    <h2 id="walks" class="text-[30px] font-semibold text-font_color mt-8">Take Short Walks After Meals
                    </h2>
                    <p class="text-[20px] text-gray-600">
                        Skipping breakfast or grabbing a sugary pastry can cause blood sugar spikes. Instead, opt for a
                        balanced
                        meal with protein (like eggs, Greek yogurt, or nut butter) and fiber (whole grains, veggies). This
                        combo
                        keeps you full and stabilizes glucose levels all morning.
                    </p>
                    <img src="{{ asset('assets/image/configuration.jpg') }}" alt=""
                        class="max-h-[400px] w-full object-cover rounded-xl">
                </div>
                <div class="mt-6">


                    <h2 id="hydrate" class="text-[30px] font-semibold text-font_color mt-8">Hydrate Smartly</h2>
                    <p class="text-[20px] text-gray-600">
                        Skipping breakfast or grabbing a sugary pastry can cause blood sugar spikes. Instead, opt for a
                        balanced
                        meal with protein (like eggs, Greek yogurt, or nut butter) and fiber (whole grains, veggies). This
                        combo
                        keeps you full and stabilizes glucose levels all morning.
                    </p>
                    <img src="{{ asset('assets/image/configuration.jpg') }}" alt=""
                        class="max-h-[400px] w-full object-cover rounded-xl">
                </div>
                <div class="mt-6">

                    <h2 id="check" class="text-[30px] font-semibold text-font_color mt-8">Check Blood Sugar at
                        Consistent Times</h2>
                    <p class="text-[20px] text-gray-600">
                        Skipping breakfast or grabbing a sugary pastry can cause blood sugar spikes. Instead, opt for a
                        balanced
                        meal with protein (like eggs, Greek yogurt, or nut butter) and fiber (whole grains, veggies). This
                        combo
                        keeps you full and stabilizes glucose levels all morning.
                    </p>
                    <img src="{{ asset('assets/image/configuration.jpg') }}" alt=""
                        class="max-h-[400px] w-full object-cover rounded-xl">
                </div>
                <div class="mt-6">


                    <h2 id="stress" class="text-[30px] font-semibold text-font_color mt-8">Prioritize Stress Relief</h2>
                    <p class="text-[20px] text-gray-600">
                        Skipping breakfast or grabbing a sugary pastry can cause blood sugar spikes. Instead, opt for a
                        balanced
                        meal with protein (like eggs, Greek yogurt, or nut butter) and fiber (whole grains, veggies). This
                        combo
                        keeps you full and stabilizes glucose levels all morning.
                    </p>
                    <img src="{{ asset('assets/image/configuration.jpg') }}" alt=""
                        class="max-h-[400px] w-full object-cover rounded-xl">
                </div>




            </article>
        </div>
    </section>


    <section class="px-4 py-12 mx-auto my-16 bg-white md:px-10">
        <div class="gap-5 mb-12 md:flex">
            <div class="">
                <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                    Blogs
                </span>
                <h2 class="text-[45px] md:text-5xl font-semibold mt-8 text-font_color">Insights & News Stay Updated with
                    Sugarpros</h2>
            </div>
            <div class="text-[20px] text-gray-800 md:my-0 my-10">
                <p class="mb-4">Check out EaseCloud’s blog for expert insights and the newest trends.</p>

                <a href="{{ route('blog') }}"
                    class="px-4 py-2 md:mt-4 text-[16px] text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90">
                    View all Blogs
                </a>
            </div>

        </div>

        <div class="grid gap-6 mx-auto mt-8 max-w-7xl sm:grid-cols-2 lg:grid-cols-3">
            <div class="transition rounded-xl hover:shadow-md">
                <a href="">
                    <img src="{{ asset('assets/image/ba.png') }}" alt=""
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />
                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Simple Daily Habits to Better Manage Your Diabetes
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Living with diabetes doesn’t have to feel overwhelming. Here are simple habits to stay in
                            control.
                        </p>
                    </div>
                </a>
            </div>

            <div class="transition rounded-xl hover:shadow-md">
                <a href="">
                    <img src="{{ asset('assets/image/bb.png') }}" alt=""
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />

                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Start Your Day with a Protein-Rich Breakfast
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Breakfast is the most important meal—especially for managing blood sugar levels efficiently.
                        </p>
                    </div>
                </a>
            </div>

            <div class="transition rounded-xl hover:shadow-md">
                <a href="">
                    <img src="{{ asset('assets/image/bc.png') }}" alt="Blog 3"
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />
                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Take Short Walks After Meals
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            A 10-15 minute walk post-meal can help reduce sugar spikes and improve overall wellness.
                        </p>
                    </div>
                </a>
            </div>

        </div>
    </section>






@endsection

@section('script')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll("aside ul li a");

            links.forEach(link => {
                link.addEventListener("click", function() {
                    // আগের active class গুলো মুছে ফেলি
                    links.forEach(el => el.classList.remove("text-[#298AAB]", "font-bold"));

                    // এই লিংকে active class যোগ করি
                    this.classList.add("text-[#298AAB]", "font-bold");
                });
            });
        });
    </script>


@endsection
