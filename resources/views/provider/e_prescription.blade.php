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
                <div class="mb-4 text-center lg:text-left">
                    <h1 class="text-xl font-semibold text-[#000000]">
                        E-Prescription
                    </h1>
                    <p class="text-gray-600">
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
                            <input type="text" name="diagnosis" id="diagnosis" class="epr_input"
                                value="{{ old('diagnosis') }}" placeholder="e.g., Upper Respiratory Infection">
                            <p class="text-sm text-gray-500 mt-1">Enter ICD-10 code or diagnosis description</p>
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

        /**
         * Open DxScript using the PROXY method to avoid geo-restrictions
         * This method routes the SSO connection through your server
         */
        async function openDxScript() {
            const loadingSpinner = document.getElementById('loading-spinner');
            loadingSpinner.classList.remove('hidden');

            try {
                const csrfToken = "{{ csrf_token() }}";
                const patientId = "{{ $appointment->patient_id ?? '' }}";

                console.log('Requesting DxScript token for patient:', patientId);

                // IMPORTANT: Use the correct route - changed from /get-token to /token-with-proxy
                const response = await fetch('/provider/dxscript/token-with-proxy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        redirect_to: 'RxSelectMed' // Opens directly to Rx Writer page
                    })
                });

                // Check if response is OK
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP Error:', response.status, errorText);
                    throw new Error(`Server returned ${response.status}: ${errorText}`);
                }

                const data = await response.json();
                console.log('DxScript Response:', data);

                if (data.success) {
                    // Open the PROXIED SSO URL (routes through your server)
                    // This avoids the ERR_TUNNEL_CONNECTION_FAILED error
                    const dxWindow = window.open(
                        data.sso_url,
                        'DxScript',
                        'width=1400,height=900,menubar=no,toolbar=no,location=no,status=no'
                    );

                    if (!dxWindow) {
                        alert('Pop-up blocked! Please allow pop-ups for this site and try again.');
                    } else {
                        // Show success message
                        showNotification('DxScript opened successfully!', 'success');
                    }
                } else {
                    // Show detailed error
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

        /**
         * Show notification (optional - for better UX)
         */
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

        /**
         * Alternative: Open DxScript in iframe (if you prefer embedded view)
         */
        async function openDxScriptInIframe() {
            const loadingSpinner = document.getElementById('loading-spinner');
            loadingSpinner.classList.remove('hidden');

            try {
                const csrfToken = "{{ csrf_token() }}";
                const patientId = "{{ $appointment->patient_id ?? '' }}";

                const response = await fetch('/provider/dxscript/token-with-proxy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        redirect_to: 'RxSelectMed'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Create fullscreen modal with iframe
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
                    modal.innerHTML = `
                        <div class="bg-white rounded-lg w-[95%] h-[95%] flex flex-col">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-lg font-semibold">DxScript E-Prescribing</h3>
                                <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                                    <i class="fa-solid fa-times text-xl"></i>
                                </button>
                            </div>
                            <iframe src="${data.sso_url}" class="flex-1 w-full" frameborder="0"></iframe>
                        </div>
                    `;
                    document.body.appendChild(modal);
                } else {
                    alert('Failed to open DxScript: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Network error: ' + error.message);
            } finally {
                loadingSpinner.classList.add('hidden');
            }
        }

        /**
         * Test DxScript connection (for debugging)
         */
        async function testDxScriptConnection() {
            try {
                const response = await fetch('/provider/test-dxscript');
                const data = await response.json();
                console.log('DxScript Test Results:', data);

                // Show results in a modal or alert
                if (data.method_1_correct_params?.success) {
                    alert('✅ DxScript connection successful!');
                } else {
                    alert('❌ DxScript connection failed. Check console for details.');
                }
            } catch (error) {
                console.error('Test failed:', error);
                alert('Test failed: ' + error.message);
            }
        }
    </script>
@endsection
