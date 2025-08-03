<header class="w-full bg-white">
    <div class="flex items-center justify-between px-4 py-3 mx-auto max-w-7xl">
        <div class="flex items-center space-x-2">
            <a href="/" target="_blank">
                <img src="/assets/image/logo.png" alt="SugarPros Logo" class="w-auto h-11" />
            </a>
        </div>

        <button class="text-gray-600 md:hidden focus:outline-none" id="menuToggle">
            <i class="text-xl fas fa-bars"></i>
        </button>

        <nav class="hidden md:flex space-x-6 text-[16px] text-slate-500">
            <a href="/admin/dashboard" class="emr">EMR System</a>
            <a href="/admin/sugarpros-ai" class="ai">SugarPros AI</a>
            <a href="/admin/patient-claims-biller" class="patinet_biller">Patient Claims Biller</a>
            <a href="/admin/appointments" class="book appointments">Active Appointments</a>
            <a href="/admin/patients" class="book patients">Patients</a>
            <a href="/admin/providers" class="book providers">Providers</a>
            <a href="/admin/adress-page" class="book address_page">Address</a>
        </nav>

        <div class="items-center hidden space-x-4 md:flex">
            <div class="relative group">
                <div onclick="showAccountInfo(this)">
                    <div
                        class="w-[46px] h-[46px] rounded-lg flex justify-center items-center hover:bg-slate-200 overflow-hidden account_nav cursor-pointer">
                        <img src="{{ $brandicon }}" class="border-1 p-1 w-full h-full" alt="">
                    </div>
                </div>
                <div
                    class="absolute right-0 top-12 z-20 hidden min-w-[250px] bg-white rounded-lg shadow-lg show_account_info group-focus:block py-4 px-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-3">
                        <img src="{{ $brandicon }}" class="w-12 h-12 rounded-full border p-1" alt="">
                        <div class="flex flex-col">
                            <span
                                class="text-base font-semibold leading-tight truncate max-w-[150px]">{{ Auth::guard('admin')->user()->name }}</span>
                            <span
                                class="text-sm text-gray-500 truncate max-w-[150px]">{{ Auth::guard('admin')->user()->email }}</span>
                        </div>
                    </div>
                    <a href="/admin/account"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Admin Account</a>
                    <a href="/admin/services"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Services</a>
                    <a href="/admin/reviews"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Reviews</a>
                    <a href="/admin/categories"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Categories</a>
                    <a href="/admin/blogs"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Blogs</a>
                    <a href="/admin/add-new-blog"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Add Blog</a>
                    <a href="/admin/all-blogs"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">FAQs</a>
                    <a href="/admin/settings"
                        class="block px-2 py-2 text-gray-700 rounded hover:bg-slate-100 transition">Website Settings</a>
                    <a href="/admin/logout"
                        class="block px-2 py-2 text-red-600 rounded hover:bg-slate-100 transition">Logout</a>
                </div>
            </div>
        </div>
    </div>


    <div id="menu" class="md:hidden px-4 pb-4 space-y-2 text-[16px] text-slate-500 hidden">
        <a href="/admin/dashboard" class="block emr">EMR System</a>
        <a href="#" class="block ai">SugarPros AI</a>
        <a href="#" class="block claims">Patient Claims Biller</a>
        <a href="/admin/appointment" class="block appoint">Active Appointments</a>

        <div class="flex items-center pt-2 space-x-4">
            <a href="#">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg  flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="/assets/image/p1.png" class="w-[20px] h-20px]" alt="">
                </div>
            </a>

            <a href="/admin/recent-chat">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg  flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="/assets/image/p2.png" class="w-[20px] h-20px]" alt="">
                </div>
            </a>
            <a href="#">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg  flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200">
                    <img src="/assets/image/p3.png" class="w-[20px] h-20px]" alt="">
                </div>
            </a>

            <a href="/admin/account">
                <div
                    class="border border-[#E2E8F0] w-[46px] rounded-lg  flex justify-center items-center h-[46px] bg-slate-100 hover:bg-slate-200 overflow-hidden">
                    <img src="/assets/image/p4.png" class="w-full h-[46px]" alt="">
                </div>
            </a>
        </div>
    </div>
</header>



<script>
    function showAccountInfo() {
        $(".show_account_info").toggleClass("hidden");
    }
</script>
