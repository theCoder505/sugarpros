@extends('layouts.admin_app')

@section('title', 'Setup Address')

@section('link')

@endsection

@section('styles')
    <style>
        .custom-blue {
            color: #133a59;
        }

        .custom-blue-bg {
            background-color: #133a59;
        }

        .custom-blue-border {
            border-color: #133a59;
        }

        .custom-blue-hover:hover {
            background-color: #0d2a42;
        }

        .custom-blue-light-bg {
            background-color: #e6eef5;
        }

        .custom-blue-light-border {
            border-color: #c2d4e8;
        }

        .custom-blue-focus:focus {
            ring-color: #a3c0db;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto my-12 px-6 lg:px-0 space-y-6 md:max-w-6xl">
        <h1 class="text-3xl font-semibold mb-6">Address & Location Setup For Website</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-4">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-road text-lg"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add Street</h2>
                </div>
                <form action="/admin/add-street" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1 font-medium custom-blue">Street Name</label>
                        <input type="text" name="street"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                            required placeholder="Enter street name">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add Street
                    </button>
                </form>
                <div class="mt-6">
                    <h3 class="font-semibold custom-blue mb-2 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>Street List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $streetArray = [];
                            if (!empty($streets)) {
                                $streetArray = json_decode($streets, true) ?? [];
                            }
                        @endphp
                        @forelse ($streetArray as $street)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $street }}</span>
                                <a href="/admin/remove-street/{{ $street }}" class="hover:text-red-700 transition"
                                    title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No streets added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-4">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-building text-lg"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add City</h2>
                </div>
                <form action="/admin/add-city" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1 font-medium custom-blue">City Name</label>
                        <input type="text"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                            required placeholder="Enter city name" name="city">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add City
                    </button>
                </form>
                <div class="mt-6">
                    <h3 class="font-semibold custom-blue mb-2 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>City List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $citiesArray = [];
                            if (!empty($cities)) {
                                $citiesArray = json_decode($cities, true) ?? [];
                            }
                        @endphp
                        @forelse ($citiesArray as $city)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $city }}</span>
                                <a href="/admin/remove-city/{{ $city }}" class="hover:text-red-700 transition"
                                    title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No cities added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-4">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-flag text-lg"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add State</h2>
                </div>
                <form action="/admin/add-state" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1 font-medium custom-blue">State Name</label>
                        <input type="text"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                            required placeholder="Enter state name" name="state">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add State
                    </button>
                </form>
                <div class="mt-6">
                    <h3 class="font-semibold custom-blue mb-2 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>State List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $stateArray = [];
                            if (!empty($states)) {
                                $stateArray = json_decode($states, true) ?? [];
                            }
                        @endphp
                        @forelse ($stateArray as $state)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $state }}</span>
                                <a href="/admin/remove-state/{{ $state }}" class="hover:text-red-700 transition"
                                    title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No state added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-6">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-map-pin text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add Zip Code</h2>
                </div>
                <form action="/admin/add-zip-code" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 custom-blue font-semibold">Zip Code</label>
                        <input type="text" name="zip_code"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                            required placeholder="Enter zip code">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add Zip Code
                    </button>
                </form>
                <div class="mt-8">
                    <h3 class="font-semibold custom-blue mb-3 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>Zip Code List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $zip_codeArray = [];
                            if (!empty($zip_codes)) {
                                $zip_codeArray = json_decode($zip_codes, true) ?? [];
                            }
                        @endphp
                        @forelse ($zip_codeArray as $zip_code)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $zip_code }}</span>
                                <a href="/admin/remove-zip-code/{{ $zip_code }}" class="hover:text-red-700 transition"
                                    title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No Zipcode added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-6">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-phone text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add Phone Prefix Code (Country Codes)</h2>
                </div>
                <form action="/admin/add-country-code" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 custom-blue font-semibold">Prefix Code</label>
                        <input type="text" name="prefixcode"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                            required placeholder="Enter Prefix Code">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add Prefix Code
                    </button>
                </form>
                <div class="mt-8">
                    <h3 class="font-semibold custom-blue mb-3 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>Prefix Code List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $prefixcodeArray = [];
                            if (!empty($prefixcode)) {
                                $prefixcodeArray = json_decode($prefixcode, true) ?? [];
                            }
                        @endphp
                        @forelse ($prefixcodeArray as $prefixcode)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $prefixcode }}</span>
                                <a href="/admin/remove-country-code/{{ $prefixcode }}"
                                    class="hover:text-red-700 transition" title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No prefix code added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <div class="flex items-center mb-6">
                    <div
                        class="custom-blue-bg text-white rounded-full p-2 mr-3 shadow h-10 w-10 flex justify-center itms-center">
                        <i class="fa fa-globe text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold custom-blue">Add New Language</h2>
                </div>
                <form action="/admin/add-language" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 custom-blue font-semibold">Language</label>
                        <input type="text" name="language"
                            class="w-full border-2 custom-blue-light-border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                            required placeholder="Enter Language">
                    </div>
                    <button type="submit"
                        class="w-full custom-blue-bg hover:custom-blue-hover transition text-white px-4 py-2 rounded-lg font-semibold shadow">
                        <i class="fa fa-plus mr-2"></i>Add Language
                    </button>
                </form>
                <div class="mt-8">
                    <h3 class="font-semibold custom-blue mb-3 flex items-center">
                        <i class="fa fa-list-ul mr-2"></i>Language List
                    </h3>
                    <ul class="space-y-2">
                        @php
                            $languageArray = [];
                            if (!empty($languages)) {
                                $languageArray = json_decode($languages, true) ?? [];
                            }
                        @endphp
                        @forelse ($languageArray as $languages)
                            <li
                                class="flex items-center justify-between bg-white rounded-lg px-4 py-2 shadow-sm border custom-blue-light-border hover:custom-blue-light-bg transition">
                                <span class="text-blue-900 font-medium">{{ $languages }}</span>
                                <a href="/admin/remove-language/{{ $languages }}"
                                    class="hover:text-red-700 transition" title="Remove">
                                    <i class="fa fa-trash text-red-600 text-lg"></i>
                                </a>
                            </li>
                        @empty
                            <li class="text-gray-500 italic">No prefix code added yet.</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(".address_page").addClass("font-semibold");
    </script>
@endsection
