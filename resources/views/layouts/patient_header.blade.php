<header class="w-full bg-[#fff]">
    <div class="flex items-center justify-between px-4 py-3 mx-auto">
        <div class="flex items-center space-x-2">
            <a href="/">
                <img src="{{ asset('assets/image/logo.png') }}" alt="SugarPros Logo" class="w-auto h-11" />
            </a>
        </div>

        <button class="text-gray-600 md:hidden focus:outline-none" onclick="toggleMobileMenu()">
            <i class="text-xl fas fa-bars"></i>
        </button>

        <nav class="hidden md:flex space-x-6 text-[16px] text-slate-500">
            <a href="{{ route('dashboard') }}" class="dashboard">Dashboard</a>
            <a href="{{ route('appointment') }}" class="book">Book</a>
            <a href="{{ route('appointment_list') }}" class="appointment">Appointments</a>
            <a href="{{ route('sugarpro_ai') }}" class="sugarpro_ai">SugarPros AI</a>
            <div class="relative group">
                <button class="flex items-center space-x-1" onclick="showDropDown(this)">
                    <span>My Data</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="absolute z-10 hidden p-2 mt-0 bg-white rounded shadow-md dropdown_items min-w-[225px]">
                    <a href="/dexcom" class="block px-4 py-2 hover:bg-gray-100 dexcom">Dexcom/Libre Results</a>
                    <a href="/nutrition-tracker" class="block px-4 py-2 hover:bg-gray-100 fatsecret">Nutrition Tracker</a>
                    <a href="/clinical-notes" class="block px-4 py-2 hover:bg-gray-100 dexcom">Clinical Notes</a>
                    <a href="/quest-lab" class="block px-4 py-2 hover:bg-gray-100 dexcom">Quest Lab</a>
                    <a href="/e-prescriptions" class="block px-4 py-2 hover:bg-gray-100 dexcom">E-Prescriptions</a>
                </div>
            </div>
        </nav>

        <div class="items-center hidden space-x-4 md:flex">
            <a href="/notifications">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200 notification_nav relative">
                    <img src="{{ asset('assets/image/p1.png') }}" class="w-[20px] h-20px]" alt="">
                    <span class="unreads">
                        @php
                            use App\Models\Notification;
                            $unreadCount = Notification::where('user_id', Auth::user()->patient_id)
                                ->where('read_status', 0)
                                ->count();
                        @endphp
                        @if ($unreadCount > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $unreadCount }}</span>
                        @endif
                    </span>
                </div>
            </a>
            <a href="/chats">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200 chats_nav relative">
                    <img src="{{ asset('assets/image/p2.png') }}" class="w-[20px] h-20px]" alt="">
                    <span class="unreads unseen_mesages">
                        @php
                            use App\Models\ChatRecord;
                            $patient_id = Auth::user()->patient_id;
                            $unseenChats = ChatRecord::where('received_by', $patient_id)->where('status', 'delivered')->count();
                        @endphp
                        @if ($unseenChats > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $unseenChats }}</span>
                        @endif
                    </span>
                </div>
            </a>

            <div class="relative group">
                <div onclick="showAccountInfo(this)">
                    <div
                        class="w-[46px] h-[46px] rounded-lg flex justify-center items-center hover:bg-slate-200 overflow-hidden account_nav cursor-pointer">
                        @php
                            $src = Auth::user()->profile_picture
                                ? '/' . Auth::user()->profile_picture
                                : '/assets/image/dummy_user.png';
                        @endphp
                        <img src="{{ $src }}" class="object-cover w-full h-full" alt="">
                    </div>
                </div>
                <div
                    class="absolute right-0 top-12 z-20 hidden min-w-[250px] bg-white rounded-lg shadow-lg show_account_info group-focus:block py-4 px-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-3">
                        <img src="{{ $src }}" class="w-12 h-12 rounded-full object-cover" alt="">
                        <div class="flex flex-col">
                            <span
                                class="text-base font-semibold leading-tight truncate max-w-[150px]">{{ Auth::user()->name }}</span>
                            <span
                                class="text-sm text-gray-500 truncate max-w-[150px]">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    <a href="/account"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Account</a>
                    <a href="/notifications"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Notifications</a>
                    <a href="/settings"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Settings</a>
                    <a href="/subscriptions"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Subscriptions</a>
                    <a href="/logout"
                        class="block px-2 py-2 text-red-600 rounded hover:bg-slate-100 transition">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated Mobile Menu -->
    <div id="mobile-menu" class="md:hidden px-4 pb-4 space-y-3 text-[16px] text-slate-500 hidden">
        <a href="{{ route('dashboard') }}" class="block py-2 dashboard">Dashboard</a>
        <a href="{{ route('appointment') }}" class="block py-2 book">Book</a>
        <a href="{{ route('appointment_list') }}" class="block py-2 appointment">Appointments</a>
        <a href="{{ route('sugarpro_ai') }}" class="block py-2 sugarpro_ai">SugarPros AI</a>

        <div>
            <button onclick="this.nextElementSibling.classList.toggle('hidden')"
                class="flex items-center justify-between w-full py-2 text-left">
                <span>My Data</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="hidden pl-4 space-y-2">
                <a href="/dexcom" class="block py-2 hover:bg-gray-100 dexcom">Dexcom/Libre Results</a>
                <a href="/nutrition-tracker" class="block py-2 hover:bg-gray-100 fatsecret">Nutrition Tracker</a>
                <a href="/clinical-notes" class="block py-2 hover:bg-gray-100 dexcom">Clinical Notes</a>
                <a href="/quest-lab" class="block py-2 hover:bg-gray-100 dexcom">Quest Lab</a>
                <a href="/e-prescriptions" class="block py-2 hover:bg-gray-100 dexcom">E-Prescriptions</a>
            </div>
        </div>

        <div class="flex items-center pt-4 space-x-4">
            <a href="/notifications" class="relative">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="{{ asset('assets/image/p1.png') }}" class="w-[20px] h-20px]" alt="">
                    @if ($unreadCount > 0)
                        <span
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </div>
            </a>
            <a href="/chats">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="{{ asset('assets/image/p2.png') }}" class="w-[20px] h-20px]" alt="">
                </div>
            </a>
            <a href="/settings">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="{{ asset('assets/image/p3.png') }}" class="w-[20px] h-20px]" alt="">
                </div>
            </a>
            <a href="{{ route('account') }}">
                <div
                    class="w-[46px] rounded-lg flex justify-center items-center h-[46px] hover:bg-slate-200 overflow-hidden">
                    <img src="{{ $src }}" class="w-full h-[46px]" alt="">
                </div>
            </a>
            <a href="/logout">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                    </svg>
                </div>
            </a>
        </div>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }


    function showDropDown(){
        $(".dropdown_items").toggleClass("hidden");
    }

    function showAccountInfo(){
        $(".show_account_info").toggleClass("hidden");
    }
</script>
