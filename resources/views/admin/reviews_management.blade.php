@extends('layouts.admin_app')

@section('title', 'Manage FAQs')

@section('link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('styles')
    <style>
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.25rem 1.75rem 0.25rem 0.75rem;
        }

        .faq-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .faq-table th {
            background-color: #f3f4f6;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
        }

        .faq-table td {
            padding: 1rem;
            background-color: white;
            border: 1px solid #e5e7eb;
        }

        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .btn-submit {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #2563eb;
        }

        .text-input,
        .textarea-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: border-color 0.2s;
        }

        .text-input:focus,
        .textarea-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .textarea-input {
            min-height: 60px;
            resize: vertical;
        }

        .new-faq-row {
            background-color: #f0f9ff;
        }

        .new-faq-row td {
            border: 1px dashed #93c5fd;
        }

        #faqsTable_paginate {
            margin-bottom: 1rem;
        }

        .paginate_button {
            background: #3b82f6;
            color: #fff;
            border-radius: 5px;
            padding: 5px 15px;
            text-align: center;
            margin: 3px;
            cursor: pointer;
        }

        #faqsTable_paginate .current {
            background: transparent;
            border: 1px solid #3b82f6;
            color: #333;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Frequently Asked Questions</h1>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="faqsTable" class="faq-table stripe hover">
                            <thead>
                                <tr>
                                    <th class="w-16">#</th>
                                    <th>Review By</th>
                                    <th>Star</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th class="w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviews as $key => $item)
                                    <tr class="transition hover:shadow-lg hover:bg-blue-50">
                                        <td class="font-semibold text-gray-700">{{ $key + 1 }}</td>
                                        <td class="text-gray-800">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium">
                                                    {{ Str::limit($item->reviewed_by, 25) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $item->review_star)
                                                        <i class="fas fa-star text-yellow-400"></i>
                                                    @else
                                                        <i class="far fa-star text-gray-300"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="text-gray-600">
                                            <span class="min_review cursor-pointer" onclick="toggleReview(this)">
                                                {{ Str::limit($item->main_review, 60) }}
                                            </span>
                                            <span class="full_review cursor-pointer hidden" onclick="toggleReview(this)">
                                                {{ $item->main_review }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <center>
                                                    <span
                                                        class="inline-block text-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Showing</span>
                                                </center>
                                            @else
                                                <span
                                                    class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold block text-center w-[100px]">Not
                                                    Showing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex gap-2 justify-center items-center">
                                                <a href="/admin/update-review/{{ $item->id }}/show"
                                                    class="action-btn px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-semibold transition"
                                                    title="Show">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/update-review/{{ $item->id }}/hide"
                                                    class="action-btn px-3 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200 text-xs font-semibold transition"
                                                    title="Hide">
                                                    <i class="fas fa-eye-slash"></i>
                                                </a>
                                                <a href="/admin/delete-review/{{ $item->id }}"
                                                    class="action-btn px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs font-semibold transition"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you totally want to remove this Review?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-gray-500">No FAQs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#faqsTable').DataTable({
                responsive: true,
                searching: true,
                ordering: true,
                paging: true,
                info: true,
                columnDefs: [{
                    orderable: false,
                    targets: [5]
                }],
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
            });

            $(".faqs").addClass("font-semibold text-blue-600");
        });



        function toggleReview(passedThis){
            $(passedThis).parent().children('.min_review').toggleClass('hidden');
            $(passedThis).parent().children('.full_review').toggleClass('hidden');
        }
    </script>
@endsection
