@extends('layouts.provider')

@section('title', 'E Prescriptions')

@section('style')
    <style>
        .epr_input_group {
            margin-bottom: 1.5rem;
        }

        .epr_input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .epr_input:focus {
            outline: none;
            border-color: #2889AA;
            box-shadow: 0 0 0 3px rgba(40, 137, 170, 0.1);
        }

        .regular_epr {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .regular_epr:hover {
            background-color: #F3F4F6;
        }

        .active_epr {
            background-color: #2889AA;
            color: white;
        }

        .prescription-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .prescription-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    @include('layouts.provider_header')

    <div class="max-w-[1400px] mx-auto py-3 lg:py-6 lg:px-6">
        <div class="">
            <div class="flex gap-4 px-4 lg:px-12">
                <div class="bg-[#2889AA] flex items-center justify-center h-12 w-12 rounded-lg">
                    <img src="/assets/image/link_icon.png" alt="">
                </div>
                <div class="mb-4 lg:text-left">
                    <h1 class="text-xl font-semibold text-[#000000]">
                        E-Prescription
                    </h1>
                    <p class="text-gray-600 text-center">
                        Add and manage patient medications
                    </p>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mx-4 lg:mx-12 mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 lg:mx-12 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-4 lg:mx-12 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="py-6 px-4 border-t-2 border-[#EDECEC] lg:px-12 bg-[#F8FAFC]">
                <div class="lg:inline-block">
                    <div class="bg-white rounded-lg p-1 border border-[#EDECEC] grid lg:flex gap-2">
                        <div class="add_new_presscription regular_epr active_epr"
                            onclick="activateSpecPrescription('add_new_presscription')">
                            <i class="fa-solid fa-file-invoice"></i>
                            <p>New Prescription</p>
                        </div>
                        <div class="all_prescriptions regular_epr" onclick="activateSpecPrescription('all_prescriptions')">
                            <i class="fa-solid fa-list"></i>
                            <p>All Prescriptions</p>
                        </div>
                        <div class="send_to_dxscript regular_epr" onclick="activateSpecPrescription('send_to_dxscript')">
                            <i class="fa-solid fa-paper-plane"></i>
                            <p>Send via DxScript</p>
                        </div>
                    </div>
                </div>

                {{-- New Prescription Form --}}
                <div id="add_new_presscription" class="form_options">
                    <form class="space-y-6" id="new_prescription_form_data" action="/provider/add-e-prescription"
                        method="POST">
                        @csrf

                        <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">

                        <div class="epr_input_group">
                            <label for="medication">
                                MEDICATION <strong class="text-red-500">*</strong>
                            </label>
                            <input type="text" name="medication" id="medication" class="epr_input"
                                value="{{ old('medication') }}" required placeholder="e.g., Amoxicillin 500mg">
                            <p class="text-sm text-gray-500 mt-1">Enter the medication name and strength</p>
                        </div>

                        <div class="epr_input_group">
                            <label for="daily_use">DAILY USE (SIG) <strong class="text-red-500">*</strong></label>
                            <input type="text" name="daily_use" id="daily_use" class="epr_input"
                                value="{{ old('daily_use') }}" required
                                placeholder="e.g., Take 1 tablet by mouth three times daily">
                            <p class="text-sm text-gray-500 mt-1">Enter the dosage instructions</p>
                        </div>

                        <div class="epr_input_group">
                            <label for="diagnosis">DIAGNOSIS</label>
                            <select name="diagnosis" id="diagnosis" class="epr_input">
                                <option value="">Select A ICD-10 Diagnosis</option>
                                <!-- Type 2 Diabetes Mellitus -->
                                <option value="E11.9 - Type 2 diabetes mellitus without complications" {{ old('diagnosis') == 'E11.9 - Type 2 diabetes mellitus without complications' ? 'selected' : '' }}>E11.9 - Type 2 diabetes mellitus without complications</option>
                                <option value="E11.65 - Type 2 diabetes mellitus with hyperglycemia" {{ old('diagnosis') == 'E11.65 - Type 2 diabetes mellitus with hyperglycemia' ? 'selected' : '' }}>E11.65 - Type 2 diabetes mellitus with hyperglycemia</option>
                                <option value="E11.21 - Type 2 diabetes mellitus with nephropathy" {{ old('diagnosis') == 'E11.21 - Type 2 diabetes mellitus with nephropathy' ? 'selected' : '' }}>E11.21 - Type 2 diabetes mellitus with nephropathy</option>
                                <option value="E11.22 - Type 2 diabetes mellitus with chronic kidney disease" {{ old('diagnosis') == 'E11.22 - Type 2 diabetes mellitus with chronic kidney disease' ? 'selected' : '' }}>E11.22 - Type 2 diabetes mellitus with chronic kidney disease</option>
                                <option value="E11.40 - Type 2 diabetes mellitus with neuropathy, unspecified" {{ old('diagnosis') == 'E11.40 - Type 2 diabetes mellitus with neuropathy, unspecified' ? 'selected' : '' }}>E11.40 - Type 2 diabetes mellitus with neuropathy, unspecified</option>
                                <option value="E11.8 - Type 2 diabetes mellitus with other specified complications" {{ old('diagnosis') == 'E11.8 - Type 2 diabetes mellitus with other specified complications' ? 'selected' : '' }}>E11.8 - Type 2 diabetes mellitus with other specified complications</option>
                                <option value="E13.9 - Other specified diabetes mellitus without complications" {{ old('diagnosis') == 'E13.9 - Other specified diabetes mellitus without complications' ? 'selected' : '' }}>E13.9 - Other specified diabetes mellitus without complications</option>
                                
                                <!-- Type 1 Diabetes Mellitus -->
                                <option value="E10.9 - Type 1 diabetes mellitus without complications" {{ old('diagnosis') == 'E10.9 - Type 1 diabetes mellitus without complications' ? 'selected' : '' }}>E10.9 - Type 1 diabetes mellitus without complications</option>
                                <option value="E10.65 - Type 1 diabetes mellitus with hyperglycemia" {{ old('diagnosis') == 'E10.65 - Type 1 diabetes mellitus with hyperglycemia' ? 'selected' : '' }}>E10.65 - Type 1 diabetes mellitus with hyperglycemia</option>
                                <option value="E10.649 - Type 1 diabetes mellitus with hypoglycemia without coma" {{ old('diagnosis') == 'E10.649 - Type 1 diabetes mellitus with hypoglycemia without coma' ? 'selected' : '' }}>E10.649 - Type 1 diabetes mellitus with hypoglycemia without coma</option>
                                <option value="E10.21 - Type 1 diabetes mellitus with nephropathy" {{ old('diagnosis') == 'E10.21 - Type 1 diabetes mellitus with nephropathy' ? 'selected' : '' }}>E10.21 - Type 1 diabetes mellitus with nephropathy</option>
                                <option value="E10.22 - Type 1 diabetes mellitus with chronic kidney disease" {{ old('diagnosis') == 'E10.22 - Type 1 diabetes mellitus with chronic kidney disease' ? 'selected' : '' }}>E10.22 - Type 1 diabetes mellitus with chronic kidney disease</option>
                                <option value="E10.40 - Type 1 diabetes mellitus with neuropathy, unspecified" {{ old('diagnosis') == 'E10.40 - Type 1 diabetes mellitus with neuropathy, unspecified' ? 'selected' : '' }}>E10.40 - Type 1 diabetes mellitus with neuropathy, unspecified</option>
                                <option value="E10.42 - Type 1 diabetes mellitus with polyneuropathy" {{ old('diagnosis') == 'E10.42 - Type 1 diabetes mellitus with polyneuropathy' ? 'selected' : '' }}>E10.42 - Type 1 diabetes mellitus with polyneuropathy</option>
                                <option value="E10.51 - Type 1 diabetes mellitus with circulatory complications" {{ old('diagnosis') == 'E10.51 - Type 1 diabetes mellitus with circulatory complications' ? 'selected' : '' }}>E10.51 - Type 1 diabetes mellitus with circulatory complications</option>
                                <option value="E10.59 - Type 1 diabetes mellitus with other circulatory complications" {{ old('diagnosis') == 'E10.59 - Type 1 diabetes mellitus with other circulatory complications' ? 'selected' : '' }}>E10.59 - Type 1 diabetes mellitus with other circulatory complications</option>
                                <option value="E10.610 - Type 1 diabetes mellitus with diabetic neuropathic arthropathy" {{ old('diagnosis') == 'E10.610 - Type 1 diabetes mellitus with diabetic neuropathic arthropathy' ? 'selected' : '' }}>E10.610 - Type 1 diabetes mellitus with diabetic neuropathic arthropathy</option>
                                <option value="E10.618 - Type 1 diabetes mellitus with other musculoskeletal complications" {{ old('diagnosis') == 'E10.618 - Type 1 diabetes mellitus with other musculoskeletal complications' ? 'selected' : '' }}>E10.618 - Type 1 diabetes mellitus with other musculoskeletal complications</option>
                                <option value="E10.621 - Type 1 diabetes mellitus with foot ulcer" {{ old('diagnosis') == 'E10.621 - Type 1 diabetes mellitus with foot ulcer' ? 'selected' : '' }}>E10.621 - Type 1 diabetes mellitus with foot ulcer</option>
                                <option value="E10.69 - Type 1 diabetes mellitus with other specified complications" {{ old('diagnosis') == 'E10.69 - Type 1 diabetes mellitus with other specified complications' ? 'selected' : '' }}>E10.69 - Type 1 diabetes mellitus with other specified complications</option>
                                <option value="E10.8 - Type 1 diabetes mellitus with unspecified complications" {{ old('diagnosis') == 'E10.8 - Type 1 diabetes mellitus with unspecified complications' ? 'selected' : '' }}>E10.8 - Type 1 diabetes mellitus with unspecified complications</option>
                                <option value="E10.11 - Type 1 diabetes mellitus with ketoacidosis with coma" {{ old('diagnosis') == 'E10.11 - Type 1 diabetes mellitus with ketoacidosis with coma' ? 'selected' : '' }}>E10.11 - Type 1 diabetes mellitus with ketoacidosis with coma</option>
                                <option value="E10.10 - Type 1 diabetes mellitus with ketoacidosis without coma" {{ old('diagnosis') == 'E10.10 - Type 1 diabetes mellitus with ketoacidosis without coma' ? 'selected' : '' }}>E10.10 - Type 1 diabetes mellitus with ketoacidosis without coma</option>
                                
                                <!-- Obesity and Weight Management -->
                                <option value="E66.01 - Morbid obesity due to excess calories" {{ old('diagnosis') == 'E66.01 - Morbid obesity due to excess calories' ? 'selected' : '' }}>E66.01 - Morbid obesity due to excess calories</option>
                                <option value="E66.9 - Obesity, unspecified" {{ old('diagnosis') == 'E66.9 - Obesity, unspecified' ? 'selected' : '' }}>E66.9 - Obesity, unspecified</option>
                                <option value="E66.3 - Overweight" {{ old('diagnosis') == 'E66.3 - Overweight' ? 'selected' : '' }}>E66.3 - Overweight</option>
                                <option value="Z68.41 - Body mass index (BMI) 40.0-44.9" {{ old('diagnosis') == 'Z68.41 - Body mass index (BMI) 40.0-44.9' ? 'selected' : '' }}>Z68.41 - Body mass index (BMI) 40.0-44.9</option>
                                <option value="Z68.42 - Body mass index (BMI) 45.0-49.9" {{ old('diagnosis') == 'Z68.42 - Body mass index (BMI) 45.0-49.9' ? 'selected' : '' }}>Z68.42 - Body mass index (BMI) 45.0-49.9</option>
                                <option value="Z68.43 - Body mass index (BMI) 50.0-59.9" {{ old('diagnosis') == 'Z68.43 - Body mass index (BMI) 50.0-59.9' ? 'selected' : '' }}>Z68.43 - Body mass index (BMI) 50.0-59.9</option>
                                <option value="Z68.44 - Body mass index (BMI) 60.0-69.9" {{ old('diagnosis') == 'Z68.44 - Body mass index (BMI) 60.0-69.9' ? 'selected' : '' }}>Z68.44 - Body mass index (BMI) 60.0-69.9</option>
                                <option value="Z68.45 - Body mass index (BMI) 70 or greater" {{ old('diagnosis') == 'Z68.45 - Body mass index (BMI) 70 or greater' ? 'selected' : '' }}>Z68.45 - Body mass index (BMI) 70 or greater</option>
                                <option value="Z71.3 - Dietary counseling and surveillance" {{ old('diagnosis') == 'Z71.3 - Dietary counseling and surveillance' ? 'selected' : '' }}>Z71.3 - Dietary counseling and surveillance</option>
                                
                                <!-- Health Behavior and Psychosocial Factors -->
                                <option value="Z91.14 - Patient's other noncompliance with medication regimen" {{ old('diagnosis') == 'Z91.14 - Patient\'s other noncompliance with medication regimen' ? 'selected' : '' }}>Z91.14 - Patient's other noncompliance with medication regimen</option>
                                <option value="Z91.19 - Patient noncompliance, other specified" {{ old('diagnosis') == 'Z91.19 - Patient noncompliance, other specified' ? 'selected' : '' }}>Z91.19 - Patient noncompliance, other specified</option>
                                <option value="Z63.6 - Dependent relative needing care" {{ old('diagnosis') == 'Z63.6 - Dependent relative needing care' ? 'selected' : '' }}>Z63.6 - Dependent relative needing care</option>
                                <option value="Z73.1 - Type A behavior pattern" {{ old('diagnosis') == 'Z73.1 - Type A behavior pattern' ? 'selected' : '' }}>Z73.1 - Type A behavior pattern</option>
                                <option value="Z73.89 - Other problems related to life management difficulty" {{ old('diagnosis') == 'Z73.89 - Other problems related to life management difficulty' ? 'selected' : '' }}>Z73.89 - Other problems related to life management difficulty</option>
                                <option value="Z71.89 - Other specified counseling" {{ old('diagnosis') == 'Z71.89 - Other specified counseling' ? 'selected' : '' }}>Z71.89 - Other specified counseling</option>
                                <option value="Z60.0 - Social environment problems" {{ old('diagnosis') == 'Z60.0 - Social environment problems' ? 'selected' : '' }}>Z60.0 - Social environment problems</option>
                                <option value="Z72.4 - Inappropriate diet and eating habits" {{ old('diagnosis') == 'Z72.4 - Inappropriate diet and eating habits' ? 'selected' : '' }}>Z72.4 - Inappropriate diet and eating habits</option>
                                <option value="Z71.82 - Exercise counseling" {{ old('diagnosis') == 'Z71.82 - Exercise counseling' ? 'selected' : '' }}>Z71.82 - Exercise counseling</option>
                                
                                <!-- Mental Health Diagnoses (for psychotherapy only) -->
                                <option value="F32.9 - Major depressive disorder, single episode, unspecified" {{ old('diagnosis') == 'F32.9 - Major depressive disorder, single episode, unspecified' ? 'selected' : '' }}>F32.9 - Major depressive disorder, single episode, unspecified</option>
                                <option value="F41.1 - Generalized anxiety disorder" {{ old('diagnosis') == 'F41.1 - Generalized anxiety disorder' ? 'selected' : '' }}>F41.1 - Generalized anxiety disorder</option>
                                <option value="F43.10 - Post-traumatic stress disorder" {{ old('diagnosis') == 'F43.10 - Post-traumatic stress disorder' ? 'selected' : '' }}>F43.10 - Post-traumatic stress disorder</option>
                                <option value="F50.9 - Eating disorder, unspecified" {{ old('diagnosis') == 'F50.9 - Eating disorder, unspecified' ? 'selected' : '' }}>F50.9 - Eating disorder, unspecified</option>
                                <option value="F45.29 - Somatic symptom disorder" {{ old('diagnosis') == 'F45.29 - Somatic symptom disorder' ? 'selected' : '' }}>F45.29 - Somatic symptom disorder</option>
                                
                                <!-- Common Comorbidities -->
                                <option value="I10 - Essential (primary) hypertension" {{ old('diagnosis') == 'I10 - Essential (primary) hypertension' ? 'selected' : '' }}>I10 - Essential (primary) hypertension</option>
                                <option value="E78.5 - Hyperlipidemia, unspecified" {{ old('diagnosis') == 'E78.5 - Hyperlipidemia, unspecified' ? 'selected' : '' }}>E78.5 - Hyperlipidemia, unspecified</option>
                                <option value="N18.4 - Chronic kidney disease, stage 4" {{ old('diagnosis') == 'N18.4 - Chronic kidney disease, stage 4' ? 'selected' : '' }}>N18.4 - Chronic kidney disease, stage 4</option>
                                <option value="N18.9 - Chronic kidney disease, unspecified" {{ old('diagnosis') == 'N18.9 - Chronic kidney disease, unspecified' ? 'selected' : '' }}>N18.9 - Chronic kidney disease, unspecified</option>
                                <option value="G47.33 - Obstructive sleep apnea" {{ old('diagnosis') == 'G47.33 - Obstructive sleep apnea' ? 'selected' : '' }}>G47.33 - Obstructive sleep apnea</option>
                                <option value="E03.9 - Hypothyroidism, unspecified" {{ old('diagnosis') == 'E03.9 - Hypothyroidism, unspecified' ? 'selected' : '' }}>E03.9 - Hypothyroidism, unspecified</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Select an ICD-10 diagnosis code</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="epr_input_group">
                                <label for="start_date">
                                    START DATE <strong class="text-red-500">*</strong>
                                </label>
                                <input type="date" name="start_date" id="start_date" class="epr_input"
                                    value="{{ old('start_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="epr_input_group">
                                <label for="end_date">END DATE <strong class="text-red-500">*</strong></label>
                                <input type="date" name="end_date" id="end_date" class="epr_input"
                                    value="{{ old('end_date') }}" required>
                            </div>
                        </div>

                        <div class="epr_input_group">
                            <label for="comments">COMMENTS</label>
                            <textarea name="comments" id="comments" class="epr_input resize-none" rows="4"
                                placeholder="Enter any additional notes or comments about this medication" maxlength="500">{{ old('comments') }}</textarea>
                            <div class="flex justify-between mt-1">
                                <p class="text-sm text-gray-500">Enter any additional notes or comments</p>
                                <p class="text-sm text-gray-500"><span id="char_count">0</span>/500</p>
                            </div>
                        </div>

                        <div class="border-t border-[#EDECEC] pt-4 epr_input_group">
                            <h3 class="font-semibold text-lg mb-4">Prescription Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="group">
                                    <label for="dispense_quantity">
                                        DISPENSE <strong class="text-red-500">*</strong>
                                    </label>
                                    <input type="number" name="dispense_quantity" id="dispense_quantity"
                                        class="epr_input" required min="1" value="{{ old('dispense_quantity') }}"
                                        placeholder="Enter quantity">
                                </div>

                                <div class="group">
                                    <label for="unit_of_drugs">
                                        UNITS OF DRUGS <strong class="text-red-500">*</strong>
                                    </label>
                                    <select name="unit_of_drugs" id="unit_of_drugs" class="epr_input" required>
                                        <option value="">Select Units</option>
                                        <option value="tablets" {{ old('unit_of_drugs') == 'tablets' ? 'selected' : '' }}>
                                            Tablets</option>
                                        <option value="capsules"
                                            {{ old('unit_of_drugs') == 'capsules' ? 'selected' : '' }}>Capsules</option>
                                        <option value="ml" {{ old('unit_of_drugs') == 'ml' ? 'selected' : '' }}>ml
                                        </option>
                                        <option value="mg" {{ old('unit_of_drugs') == 'mg' ? 'selected' : '' }}>mg
                                        </option>
                                    </select>
                                </div>

                                <div class="group">
                                    <label for="days_supply">
                                        DAYS SUPPLY <strong class="text-red-500">*</strong>
                                    </label>
                                    <input type="number" required name="days_supply" id="days_supply" class="epr_input"
                                        min="1" value="{{ old('days_supply') }}"
                                        placeholder="Enter number of days">
                                </div>
                            </div>
                        </div>

                        <div class="epr_input_group">
                            <label for="provider_name">NAME OF PRESCRIBING PROVIDER</label>
                            <input type="text" name="provider_name" id="provider_name" class="epr_input"
                                value="{{ old('provider_name', Auth::guard('provider')->user()->name) }}"
                                placeholder="Enter provider name">
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button"
                                class="cancel cursor-pointer bg-white border-2 border-[#EDECEC] rounded-lg px-6 py-3"
                                onclick="cancelForm()">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-[#2889AA] py-3 px-6 text-white flex-1 rounded-lg hover:bg-[#1f6a85] transition">
                                <i class="fa-solid fa-save mr-2"></i> Save Prescription
                            </button>
                        </div>
                    </form>
                </div>

                {{-- All Prescriptions --}}
                <div id="all_prescriptions" class="form_options hidden">
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">All Prescriptions</h3>

                        @php
                            $prescriptions = $prescriptions ?? collect();
                        @endphp

                        @if ($prescriptions->count() > 0)
                            @foreach ($prescriptions as $prescription)
                                <div class="prescription-card">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $prescription->medication }}</h4>
                                            <p class="text-sm text-gray-600">{{ $prescription->daily_use }}</p>
                                        </div>
                                        <div>
                                            {!! $prescription->status_badge !!}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Quantity:</span>
                                            <p class="font-medium">{{ $prescription->dispense_quantity }}
                                                {{ $prescription->unit_of_drugs }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Days Supply:</span>
                                            <p class="font-medium">{{ $prescription->days_supply }} days</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Start Date:</span>
                                            <p class="font-medium">{{ $prescription->start_date->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">End Date:</span>
                                            <p class="font-medium">{{ $prescription->end_date->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                    @if ($prescription->comments)
                                        <div class="mt-3 p-3 bg-gray-50 rounded">
                                            <span class="text-gray-500 text-sm">Comments:</span>
                                            <p class="text-sm">{{ $prescription->comments }}</p>
                                        </div>
                                    @endif

                                    @if ($prescription->isDraft())
                                        <div class="mt-4 flex gap-2">
                                            <button onclick="sendToDxScript({{ $prescription->id }})"
                                                class="bg-[#2889AA] text-white px-4 py-2 rounded hover:bg-[#1f6a85]">
                                                <i class="fa-solid fa-paper-plane mr-2"></i> Send via DxScript
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12 bg-white rounded-lg">
                                <i class="fa-solid fa-prescription text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No prescriptions found for this appointment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Send to DxScript --}}
                <div id="send_to_dxscript" class="form_options hidden">
                    <div class="mt-6 bg-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Send Prescription via DxScript</h3>
                        <p class="text-gray-600 mb-4">Click the button below to open DxScript and send the prescription
                            electronically.</p>

                        <button onclick="openDxScript()"
                            class="bg-[#2889AA] text-white px-6 py-3 rounded-lg hover:bg-[#1f6a85]">
                            <i class="fa-solid fa-external-link-alt mr-2"></i> Open DxScript
                        </button>

                        <div id="loading-spinner" class="hidden mt-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="animate-spin h-5 w-5 border-2 border-[#2889AA] border-t-transparent rounded-full">
                                </div>
                                <span>Connecting to DxScript...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function activateSpecPrescription(element) {
            $(".regular_epr").removeClass("active_epr");
            $("." + element).addClass("active_epr");
            $(".form_options").addClass("hidden");
            $("#" + element).removeClass("hidden");
        }

        function cancelForm() {
            $(".regular_epr").removeClass("active_epr");
            $(".all_prescriptions").addClass("active_epr");
            $(".form_options").addClass("hidden");
            $("#all_prescriptions").removeClass("hidden");
            document.getElementById("new_prescription_form_data").reset();
            document.getElementById("char_count").textContent = "0";
        }

        // Character count for comments
        document.addEventListener('DOMContentLoaded', function() {
            const commentsTextarea = document.getElementById('comments');
            const charCount = document.getElementById('char_count');

            if (commentsTextarea) {
                charCount.textContent = commentsTextarea.value.length;
                commentsTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }

            // Set minimum end date to start date
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', function() {
                    endDateInput.min = this.value;
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }
                });
            }
        });

        // DxScript Integration
        function sendToDxScript(prescriptionId) {
            window.location.href = `/provider/send-to-dxscript/${prescriptionId}`;
        }

        async function openDxScript() {
            const loadingSpinner = document.getElementById('loading-spinner');
            loadingSpinner.classList.remove('hidden');

            try {
                const csrfToken = "{{ csrf_token() }}";
                const patientId = "{{ $appointment->patient_id ?? '' }}";

                console.log('Requesting DxScript token for patient:', patientId);

                const response = await fetch('/provider/dxscript/token-with-proxy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        redirect_to: 'RxSelectMed'
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP Error:', response.status, errorText);
                    throw new Error(`Server returned ${response.status}: ${errorText}`);
                }

                const data = await response.json();
                console.log('DxScript Response:', data);

                if (data.success) {
                    const dxWindow = window.open(
                        data.sso_url,
                        'DxScript',
                        'width=1400,height=900,menubar=no,toolbar=no,location=no,status=no'
                    );

                    if (!dxWindow) {
                        alert('Pop-up blocked! Please allow pop-ups for this site and try again.');
                    } else {
                        showNotification('DxScript opened successfully!', 'success');
                    }
                } else {
                    let errorMsg = 'Failed to connect to DxScript:\n\n';
                    errorMsg += data.error || 'Unknown error';

                    if (data.error_message) {
                        errorMsg += '\n' + data.error_message;
                    }

                    if (data.suggestion) {
                        errorMsg += '\n\nSuggestion: ' + data.suggestion;
                    }

                    console.error('DxScript Error Details:', data);
                    alert(errorMsg);
                }
            } catch (error) {
                console.error('DxScript Connection Error:', error);

                let userMessage = 'Failed to connect to DxScript.\n\n';
                userMessage += 'Error: ' + error.message + '\n\n';
                userMessage += 'Please check:\n';
                userMessage += '1. Your internet connection\n';
                userMessage += '2. Server is running and accessible\n';
                userMessage += '3. DxScript credentials are correct\n';

                alert(userMessage);
            } finally {
                loadingSpinner.classList.add('hidden');
            }
        }

        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-50 border-green-200 text-green-700',
                error: 'bg-red-50 border-red-200 text-red-700',
                info: 'bg-blue-50 border-blue-200 text-blue-700'
            };

            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 ${colors[type]} px-6 py-3 rounded-lg border shadow-lg z-50 max-w-md`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.5s';
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }
    </script>
@endsection