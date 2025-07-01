@extends('layouts.admin_app')

@section('title', 'Manage Services')

@section('link')

@endsection

@section('styles')

@endsection

@section('content')
    <main class="mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Manage Services</h1>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="grid grid-cols-3 gap-4 p-8">
                    <div class="item overflow-hidden">
                        <form action="/admin/add-new-service" method="POST" enctype="multipart/form-data"
                            class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl flex flex-col gap-4 border-2 shadow-lg shadow-blue-200 hover:border-blue-500">
                            @csrf
                            <div class="basic_details flex flex-col items-center gap-3">
                                <label for="uploadImage" class="cursor-pointer group relative">
                                    <img id="previewImage" src="/assets/image/uploadimage.png" alt="Upload Service Image"
                                        class="object-cover max-w-full border-4 border-blue-200 shadow group-hover:opacity-80 transition duration-200">
                                    <span
                                        class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs px-2 py-1 rounded opacity-80 group-hover:opacity-100 transition">Upload
                                        Image</span>
                                </label>
                                <input type="file" name="service_image" id="uploadImage" accept="image/*" class="hidden"
                                    onchange="showImage(this)">
                                <input type="text"
                                    class="border-2 border-blue-200 rounded-lg py-2 px-4 w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                                    required placeholder="Service Title" name="service_title" autocomplete="off">
                            </div>
                            <div class="all_details grid gap-3 mt-2 px-1">
                                <div class="item flex gap-3 border border-blue-100 bg-blue-50/50 p-3 rounded-lg shadow-sm">
                                    <div class="grid gap-2 w-full">
                                        <input type="text"
                                            class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                            name="point_heading[]" required placeholder="Service Point Heading"
                                            autocomplete="off">
                                        <input type="text"
                                            class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                            name="point_shortnote[]" required placeholder="Service Point Shortnote"
                                            autocomplete="off">
                                    </div>
                                    <div class="grid gap-2">
                                        <button type="button" onclick="addItem(this)"
                                            class="text-blue-600 font-bold border-2 border-blue-400 rounded-md bg-white w-10 h-10 hover:bg-blue-500 hover:text-white flex justify-center items-center transition">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold w-full rounded-lg text-lg p-2 mt-2 shadow hover:from-blue-700 hover:to-blue-500 transition">
                                <i class="fa fa-plus-circle mr-2"></i> Add Service
                            </button>
                        </form>
                    </div>

                    @forelse ($allServices as $item)
                        <div
                            class="item rounded-xl overflow-hidden">
                            <form action="/admin/update-service" method="post" enctype="multipart/form-data"
                                class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl flex flex-col gap-4 border-2 shadow-lg shadow-blue-200 hover:border-blue-500">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $item->id }}">
                                <div class="basic_details flex flex-col items-center gap-3">
                                    <label for="uploadImage{{ $item->id }}" class="cursor-pointer group relative">
                                        <img id="previewImage{{ $item->id }}" src="/{{ $item->service_image }}"
                                            alt="Service Image"
                                            class="object-cover max-w-full border-4 border-blue-200 shadow group-hover:opacity-80 transition duration-200">
                                        <span
                                            class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs px-2 py-1 rounded opacity-80 group-hover:opacity-100 transition">Change
                                            Image</span>
                                    </label>
                                    <input type="file" name="service_image" id="uploadImage{{ $item->id }}"
                                        accept="image/*" class="hidden" onchange="showImage(this)">

                                    <input type="text"
                                        class="border-2 border-blue-200 rounded-lg py-2 px-4 w-full focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                                        required placeholder="Service Title" name="service_title"
                                        value="{{ $item->service_title }}" autocomplete="off">
                                </div>
                                <div class="all_details grid gap-3 mt-2 px-1" id="pointsContainer{{ $item->id }}">
                                    @php
                                        $headings = json_decode($item->service_points);
                                        $shortnotes = json_decode($item->service_point_details);
                                    @endphp

                                    @if ($headings && $shortnotes)
                                        @for ($i = 0; $i < count($headings); $i++)
                                            <div
                                                class="item flex gap-3 border border-blue-100 bg-blue-50/50 p-3 rounded-lg shadow-sm">
                                                <div class="grid gap-2 w-full">
                                                    <input type="text"
                                                        class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                                        name="point_heading[]" required placeholder="Service Point Heading"
                                                        value="{{ $headings[$i] ?? '' }}" autocomplete="off">
                                                    <input type="text"
                                                        class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                                        name="point_shortnote[]" required
                                                        placeholder="Service Point Shortnote"
                                                        value="{{ $shortnotes[$i] ?? '' }}" autocomplete="off">
                                                </div>
                                                <div class="grid gap-2">
                                                    @if ($i === 0)
                                                        <button type="button" onclick="addItem(this)"
                                                            class="text-blue-600 font-bold border-2 border-blue-400 rounded-md bg-white w-10 h-10 hover:bg-blue-500 hover:text-white flex justify-center items-center transition">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" onclick="removeItem(this)"
                                                            class="text-red-600 font-bold border-2 border-red-400 rounded-md bg-white w-10 h-10 hover:bg-red-500 hover:text-white flex justify-center items-center transition">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold w-full rounded-lg text-lg p-2 shadow hover:from-blue-700 hover:to-blue-500 transition">
                                        <i class="fa fa-save mr-2"></i> Update
                                    </button>
                                    <a href="/admin/delete-service/{{ $item->id }}"
                                        class="bg-gradient-to-r from-red-600 to-red-400 text-white font-semibold w-full rounded-lg text-lg p-2 shadow hover:from-red-700 hover:to-red-500 transition flex justify-center items-center"
                                        onclick="return confirm('Are you sure you want to delete this service?')">
                                        <i class="fa fa-trash mr-2"></i> Delete
                                    </a>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div
                            class="item rounded-xl overflow-hidden shadow flex items-center justify-center text-4xl border font-semibold text-red-600">
                            No Service Yet!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
@section('scripts')
    <script>
        function addItem(passedThis) {
            let newItem = `<div class="item flex gap-3 border border-blue-100 bg-blue-50/50 p-3 rounded-lg shadow-sm">
                                    <div class="grid gap-2 w-full">
                                        <input type="text"
                                            class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                            name="point_heading[]" required placeholder="Service Point Heading"
                                            autocomplete="off">
                                        <input type="text"
                                            class="border-2 border-blue-100 rounded px-4 py-2 w-full focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition"
                                            name="point_shortnote[]" required placeholder="Service Point Shortnote"
                                            autocomplete="off">
                                    </div>
                                    <div class="grid gap-2">
                                        <button type="button" onclick="addItem(this)"
                                            class="text-blue-600 font-bold border-2 border-blue-400 rounded-md bg-white w-10 h-10 hover:bg-blue-500 hover:text-white flex justify-center items-center transition">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" onclick="removeItem(this)"
                                            class="text-red-600 font-bold border-2 border-red-400 rounded-md bg-white w-10 h-10 hover:bg-red-500 hover:text-white flex justify-center items-center transition">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>`;
            $(passedThis).parent().parent().parent(".all_details").append(newItem);
        }

        function removeItem(passedThis) {
            $(passedThis).parent().parent().remove();
        }

        function showImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Get the parent form of the input
                    var form = $(input).closest('form');
                    // Find the preview image within this form
                    var previewImage = form.find('img[id^="previewImage"]');
                    // Set the src attribute
                    previewImage.attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Update all onchange handlers to pass 'this' correctly
        $(document).ready(function() {
            $('input[type="file"][name="service_image"]').each(function() {
                $(this).attr('onchange', 'showImage(this)');
            });
        });
    </script>
@endsection
