<section>
    <div class="my-10 overflow-x-auto bg-white rounded-lg p-4">

        <div class="flex justify-between items-center mb-4 py-4">
            <h2 class="text-xl font-semibold">API Integration Health</h2>
            <div class="relative w-1/3">
                <input type="text" id="tableSearch" placeholder="Search Something..."
                    class="w-full py-2 px-8 pr-10  rounded-lg border border-gray-300 focus:outline-none ">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>



        <table id="ApiHealthIntegration" class="display w-full text-sm text-left">
            {{--  <h2 class="mb-4 text-xl font-semibold">API Integration Health</h2>  --}}
            <thead class="pt-4 bg-gray-100">
                <tr>
                    <th class="text-left">API Name</th>
                    <th class="text-left">Uptime (7D)</th>
                    <th class="text-left">Errors (24h)</th>
                    <th class="text-left">Last Sync</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Last Response Time</th>
                    <th class="text-left">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm text-[#000000]">
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <!-- 6 more sample rows -->
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>William John</td>
                    <td>99.9%</td>
                    <td>23</td>
                    <td>5 min ago</td>
                    <td>
                        Slow
                    </td>
                    <td>45 min ago</td>
                    <td>
                        <button
                            class="px-6 py-2 text-[#FF6400] bg-[#FF64001A] text-[13px] font-bold rounded-full hover:bg-orange-200">
                            View Logs
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>