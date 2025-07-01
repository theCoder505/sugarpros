@extends('layouts.admin_app')

@section('title', 'Manage FAQs')

@section('link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('styles')
    <style>
        .category-table {
            width: 100%;
            border-collapse: collapse;
        }

        .category-table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f8fafc;
            font-weight: 600;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .category-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .input-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #f8fafc;
            transition: border-color 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: white;
        }

        .select-field {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #f8fafc;
            color: #334155;
        }

        .textarea-input {
            width: 100%;
            min-height: 60px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #f8fafc;
            resize: vertical;
            transition: border-color 0.2s;
        }

        .textarea-input:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: white;
        }

        .btn-submit {
            padding: 8px 16px;
            background-color: #3b82f6;
            color: white;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #2563eb;
        }

        .action-btn {
            transition: color 0.2s;
        }

        .new-category-row {
            background-color: #f0fdf4;
        }

        .new-category-row:hover {
            background-color: #dcfce7;
        }
    </style>
@endsection

@section('content')
    <main class="mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Categories Management</h1>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="categoriesTable" class="category-table stripe hover">
                            <thead>
                                <tr>
                                    <th class="w-16">#</th>
                                    <th>Thumbnail</th>
                                    <th>Category title</th>
                                    <th class="w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody>



                                @forelse ($categories as $key => $category)
                                    <tr class="transition hover:shadow-lg hover:bg-blue-50 odd">
                                        <form action="/admin/update-category" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <td class="font-semibold text-gray-700 h-[140px] flex items-center text-lg">{{ $key + 1 }}</td>
                                            <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                            <td class="w-[200px]">
                                                <div>
                                                    <label for="previewImage{{ $category->id }}"
                                                        class="block text-sm font-medium text-gray-700 mb-2 cursor-pointer">
                                                        <img src="/{{ $category->image }}" alt="image preview"
                                                            class="max-w-[200px] h-[100px]">
                                                    </label>
                                                    <input type="file" class="hidden"
                                                        id="previewImage{{ $category->id }}" name="image" accept="image/*"
                                                        onchange="showImage(this)">
                                                </div>
                                            </td>
                                            <td class="text-gray-800">
                                                <div class="flex h-[100px] items-center">
                                                    <div class="flex items-center gap-2 h-full w-full">
                                                        <input type="text" class="border rounded px-4 py-2 w-full"
                                                            required value="{{ $category->category }}"
                                                            placeholder="Category Name" name="category">
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="flex gap-4 h-[100px] justify-center items-center w-full">
                                                    <button type="submit"
                                                        class="action-btn text-blue-500 hover:text-blue-700" title="Update">
                                                        <i class="fas text-2xl fa-save"></i>
                                                    </button>
                                                    <a href="/admin/delete-category/{{ $category->id }}"
                                                        class="action-btn text-red-500 hover:text-red-700 block"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="fas text-2xl fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <h3 class="text-center text-gray-400 font-semibold text-lg">No Categories Yet!
                                            </h3>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <section class="my-20 max-w-xl mx-auto bg-white rounded-xl shadow-md p-8">
            <h1 class="text-2xl font-semibold uppercase text-gray-600 text-center mb-4">Add New Category</h1>
            <form action="/admin/add-new-category" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label for="previewImage" class="block text-sm font-medium text-gray-700 mb-2 cursor-pointer">
                        <img src="/assets/image/uploadimg.png" alt="image preview" class="w-auto h-52 mx-auto">
                    </label>
                    <input type="file" class="hidden" id="previewImage" name="image" accept="image/*"
                        onchange="showImage(this)" required>
                </div>

                <div>
                    <label for="category" class="block text-md font-semibold text-gray-700 mb-2">Category Name</label>
                    <input type="text" placeholder="Enter category name" class="textarea-input" required name="category">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-submit flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </button>
                </div>
            </form>
        </section>

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


        function showImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).parent().children('label').children('img').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
