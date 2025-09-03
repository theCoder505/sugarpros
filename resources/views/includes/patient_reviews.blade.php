<section class="px-4 py-12 mx-auto my-16 bg-white max-w-8xl md:px-12 max-w-7xl">
    <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2  rounded-3xl px-4">
        PATIENT REVIEWS
    </span>
    <div class="flex flex-col items-end justify-between mt-8 md:flex-row">
        <h2 class="text-[35px]  font-bold text-font_color mb-4 md:mb-6 max-w-[950px]">
            We have served in love and our patients have poured endless testimonials for our work.
        </h2>

        @if ($to_show != 'all')
            <a href="/all-reviews"
                class="hidden px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90 text-center w-[250px]">
                View all reviews
            </a>
        @endif
    </div>


    <div class="grid gap-8 mt-16 md:grid-cols-3">
        @forelse ($allReviews as $review)
            <div class="">
                <div class="flex items-center mb-4">
                    @forelse ($users as $user)
                        @if ($user->patient_id == $review->reviewed_by)
                            <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}"
                                class="w-12 h-12 mr-3 rounded-full">
                            <div>
                                <p class="text-lg font-semibold text-[#1E2939]">{{ $user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    @empty
                    @endforelse
                </div>
                <p class="text-[18px] text-gray-800 line-clamp-4 review">
                    {{ $review->main_review }}
                </p>
                <span onclick="toggleShow(this)"
                    class="cursor-pointer inline-block mt-2 text-[18px] text-[#298AAB] underline  hover:opacity-90">
                    See More ↓
                </span>
            </div>
        @empty
        @endforelse
    </div>

</section>






<script>
    function toggleShow(passedThis) {
        const $parent = $(passedThis).parent();
        $parent.children('.review').toggleClass('line-clamp-4');
        const isExpanded = !$parent.children('.review').hasClass('line-clamp-4');
        if (isExpanded) {
            $(passedThis).text('See Less ↑');
        } else {
            $(passedThis).text('See More ↓');
        }
    }
</script>
