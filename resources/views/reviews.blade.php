@extends('layouts.app')

@section('title', 'Review')

@section('link')

@endsection

@section('style')
    <style>
        .Reviews {
            font-weight: bold;
            color: #298AAB;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Patient Reviews
                </h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="/" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Customer Reviews
                    </span>
                </div>
            </div>
        </div>
    </section>



    @include('includes.patient_reviews')



    @if (Auth::check())
        <section class="max-w-xl mx-auto my-6 bg-white rounded-xl shadow-lg p-8" id="reviews">
            <h2 class="text-3xl md:text-4xl font-bold text-[#133A59] mb-6 text-center">Review Us</h2>
            <form action="/add-review" method="post" class="space-y-6">
                @csrf
                <div class="flex gap-2 items-center justify-center review_star mb-4">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa fa-star {{ $review_star >= $i ? 'text-orange-400' : 'text-gray-300' }} hover:text-orange-400 text-2xl cursor-pointer transition-colors"
                            onclick="toggleStar(this)" data-mark="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="star" class="star" value="{{ $review_star }}">
                <textarea name="review" rows="5"
                    class="w-full border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-[#133A59] resize-none"
                    placeholder="Write your review here...">{{ $ownReview }}</textarea>
                <button type="submit"
                    class="w-full bg-[#298AAB] hover:bg-[#133A59] text-white font-semibold text-lg py-3 rounded-lg transition">Submit
                    Review</button>
            </form>
        </section>
    @endif






    @include('includes.faq')


    <section class="bg-[#0e3757] md:rounded-2xl   md:mx-16 md:my-12 overflow-hidden">
        <div class="max-w-7xl mx-auto grid py-8 md:py-0 grid-cols-1 md:grid-cols-2 items-center gap-8">

            <div class="text-white max-w-1/2 p-4 md:p-8">
                <h2 class="text-[40px] font-bold leading-tight md:max-w-[320px]">Let’s Get Started</h2>

                <a href="/sign-up"
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
    <script>
        function toggleStar(el) {
            const stars = document.querySelectorAll('.review_star .fa-star');
            const mark = parseInt(el.getAttribute('data-mark'));
            stars.forEach((star, idx) => {
                star.classList.toggle('text-orange-400', idx < mark);
                star.classList.toggle('text-gray-300', idx >= mark);
            });
            document.querySelector('input.star').value = mark;
        }
    </script>
@endsection
