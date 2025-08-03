@extends('layouts.provider')

@section('title', 'Upload Claims')

@section('style')
    <style>
        .form-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
    </style>
@endsection

@section('content')
    @include('layouts.provider_header')

    <div class="min-h-screen bg-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-8">
                            <div class="flex items-center space-x-2">
                                <div class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium">
                                    EMR System
                                </div>
                                <span class="text-gray-400">|</span>
                                <span class="text-blue-600 font-medium">SugarPros AI</span>
                                <span class="text-gray-400">|</span>
                                <span class="text-gray-600">Patient Claims Biller</span>
                                <span class="text-gray-400">|</span>
                                <span class="text-gray-600">Active Appointments</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6v6H9z"></path>
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-3 bg-gray-50 flex items-center justify-between text-sm">
                    <div class="flex items-center space-x-6">
                        <div>
                            <span class="text-gray-600">Encounter:</span>
                            <span class="font-medium">07/18/2025</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Bill Date:</span>
                            <span class="font-medium">07/18/2025</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Bill ID:</span>
                            <span class="font-medium">98-4581-W95G24-09</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Patient Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Patient</h3>
                                <button class="text-blue-600 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" value="Lorem Ipsum" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">DOB</label>
                                    <input type="text" value="04/21/1960" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                                    <input type="text" value="YLS45681" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                    <input type="text" value="Male" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" value="(901) 832-8878" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" rows="2" readonly>725 LOEB ST,
Memphis, TN 38111</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Insurance Information -->
                    <div class="bg-white rounded-lg shadow-sm mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Insurance</h3>
                                <button class="text-blue-600 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Coverage type</label>
                                    <input type="text" value="Medical" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary</label>
                                    <input type="text" value="Tennessee Medicare" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan name</label>
                                    <input type="text" value="Medicare" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan type</label>
                                    <input type="text" value="Medicare" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Insurance ID</label>
                                    <input type="text" value="3G56Q19A71" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Group ID</label>
                                    <input type="text" value="--" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Effective date</label>
                                    <input type="text" value="06/05/2025" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Eligibility</label>
                                    <input type="text" value="Not available" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Claims address</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" rows="3" readonly>PO BOX 100306,
Columbia, SC
29202-3306</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Guarantor</label>
                                <input type="text" value="Self" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm">
                        <!-- Service 1 -->
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Service 1</h3>
                                <div class="flex items-center space-x-3">
                                    <button class="text-blue-600 hover:text-blue-700 text-sm">Clear all</button>
                                    <button class="text-blue-600 hover:text-blue-700 text-sm">Duplicate</button>
                                    <button class="text-red-600 hover:text-red-700 text-sm">Delete</button>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-6 space-y-6">
                            <!-- Modifiers -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modifiers</label>
                                <div class="relative">
                                    <input type="text" placeholder="Search here" class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md form-input">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">BILLING CODE*</label>
                                <div class="flex items-center space-x-2">
                                    <select class="px-3 py-2 border border-gray-300 rounded-md form-input">
                                        <option>99214</option>
                                    </select>
                                    <span class="text-sm text-gray-500">Office or other outpatient visit for the evaluation...</span>
                                </div>
                            </div>

                            <!-- ICD-10 Diagnoses -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ICD-10 DIAGNOSES</label>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <select class="px-3 py-2 border border-gray-300 rounded-md form-input">
                                            <option>E11.9</option>
                                        </select>
                                        <span class="text-sm text-gray-500">Office or other outpatient visit for the evaluation...</span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-md">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <span class="text-sm font-medium">E11.65 - Type 2 diabetes mellitus with hyperglycemia</span>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-md">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <span class="text-sm font-medium">E66.9 - Obesity, unspecified</span>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-md">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <span class="text-sm font-medium">Z68.42 - Body mass index (BMI) 45.0-49.9, adult</span>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-md">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <span class="text-sm font-medium">I10 - Essential (primary) hypertension</span>
                                            </div>
                                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Fields -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">START DATE*</label>
                                    <input type="text" placeholder="MM/DD/YYYY" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">END DATE*</label>
                                    <input type="text" placeholder="MM/DD/YYYY" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                                </div>
                            </div>

                            <!-- Units and Quantity -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">UNITS</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                                        <option>MM/DD/YYYY</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">QTY</label>
                                    <input type="number" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                                </div>
                            </div>

                            <!-- Billed Charge -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">BILLED CHARGE</label>
                                <input type="text" value="$0.00" class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                            </div>
                        </div>

                        <!-- Service 2 -->
                        <div class="border-t border-gray-200">
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900">Service 2</h3>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Add Service Button -->
                        <div class="border-t border-gray-200 px-6 py-4">
                            <button class="w-full flex items-center justify-center py-3 px-4 border-2 border-dashed border-gray-300 rounded-md text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add service
                            </button>
                        </div>

                        <!-- Notes Section -->
                        <div class="border-t border-gray-200 px-6 py-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" rows="3" placeholder="Type here"></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 mt-6">
                        <button class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Save
                        </button>
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Send to Biller
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(".claims").addClass('text-black');
        
        // Add any additional JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form interactions
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // Handle form submission
                });
            }
        });
    </script>
@endsection