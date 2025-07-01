<section class="bg-[#fdf1eb] my-16 py-20 px-8 md:px-0 max-w-7xl mx-auto">
    <div class="max-w-6xl mx-auto">
        <div class="items-end grid-cols-1 mb-10 lg:grid lg:grid-cols-2">
            <div class="max-w-xl">
                <span class="text-[16px] text-font_color uppercase bg-[#FF650033]/20 py-2 rounded-3xl px-4">
                    Our Services
                </span>
                <h2 class="mt-8 text-3xl font-bold md:text-4xl text-slate-800">Your Diabetes Care,<br> Simplified
                </h2>
            </div>
            <div class="mt-8 lg:mt-0">
                <p class="mb-4 text-gray-800 text-[18px]">
                    Comprehensive virtual care designed for real life—because managing diabetes shouldn't feel like
                    a second job.
                </p>
                <a href="/our-service" class="px-4 py-2 text-white rounded-lg md:inline-block bg-button_lite hover:opacity-90">
                    View all Services
                </a>
            </div>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($allServices as $service)
                @php
                    $headings = json_decode($service->service_points);
                    $shortnotes = json_decode($service->service_point_details);
                @endphp
                
                <div class="mt-8 md:mt-0">
                    <img src="{{ asset($service->service_image) }}" alt="{{ $service->service_title }}"
                        class="object-cover w-full mb-4 rounded-md">
                    <h3 class="mb-2 text-[24px] font-semibold text-font_color">{{ $service->service_title }}</h3>
                    <span class="text-[15px] text-gray-500">Stay ahead with seamless data tracking</span>
                    <ul class="space-y-2 text-[15px] mt-5 list-disc list-inside text-gray-600">
                        @if($headings && $shortnotes)
                            @for($i = 0; $i < count($headings); $i++)
                                <li>
                                    <strong>{{ $headings[$i] ?? '' }}:</strong> 
                                    {{ $shortnotes[$i] ?? '' }}
                                </li>
                            @endfor
                        @endif
                    </ul>
                </div>
            @empty
                <div class="col-span-3 text-center py-10">
                    <p class="text-gray-500 text-lg">No services available yet. Please check back later.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>