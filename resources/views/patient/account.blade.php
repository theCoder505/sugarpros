@extends('layouts.patient_portal')

@section('title', 'Account')

@section('link')

@endsection

@section('style')
    <style>
        .account {
            font-weight: bold;
            color: #2889AA !important;
        }
        .account_nav{
            border: 2px solid #2889AA;
        }
    </style>

@endsection


@section('content')

    @include('layouts.patient_header')

    <div class="bg-gray-100 ">
        <div class=" md:flex md:max-w-7xl py-[2rem] mx-auto rounded-lg">
            <!-- Sidebar -->
            <aside class="md:w-64 bg-white rounded-l-lg">
                <h3 class="border-b text-[18px] p-4 font-bold border[#0000001A]/10">Account</h3>
                <nav class="space-y-4 p-4 text-[14px]">
                    <a href="/account" class="flex items-center  text-[#000000] account space-x-2">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                    </a>
                    <a href="/settings" class="flex items-center text-[#000000] hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="/notifications" class="flex items-center text-[#000000] hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-bell"></i>
                        <span>Notification</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 bg-white border-l rounded-r-lg border-[#00000A]/10 p-4">
                <h3 class="text-[18px] font-bold ">Account</h3>
                <form action="/update-profile-picture" method="post" enctype="multipart/form-data">
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

                @forelse ($accountDetails as $account)
                    <form class="mt-8 space-y-6 my-10" method="POST" action="/update-account-details"
                        enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label class="font-semibold text-[20px] block">First, can I know your full name? <span
                                    class="text-sm text-[#00000099]/60">(We need this exactly as it appears on your
                                    insurance
                                    card!)</span></label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label for="First" class="block text-sm font-medium text-gray-700 mb-1">
                                        First Name
                                    </label>
                                    <input type="text" id="First" placeholder="First Name" name="fname" required
                                        value="{{ $account->fname }}"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none ">
                                </div>
                                <div>
                                    <label for="middle" class="block text-sm font-medium text-gray-700 mb-1">
                                        Middle Name
                                    </label>
                                    <input type="text" id="middle" placeholder="Middle Name" name="mname" required
                                        value="{{ $account->mname }}"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Last Name
                                    </label>
                                    <input type="text" id="last_name" placeholder="Last Name" name="lname" required
                                        value="{{ $account->lname }}"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                </div>

                            </div>
                        </div>

                        <div class="md:max-w-[49%] my-4">
                            <label for="last_name" class="block text-[20px] font-medium text-[#000] mb-1">Can I get your
                                birthday? <span class="text-sm text-gray-500">(MM/DD/YYYY)</span></label>
                            <input type="date" id="last_name" placeholder="MM/DD/YYYY" name="dob" required
                                value="{{ $account->dob }}"
                                class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none max-w-1/2">
                        </div>

                        <div class="md:max-w-[49%] my-4">
                            <label for="last_name" class="block text-[20px] font-medium text-[#000] mb-1">
                                Great Job. What was your assigned gender at birth?
                            </label>
                            <select id="gender" name="gender" required
                                class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                <option value="male" {{ $account->gender == 'male' ? 'selected' : '' }}>
                                    Male
                                </option>
                                <option value="female" {{ $account->gender == 'female' ? 'selected' : '' }}>
                                    Female
                                </option>
                                <option value="other" {{ $account->gender == 'other' ? 'selected' : '' }}>
                                    Other
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[20px] font-medium text-[#000] mb-1">Whats your email address and phone
                                number?</label>
                            <div class="max-w-full mt-2 flex justify-between items-center">
                                <div class="mt-2">
                                    <span class="text-[14px] font-semibold">Email</span>
                                    <p class="text-gray-600 mt-1">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="/settings" class="border px-4 py-2 rounded-md">Change Email</a>
                            </div>

                            <div class="max-w-full mt-2 flex justify-between items-center">
                                <div class="mt-2">
                                    <span class="text-[14px] font-semibold">Password</span>
                                    <p class="text-gray-600 mt-1">*********</p>
                                </div>
                                <a href="/settings" class="border px-4 py-2 rounded-md">Reset Password</a>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[20px] font-medium text-[#000] ">Just in case, whos your emergency
                                contact?</label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label class="text-[14px] font-semibold">Their Name</label>
                                    <input type="text" placeholder="Type here"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none"
                                        name="emmergency_name" required value="{{ $account->emmergency_name ?? '' }}">
                                </div>
                                <div>
                                    <label class="text-[14px] font-semibold">Relationship</label>
                                    <input type="text" placeholder="Type here"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none"
                                        name="emmergency_relationship" required
                                        value="{{ $account->emmergency_relationship ?? '' }}">
                                </div>
                                <div>
                                    <label class="text-[14px] font-semibold">Their Phone Number</label>
                                    <input type="text" placeholder="Type here"
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none"
                                        name="emmergency_phone" required value="{{ $account->emmergency_phone ?? '' }}">
                                </div>

                            </div>
                        </div>


                        <div>
                            <label class="font-semibold text-[20px] block">What’s your current address?</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label class="text-[14px] font-semibold">Street</label>
                                    <select id="street" name="street" required
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $streetOptions = json_decode($streets, true);
                                        @endphp
                                        @if(is_array($streetOptions))
                                            @foreach($streetOptions as $street)
                                                <option value="{{ $street }}" {{ $account->street == $street ? 'selected' : '' }}>{{ $street }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[14px] font-semibold">City</label>
                                    <select id="city" name="city" required
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($cities, true);
                                        @endphp
                                        @if(is_array($options))
                                            @foreach($options as $city)
                                                <option value="{{ $city }}" {{ $account->city == $city ? 'selected' : '' }}>{{ $city }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[14px] font-semibold">State</label>
                                    <select id="state" name="state" required
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($states, true);
                                        @endphp
                                        @if(is_array($options))
                                            @foreach($options as $state)
                                                <option value="{{ $state }}" {{ $account->state == $state ? 'selected' : '' }}>{{ $state }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[14px] font-semibold">Zip Code</label>
                                    <select id="zip_code" name="zip_code" required
                                        class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                                        <option value="" disabled selected>Select Here</option>
                                        @php
                                            $options = json_decode($zip_codes, true);
                                        @endphp
                                        @if(is_array($options))
                                            @foreach($options as $zipcode)
                                                <option value="{{ $zipcode }}" {{ $account->zip_code == $zipcode ? 'selected' : '' }}>{{ $zipcode }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                            </div>
                        </div>


                        <div>
                            <label class="font-semibold text-[20px] block">For billing, do you have insurance? If so, we’ll
                                need</label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label class="text-[14px] font-semibold">Insurance Provider</label>
                                    <input type="text" name="insurance_provider"
                                        placeholder="Enter insurance provider"
                                        class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none"
                                        value="{{ $account->insurance_provider ?? '' }}">
                                </div>

                                <div>
                                    <label class="text-[14px] font-semibold">Plan Number</label>
                                    <input type="text" name="insurance_plan_number" placeholder="Enter plan number"
                                        class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none"
                                        value="{{ $account->insurance_plan_number ?? '' }}">
                                </div>

                                <div>
                                    <label class="text-[14px] font-semibold">Group Number <span
                                            class="text-[#000000]/40 font-normal">(If Applicable)</span></label>
                                    <input type="text" name="insurance_group_number" placeholder="Enter group number"
                                        class="w-full bg-white px-3 py-2 mt-1 border border-gray-300 rounded-md outline-none"
                                        value="{{ $account->insurance_group_number ?? '' }}">
                                </div>


                            </div>
                        </div>


                        <div>
                            <label class="font-semibold text-[20px] block">To verify your identity, could you snap a photo
                                of
                                your driver’s license or state ID?</label>

                            <div class="w-full my-4">
                                <label id="upload-box" for="file-upload"
                                    class="w-full h-[230px] bg-white rounded-lg border-2 border-dashed border-[#BDBDBD] flex flex-col items-center justify-center text-center cursor-pointer hover:border-[#FF6400] transition-colors duration-200">
                                    @if ($account->license)
                                        <img id="preview-image"
                                            class="max-w-full h-full rounded-md border border-gray-300 object-cover"
                                            src="{{ asset($account->license) }}" />
                                    @else
                                        <div
                                            class="w-20 h-20 bg-gray-300 flex justify-center items-center rounded-full my-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-medium text-gray-700 mb-2">Upload your picture
                                            here
                                        </p>
                                        <p class="text-sm text-gray-500 mb-6">Don't worry—this is stored securely!
                                        </p>
                                    @endif
                                </label>
                                <input type="file" class="hidden" name="license"
                                    {{ !$account->license ? 'required' : '' }} onchange="OnFileChange(this)"
                                    id="file-upload" accept="image/*" />
                                <div id="file-name"
                                    class="mt-2 text-sm text-gray-600 {{ $account->license ? '' : 'hidden' }}">
                                    @if ($account->license)
                                        {{ basename($account->license) }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-[20px] block">Lastly, we might need your Social Security
                                Number
                                for insurance
                                claims. Is that okay?</label>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SSN</label>
                                <input type="text" placeholder="Type here" name="ssn" required
                                    value="{{ $account->ssn }}"
                                    class="w-full  bg-white px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-[20px] block">Almost done! How would you like us to
                                communicate
                                with you?</label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                    <input type="radio" name="communication" value="email" class="peer w-4 h-4"
                                        {{ $account->notification_type == 'email' ? 'checked' : '' }} />
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">Email</span>
                                    </div>
                                </label>

                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                    <input type="radio" name="communication" value="text" class="peer w-4 h-4"
                                        {{ $account->notification_type == 'text' ? 'checked' : '' }} />
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">Text</span>
                                    </div>
                                </label>

                                <label
                                    class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:border-[#133A59]">
                                    <input type="radio" name="communication" value="app" class="peer w-4 h-4"
                                        {{ $account->notification_type == 'app' ? 'checked' : '' }} />
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-900 text-[16px] peer-checked:text-[#133A59] ml-2">App
                                            Notifications</span>
                                    </div>
                                </label>
                            </div>
                        </div>


                        <div class="flex gap-4 items-center justify-end">
                            <button type="submit"
                                class="max-w-[150px] bg-[#2889AA] hover:bg-opacity-90  text-white px-4 py-2  text-sm rounded-lg transition duration-200 float-right">
                                Save Changes
                            </button>

                            <a href="/basic"
                                class="max-w-[200px] border border-[#2889AA] hover:bg-[#2889AA] text-[#2889AA] hover:text-white px-4 py-2  text-sm rounded-lg transition duration-200 float-right">
                                Update Basic Details
                            </a>
                        </div>
                    </form>
                @empty
                @endforelse
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
    </script>
@endsection
