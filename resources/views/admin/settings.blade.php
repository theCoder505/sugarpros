@extends('layouts.admin_app')

@section('title', 'System Settings')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection


@section('styles')
    <style>
        .settings-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .settings-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .settings-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        .settings-body {
            padding: 1.5rem;
        }

        .image-preview {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border: 2px dashed #cbd5e0;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-preview:hover {
            border-color: #4299e1;
        }

        .image-upload-input {
            display: none;
        }

        .section-title {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        label {
            font-weight: bold !important;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto my-12 space-y-6 md:max-w-6xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @forelse ($settings as $settings)
            <form action="/admin/update-settings" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Brand Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">Brand Settings</h2>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                            <input type="text" name="brandname" value="{{ $settings->brandname ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <input type="text" name="currency" value="{{ $settings->currency ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="flex flex-col items-start">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand Logo</label>
                            <input type="file" name="brandlogo" id="brandlogo" class="image-upload-input"
                                accept="image/*">
                            <img src="{{ asset($settings->brandlogo ?? '/assets/image/logo.png') }}" id="brandlogo-preview"
                                class="image-preview" onclick="document.getElementById('brandlogo').click()">
                            <p class="mt-1 text-xs text-gray-500">Click to upload new logo</p>
                        </div>

                        <div class="flex flex-col items-start">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand Icon (Favicon)</label>
                            <input type="file" name="brandicon" id="brandicon" class="image-upload-input"
                                accept="image/*">
                            <img src="{{ asset($settings->brandicon ?? '/assets/image/icon.png') }}" id="brandicon-preview"
                                class="image-preview" onclick="document.getElementById('brandicon').click()">
                            <p class="mt-1 text-xs text-gray-500">Click to upload new favicon</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">Contact Information</h2>
                    </div>
                    <div class="settings-body grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone Number</label>
                            <input type="text" name="contact_phone" value="{{ $settings->contact_phone ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $settings->contact_email ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                            <input type="text" name="fb_url" value="{{ $settings->fb_url ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                            <input type="text" name="twitter_url" value="{{ $settings->twitter_url ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                            <input type="text" name="instagram_url" value="{{ $settings->instagram_url ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Linkedin URL</label>
                            <input type="text" name="linkedin_url" value="{{ $settings->linkedin_url ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Stripe Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">Stripe Payment Settings</h2>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Client ID</label>
                            <input type="text" name="stripe_client_id" value="{{ $settings->stripe_client_id ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Secret Key</label>
                            <input type="text" name="stripe_secret_key" value="{{ $settings->stripe_secret_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">One Time Service Flat Fee</label>
                            <input type="number" step="0.01" name="stripe_amount"
                                value="{{ $settings->stripe_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicare Amount</label>
                            <input type="number" step="0.01" name="medicare_amount"
                                value="{{ $settings->medicare_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        {{-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Key</label>
                            <input type="text" name="subscription_key" value="{{ $settings->subscription_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div> --}}
                    </div>
                </div>





                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">Stripe Monthly Plans</h2>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Package Amount</label>
                            <input type="number" step="0.01" name="monthly_basic_amount"
                                value="{{ $settings->monthly_basic_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Package Price Key</label>
                            <input type="text" name="monthly_basic_price_key" value="{{ $settings->monthly_basic_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6 border-t-2 border-b-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Premium Package Amount</label>
                            <input type="number" step="0.01" name="monthly_premium_amount"
                                value="{{ $settings->monthly_premium_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Premium Package Price Key</label>
                            <input type="text" name="monthly_premium_price_key" value="{{ $settings->monthly_premium_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">VIP Package Amount</label>
                            <input type="number" step="0.01" name="monthly_vip_amount"
                                value="{{ $settings->monthly_vip_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">VIP Package Price Key</label>
                            <input type="text" name="monthly_vip_price_key" value="{{ $settings->monthly_vip_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>


                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">Stripe Annual Plans</h2>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Package Amount</label>
                            <input type="number" step="0.01" name="annual_basic_amount"
                                value="{{ $settings->annual_basic_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Basic Package Price Key</label>
                            <input type="text" name="annual_basic_price_key" value="{{ $settings->annual_basic_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6 border-t-2 border-b-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Premium Package Amount</label>
                            <input type="number" step="0.01" name="annual_premium_amount"
                                value="{{ $settings->annual_premium_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Premium Package Price Key</label>
                            <input type="text" name="annual_premium_price_key" value="{{ $settings->annual_premium_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">VIP Package Amount</label>
                            <input type="number" step="0.01" name="annual_vip_amount"
                                value="{{ $settings->annual_vip_amount ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">VIP Package Price Key</label>
                            <input type="text" name="annual_vip_price_key" value="{{ $settings->annual_vip_price_key ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>







                <!-- API Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2 class="text-lg font-semibold text-gray-800">API Settings</h2>
                    </div>
                    <div class="settings-body grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">OpenAI API Key</label>
                            <input type="text" name="OPENAI_API_KEY" value="{{ $settings->OPENAI_API_KEY ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dexcom Client ID</label>
                            <input type="text" name="DEXCOM_CLIENT_ID" value="{{ $settings->DEXCOM_CLIENT_ID ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dexcom Client Secret</label>
                            <input type="text" name="DEXCOM_CLIENT_SECRET"
                                value="{{ $settings->DEXCOM_CLIENT_SECRET ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dexcom Redirect URI</label>
                            <input type="text" name="DEXCOM_REDIRECT_URI"
                                value="{{ $settings->DEXCOM_REDIRECT_URI ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Web Root URL</label>
                            <input type="text" name="meeting_web_root_url"
                                value="{{ $settings->meeting_web_root_url ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Claim MD API Key (Account Key)</label>
                            <input type="text" name="CLAIM_MD_API_KEY"
                                value="{{ $settings->CLAIM_MD_API_KEY ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">FatSecret Key</label>
                            <input type="text" name="FATSECRET_KEY" value="{{ $settings->FATSECRET_KEY ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">FatSecret Secret</label>
                            <input type="text" name="FATSECRET_SECRET"
                                value="{{ $settings->FATSECRET_SECRET ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>


                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>

        @empty
        @endforelse
    </main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Highlight settings in sidebar
            $("#settings-link").addClass("font-semibold text-indigo-600");

            // Image preview for brand logo
            $('#brandlogo').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#brandlogo-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Image preview for brand icon
            $('#brandicon').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#brandicon-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
