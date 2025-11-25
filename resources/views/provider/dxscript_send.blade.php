@extends('layouts.provider')

@section('title', 'Send Prescription - DxScript')

@section('style')
<style>
    .prescription-detail {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #E5E7EB;
    }
    .prescription-detail:last-child {
        border-bottom: none;
    }
    #dxscript-iframe {
        width: 100%;
        height: 800px;
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
    @include('layouts.provider_header')

    <div class="max-w-[1400px] mx-auto py-6 px-4 lg:px-6">
        <div class="mb-6">
            <a href="/provider/view-appointment/{{ $prescription->appointment_uid }}" 
               class="text-[#2889AA] hover:underline">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Appointment
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Prescription Details --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg p-6 shadow">
                    <h2 class="text-xl font-semibold mb-4">Prescription Details</h2>
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Patient:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->patient_name }}</span>
                    </div>
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Medication:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->medication }}</span>
                    </div>
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Instructions:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->daily_use }}</span>
                    </div>
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Quantity:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->dispense_quantity }} {{ $prescription->unit_of_drugs }}</span>
                    </div>
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Days Supply:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->days_supply }} days</span>
                    </div>
                    
                    @if($prescription->diagnosis)
                    <div class="prescription-detail">
                        <span class="text-gray-600">Diagnosis:</span>
                        <span class="font-light text-right max-w-[250px]">{{ $prescription->diagnosis }}</span>
                    </div>
                    @endif
                    
                    <div class="prescription-detail">
                        <span class="text-gray-600">Status:</span>
                        <span>{!! $prescription->status_badge !!}</span>
                    </div>

                    <div class="mt-6">
                        <button onclick="openDxScriptInIframe()" 
                                class="w-full bg-[#2889AA] text-white py-3 rounded-lg hover:bg-[#1f6a85] transition">
                            <i class="fa-solid fa-external-link-alt mr-2"></i> Open in DxScript
                        </button>
                        
                        <button onclick="openDxScriptNewWindow()" 
                                class="w-full mt-3 bg-white border-2 border-[#2889AA] text-[#2889AA] py-3 rounded-lg hover:bg-gray-50 transition">
                            <i class="fa-solid fa-window-restore mr-2"></i> Open in New Window
                        </button>
                    </div>

                    <div id="loading-status" class="mt-4 hidden">
                        <div class="flex items-center gap-3 text-[#2889AA]">
                            <div class="animate-spin h-5 w-5 border-2 border-[#2889AA] border-t-transparent rounded-full"></div>
                            <span>Connecting to DxScript...</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DxScript Iframe --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">DxScript ePrescribing</h2>
                        <button onclick="closeDxScript()" class="text-red-600 hover:text-red-800">
                            <i class="fa-solid fa-times"></i> Close
                        </button>
                    </div>
                    
                    <div id="iframe-container" class="hidden">
                        <iframe id="dxscript-iframe"></iframe>
                    </div>

                    <div id="placeholder" class="text-center py-20">
                        <i class="fa-solid fa-prescription text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Click "Open in DxScript" to send this prescription</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    const prescriptionId = {{ $prescription->id }};
    const patientId = '{{ $prescription->patient_id }}';

    /**
     * Open DxScript in iframe
     */
    async function openDxScriptInIframe() {
        const loadingStatus = document.getElementById('loading-status');
        const iframe = document.getElementById('dxscript-iframe');
        const iframeContainer = document.getElementById('iframe-container');
        const placeholder = document.getElementById('placeholder');

        loadingStatus.classList.remove('hidden');

        try {
            const response = await fetch('/provider/dxscript/get-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                },
                body: JSON.stringify({
                    provider_username: 'suguatprovider',
                    patient_id: patientId,
                })
            });

            const data = await response.json();

            if (data.success) {
                iframe.src = data.sso_url;
                placeholder.classList.add('hidden');
                iframeContainer.classList.remove('hidden');

                // Listen for messages from DxScript
                window.addEventListener('message', handleDxScriptMessage);
            } else {
                alert('Failed to connect to DxScript: ' + data.error);
            }
        } catch (error) {
            console.error('DxScript Error:', error);
            alert('An error occurred while connecting to DxScript.');
        } finally {
            loadingStatus.classList.add('hidden');
        }
    }

    /**
     * Open DxScript in new window
     */
    async function openDxScriptNewWindow() {
        const loadingStatus = document.getElementById('loading-status');
        loadingStatus.classList.remove('hidden');

        try {
            const response = await fetch('/provider/dxscript/get-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    provider_username: 'suguatprovider',
                    patient_id: patientId,
                })
            });

            const data = await response.json();

            if (data.success) {
                const dxWindow = window.open(
                    data.sso_url,
                    'DxScript',
                    'width=1200,height=800,resizable=yes,scrollbars=yes'
                );

                if (!dxWindow) {
                    alert('Please allow popups for this website');
                }
            } else {
                alert('Failed to connect to DxScript: ' + data.error);
            }
        } catch (error) {
            console.error('DxScript Error:', error);
            alert('An error occurred while connecting to DxScript.');
        } finally {
            loadingStatus.classList.add('hidden');
        }
    }

    /**
     * Handle messages from DxScript iframe
     */
    function handleDxScriptMessage(event) {
        // Verify origin
        if (event.origin !== 'https://test2.sigmapoc.com') {
            return;
        }

        const { type, data } = event.data;

        switch (type) {
            case 'prescription_sent':
                updatePrescriptionStatus('sent', data);
                break;
            case 'prescription_cancelled':
                updatePrescriptionStatus('cancelled', data);
                break;
            case 'close_request':
                closeDxScript();
                break;
        }
    }

    /**
     * Update prescription status in database
     */
    async function updatePrescriptionStatus(status, data = {}) {
        try {
            const response = await fetch('/provider/dxscript/prescription-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    prescription_id: prescriptionId,
                    status: status,
                    dxscript_prescription_id: data.prescription_id || null,
                    pharmacy_name: data.pharmacy_name || null,
                    pharmacy_ncpdp: data.pharmacy_ncpdp || null,
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Prescription ' + status + ' successfully!');
                window.location.reload();
            }
        } catch (error) {
            console.error('Error updating prescription status:', error);
        }
    }

    /**
     * Close DxScript iframe
     */
    function closeDxScript() {
        const iframe = document.getElementById('dxscript-iframe');
        const iframeContainer = document.getElementById('iframe-container');
        const placeholder = document.getElementById('placeholder');

        iframe.src = '';
        iframeContainer.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
</script>
@endsection