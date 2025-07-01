<section class="px-6 py-12 bg-white md:px-20 max-w-7xl mx-auto">
    <div class="mb-12 text-center">
        <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
            FAQS
        </span>
        <h2 class="text-[45px] font-semibold mt-4 text-font_color">Frequently asked questions.</h2>
    </div>

    <div class="space-y-2" id="faq-container">
        @forelse ($allFaqs as $key => $faq)
            <div class="pt-4 pb-4 border-b">
                <button
                    class="flex items-center justify-between w-full text-base font-semibold text-left text-font_color toggle-faq">
                    <span class="text-[22px]"><span class="mr-2">{{ $key + 1 }}</span> {{ $faq->question }}
                    </span>
                    <span class="text-2xl transition-transform duration-200 icon">+</span>
                </button>
                <div class="faq-content hidden mt-4 text-gray-600">
                    <pre class="text-[18px] font-sans">{!! $faq->answer !!}</pre>
                </div>
            </div>
        @empty
        @endforelse
    </div>
</section>
