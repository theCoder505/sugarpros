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
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th class="w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allFaqs as $key => $item)
                                    <tr>
                                        <form action="/admin/update-faq" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <textarea name="question" class="textarea-input" required>{{ $item->question }}</textarea>
                                            </td>
                                            <td>
                                                <textarea name="answer" class="textarea-input" required>{{ $item->answer }}</textarea>
                                            </td>
                                            <td>
                                                <div class="flex gap-4 h-20 justify-center items-center w-full">
                                                    <button type="submit"
                                                        class="action-btn text-blue-500 hover:text-blue-700" title="Update">
                                                        <i class="fas text-2xl fa-save"></i>
                                                    </button>
                                                    <a href="/admin/delete-faq/{{ $item->id }}"
                                                        class="action-btn text-red-500 hover:text-red-700 block"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this FAQ?')">
                                                        <i class="fas text-2xl fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">No FAQs found</td>
                                    </tr>
                                @endforelse
                                <tr class="new-faq-row">
                                    <form action="/admin/add-new-faq" method="post">
                                        @csrf
                                        <td>#</td>
                                        <td>
                                            <textarea name="question" class="textarea-input" placeholder="Enter new question" required></textarea>
                                        </td>
                                        <td>
                                            <textarea name="answer" class="textarea-input" placeholder="Enter the answer" required></textarea>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn-submit flex items-center space-x-1 mx-auto">
                                                <i class="fas fa-plus mr-1"></i>
                                                <span>Add</span>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
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
                        targets: [3]
                    } // Disable sorting for actions column
                ],
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
            });

            $(".faqs").addClass("font-semibold text-blue-600");
        });
    </script>
@endsection
