@extends('layouts.provider')

@section('title', 'Account')

@section('link')

@endsection

@section('style')
    <style>
        .account {
            font-weight: bold;
            color: #2889AA !important;
        }
    </style>

@endsection


@section('content')
    @include('layouts.provider_header')

    <div class="bg-gray-100 ">
        <div class="md:flex md:max-w-7xl py-[2rem] mx-auto rounded-lg">
            <!-- Sidebar -->
            <aside class="md:w-64 bg-white rounded-l-lg">
                <h3 class="border-b text-[18px] p-4 font-bold border[#0000001A]/10">Account</h3>
                <nav class="space-y-4 p-4 text-[14px]">
                    <a href="/provider/account"
                        class="flex items-center  text-[#000000] account hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                    </a>
                    <a href="/provider/settings" class="flex items-center text-[#000000] hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="/provider/notifications"
                        class="flex items-center text-[#000000] hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-bell"></i>
                        <span>Notification</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 bg-white border-l rounded-r-lg border-[#00000A]/10 p-4">
                <h3 class="text-[18px] font-bold ">Account</h3>



                <form action="/provider/update-profile-picture" method="post" enctype="multipart/form-data">
                    <div class="flex items-center space-x-4 mt-4">
                        @csrf
                        <label for="userImage">
                            @if ($profile_picture == null)
                                <img src="{{ asset('assets/image/uploadimg.png') }}" alt="Profile"
                                    class="w-[124px] h-[124px] rounded-full object-cover cursor-pointer"
                                    id="profilePicture">
                            @else
                                <img src="{{ '/' . $profile_picture }}" alt="Profile"
                                    class="w-[124px] h-[124px] rounded-full object-cover cursor-pointer"
                                    id="profilePicture">
                            @endif
                        </label>
                        <input type="file" name="profilepicture" id="userImage" class="hidden"
                            onchange="showProfilePicture(this)" accept="image/*">
                        <div class="md:space-x-2 space-y-2  mt-5">
                            <button type="submit"
                                class="max-w-[150px] bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2  text-sm rounded-lg transition duration-200">
                                Change Picture
                            </button>
                            <button type="button" class="bg-[#F544441A]/10 text-[#F54444] px-4 py-2 rounded"
                                onclick="deleteAccount(this)">Delete</button>
                        </div>
                    </div>
                </form>




                <form class="mt-8 space-y-6 my-10" action="/provider/update-provider-account" method="POST">
                    @csrf
                    <div>
                        <label class="font-semibold text-[20px] block">
                            Personal information
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div>
                                <label for="First" class="block text-sm font-medium text-gray-700 mb-1">
                                    First Name
                                </label>
                                <input type="text" id="First" placeholder="Type here..."
                                    value="{{ Auth::guard('provider')->user()->first_name }}" name="first_name"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Name
                                </label>
                                <input type="text" id="last_name" placeholder="Type here..."
                                    value="{{ Auth::guard('provider')->user()->last_name }}" name="last_name"
                                    class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email
                            </label>
                            <input type="email" id="email" placeholder="Type here..."
                                value="{{ Auth::guard('provider')->user()->email }}" name="email"
                                class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                        </div>

                        <div class="">
                            <label for="mobile" class="block text-[16px] font-semibold text-[#000000] mb-1">
                                Phone Number
                            </label>
                            <div class="flex items-center px-2 py-2 border border-gray-300 rounded-md shadow-sm">
                                <select name="prefix_code" class="text-gray-700 bg-transparent focus:outline-none" required>
                                    @php
                                        $options = json_decode($prefixcode, true);
                                    @endphp
                                    @if (is_array($options))
                                        @foreach ($options as $code)
                                            <option value="{{ $code }}"
                                                {{ Auth::guard('provider')->user()->prefix_code == $code ? 'selected' : '' }}>
                                                {{ $code }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="tel" id="mobile" placeholder="(555) 135-7924" name="phone_number"
                                    value="{{ Auth::guard('provider')->user()->mobile }}"
                                    class="w-full ml-2 placeholder-gray-400 border-none focus:outline-none" required />
                            </div>
                        </div>
                    </div>


                    <div class="md:max-w-full my-4">
                        <p class="block text-[20px] font-medium text-[#000] mb-1">
                            Preferences
                        </p>

                        <label for="language" class="block text-sm font-medium text-gray-700 mb-1">
                            Language
                        </label>
                        <select id="language" name="language"
                            class="w-full  bg-white text-[#A3A3A3] px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            @php
                                $options = json_decode($languages, true);
                            @endphp
                            @if (is_array($options))
                                @foreach ($options as $language)
                                    <option value="{{ $language }}"
                                        {{ Auth::guard('provider')->user()->language == $language ? 'selected' : '' }}>
                                        {{ $language }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>




                    <button type="submit"
                        class="max-w-[150px] bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2  text-sm rounded-lg transition duration-200 float-right">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>






@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const uploadBox = document.getElementById("upload-box");
            const fileInput = document.getElementById("file-upload");
            const fileNameDisplay = document.getElementById("file-name");

            uploadBox.addEventListener("click", function() {
                fileInput.click();
            });

            fileInput.addEventListener("change", function() {
                if (this.files.length > 0) {
                    const file = this.files[0];

                    fileNameDisplay.textContent = `Selected: ${file.name}`;
                    fileNameDisplay.classList.remove("hidden");

                    uploadBox.classList.add("border-[#FF6400]", "bg-[#FFF5F0]");

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewImage = document.getElementById("preview-image");
                        previewImage.src = e.target.result;
                        document.getElementById("preview-container").classList.remove("hidden");
                    };
                    reader.readAsDataURL(file);
                }
            });
        });






        $(function() {
            function updateRemoveButtons() {
                let rows = $('#about-me-fields .about-me-row');
                rows.find('.remove-about-me').toggle(rows.length > 1);
            }

            updateRemoveButtons();

            $('#add-about-me').on('click', function() {
                let rows = $('#about-me-fields .about-me-row').length;
                if (rows < 5) {
                    let newRow = `<div class="flex items-center space-x-2 about-me-row">
                                        <input type="text" name="about_me[]" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none" placeholder="Enter a point about yourself" maxlength="255" />
                                        <button type="button" class="remove-about-me bg-red-100 text-red-500 px-2 py-1 rounded hover:bg-red-200" title="Remove">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>`;
                    $('#about-me-fields').append(newRow);
                    updateRemoveButtons();
                } else {
                    toastr.warning('Can\'t add more than five about points!');
                }

                if (rows == 4) {
                    $("#add-about-me").addClass('hidden');
                }
            });

            $('#about-me-fields').on('click', '.remove-about-me', function() {
                $(this).closest('.about-me-row').remove();
                $("#add-about-me").removeClass('hidden');
                updateRemoveButtons();
            });
        });
    </script>
@endsection
