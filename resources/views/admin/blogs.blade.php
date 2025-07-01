@extends('layouts.admin_app')

@section('title', 'All Blogs')

@section('link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper {
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .blog-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            padding: 12px 15px;
        }
        
        .blog-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        
        .view-btn {
            background-color: #dbeafe;
            color: #1dd87a;
            margin-right: 8px;
        }
        
        .view-btn:hover {
            background-color: #bfdbfe;
        }
        
        .edit-btn {
            background-color: #dbeafe;
            color: #1d4ed8;
            margin-right: 8px;
        }
        
        .edit-btn:hover {
            background-color: #bfdbfe;
        }
        
        .delete-btn {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .delete-btn:hover {
            background-color: #fecaca;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 1200px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: black;
        }
        
        .thumbnail-preview {
            max-width: 150px;
            max-height: 100px;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content')
    <main class="px-4 py-8 mx-auto max-w-7xl">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Blog Posts</h1>
        </div>
        
        <div class="bg-white rounded-lg shadow">
            <table id="blogsTable" class="blog-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Reading Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blogs as $blog)
                    <tr>
                        <td>{{ $blog->id }}</td>
                        <td>
                            <img src="{{ asset($blog->thumbnail) }}" alt="Thumbnail" class="thumbnail-preview">
                        </td>
                        <td>{{ $blog->title }}</td>
                        <td>{{ $blog->category }}</td>
                        <td>{{ $blog->time_to_read }} Minute</td>
                        <td>
                            <a target="_blank" href="/blogs/{{ $blog->id }}/{{ $blog->category }}/{{ $blog->title }}" class="action-btn view-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="action-btn edit-btn" onclick="openEditModal({{ $blog->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="/admin/delete-blog/{{ $blog->id }}" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this blog?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
    
    <!-- Edit Blog Modal -->
    <div id="editBlogModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h2 class="text-2xl font-bold mb-6">Edit Blog Post</h2>
            
            <form id="editBlogForm" method="POST" action="/admin/update-blog" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="editBlogId">
                
                <!-- Form content will be loaded here via AJAX -->
                <div id="editBlogFormContent"></div>
                
                <div class="flex justify-end mt-6 space-x-4">
                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="confirmDelete()">
                        Delete Blog
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Update Blog
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#blogsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                    { orderable: false, targets: [1, -1] }
                ]
            });
        });
        
        function openEditModal(blogId) {
            // Show loading state
            $('#editBlogFormContent').html('<div class="text-center py-8"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            
            // Fetch blog data via AJAX
            $.ajax({
                url: '/admin/get-blog/' + blogId,
                type: 'GET',
                success: function(response) {
                    $('#editBlogId').val(blogId);
                    $('#editBlogFormContent').html(response);
                    $('#editBlogModal').show();
                },
                error: function() {
                    $('#editBlogFormContent').html('<div class="text-center py-8 text-red-500">Error loading blog data</div>');
                }
            });
        }
        
        function closeEditModal() {
            $('#editBlogModal').hide();
        }
        
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this blog?')) {
                const blogId = $('#editBlogId').val();
                window.location.href = '/admin/delete-blog/' + blogId;
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editBlogModal')) {
                closeEditModal();
            }
        }
    </script>
@endsection