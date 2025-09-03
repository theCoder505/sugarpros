<header class="pb-4 relative w-full z-20 bg-[#133A59]/10 lg:bg-[unset] headerpage top-0 left-0">
    <div class="px-4 mx-auto max-w-[1420px] sm:px-6 lg:px-8 relative z-20">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}">
                    <img class="h-10 mt-4" src="{{ asset('assets/image/logo.png') }}" alt="Logo">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-6 text-[16px] text-gray-700 mt-4">
                <a href="{{ route('home') }}" class="hover:text-button  Home">Home</a>
                <a href="{{ route('about') }}" class="hover:text-button About">About Us</a>
                <a href="{{ route('service') }}" class="hover:text-button Services">Our Services</a>
                <a href="{{ route('reviews') }}" class="hover:text-button Reviews">Patient Reviews</a>
                <a href="/our-blogs" class="hover:text-button Blog">Blog</a>
                <a href="{{ route('faq') }}" class="hover:text-button FAQs">FAQs</a>
                @if (Auth::guard('biller-admin')->check())
                    <a href="/biller-admin/appointments" class="hover:text-button appointments">Appointments</a>
                @endif
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3 font-medium">

                <div class="pt-2 space-y-2 space-x-2">
                    @if (Auth::guard('provider')->check())
                        <a href="/provider/dashboard"
                            class="hidden px-4 py-2 bg-white border border-gray-200 rounded-lg md:inline-block text-button hover:bg-gray-100">
                            Dashboard
                        </a>
                    @elseif (Auth::guard('biller-admin')->check())
                        <div class="flex gap-4">
                            <a href="/biller-admin/dashboard"
                                class="hidden px-4 py-2 bg-white border border-gray-200 rounded-lg md:inline-block text-button hover:bg-gray-100 dashboard">
                                Dashboard
                            </a>
                            <form action="{{ route('biller-admin.logout') }}" method="POST" class="p-0 m-0">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-red-50 text-red-500 border-2 border-red-400 rounded-lg font-normal hover:bg-red-400 hover:text-white">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @elseif (Auth::check())
                        <a href="/dashboard"
                            class="hidden px-4 py-2 bg-white border border-gray-200 rounded-lg md:inline-block text-button hover:bg-gray-100">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="hidden px-4 py-2 bg-white border border-gray-200 rounded-lg md:inline-block text-button hover:bg-gray-100">
                            Sign In
                        </a>
                        <a href="{{ route('sign.up') }}"
                            class="hidden px-4 py-2 bg-white border rounded-lg md:inline-block text-button border-button hover:bg-blue-50">
                            Sign Up
                        </a>
                        <a href="/provider/login"
                            class="hidden px-4 py-2 text-white rounded-lg md:inline-block bg-button hover:opacity-90">
                            Provider Access
                        </a>
                    @endif




                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden text-gray-700 focus:outline-none">
                        <!-- Hamburger Icon -->
                        <svg id="iconMenu" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <!-- Close Icon -->
                        <svg id="iconClose" class="hidden w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden px-4 pb-4 space-y-2 text-[16px]  text-font_normal" id="mobileMenu">
            <a href="{{ route('home') }}" class="block hover:text-button  Home">Home</a>
            <a href="{{ route('about') }}" class="block hover:text-button  About">About Us</a>
            <a href="{{ route('service') }}" class="block hover:text-button  Services">Our Services</a>
            <a href="{{ route('reviews') }}" class="block hover:text-button  Reviews">Patient Reviews</a>
            <a href="{{ route('pricing') }}" class="block hover:text-button Pricing">Pricing</a>
            <a href="{{ route('blog') }}" class="block hover:text-button  Blog">Blog</a>
            <a href="{{ route('faq') }}" class="block hover:text-button  FAQs">FAQs</a>

            <div class="pt-2 space-y-2">
                @if (Auth::guard('provider')->check())
                    <a href="/provider/dashboard"
                        class="inline-block px-4 py-2 bg-white border border-gray-200 rounded-lg text-button hover:bg-gray-100">
                        Dashboard
                    </a>
                @elseif (Auth::check())
                    <a href="/dashboard"
                        class="inline-block px-4 py-2 bg-white border border-gray-200 rounded-lg text-button hover:bg-gray-100">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-4 py-2 bg-white border border-gray-200 rounded-lg text-button hover:bg-gray-100">
                        Sign In
                    </a>
                    <a href="{{ route('sign.up') }}"
                        class="inline-block px-4 py-2 bg-white border rounded-lg text-button border-button hover:bg-blue-50">
                        Sign Up
                    </a>
                    <a href="#" class="inline-block px-4 py-2 text-white rounded-lg bg-button hover:opacity-90">
                        Provider Access
                    </a>
                @endif

            </div>
        </div>
</header>
