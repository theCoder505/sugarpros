@extends('layouts.provider')

@section('title', 'Patient Claims Biller Responses')

@section('style')
    <style>
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .medicare-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .medicare-completed {
            background-color: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .medicare-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .claim-card {
            transition: all 0.2s ease;
            border-left: 4px solid;
        }

        .claim-card.accepted {
            border-left-color: #10b981;
        }

        .claim-card.rejected {
            border-left-color: #ef4444;
        }

        .claim-card.warning {
            border-left-color: #f59e0b;
        }

        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .spinner {
            display: none;
            width: 40px;
            height: 40px;
            margin: 0 auto;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: #2d92b3;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .status-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    @include('layouts.provider_header')


    <div class="container px-6 lg:px-0 py-8 max-w-7xl mx-auto">
        <div class="lg:flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Patient Claims Biller Responses</h1>
            <div class="flex space-x-4">
                <button onclick="fetchList()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full lg:w-auto mt-2 lg:mt-0">
                    Refresh List
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="spinner"></div>

            <div id="claimsList" class="space-y-4"></div>
        </div>
    </div>

    <!-- Modal for Claim Details -->
    <div id="claimModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold" id="modalTitle">Claim Details</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="space-y-4"></div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fetch claims list
        function fetchList() {
            $('#claimsList').html('');
            $('.spinner').show();

            $.ajax({
                url: '/provider/claim-md/get-claims',
                type: 'GET',
                success: function(response) {
                    $('.spinner').hide();
                    if (response.success && response.data.length) {
                        response.data.forEach(claim => {
                            $('#claimsList').append(formatClaimCard(claim));
                        });
                    } else {
                        $('#claimsList').html(`
                            <div class="text-center py-8 text-gray-500">
                                No claims found in the system
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    $('.spinner').hide();
                    $('#claimsList').html(`
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                            Error loading claims: ${xhr.responseJSON?.message || xhr.statusText}
                        </div>
                    `);
                }
            });
        }

        // Get claim status text
        function getClaimStatusText(statusCode) {
            const statusMap = {
                'A': 'Accepted',
                'R': 'Rejected',
                'W': 'Warning'
            };
            return statusMap[statusCode] || statusCode;
        }

        // Format claim card
        function formatClaimCard(claim) {
            const statusClass = {
                'A': 'status-accepted',
                'R': 'status-rejected',
                'W': 'status-warning'
            } [claim.claim_status] || 'status-rejected';

            const statusText = getClaimStatusText(claim.claim_status);

            const cardClass = {
                'A': 'accepted',
                'R': 'rejected',
                'W': 'warning'
            } [claim.claim_status] || 'rejected';

            // Medicare status badge
            const medicareClass = claim.medicare_status == 'completed' ? 'medicare-completed' : 'medicare-pending';
            const medicareText = claim.medicare_status == 'completed' ? 'Completed' : 'Pending';
            const medicareIcon = claim.medicare_status == 'completed' ?
                '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' :
                '<svg class="w-3 h-3 animate-spin" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path></svg>';

            // Parse response data
            const claimDetails = claim.claim_response.claim ? claim.claim_response.claim[0] : {};
            const messages = claimDetails.messages || [];

            // Format date
            const formattedDate = new Date(claim.created_at).toLocaleString();

            // Action buttons
            let actionButtons = '';
            if (claim.claim_status == 'A') {
                actionButtons = `
                    <a href="/provider/mark-appointment-proceed/${claim.appointment_uid}" 
                       class="action-btn bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Proceed Appointment
                    </a>
                `;
            }

            actionButtons += `
                <button onclick="viewClaimDetails('${claim.id}')" 
                   class="action-btn bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    View Details
                </button>
                <button onclick="deleteClaim('${claim.id}')" 
                   class="action-btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    Delete Claim
                </button>
            `;

            return `
                <div class="claim-card ${cardClass} bg-white rounded-lg shadow-sm p-5">
                    <div class="lg:flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg">${claim.patient_name} - ${claim.appointment_uid}</h3>
                            <div class="status-row mt-2">
                                <span class="${statusClass} status-badge">${statusText}</span>
                                <span class="${medicareClass} medicare-badge">
                                    ${medicareIcon}
                                    Appointment Process: ${medicareText}
                                </span>
                                <span class="text-gray-600 text-sm">${formattedDate}</span>
                            </div>
                            ${messages.length ? `
                                                <div class="mt-3">
                                                    <p class="text-sm font-medium">Primary Message:</p>
                                                    <p class="text-sm text-gray-700">${messages[0].message || 'No message'}</p>
                                                </div>
                                            ` : ''}
                        </div>
                        <div class="grid grid-cols-2 mt-4 gap-2 lg:mt-0 lg:gap-0 lg:flex lg:space-x-2 lg:ml-4">
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
        }

        // View claim details
        function viewClaimDetails(claimId) {
            $('#modalContent').html('<p>Loading details...</p>');
            $('#claimModal').removeClass('hidden');

            $.ajax({
                url: `/provider/claim-md/get-claim/${claimId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const claim = response.data;
                        const claimDetails = claim.claim_response.claim ? claim.claim_response.claim[0] : {};

                        let detailsHtml = `
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-semibold">Appointment UID:</h4>
                                    <p>${claim.appointment_uid}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Patient:</h4>
                                    <p>${claim.patient_info.name} (ID: ${claim.patient_info.patient_id})</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Claim Status:</h4>
                                    <p>${getClaimStatusText(claim.claim_status)}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Appointment Process:</h4>
                                    <p>${claim.medicare_status == 'completed' ? 'Completed' : 'Pending'}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold">ClaimMD ID:</h4>
                                    <p>${claim.claimmd_id || 'N/A'}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Submitted At:</h4>
                                    <p>${new Date(claim.created_at).toLocaleString()}</p>
                                </div>
                        `;

                        if (claimDetails.messages && claimDetails.messages.length) {
                            detailsHtml += `
                                <div>
                                    <h4 class="font-semibold">Messages:</h4>
                                    <ul class="list-disc pl-5 space-y-1">`;

                            claimDetails.messages.forEach(msg => {
                                detailsHtml += `
                                    <li class="${msg.status == 'R' ? 'text-red-600' : msg.status == 'W' ? 'text-yellow-600' : 'text-green-600'}">
                                        <strong>${msg.mesgid || 'Message'}:</strong> ${msg.message || 'No message'}
                                    </li>`;
                            });

                            detailsHtml += `</ul></div>`;
                        }

                        $('#modalContent').html(detailsHtml);
                        $('#modalTitle').text(`Claim Details: ${claim.appointment_uid}`);
                    } else {
                        $('#modalContent').html(`<p class="text-red-500">Error loading claim details</p>`);
                    }
                },
                error: function() {
                    $('#modalContent').html(`<p class="text-red-500">Error loading claim details</p>`);
                }
            });
        }

        // Delete claim
        function deleteClaim(claimId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will also delete it from ClaimMD. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/provider/claim-md/delete-claim/${claimId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Claim has been deleted.',
                                    'success'
                                );
                                fetchList();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message || 'Failed to delete claim',
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseJSON?.message || xhr.statusText,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Close modal
        function closeModal() {
            $('#claimModal').addClass('hidden');
        }

        // Initial load
        $(document).ready(function() {
            fetchList();
        });


        $(".claims").addClass('font-semibold');
    </script>
@endsection
