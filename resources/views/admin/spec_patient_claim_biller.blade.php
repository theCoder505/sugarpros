@extends('layouts.admin_app')

@section('title', 'Patient Claims Biller On Appointment: ' . $appointment_uid)

@section('styles')
    <style>
        .pcb_input:focus {
            outline: none;
            border-color: #2d92b3;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .icd10-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 300px;
            overflow-y: auto;
            z-index: 50;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .icd10-dropdown.active {
            display: block;
        }
        
        .icd10-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.15s;
        }
        
        .icd10-item:hover {
            background-color: #f3f4f6;
        }
        
        .icd10-item:last-child {
            border-bottom: none;
        }
        
        .icd10-code {
            font-weight: 600;
            color: #2d92b3;
            font-size: 0.875rem;
        }
        
        .icd10-description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .no-results {
            padding: 1rem;
            text-align: center;
            color: #9ca3af;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    @php
        $icd10Codes = [
            // Type 2 Diabetes Mellitus
            ['code' => 'E11.9', 'description' => 'Type 2 diabetes mellitus without complications'],
            ['code' => 'E11.65', 'description' => 'Type 2 diabetes mellitus with hyperglycemia'],
            ['code' => 'E11.21', 'description' => 'Type 2 diabetes mellitus with nephropathy'],
            ['code' => 'E11.22', 'description' => 'Type 2 diabetes mellitus with chronic kidney disease'],
            ['code' => 'E11.40', 'description' => 'Type 2 diabetes mellitus with neuropathy, unspecified'],
            ['code' => 'E11.8', 'description' => 'Type 2 diabetes mellitus with other specified complications'],
            ['code' => 'E13.9', 'description' => 'Other specified diabetes mellitus without complications'],
            
            // Type 1 Diabetes Mellitus
            ['code' => 'E10.9', 'description' => 'Type 1 diabetes mellitus without complications'],
            ['code' => 'E10.65', 'description' => 'Type 1 diabetes mellitus with hyperglycemia'],
            ['code' => 'E10.649', 'description' => 'Type 1 diabetes mellitus with hypoglycemia without coma'],
            ['code' => 'E10.21', 'description' => 'Type 1 diabetes mellitus with nephropathy'],
            ['code' => 'E10.22', 'description' => 'Type 1 diabetes mellitus with chronic kidney disease'],
            ['code' => 'E10.40', 'description' => 'Type 1 diabetes mellitus with neuropathy, unspecified'],
            ['code' => 'E10.42', 'description' => 'Type 1 diabetes mellitus with polyneuropathy'],
            ['code' => 'E10.51', 'description' => 'Type 1 diabetes mellitus with circulatory complications'],
            ['code' => 'E10.59', 'description' => 'Type 1 diabetes mellitus with other circulatory complications'],
            ['code' => 'E10.610', 'description' => 'Type 1 diabetes mellitus with diabetic neuropathic arthropathy'],
            ['code' => 'E10.618', 'description' => 'Type 1 diabetes mellitus with other musculoskeletal complications'],
            ['code' => 'E10.621', 'description' => 'Type 1 diabetes mellitus with foot ulcer'],
            ['code' => 'E10.69', 'description' => 'Type 1 diabetes mellitus with other specified complications'],
            ['code' => 'E10.8', 'description' => 'Type 1 diabetes mellitus with unspecified complications'],
            ['code' => 'E10.11', 'description' => 'Type 1 diabetes mellitus with ketoacidosis with coma'],
            ['code' => 'E10.10', 'description' => 'Type 1 diabetes mellitus with ketoacidosis without coma'],
            
            // Obesity and Weight Management
            ['code' => 'E66.01', 'description' => 'Morbid obesity due to excess calories'],
            ['code' => 'E66.9', 'description' => 'Obesity, unspecified'],
            ['code' => 'E66.3', 'description' => 'Overweight'],
            ['code' => 'Z68.41', 'description' => 'Body mass index (BMI) 40.0-44.9'],
            ['code' => 'Z68.42', 'description' => 'Body mass index (BMI) 45.0-49.9'],
            ['code' => 'Z68.43', 'description' => 'Body mass index (BMI) 50.0-59.9'],
            ['code' => 'Z68.44', 'description' => 'Body mass index (BMI) 60.0-69.9'],
            ['code' => 'Z68.45', 'description' => 'Body mass index (BMI) 70 or greater'],
            ['code' => 'Z71.3', 'description' => 'Dietary counseling and surveillance'],
            
            // Health Behavior and Psychosocial Factors
            ['code' => 'Z91.14', 'description' => 'Patient\'s other noncompliance with medication regimen'],
            ['code' => 'Z91.19', 'description' => 'Patient noncompliance, other specified'],
            ['code' => 'Z63.6', 'description' => 'Dependent relative needing care'],
            ['code' => 'Z73.1', 'description' => 'Type A behavior pattern'],
            ['code' => 'Z73.89', 'description' => 'Other problems related to life management difficulty'],
            ['code' => 'Z71.89', 'description' => 'Other specified counseling'],
            ['code' => 'Z60.0', 'description' => 'Social environment problems'],
            ['code' => 'Z72.4', 'description' => 'Inappropriate diet and eating habits'],
            ['code' => 'Z71.82', 'description' => 'Exercise counseling'],
            
            // Mental Health Diagnoses (for psychotherapy only)
            ['code' => 'F32.9', 'description' => 'Major depressive disorder, single episode, unspecified'],
            ['code' => 'F41.1', 'description' => 'Generalized anxiety disorder'],
            ['code' => 'F43.10', 'description' => 'Post-traumatic stress disorder'],
            ['code' => 'F50.9', 'description' => 'Eating disorder, unspecified'],
            ['code' => 'F45.29', 'description' => 'Somatic symptom disorder'],
            
            // Common Comorbidities
            ['code' => 'I10', 'description' => 'Essential (primary) hypertension'],
            ['code' => 'E78.5', 'description' => 'Hyperlipidemia, unspecified'],
            ['code' => 'N18.4', 'description' => 'Chronic kidney disease, stage 4'],
            ['code' => 'N18.9', 'description' => 'Chronic kidney disease, unspecified'],
            ['code' => 'G47.33', 'description' => 'Obstructive sleep apnea'],
            ['code' => 'E03.9', 'description' => 'Hypothyroidism, unspecified'],
        ];
    @endphp

    @forelse ($appointment as $item)
        <form action="/admin/add-new-patient-claims-md" method="post" class="pcbForm">
            @csrf
            <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">
            <input type="hidden" name="action" value="pcb" class="action">
            <input type="hidden" name="patient_fname" value="{{$item->fname}}" class="fname">
            <input type="hidden" name="patient_lname" value="{{$item->lname}}" class="lname">
            <div class="min-h-screen bg-gray-100 px-4 lg:px-0 py-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Main Content -->
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                        <div class="lg:col-span-3 grid grid-cols-1 lg:grid-cols-3 rounded-t-lg overflow-hidden">
                            <div class="lg:col-span-1 bg-white px-4 py-4 lg:border-r border-b-2 text-gray-700">
                                <div class="lg:flex justify-between">
                                    <div class="">
                                        Encounter <span
                                            class="text-black font-semibold">{{ \Carbon\Carbon::parse($item->created_at)->format('j/m/Y') }}</span>
                                    </div>
                                    <div class="">
                                        Bill Date <span
                                            class="text-black font-semibold">{{ \Carbon\Carbon::parse($item->date)->format('j/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-2 bg-white px-4 py-4 lg:border-l text-gray-700">
                                Bill ID <span class="text-black font-semibold">{{ $appointment_uid }}</span>
                            </div>
                        </div>
                        <!-- Patient Information -->
                        <div class="lg:col-span-1 bg-white px-4 py-6 rounded-b-lg">
                            <div class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden">
                                <div class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">Patient</h3>
                                        <div class="text-[#2d92b3] hover:text-teal-700 cursor-pointer">
                                            <img src="/assets/image/edit_icon.png" alt="" class="w-5 h-auto">
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text"
                                            value="{{ $claims_biller->name ?? $item->fname . ' ' . $item->lname }}" required
                                            name="name" class="pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">DOB</label>
                                        <input type="date" value="{{ $claims_biller->dob ?? $appointer_dob }}" required
                                            name="dob" class="uppercase pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                                        <input type="text"
                                            value="{{ $claims_biller->patient_id ?? $appointer_patient_id }}" required
                                            name="patient_id" class="pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <input type="text" value="{{ $claims_biller->gender ?? $appointer_gender }}"
                                            required name="gender" class="capitalize pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="text" value="{{ $claims_biller->phone ?? $item->users_phone }}"
                                            required name="phone" class="pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea class="pcb_input" required name="address" rows="2">{{ $claims_biller->address ?? $address }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance Information -->
                            <div class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden mt-6">
                                <div class="px-4 py-2 border-b-2 border-gray-200 bg-gray-100">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">Insurance</h3>
                                        <div class="text-[#2d92b3] hover:text-teal-700 cursor-pointer">
                                            <img src="/assets/image/edit_icon.png" alt="" class="w-5 h-auto">
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Coverage type</label>
                                        <input type="text" value="{{ $claims_biller->coverage_type ?? 'Medical' }}"
                                            class="pcb_input" required name="coverage_type">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary</label>
                                        <input type="text"
                                            value="{{ $claims_biller->primary ?? $item->insurance_company }}"
                                            class="pcb_input" required name="primary">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan name</label>
                                        <input type="text" value="{{ $claims_biller->plan_name ?? 'Medicare' }}"
                                            class="pcb_input" required name="plan_name">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan type</label>
                                        <input type="text"
                                            value="{{ $claims_biller->plan_type ?? $item->insurance_plan_type }}"
                                            class="pcb_input" required name="plan_type">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Insurance ID</label>
                                        <input type="text"
                                            value="{{ $claims_biller->insurance_ID ?? $item->policy_id }}"
                                            class="pcb_input" required name="insurance_ID">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Payer ID</label>
                                        <input type="text"
                                            value="{{ $claims_biller->group_ID ?? $item->group_number }}"
                                            class="pcb_input" required name="group_ID">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Effective date</label>
                                        <input type="date" value="{{ $claims_biller->effective_date ?? $item->date }}"
                                            required name="effective_date" class="uppercase pcb_input">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Eligibility</label>
                                        <input type="text"
                                            value="{{ $claims_biller->eligibility ?? 'Not available' }}"
                                            class="pcb_input" required name="eligibility">
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Claims address</label>
                                        <textarea class="pcb_input" required name="claim_address" rows="3">{{ $claims_biller->claim_address ?? $item->users_address }}</textarea>
                                    </div>
                                    <div class="form_group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Guarantor</label>
                                        <input type="text" value="{{ $claims_biller->gurantor ?? 'Self' }}"
                                            class="pcb_input" required name="gurantor">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="hidden lg:flex justify-center gap-4 mt-6">
                                <div onclick="saveForm(this)"
                                    class="px-6 py-2 border-2 border-[#2d92b3] rounded-md text-[#2d92b3] hover:text-white transition-colors font-semibold w-[150px] hover:bg-[#2d92b3] text-center cursor-pointer">
                                    Save
                                </div>
                                <div class="px-6 py-2 bg-[#2d92b3] text-white rounded-md hover:bg-[#133a59] transition-colors font-semibold w-full text-center cursor-pointer"
                                    onclick="sendToBiller(this)">
                                    Send to Biller
                                </div>
                            </div>
                        </div>

                        <!-- Services Section -->
                        <div class="lg:col-span-2 lg:px-5 py-6">
                            <div class="rounded-lg shadow-sm grid gap-6">
                                <div class="services grid gap-6">
                                    @if (isset($services_data))
                                        @php
                                            $serviceCount = count($services_data['billing_code'] ?? []);
                                        @endphp
                                        @for ($i = 0; $i < $serviceCount; $i++)
                                            <div class="service bg-white rounded-xl border-2 border-gray-200"
                                                data-index="{{ $i }}">
                                                <div class="px-6 py-4 border-b border-gray-200">
                                                    <div class="lg:flex items-center justify-between">
                                                        <h3 class="text-lg font-semibold text-gray-900">Service
                                                            {{ $i + 1 }}</h3>
                                                        <div class="flex items-center space-x-4 controls">
                                                            <div class="text-[#2d92b3] hover:text-teal-700 text-sm cursor-pointer control_line"
                                                                onclick="clearService(this)">Clear all</div>
                                                            <div class="text-gray-600 hover:text-black text-sm cursor-pointer control_line"
                                                                onclick="duplicateService(this)">Duplicate</div>
                                                            <div class="text-red-600 hover:text-red-500 text-sm cursor-pointer control_line"
                                                                onclick="removeService(this)">Delete</div>
                                                            <div class="text-gray-400 hover:text-gray-600 px-4 cursor-pointer"
                                                                onclick="toggleService(this)">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="px-6 py-6 space-y-6 service_details">
                                                    <!-- Modifiers -->
                                                    <div>
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">Modifiers</label>
                                                        <div class="relative">
                                                            <input type="text" placeholder="Search here"
                                                                name="modifiers[]"
                                                                class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md form-input"
                                                                value="{{ $services_data['modifiers'][$i] ?? '' }}">
                                                            <div
                                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <svg class="h-5 w-5 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Billing Code -->
                                                    <div>
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">BILLING
                                                            CODE*</label>
                                                        <div class="flex items-center rounded-md border-2 overflow-hidden">
                                                            <input type="text" name="billing_code[]" required
                                                                class="px-3 py-2 border-r border-gray-300 max-w-[75px] outline-none"
                                                                placeholder="99214"
                                                                value="{{ $services_data['billing_code'][$i] ?? '' }}">
                                                            <input type="text" name="billing_text[]" required
                                                                class="px-3 py-2 border-l border-gray-300 w-full outline-none"
                                                                placeholder="Office or other outpatient visit for the evaluation..."
                                                                value="{{ $services_data['billing_text'][$i] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <!-- ICD-10 Diagnoses -->
                                                    <div>
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">ICD-10
                                                            DIAGNOSES</label>
                                                        <div class="flex gap-2 items-center justify-between mt-2">
                                                            <div class="w-full relative diagnosis_input_wrapper">
                                                                <div
                                                                    class="flex items-center rounded-md border-2 overflow-hidden w-full diagnosis_input_group">
                                                                    <input type="text"
                                                                        class="px-3 py-2 border-r border-gray-300 max-w-[75px] outline-none diagnoses_code"
                                                                        placeholder="E11.9">
                                                                    <input type="text"
                                                                        class="px-3 py-2 border-l border-gray-300 w-full outline-none diagnoses_text"
                                                                        placeholder="Search ICD-10 code or description..."
                                                                        autocomplete="off">
                                                                </div>
                                                                <div class="icd10-dropdown">
                                                                    @foreach($icd10Codes as $icd)
                                                                        <div class="icd10-item" data-code="{{ $icd['code'] }}" data-description="{{ $icd['description'] }}">
                                                                            <div class="icd10-code">{{ $icd['code'] }}</div>
                                                                            <div class="icd10-description">{{ $icd['description'] }}</div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="w-10 h-10 text-white bg-[#2d92b3] rounded cursor-pointer flex items-center justify-center text-lg"
                                                                onclick="addNewDiagnosis(this)">
                                                                <i class="fa fa-plus"></i>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="all_diagnoses flex justify-start mt-4 items-center flex-wrap gap-2">
                                                            @if (isset($services_data['diagnoses'][$i]))
                                                                @foreach ($services_data['diagnoses'][$i] as $diagnosis)
                                                                    <div class="inline-block">
                                                                        <div
                                                                            class="flex items-center justify-between px-3 py-2 gap-4 bg-gray-100 rounded-full">
                                                                            <div class="flex items-center space-x-2">
                                                                                <span
                                                                                    class="text-sm font-medium">{{ $diagnosis }}</span>
                                                                            </div>
                                                                            <div class="w-6 h-6 flex justify-center items-center bg-[#2d92b3] rounded-full text-white cursor-pointer"
                                                                                onclick="removediagnosis(this)">
                                                                                <i class="fa fa-times"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Date Fields -->
                                                    <div class="grid grid-cols-2 gap-6">
                                                        <div>
                                                            <label
                                                                class="block text-sm font-semibold text-gray-700 mb-2">START
                                                                DATE*</label>
                                                            <input type="date" placeholder="MM/DD/YYYY"
                                                                name="start_date[]" required
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md form-input uppercase"
                                                                value="{{ $services_data['start_date'][$i] ?? '' }}">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-sm font-semibold text-gray-700 mb-2">END
                                                                DATE*</label>
                                                            <input type="date" placeholder="MM/DD/YYYY"
                                                                name="end_date[]" required
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md form-input uppercase"
                                                                value="{{ $services_data['end_date'][$i] ?? '' }}">
                                                        </div>
                                                    </div>

                                                    <!-- Units and Quantity -->
                                                    <div class="grid grid-cols-2 gap-6">
                                                        <div>
                                                            <label
                                                                class="block text-sm font-semibold text-gray-700 mb-2">UNITS</label>
                                                            <input type="text" placeholder="Add Units here"
                                                                name="units[]" required
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md form-input"
                                                                value="{{ $services_data['units'][$i] ?? '' }}">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-sm font-semibold text-gray-700 mb-2">QTY</label>
                                                            <input type="number" name="quantity[]" required
                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md form-input"
                                                                value="{{ $services_data['quantity'][$i] ?? '1' }}">
                                                        </div>
                                                    </div>

                                                    <!-- Billed Charge -->
                                                    <div>
                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 mb-2">BILLED
                                                            CHARGE</label>
                                                        <input type="text" name="billed_charge[]" required
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md form-input"
                                                            value="{{ $services_data['billed_charge'][$i] ?? '$0.00' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                </div>

                                <div class="w-full flex gap-2 font-semibold items-center justify-center py-3 px-4 border-2 border-[#2d92b3] rounded-md text-[#2d92b3] hover:text-white hover:bg-[#2d92b3] transition-colors cursor-pointer"
                                    onclick="addNewService(this)">
                                    Add Service
                                    <i class="fa fa-plus"></i>
                                </div>

                                <div class="px-6 py-4 bg-white rounded-xl border-2 border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md form-input" rows="3" name="notes"
                                        placeholder="Type here">{{ $claims_biller->notes ?? '' }}</textarea>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex lg:hidden justify-center gap-4">
                                    <div onclick="saveForm(this)"
                                        class="px-6 py-2 border-2 border-[#2d92b3] rounded-md text-[#2d92b3] hover:text-white transition-colors font-semibold w-[150px] hover:bg-[#2d92b3] text-center cursor-pointer">
                                        Save
                                    </div>
                                    <div class="px-6 py-2 bg-[#2d92b3] text-white rounded-md hover:bg-[#133a59] transition-colors font-semibold w-full text-center cursor-pointer"
                                        onclick="sendToBiller(this)">
                                        Send to Biller
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @empty
    @endforelse
@endsection

@section('scripts')
    <script src="/assets/js/pcb.js"></script>
    <script>
        // ICD-10 Codes data
        const icd10CodesData = @json($icd10Codes);
        
        // Initialize ICD-10 dropdown functionality
        function initICD10Dropdown(wrapper) {
            const codeInput = wrapper.find('.diagnoses_code');
            const textInput = wrapper.find('.diagnoses_text');
            const dropdown = wrapper.find('.icd10-dropdown');
            
            // Show dropdown on focus
            textInput.on('focus', function() {
                filterICD10Codes('');
                dropdown.addClass('active');
            });
            
            // Filter on input
            textInput.on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                filterICD10Codes(searchTerm);
            });
            
            // Handle click on dropdown item
            dropdown.on('click', '.icd10-item', function() {
                const code = $(this).data('code');
                const description = $(this).data('description');
                
                codeInput.val(code);
                textInput.val(description);
                dropdown.removeClass('active');
            });
            
            // Filter ICD-10 codes
            function filterICD10Codes(searchTerm) {
                let html = '';
                let hasResults = false;
                
                icd10CodesData.forEach(function(item) {
                    const codeMatch = item.code.toLowerCase().includes(searchTerm);
                    const descMatch = item.description.toLowerCase().includes(searchTerm);
                    
                    if (searchTerm === '' || codeMatch || descMatch) {
                        html += `
                            <div class="icd10-item" data-code="${item.code}" data-description="${item.description}">
                                <div class="icd10-code">${item.code}</div>
                                <div class="icd10-description">${item.description}</div>
                            </div>
                        `;
                        hasResults = true;
                    }
                });
                
                if (!hasResults) {
                    html = '<div class="no-results">No matching ICD-10 codes found</div>';
                }
                
                dropdown.html(html);
            }
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!wrapper.is(e.target) && wrapper.has(e.target).length === 0) {
                    dropdown.removeClass('active');
                }
            });
        }
        
        // Initialize all existing dropdowns
        $(document).ready(function() {
            $('.diagnosis_input_wrapper').each(function() {
                initICD10Dropdown($(this));
            });
        });
        
        // Override addNewService to initialize dropdown for new services
        const originalAddNewService = window.addNewService;
        window.addNewService = function(element) {
            if (originalAddNewService) {
                originalAddNewService(element);
            }
            // Initialize dropdown for the newly added service
            setTimeout(function() {
                $('.diagnosis_input_wrapper').each(function() {
                    if (!$(this).data('initialized')) {
                        initICD10Dropdown($(this));
                        $(this).data('initialized', true);
                    }
                });
            }, 100);
        };
        
        // Override duplicateService to initialize dropdown for duplicated services
        const originalDuplicateService = window.duplicateService;
        window.duplicateService = function(element) {
            if (originalDuplicateService) {
                originalDuplicateService(element);
            }
            // Initialize dropdown for the duplicated service
            setTimeout(function() {
                $('.diagnosis_input_wrapper').each(function() {
                    if (!$(this).data('initialized')) {
                        initICD10Dropdown($(this));
                        $(this).data('initialized', true);
                    }
                });
            }, 100);
        };
        
        @if (isset($services_data))
            // Initialize services array with existing data - avoid redeclaration
            if (typeof services === 'undefined') {
                var services = [];
            }
            
            @foreach ($services_data['billing_code'] ?? [] as $index => $billing_code)
                services[{{ $index }}] = {
                    modifiers: "{{ $services_data['modifiers'][$index] ?? '' }}",
                    billing_code: "{{ $billing_code }}",
                    billing_text: "{{ $services_data['billing_text'][$index] ?? '' }}",
                    diagnoses: [], // Start with empty array - we'll populate from DOM
                    start_date: "{{ $services_data['start_date'][$index] ?? '' }}",
                    end_date: "{{ $services_data['end_date'][$index] ?? '' }}",
                    units: "{{ $services_data['units'][$index] ?? '' }}",
                    quantity: "{{ $services_data['quantity'][$index] ?? '1' }}",
                    billed_charge: "{{ $services_data['billed_charge'][$index] ?? '$0.00' }}"
                };

                // Get diagnoses from the DOM for this service
                const serviceElement = $(`.service[data-index="{{ $index }}"]`);
                if (serviceElement.length) {
                    services[{{ $index }}].diagnoses = serviceElement.find('.all_diagnoses .inline-block').map(
                        function() {
                            return $(this).find('span').text();
                        }).get();
                }
            @endforeach
        @else
            // Only declare services if it doesn't exist already  
            if (typeof services === 'undefined') {
                var services = [];
            }
        @endif
    </script>
@endsection
