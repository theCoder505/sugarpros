@extends('layouts.provider')

@section('title', 'Upload Claims')


@section('style')
    <style>
        .claims{
            font-weight: 500;
            color: #000000;
        }

        .claim-card {
            transition: all 0.2s ease;
        }

        .claim-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
        }

        .spinner {
            display: none;
            margin: 20px auto;
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }



        .modal-content {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Animation for modal */
        .modal-enter {
            opacity: 0;
            transform: scale(0.9);
        }

        .modal-enter-active {
            opacity: 1;
            transform: translateX(0);
            transition: opacity 300ms, transform 300ms;
        }

        .modal-exit {
            opacity: 1;
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 300ms, transform 300ms;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection


@section('content')
    @include('layouts.provider_header')


    <div class="container py-8 max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Upload Claim Files</h1>

            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="claim_file">
                        Claim File (EDI 837)
                    </label>
                    <input type="file" name="claim_file" id="claim_file" class="border rounded p-2 w-full" required
                        accept=".txt,.837">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="file_name">
                        File Name
                    </label>
                    <input type="text" name="file_name" id="file_name" class="border rounded p-2 w-full" required
                        placeholder="e.g., claims_20230801.txt">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 uploadBtn">
                    Upload File
                </button>
            </form>

            <div id="uploadResult" class="mt-6 hidden">
                <h2 class="text-xl font-semibold mb-2">Upload Result</h2>
                <pre id="resultContent" class="bg-gray-100 p-4 rounded overflow-auto"></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h1 class="text-2xl font-bold mb-6">Upload History</h1>
            <button id="fetchUploads" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                onclick="fetchList(this)">
                Latest Upload List
            </button>

            <div class="spinner"></div>

            <div id="uploadList" class="mt-4 hidden">
                <pre id="listContent" class="bg-gray-100 px-4 rounded overflow-auto"></pre>
            </div>
        </div>
    </div>


@endsection


@section('script')
    <script>
        // File Upload Form
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            $(".uploadBtn").html('Processing...');

            let formData = new FormData(this);
            $('#uploadResult').addClass('hidden');

            $.ajax({
                url: '/provider/claim-md/upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $(".uploadBtn").html('Upload File');
                    if (response.success) {
                        if (response.data && response.data.error && response.data.error
                            .error_mesg) {
                            console.log(response.data.error.error_mesg);
                            toastr.error('Processing Aborted! Duplicate File Found.');
                        } else {
                            toastr.success('Claim MD Added Successfully!');
                            fetchList();
                        }
                    } else {
                        toastr.error('Error! Try again Later!');
                        console.log(response.error);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error! Try again Later!');
                    console.log(response.error);
                }
            });
        });

        // Fetch Upload List
        function fetchList() {

            $('#uploadList').addClass('hidden');
            $('.spinner').show();

            $.ajax({
                url: '/provider/claim-md/uploadlist',
                type: 'POST',
                data: {
                    AccountKey: '{{ $CLAIM_MD_API_KEY }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('.spinner').hide();
                    if (response.success) {
                        $('#listContent').html(formatUploadListResponse(response.data));
                    } else {
                        $('#listContent').html(`
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                            ${response.error}
                        </div>
                    `);
                    }
                    $('#uploadList').removeClass('hidden');
                },
                error: function(xhr) {
                    $('#listContent').html(`
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        Server Error: ${xhr.statusText}
                    </div>
                `);
                    $('#uploadList').removeClass('hidden');
                }
            });
        }



        fetchList();

        // Format Upload Response
        function formatUploadResponse(data) {
            if (!data) return '<div class="text-gray-500">No data received</div>';

            let html = '';

            // Handle both XML-converted and native JSON responses
            const result = data.result || data;
            const claims = result.claim || [];
            const messages = result.messages || [];

            // Summary Card
            html += `
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800">
                            ${result.messages || 'Claims processed successfully'}
                        </h3>
                        <p class="text-blue-600 mt-1">
                            ${Array.isArray(claims) ? claims.length : 1} claim(s) processed
                        </p>
                    </div>`;

            // Claims List
            if (Array.isArray(claims)) {
                claims.forEach(claim => {
                    html += formatClaimCard(claim);
                });
            } else if (claims) {
                html += formatClaimCard(claims);
            }

            return html;
        }

        function formatClaimCard(claim) {
            const statusClass = {
                'A': 'bg-green-100 text-green-800',
                'R': 'bg-red-100 text-red-800',
                'W': 'bg-yellow-100 text-yellow-800',
                'P': 'bg-blue-100 text-blue-800'
            } [claim.status] || 'bg-gray-100 text-gray-800';

            const statusText = {
                'A': 'Accepted',
                'R': 'Rejected',
                'W': 'Warning',
                'P': 'Pending'
            } [claim.status] || claim.status;

            let messagesHtml = '';
            if (claim.messages) {
                const messages = Array.isArray(claim.messages) ? claim.messages : [claim.messages];
                messagesHtml = `
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h4 class="font-medium text-gray-700 mb-2">Messages:</h4>
                            <ul class="space-y-1">` +
                    messages.map(msg => `
                                <li class="text-sm ${msg.status === 'W' ? 'text-yellow-600' : 'text-red-600'}">
                                    <span class="font-medium">${msg.mesgid || 'Message'}:</span>
                                    ${msg.message || msg}
                                </li>
                            `).join('') +
                    `</ul>
                        </div>`;
            }

            return `
                    <div class="border rounded-lg overflow-hidden mb-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center p-4 ${statusClass}">
                            <div>
                                <span class="font-bold">Claim ID:</span> ${claim.claimid || 'N/A'}
                                <span class="ml-4"><span class="font-bold">Status:</span> ${statusText}</span>
                            </div>
                            <div>
                                <span class="font-bold">Amount:</span> $${claim.total_charge || '0.00'}
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div><span class="font-bold">Batch ID:</span> ${claim.batchid || 'N/A'}</div>
                                <div><span class="font-bold">DOS:</span> ${claim.fdos || 'N/A'}</div>
                                <div><span class="font-bold">Payer ID:</span> ${claim.payerid || 'N/A'}</div>
                                <div><span class="font-bold">PCN:</span> ${claim.pcn || 'N/A'}</div>
                            </div>
                            ${messagesHtml}
                        </div>
                    </div>`;
        }



        function formatUploadListResponse(data) {
            if (!data) return '<div class="text-gray-500">No data received</div>';

            const files = data.file || [];

            if (!files.length) {
                return '<div class="text-gray-500">No upload history found</div>';
            }

            return `
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Inbound ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">` +
                (Array.isArray(files) ? files : [files]).map(file => {
                    const statusClass = {
                        'P': 'bg-blue-100 text-blue-800',
                        'C': 'bg-green-100 text-green-800',
                        'E': 'bg-red-100 text-red-800'
                    } [file.status] || 'bg-gray-100 text-gray-800';

                    const statusText = {
                        'P': 'Pending',
                        'C': 'Completed',
                        'E': 'Error'
                    } [file.status] || file.status;

                    return `
                                <tr class="hover:bg-gray-50" id="file-row-${file.inboundid}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${file.inboundid || 'N/A'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${file.filename || 'N/A'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${file.file_count || 'N/A'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${file.file_amount || 'N/A'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${file.uploadtime ? new Date(file.uploadtime * 1000).toLocaleString() : 'N/A'}
                                    </td>
                                </tr>`;
                }).join('') +
                `</tbody>
                            </table>
                        </div>`;
        }





        function deleteFile(fileId) {
            if (!confirm('Are you sure you want to delete this file?')) return;

            // Show loading indicator
            const $row = $(`#file-row-${fileId}`);
            $row.css('opacity', '0.5');
            $row.find('button').prop('disabled', true);

            $.ajax({
                url: '/provider/claim-md/deletefile',
                type: 'POST',
                data: {
                    file_id: fileId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'File deleted successfully');
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.error || 'Failed to delete file');
                        $row.css('opacity', '1');
                        $row.find('button').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error: ' + (xhr.responseJSON?.error || xhr.statusText));
                    $row.css('opacity', '1');
                    $row.find('button').prop('disabled', false);
                }
            });
        }











        function viewFile(fileId, filename) {
            toastr.info('Loading file content...');

            $.ajax({
                url: '/provider/claim-md/viewfile',
                type: 'POST',
                data: {
                    file_id: fileId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const modalHtml = `
                                            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                                                    <div class="flex justify-between items-center border-b pb-2">
                                                        <h3 class="text-lg font-semibold">${filename}</h3>
                                                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="mt-4 max-h-[70vh] overflow-auto">
                                                        <pre class="bg-gray-100 p-4 rounded text-sm">${response.content}</pre>
                                                    </div>
                                                </div>
                                            </div>`;

                        $('body').append(modalHtml);
                    } else {
                        toastr.error(response.error || 'Failed to load file');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading file: ' + xhr.statusText);
                }
            });
        }

        function closeModal() {
            $('.fixed.inset-0').remove();
        }

        function formatUploadListResponse(data) {
            if (!data) return '<div class="text-gray-500">No data received</div>';

            const files = data.file || [];

            if (!files.length) {
                return '<div class="text-gray-500">No upload history found</div>';
            }

            return `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Inbound ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">` +
                (Array.isArray(files) ? files : [files]).map(file => {
                    return `
                            <tr class="hover:bg-gray-50" id="file-row-${file.inboundid}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${file.inboundid || 'N/A'}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">${file.filename || 'N/A'}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">${file.file_count || 'N/A'}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">${file.file_amount || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${file.uploadtime ? new Date(file.uploadtime * 1000).toLocaleString() : 'N/A'}
                                </td>
                            </tr>`;
                }).join('') +
                `</tbody>
                        </table>
                    </div>`;
        }



        function downloadFile(fileId, filename) {
            toastr.info('Preparing download...');

            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = `/provider/claim-md/downloadfile?file_id=${fileId}&filename=${encodeURIComponent(filename)}`;
            document.body.appendChild(iframe);

            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 5000);
        }
    </script>
@endsection
