@extends('layouts.admin_app')

@section('title', 'Admin - Add New Blog')

@section('link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('styles')
    <style>
        .blog-form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .image-upload-container {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s;
            margin-bottom: 1rem;
        }

        .image-upload-container:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.5rem;
            margin-top: 1rem;
            display: none;
        }

        .content-item {
            background-color: #f9fafb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            position: relative;
        }

        .content-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .content-item-title {
            flex-grow: 1;
            margin-right: 1rem;
        }

        .delete-btn {
            background-color: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 0.375rem;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .delete-btn:hover {
            background-color: #fecaca;
        }

        .add-btn {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: none;
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 1rem;
            margin-right: 1rem;
        }

        .add-btn:hover {
            background-color: #bfdbfe;
        }

        .add-btn i {
            margin-right: 0.5rem;
        }

        .submit-btn {
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }

        .grid-cols-2 {
            gap: 1rem;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
@endsection

@section('content')
    <main class="px-4 py-8 mx-auto max-w-7xl">
        <div class="flex flex-col space-y-6">
            <div class="blog-form-container">
                <h1 class="text-3xl text-center font-semibold uppercase text-gray-700">Add New Blog</h1>
                <form action="/admin/add-new-blog" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Thumbnail Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Thumbnail Image</h2>
                        <div class="image-upload-container" onclick="document.getElementById('blogImage').click()">
                            <input type="file" id="blogImage" name="image" class="hidden" accept="image/*" required>
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to upload thumbnail image</p>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 15MB</p>
                            </div>
                            <img id="blogImagePreview" class="image-preview" alt="Thumbnail preview">
                        </div>
                    </div>

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="form-label">Blog Title</label>
                                <input type="text" id="title" name="title" class="form-input" placeholder="Enter blog title" required>
                            </div>

                            <div>
                                <label for="category" class="form-label">Category</label>
                                <select id="category" name="category" class="form-input" required>
                                    <option value="" disabled selected>Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="short_details" class="form-label">Short Description</label>
                                <textarea id="short_details" name="short_details" class="form-input form-textarea" placeholder="Write a short description..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Table of Contents Section -->
                    <div class="form-section">
                        <h2 class="form-section-title">Table of Contents</h2>
                        <div id="allContents" class="space-y-4">
                            <!-- Initial Content Item -->
                            <div class="content-item" data-index="0">
                                <div class="content-item-header">
                                    <input type="text" name="contentTitle[]" class="form-input content-item-title" placeholder="Content Title" required>
                                    <button type="button" class="delete-btn" onclick="deleteItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Content Image</label>
                                        <div class="image-upload-container" onclick="document.getElementById('contentImage0').click()">
                                            <input type="file" id="contentImage0" name="contentImage[]" class="hidden" accept="image/*" required>
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <p class="mt-1 text-xs text-gray-600">Click to upload content image</p>
                                            </div>
                                            <img id="contentImage0Preview" class="image-preview" alt="Content image preview">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Content Details</label>
                                        <textarea name="contentDetails[]" class="form-input form-textarea" placeholder="Write content details..." required></textarea>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        <button type="button" class="add-btn" onclick="addItem()">
                            <i class="fas fa-plus"></i> Add Content Section
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="action-buttons">
                        <button type="submit" class="submit-btn">
                            Publish Blog
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable if you have a blogs listing table
            $('#blogsTable').DataTable({
                responsive: true,
                searching: true,
                ordering: true,
                paging: true,
                info: true,
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>'
            });
        });

        // Image preview functionality
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Initialize image previews for existing elements
        setupImagePreview('blogImage', 'blogImagePreview');
        setupImagePreview('contentImage0', 'contentImage0Preview');

        // Add new content item
        let contentIndex = 1;
        function addItem() {
            const newItem = `
                <div class="content-item" data-index="${contentIndex}">
                    <div class="content-item-header">
                        <input type="text" name="contentTitle[]" class="form-input content-item-title" placeholder="Content Title" required>
                        <button type="button" class="delete-btn" onclick="deleteItem(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label">Content Image</label>
                            <div class="image-upload-container" onclick="document.getElementById('contentImage${contentIndex}').click()">
                                <input type="file" id="contentImage${contentIndex}" name="contentImage[]" class="hidden" accept="image/*" required>
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-1 text-xs text-gray-600">Click to upload content image</p>
                                </div>
                                <img id="contentImage${contentIndex}Preview" class="image-preview" alt="Content image preview">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Content Details</label>
                            <textarea name="contentDetails[]" class="form-input form-textarea" placeholder="Write content details..." required></textarea>
                        </div>
                    </div>
                </div>`;
            
            $('#allContents').append(newItem);
            
            // Setup image preview for the new item
            setupImagePreview(`contentImage${contentIndex}`, `contentImage${contentIndex}Preview`);
            
            contentIndex++;
        }

        // Delete content item
        function deleteItem(button) {
            $(button).closest('.content-item').remove();
        }
    </script>
@endsection