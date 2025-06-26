@extends('layouts.admin_app')

@section('title', 'Admin Dashboard')

@section('link')

@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <style>
        @import url('https://fonts.cdnfonts.com/css/geist');

        body {
            font-family: 'Geist', sans-serif;
        }

        #apiTable_filter {
            margin-bottom: 2rem;
        }

        .odd,
        .even {
            background: black;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply px-2 py-1 rounded bg-gray-100 mx-1 text-sm;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            @apply bg-blue-600 text-white;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">
        <div class="border rounded-xl shadow-2xl bg-white max-w-[600px] mx-auto p-10">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-gray-800">Add New Provider</h1>
            </div>

            <form method="POST" action="/admin/add-new-provider" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 w-40 h-40 mx-auto my-4">
                        <div>
                            <img id="profile-picture-preview" src="/assets/image/uploadimg.png"
                                class="w-full h-full rounded-full cursor-pointer shadow-xl shadow-slate-400"
                                alt="Profile Preview" />
                        </div>
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden"
                        required />
                </div>


                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter provider name" required />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter provider email" required />
                </div>

                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <div class="flex items-center mt-1">
                        <select name="prefix_code"
                            class="block w-24 px-4 py-3 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="+44">+44</option>
                            <option value="+1">+1</option>
                            <option value="+91">+91</option>
                        </select>
                        <input type="text" id="mobile" name="mobile"
                            class="block w-full px-4 py-3 border-t border-b border-r border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Enter provider mobile number" required />
                    </div>
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                    <select id="language" name="language"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required>
                        <option value="">Select language</option>
                        <option value="en">English</option>
                        <option value="en-GB">English (UK)</option>
                        <option value="de">German</option>
                        <option value="es">Spanish</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-12"
                            placeholder="Set a password" required />
                        <button type="button" onclick="togglePasswordVisibility(this)"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 focus:outline-none"
                            tabindex="-1">
                            <i id="eye-icon" class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2 text-lg text-white bg-blue-400 rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(".providers").addClass("font-semibold");


        document.getElementById('profile_picture').addEventListener('change', function(event) {
            const [file] = event.target.files;
            const preview = document.getElementById('profile-picture-preview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });


        function togglePasswordVisibility(button) {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
