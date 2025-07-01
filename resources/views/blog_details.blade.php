@extends('layouts.app')

@section('title', 'Blog-details')

@section('link')

@endsection

@section('style')
    <style>
        .Blog {
            font-weight: bold;
            color: #298AAB;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #298AAB #f1f1f1;
        }

        .active_link {
            color: #298AAB;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    @forelse ($blog as $blog_item)
        <section class="px-4 py-10 mx-auto max-w-7xl">
            <h1 class="text-[45px] font-bold text-font_color leading-tight max-w-[50rem] my-8">
                {{ $blog_item->title }}
            </h1>

            <div class="flex flex-col mb-6 text-sm sm:flex-row sm:items-center sm:justify-between text-slate-500">
                <div class="flex items-center gap-2">
                    <p class="text-lg text-gray-800">
                        {{ \Carbon\Carbon::parse($blog_item->updated_at)->format('F d, Y') }}
                    </p>
                </div>

                <div class="flex gap-4 mt-2 sm:mt-0 text-slate-600">
                    @php
                        $url = request()->fullUrl();
                        $title = $blog_item->title;
                    @endphp
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank" rel="noopener"
                        title="Share on Facebook">
                        <i class="fab fa-facebook-f hover:text-font_color"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $url }}&title={{ $title }}"
                        target="_blank" rel="noopener" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in hover:text-font_color"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $title }}"
                        target="_blank" rel="noopener" title="Share on Twitter">
                        <i class="fab fa-twitter hover:text-font_color"></i>
                    </a>
                    <span class="url_copy cursor-pointer" onclick="copyBlogUrl('{{ $url }}')">
                        <img src="{{ asset('assets/image/attach.png') }}" alt="" class="w-5 h-5 ">
                    </span>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg md:mb-6">
                <img src="{{ asset($blog_item->thumbnail) }}" alt="Featured Image" class="w-full">
            </div>



            @php
                $toc = is_array($blog_item->table_of_contents)
                    ? $blog_item->table_of_contents
                    : json_decode($blog_item->table_of_contents, true);
                $content_imgs = is_array($blog_item->content_images)
                    ? $blog_item->content_images
                    : json_decode($blog_item->content_images, true);
                $details = is_array($blog_item->content_details)
                    ? $blog_item->content_details
                    : json_decode($blog_item->content_details, true);
            @endphp



            <div class="gap-8 mx-auto md:flex md:justify-between">
                <aside
                    class="self-start px-2 mt-5 md:mt-0 space-y-4 md:col-span-1 md:sticky md:top-10 md:max-w-[400px] md:h-[300px]">
                    <p class="text-sm font-bold uppercase text-zinc-600">Information</p>
                    <ul
                        class="custom-scrollbar space-y-2 text-[16px] overflow-y-auto h-[300px] text-[#0A0A0A] shadow-sm px-1 rounded-lg">
                        @forelse ($toc as $key => $item)
                            <li data-id="details{{ $key + 1 }}" onclick="scrollToItem(this)"
                                class="hover:underline cursor-pointer item_link">
                                {{ $item }}
                            </li>
                        @empty
                            <p>No sections found.</p>
                        @endforelse
                    </ul>
                </aside>

                <article class="space-y-6 leading-relaxed md:max-w-[800px] md:col-span-3 mt-10 md:mt-0">
                    <div>
                        <p class="hidden md:block">
                            {{ $blog_item->short_details }}
                        </p>
                    </div>





                    @forelse ($toc as $key => $item)
                        <div class="mt-6" id="details{{ $key + 1 }}">
                            <h2 class="text-[30px] font-semibold text-font_color mt-8">{{ $item }}</h2>
                            <p class="text-[20px] text-gray-600 mb-4">
                                {{ $details[$key] ?? '' }}
                            </p>
                            @if (!empty($content_imgs[$key]))
                                <img src="{{ asset($content_imgs[$key]) }}" alt=""
                                    class="max-h-[400px] w-full object-cover rounded-xl">
                            @endif
                        </div>
                    @empty
                        <p>No sections found.</p>
                    @endforelse
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
                @forelse ($related_blogs as $blog)
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

    @empty
        <h1
            class="text-[45px] font-bold text-gray-500 leading-tight my-8 text-center h-96 flex justify-center items-center">
            Blog Not Found!
        </h1>
    @endforelse




@endsection

@section('script')

    <script>
        function scrollToItem(passedThis) {
            let dataID = $(passedThis).attr("data-id");
            $(".item_link").removeClass('active_link');
            $(passedThis).addClass("active_link");

            $('html, body').animate({
                scrollTop: ($("#" + dataID).offset().top - 80)
            }, 500);
        }



        function copyBlogUrl(url) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    toastr.info('URL Copied!');
                }, function() {
                    toastr.error('Failed to copy URL.');
                });
            } else {
                // Fallback for older browsers
                var tempInput = document.createElement("input");
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                try {
                    document.execCommand("copy");
                    toastr.info('URL Copied!');
                } catch (err) {
                    toastr.error('Failed to copy URL.');
                }
                document.body.removeChild(tempInput);
            }
        }
    </script>
@endsection
