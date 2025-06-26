@extends('layouts.app')

@section('title', 'Blog-Page')

@section('link')

@endsection

@section('style')
    <style>
        .Blog {
            font-weight: bold;
            color: #298AAB;
        }
    </style>

@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Blog
                </h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Blog</span>
                </div>

                <div class="max-w-4xl mx-auto text-center mt-4">
                    <div class="mt-6 flex justify-between">
                        <div class="w-full gap-4  flex justify-center items-center max-w-[500px]">
                            <input type="text" placeholder="Search here.."
                                class="w-full rounded-full border border-gray-300 px-5 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400" />
                            <button
                                class=" flex justify-center items-center rounded-full w-12 h-11 bg-font_color text-white">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 mt-6">
                        <button class="px-4 py-2 rounded-full bg-font_color text-white text-sm font-medium">All</button>
                        <button
                            class="px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200">Topic
                            name</button>
                        <button
                            class="px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200">Topic
                            name</button>
                        <button
                            class="px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200">Topic
                            name</button>
                        <button
                            class="px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200">Topic
                            name</button>
                    </div>
                </div>

            </div>



        </div>


    </section>





    <section class="bg-white px-4 py-10 md:px-10">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-[35px] font-semibold text-font_color mb-6">Latest Blogs For you</h2>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-4">
                    <img src="{{ asset('assets/image/b1.png') }}" alt="Main blog image"
                        class="w-full h-[400px] object-cover rounded-xl shadow-sm" />

                    <p class="text-sm text-neutral-800">America • March 20, 2023</p>
                    <h3 class="text-[24px] font-semibold text-font_color">
                        5 Simple Daily Habits to Better Manage Your Diabetes
                    </h3>
                    <p class="text-gray-500 text-[16px]">
                        Living with diabetes doesn’t have to feel overwhelming. Small, consistent changes can make a big
                        difference in your blood sugar control and overall well-being. Here are five practical habits to
                        help you stay on track—without drastic lifestyle overhauls.
                    </p>
                    <a href="{{ route('blog_details') }}"
                        class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline">
                        Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                    </a>
                </div>

                <div class="space-y-6">
                    <div class="flex space-x-4">
                        <img src="{{ asset('assets/image/b2.png') }}" alt="thumb"
                            class="w-[120px] h-[100px] rounded-md object-cover" />
                        <div class="flex flex-col max-h-[100px]">
                            <p class="text-sm text-gray-800">America • March 20, 2023</p>
                            <h4 class="text-[18px] font-semibold text-font_color">
                                Start Your Day with a Protein-Rich Breakfast
                            </h4>

                            <a href="{{ route('blog_details') }}"
                                class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <img src="{{ asset('assets/image/b2.png') }}" alt="thumb"
                            class="w-[120px] h-[100px] rounded-md object-cover" />
                        <div class="flex flex-col max-h-[100px]">
                            <p class="text-sm text-gray-800">America • March 20, 2023</p>
                            <h4 class="text-[18px] font-semibold text-font_color">Take Short Walks After Meals</h4>
                            <a href="{{ route('blog_details') }}"
                                class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <img src="{{ asset('assets/image/b2.png') }}" alt="thumb"
                            class="w-[120px] h-[100px] rounded-md object-cover" />
                        <div class="flex flex-col max-h-[100px]">
                            <p class="text-sm text-gray-800">America • March 20, 2023</p>
                            <h4 class="text-[18px] font-semibold text-font_color">Hydrate Smartly</h4>
                            <a href="{{ route('blog_details') }}"
                                class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <img src="{{ asset('assets/image/b2.png') }}" alt="thumb"
                            class="w-[120px] h-[100px] rounded-md object-cover" />
                        <div class="flex flex-col max-h-[100px]">
                            <p class="text-sm text-gray-800">America • March 20, 2023</p>
                            <h4 class="text-[18px] font-semibold text-font_color">Check Blood Sugar at Consistent Times</h4>
                            <a href="{{ route('blog_details') }}"
                                class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <img src="{{ asset('assets/image/b2.png') }}" alt="thumb"
                            class="w-[120px] h-[100px] rounded-md object-cover" />
                        <div class="flex flex-col max-h-[100px]">
                            <p class="text-sm text-gray-800">America • March 20, 2023</p>
                            <h4 class="text-[18px] font-semibold text-font_color">Prioritize Stress Relief</h4>
                            <a href="{{ route('blog_details') }}"
                                class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                            </a>
                        </div>
                    </div>






                </div>
            </div>
        </div>
    </section>


    <section class="bg-white px-4 my-16 py-12 md:px-10 mx-auto">
        <div class="text-center max-w-[700px] mx-auto mb-12">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                Blogs
            </span>
            <h2 class="text-[45px] md:text-5xl font-semibold mt-8 text-font_color">Insights & News Stay Updated with
                Sugarpros</h2>

        </div>

        <div class="max-w-7xl mx-auto mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class=" rounded-xl hover:shadow-md transition ">
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

            <div class=" rounded-xl hover:shadow-md transition ">
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

            <div class=" rounded-xl hover:shadow-md transition ">
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

            <div class=" rounded-xl hover:shadow-md transition ">
                <a href="">
                    <img src="{{ asset('assets/image/ba.png') }}" alt="Blog 4"
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />

                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Hydrate Smartly
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Water is essential. Stay hydrated to help regulate blood sugar levels and improve energy.
                        </p>
                    </div>
                </a>
            </div>

            <div class=" rounded-xl hover:shadow-md transition ">
                <a href="">
                    <img src="{{ asset('assets/image/bb.png') }}" alt="Blog 5"
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />
                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Check Blood Sugar at Consistent Times
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Keeping a routine for testing helps spot trends and improve your long-term control.
                        </p>
                    </div>
                </a>
            </div>

            <div class=" rounded-xl hover:shadow-md transition ">
                <a href="">
                    <img src="{{ asset('assets/image/bc.png') }}" alt="Blog 6"
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />
                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Prioritize Stress Relief
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Chronic stress raises blood sugar. Learn relaxation techniques that can help you stay balanced.
                        </p>
                    </div>
                </a>
            </div>

            <div class=" rounded-xl hover:shadow-md transition ">
                <a href="">
                    <img src="{{ asset('assets/image/ba.png') }}" alt="Blog 7"
                        class="rounded-xl mb-4max-w-[400px] max-h-[280px]object-cover" />
                    <div class="w-full px-4 my-2">
                        <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                            Small Steps, Big Impact
                        </h3>
                        <p class="text-[16px]  text-gray-500">
                            Diabetes management is a journey. Small, consistent choices lead to big improvements over time.
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </section>




@endsection

@section('script')

@endsection
