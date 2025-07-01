<div class="space-y-6">
    <!-- Thumbnail Section -->
    <div>
        <h3 class="text-lg font-medium mb-2">Thumbnail Image</h3>
        <div class="flex items-center space-x-4">
            <img id="currentThumbnail" src="{{ asset($blog->thumbnail) }}" alt="Current thumbnail" class="w-32 h-24 object-cover rounded">
            <div>
                <label class="block text-sm font-medium mb-1">Change Thumbnail</label>
                <input type="file" name="image" id="thumbnailInput" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImage(this, 'currentThumbnail')">
            </div>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $blog->title }}" class="w-full px-3 py-2 border rounded" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category" class="w-full px-3 py-2 border rounded" required>
                @foreach($categories as $category)
                    <option value="{{ $category->category }}" {{ $category->category == $blog->category ? 'selected' : '' }}>{{ $category->category }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1">Short Description</label>
            <textarea name="short_details" class="w-full px-3 py-2 border rounded" rows="3" required>{{ $blog->short_details }}</textarea>
        </div>
    </div>

    <!-- Table of Contents -->
    <div>
        <h3 class="text-lg font-medium mb-2">Table of Contents</h3>
        <div id="editContentItems" class="space-y-4">
            @php
                $contentTitles = json_decode($blog->table_of_contents);
                $contentDetails = json_decode($blog->content_details);
                $contentImages = json_decode($blog->content_images);
            @endphp
            
            @foreach($contentTitles as $index => $title)
            <div class="content-item border p-4 rounded-lg">
                <div class="flex justify-between items-center mb-3">
                    <input type="text" name="contentTitle[]" value="{{ $title }}" class="w-full px-3 py-2 border rounded mr-2" placeholder="Content Title" required>
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeContentItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Content Image</label>
                        <div class="flex items-center space-x-4">
                            @if(isset($contentImages[$index]))
                            <img src="{{ asset($contentImages[$index]) }}" alt="Content Image" class="content-image-preview w-32 h-24 object-cover rounded">
                            @else
                            <img src="/assets/image/uploadimage.png" alt="Placeholder" class="content-image-preview w-32 h-24 object-cover rounded">
                            @endif
                            <input type="file" name="contentImage[]" class="content-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewContentImage(this)">
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Content Details</label>
                    <textarea name="contentDetails[]" class="w-full px-3 py-2 border rounded" rows="4" required>{{ $contentDetails[$index] }}</textarea>
                </div>
            </div>
            @endforeach
        </div>
        
        <button type="button" class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm" onclick="addContentItem()">
            <i class="fas fa-plus mr-1"></i> Add Content Section
        </button>
    </div>
</div>

<script>
    // Preview image for thumbnail
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    }

    // Preview image for content images
    function previewContentImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(input).parent().children('img').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    }

    // Add new content item
    function addContentItem() {
        const itemCount = document.querySelectorAll('.content-item').length;
        const newItem = `
            <div class="content-item border p-4 rounded-lg mt-4">
                <div class="flex justify-between items-center mb-3">
                    <input type="text" name="contentTitle[]" class="w-full px-3 py-2 border rounded mr-2" placeholder="Content Title" required>
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeContentItem(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Content Image</label>
                        <div class="flex items-center space-x-4">
                            <img src="/assets/image/uploadimage.png" alt="Placeholder" class="content-image-preview w-32 h-24 object-cover rounded">
                            <input type="file" name="contentImage[]" class="content-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewContentImage(this)" required>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Content Details</label>
                    <textarea name="contentDetails[]" class="w-full px-3 py-2 border rounded" rows="4" placeholder="Content details..." required></textarea>
                </div>
            </div>
        `;
        document.getElementById('editContentItems').insertAdjacentHTML('beforeend', newItem);
    }
    
    // Remove content item
    function removeContentItem(button) {
        if (document.querySelectorAll('.content-item').length > 1) {
            button.closest('.content-item').remove();
        } else {
            alert('You must have at least one content section.');
        }
    }

    // Initialize event listeners for existing content image inputs
    document.addEventListener('DOMContentLoaded', function() {
        const contentImageInputs = document.querySelectorAll('.content-image-input');
        contentImageInputs.forEach(input => {
            input.addEventListener('change', function() {
                previewContentImage(this);
            });
        });
    });
</script>