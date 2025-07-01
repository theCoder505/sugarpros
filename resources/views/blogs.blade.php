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

        .active_cat {
            background: #133A59;
            color: #ffffff !important;
        }

        .active_cat:hover {
            background: #133A59 !important;
            color: #ffffff !important;
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
                                class="w-full rounded-full border border-gray-300 px-5 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 search_input"
                                onkeyup="checkIfEnter(this)" />
                            <button onclick="searchBlogs(this)"
                                class=" flex justify-center items-center rounded-full w-12 h-11 bg-font_color text-white">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 mt-6">
                        <button
                            class="category px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200 active_cat"
                            onclick="showRelatedBlogs(this)" data-id="0">All</button>
                        @forelse ($categories as $category)
                            <button onclick="showRelatedBlogs(this)" data-id="{{ $category->category }}"
                                class="category px-4 py-2 rounded-full border border-gray-300 text-sm text-slate-700 hover:bg-gray-200">{{ $category->category }}</button>
                        @empty
                        @endforelse
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
                    @forelse ($latest_blog as $latest)
                        <img src="{{ asset($latest->thumbnail) }}" alt="Main blog image"
                            class="w-full h-[400px] object-cover rounded-xl shadow-sm" />

                        <p class="text-sm text-neutral-800">
                            {{ \Carbon\Carbon::parse($latest->createt_at)->format('F d, Y') }}
                        </p>
                        <h3 class="text-[24px] font-semibold text-font_color">{{ $latest->title }}</h3>
                        <p class="text-gray-500 text-[16px]">
                            {{ \Illuminate\Support\Str::limit($latest->short_details, 250, '...') }}
                        </p>
                        <a href="/blogs/{{ $latest->id }}/{{ $latest->category }}/{{ $latest->title }}"
                            class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline">
                            Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5" alt="">
                        </a>

                    @empty
                    @endforelse
                </div>

                <div class="space-y-6">
                    @forelse ($related_second as $related_blog)
                        <div class="flex space-x-4">
                            <img src="{{ asset($related_blog->thumbnail) }}" alt="thumb"
                                class="w-[120px] h-[100px] rounded-md object-cover" />
                            <div class="flex flex-col max-h-[100px]">
                                <p class="text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($related_blog->createt_at)->format('F d, Y') }}
                                </p>
                                <h4 class="text-[18px] font-semibold text-font_color">
                                    {{ $related_blog->title }}
                                </h4>

                                <a href="/blogs/{{ $related_blog->id }}/{{ $related_blog->category }}/{{ $related_blog->title }}"
                                    class="inline-flex items-center text-[#5D8AC7] font-medium text-[16px] hover:underline mt-auto">
                                    Read More <img src="{{ asset('assets/image/up.png') }}" class=" w-5 h-5"
                                        alt="">
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </section>


    <section class="bg-white px-4 my-16 py-12 md:px-10 mx-auto" id="blosSection">
        <div class="text-center max-w-[700px] mx-auto mb-12">
            <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                Blogs
            </span>
            <h2 class="text-[45px] md:text-5xl font-semibold mt-8 text-font_color insights">
                Insights & News Stay Updated with Sugarpros
            </h2>
        </div>

        <div class="max-w-7xl mx-auto mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($blogs as $blog)
                <div class="rounded-xl hover:shadow-md transition blog_item" data-catid="{{ $blog->category }}">
                    <a href="/blogs/{{ $blog->id }}/{{ $blog->category }}/{{ $blog->title }}">
                        <img src="{{ asset($blog->thumbnail) }}" alt=""
                            class="rounded-xl mb-4 w-full h-[280px] object-cover" />
                        <div class="w-full px-4 my-2">
                            <h3 class="text-[24px] font-semibold text-font_color  mb-2 mt-5">
                                {{ $blog->title }}
                            </h3>
                            <p class="text-[16px]  text-gray-500">
                                {{ \Illuminate\Support\Str::limit($blog->short_details, 100, '...') }}
                            </p>
                        </div>
                    </a>
                </div>
            @empty
            @endforelse
        </div>
    </section>




@endsection

@section('script')
    <script>
        var insightstext = 'Insights & News Stay Updated with Sugarpros';

        function showRelatedBlogs(passedThis) {
            $(".category").removeClass('active_cat');
            $(passedThis).addClass('active_cat');
            let category = $(passedThis).attr("data-id");
            if (category == 0) {
                $(".blog_item").removeClass("hidden");
                $(".insights").html(insights);
            } else {
                $(".insights").html(category);
                $(".blog_item").addClass("hidden");
                $('.blog_item[data-catid="' + category + '"]').removeClass("hidden");
            }

            $('html, body').animate({
                scrollTop: ($("#blosSection").offset().top - 200)
            }, 500);
        }





        function searchBlogs() {
            let search = ($(".search_input").val()).trim();
            if (search == '') {
                toastr.error('Please type something to search!');
            } else {
                $(".blog_item").addClass("hidden");
                $(".insights").html('Results: ' + search);

                // Show the blog_item whose title matches the search (case-insensitive)
                $(".blog_item").each(function() {
                    let title = $(this).find("h3").text().toLowerCase();
                    if (title.includes(search.toLowerCase())) {
                        $(this).removeClass("hidden");
                    }
                });

                $('html, body').animate({
                    scrollTop: ($("#blosSection").offset().top - 200)
                }, 500);
            }
        }




        function checkIfEnter(passedThis) {
            if (event.key === "Enter") {
                searchBlogs();
            }
        }
    </script>
@endsection
