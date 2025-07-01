<footer class="bg-font_color  text-white min-h-[23rem] md:min-h-full py-10 md:px-12 lg:px-24 text-[16px]">
    <div class="flex items-start justify-between px-4 md:block">
        <div class="flex flex-wrap gap-6 pb-6 md:justify-start">
            <ul class="flex-wrap gap-6 md:flex">
                <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                <li><a href="{{ route('about') }}" class="hover:underline">About Us</a></li>
                <li><a href="/about-us" class="hover:underline">Our Team</a></li>
                <li><a href="{{ route('reviews') }}" class="hover:underline">Customer Reviews</a></li>
                <li><a href="{{ route('pricing') }}" class="hover:underline">Pricing</a></li>
                <li><a href="{{ route('blog') }}" class="hover:underline">Blog</a></li>
                <li><a href="{{ route('faq') }}" class="hover:underline">FAQs</a></li>
            </ul>
        </div>

        <div class="flex flex-col md:mt-10 md:hidden sm:flex-row sm:gap-12">
            <div class="mb-4">
                <h4 class="mb-2 text-[16px]">Phone</h4>
                <div class="flex items-center">
                    <span class="text-orange-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M6.62 10.79a15.91 15.91 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21 12.36 12.36 0 003.89.62 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.29a1 1 0 011 1 12.36 12.36 0 00.62 3.89 1 1 0 01-.21 1.11l-2.2 2.2z" />
                        </svg>
                    </span>
                    <span>{{ $contact_phone }}</span>
                </div>
            </div>
            <div class="">
                <h4 class="mb-2 text-[16px]">Email</h4>
                <div class="flex items-center mt-2 sm:mt-0">
                    <span class="text-orange-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M2 4a2 2 0 012-2h16a2 2 0 012 2v1.6L12 13 2 5.6V4zm0 3.24l10 7.14 10-7.14V20a2 2 0 01-2 2H4a2 2 0 01-2-2V7.24z" />
                        </svg>
                    </span>
                    <span>{{ $contact_email }}</span>
                </div>
            </div>
        </div>
    </div>


    <div class="mt-8">
        <div class="md:relative flex justify-center  md:block w-full h-[1px] bg-[#298AAB]/10">
            <div
                class="flex flex-col  items-center  md:items-end gap-3 md:absolute right-0 md:top-[-24px] mt-[-25px] md:mt-0">
                <div
                    class="bg-sky-600 text-[2rem] flex justify-between items-center rounded-full px-4 py-2 gap-3 w-[13rem] h-[3rem]">
                    <a href="{{ $fb_url }}" aria-label="Facebook">
                        <i class="fa-brands fa-facebook"></i>
                    </a>
                    <a href="{{ $twitter_url }}" aria-label="Twitter">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="{{ $instagram_url }}" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="{{ $linkedin_url }}" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin"></i>
                    </a>
                </div>
                <p class="text-[15px] mt-5 md:mt-4 text-slate-300">© 2025 All Rights Reserved</p>
            </div>
        </div>

        <div class="flex-col hidden mt-10 md:flex sm:flex-row sm:gap-12">
            <div class="">
                <h4 class="mb-2 text-[16px]">Phone</h4>
                <div class="flex items-center gap-2">
                    <span class="text-orange-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M6.62 10.79a15.91 15.91 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21 12.36 12.36 0 003.89.62 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.29a1 1 0 011 1 12.36 12.36 0 00.62 3.89 1 1 0 01-.21 1.11l-2.2 2.2z" />
                        </svg>
                    </span>
                    <a href="tel:+{{ $contact_phone }}">
                        <span>{{ $contact_phone }}</span>
                    </a>
                </div>
            </div>
            <div class="">
                <h4 class="mb-2 text-[16px]">Email</h4>
                <div class="flex items-center mt-2 gap-2 sm:mt-0">
                    <span class="text-orange-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M2 4a2 2 0 012-2h16a2 2 0 012 2v1.6L12 13 2 5.6V4zm0 3.24l10 7.14 10-7.14V20a2 2 0 01-2 2H4a2 2 0 01-2-2V7.24z" />
                        </svg>
                    </span>
                    <a href="mailto:{{ $contact_email }}">
                        <span>{{ $contact_email }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</footer>
