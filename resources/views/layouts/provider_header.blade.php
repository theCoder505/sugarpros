<header class="w-full bg-white fixed left-0 top-0 md:relative">
    <div class="flex items-center justify-between px-4 py-3 mx-auto">
        <div class="flex items-center space-x-2">
            <a href="/">
                <img src="{{ asset('assets/image/logo.png') }}" alt="SugarPros Logo" class="w-auto h-11" />
            </a>
        </div>

        <button class="text-gray-600 md:hidden focus:outline-none" id="menuToggle">
            <i class="text-xl fas fa-bars"></i>
        </button>

        <nav class="hidden md:flex space-x-6 text-[16px] text-slate-500">
            <a href="{{ route('provider.dashboard') }}" class="dashboard">Dashboard</a>
            <a href="/provider/patient-records" class="book patients_records">Patient Records</a>
            <a href="/provider/sugarpros-ai" class="ai">SugarPros AI</a>
            <a href="/provider/patient-claims-biller" class="claims">Patient Claims Biller</a>
            <a href="{{ route('provider.appointment') }}" class="book appoint">Active Appointments</a>
        </nav>

        <div class="items-center hidden space-x-4 md:flex">
            <a href="/provider/notifications">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg  flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200 notification_nav relative">
                    <img src="{{ asset('assets/image/p1.png') }}" class="w-[20px] h-20px]" alt="">
                    <span class="unreads">
                        @php
                            use App\Models\Notification;
                            $unreadCount = Notification::where('user_id', Auth::guard('provider')->user()->provider_id)
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
            <a href="/provider/chats">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200 chats_nav relative">
                    <img src="{{ asset('assets/image/p2.png') }}" class="w-[20px] h-20px]" alt="">
                    <span class="unreads unseen_mesages">
                        @php
                            use App\Models\ChatRecord;
                            $provider_id = Auth::guard('provider')->user()->provider_id;
                            $unseenChats = ChatRecord::where('received_by', $provider_id)->where('status', 'delivered')->count();
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
                            $src = Auth::guard('provider')->user()->profile_picture
                                ? '/' . Auth::guard('provider')->user()->profile_picture
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
                                class="text-base font-semibold leading-tight truncate max-w-[150px]">{{ Auth::guard('provider')->user()->name }}</span>
                            <span
                                class="text-sm text-gray-500 truncate max-w-[150px]">{{ Auth::guard('provider')->user()->email }}</span>
                        </div>
                    </div>
                    <a href="{{ route('provider.account') }}"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Account</a>
                    <a href="/provider/notifications"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Notifications</a>
                    <a href="/provider/settings"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Settings</a>
                    <a href="/provider/notetaker"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Notetaker</a>
                    <a href="/provider/logout"
                        class="block px-2 py-2 text-red-600 rounded hover:bg-slate-100 transition">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="menu" class="md:hidden px-4 pb-4 space-y-4 text-[16px] hidden bg-gray-900 text-gray-100">
        <a href="{{ route('provider.dashboard') }}" class="block py-2 patients_records">Patient Records</a>
        <a href="{{ route('provider.ai') }}" class="block py-2 ai">SugarPros AI</a>
        <a href="#" class="block py-2 claims">Patient Claims Biller</a>
        <a href="{{ route('provider.appointment') }}" class="block py-2 appoint">Active Appointments</a>

        <div class="pt-4 border-t border-gray-200">
            <div class="flex items-center gap-3 pb-3">
                <img src="{{ $src }}" class="w-12 h-12 rounded-full object-cover" alt="">
                <div class="flex flex-col">
                    <span
                        class="text-base font-semibold leading-tight truncate">{{ Auth::guard('provider')->user()->name }}</span>
                    <span class="text-sm text-gray-500 truncate">{{ Auth::guard('provider')->user()->email }}</span>
                </div>
            </div>
            <a href="{{ route('provider.account') }}"
                class="px-2 py-2 text-gray-400 rounded hover:bg-slate-100 flex gap-2 items-center transition">
                <i class="fas w-6 fa-user"></i>
                Account
            </a>
            <a href="/provider/notifications"
                class="px-2 py-2 text-gray-400 rounded hover:bg-slate-100 inline-block gap-2 items-center transition relative">
                <i class="fas w-6 fa-bell"></i>
                &nbsp;Notifications
                @if ($unreadCount > 0)
                    <span
                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="/provider/chats"
                class="px-2 py-2 text-gray-400 rounded hover:bg-slate-100 flex gap-2 items-center transition">
                <i class="fas w-6 fa-comments"></i>
                Chat
            </a>
            <a href="/provider/settings"
                class="px-2 py-2 text-gray-400 rounded hover:bg-slate-100 flex gap-2 items-center transition">
                <i class="fas w-6 fa-cog"></i>
                Settings
            </a>
            <a href="/provider/logout"
                class="px-2 py-2 text-red-600 rounded hover:bg-slate-100 flex gap-2 items-center transition">
                <i class="fas w-6 fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
</header>


<div class="mt-16 bg-red-500 md:hidden"></div>



<script>
    const menuToggle = document.getElementById("menuToggle");
    const menu = document.getElementById("menu");
    let menuOpen = false;

    menuToggle.addEventListener("click", () => {
        menuOpen = !menuOpen;
        menu.classList.toggle("hidden");

        menuToggle.innerHTML = menuOpen ?
            '<i class="fas fa-times"></i>' :
            '<i class="fas fa-bars"></i>';
    });



    
    function showAccountInfo(){
        $(".show_account_info").toggleClass("hidden");
    }
</script>
