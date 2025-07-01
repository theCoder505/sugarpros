<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogsController extends Controller
{






    // surface blog pages 
    public function blog()
    {
        $latest_blog = Blog::orderBy('id', 'DESc')->limit(1)->get();
        $related_second = Blog::orderBy('id', 'DESC')->skip(1)->take(5)->get();
        $blogs = Blog::orderBy('id', 'DESC')->get();
        $categories = Category::orderBy('id', 'DESC')->get();
        return view('blogs', compact('latest_blog', 'blogs', 'categories', 'related_second'));
    }







    public function blog_details($id, $category, $title)
    {
        $blog = Blog::where('id', $id)->where('category', $category)->get();
        $related_blogs = Blog::where('category', $category)->orderBy('id', 'DESC')->get();
        return view('blog_details', compact('blog', 'related_blogs'));
    }









    // Blog Management from category
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->get();

        return view('admin.categories', compact('categories'));
    }









    public function category_store(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'category_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'categories/';
            $file->move(public_path($path), $filename);
            $categoryImage = $path . $filename;
        }

        Category::insert([
            'category' => $request['category'],
            'image' => $categoryImage,
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

















    public function updateCategory(Request $request)
    {
        $cat_id = $request['cat_id'];
        $category = $request['category'];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'category_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = 'categories/';
            $file->move(public_path($path), $filename);
            $categoryImage = $path . $filename;
        } else {
            $categoryImage = Category::where('id', $cat_id)->value('image');
        }

        Category::where('id', $cat_id)->update([
            'category' => $category,
            'image' => $categoryImage,
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }









    public function removeCategory($cat_id)
    {
        Category::where('id', $cat_id)->delete();
        return redirect()->back()->with('success', 'Category Removed Successfully!');
    }

















    // main blog management
    public function addBlogPage()
    {
        $categories = Category::orderBy('id', 'DESC')->get();
        return view('admin.add_new_blog', compact('categories'));
    }






    public function allBlogs()
    {
        $latest_blog = Blog::orderBy('id', 'DESc')->limit(1)->get();
        $blogs = Blog::orderBy('id', 'DESc')->get();
        $categories = Category::orderBy('id', 'DESC')->get();
        return view('admin.blogs', compact('blogs', 'categories', 'latest_blog'));
    }






    public function addNewBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'short_details' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:15360', // 15MB max
            'contentTitle' => 'required|array',
            'contentTitle.*' => 'required|string|max:255',
            'contentDetails' => 'required|array',
            'contentDetails.*' => 'required|string',
            'contentImage' => 'required|array',
            'contentImage.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:15360',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('image')) {
            $thumbnail = $this->uploadImage($request->file('image'), 'blogs', 'blog_');
        }

        // Handle content images upload
        $contentImages = [];
        if ($request->hasFile('contentImage')) {
            foreach ($request->file('contentImage') as $image) {
                $contentImages[] = $this->uploadImage($image, 'blogs/contents', 'content_');
            }
        }

        // Calculate reading time
        $wordCount = str_word_count($request->title . ' ' . $request->short_details);
        foreach ($request->contentTitle as $title) {
            $wordCount += str_word_count($title);
        }
        foreach ($request->contentDetails as $detail) {
            $wordCount += str_word_count($detail);
        }
        $timeToRead = ceil($wordCount / 200); // Average reading speed: 200 words per minute

        // Create blog post
        Blog::create([
            'title' => $request->title,
            'category' => $request->category,
            'thumbnail' => $thumbnail,
            'short_details' => $request->short_details,
            'time_to_read' => $timeToRead,
            'table_of_contents' => json_encode($request->contentTitle),
            'content_images' => json_encode($contentImages),
            'content_details' => json_encode($request->contentDetails),
        ]);

        return redirect()->back()->with('success', 'New Blog Post Added Successfully!');
    }





    public function getBlog($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = Category::orderBy('id', 'DESC')->get();
        return view('includes.blog_edit_form', compact('blog', 'categories'));
    }













    public function updateBlog(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:blogs,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'short_details' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
            'contentTitle' => 'required|array',
            'contentTitle.*' => 'required|string|max:255',
            'contentDetails' => 'required|array',
            'contentDetails.*' => 'required|string',
            'contentImage' => 'nullable|array',
            'contentImage.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:15360',
        ]);

        $blog = Blog::findOrFail($request->id);

        // Handle thumbnail upload
        $thumbnail = $blog->thumbnail;
        if ($request->hasFile('image')) {
            if (file_exists(public_path($thumbnail))) {
                unlink(public_path($thumbnail));
            }
            $thumbnail = $this->uploadImage($request->file('image'), 'blogs', 'blog_');
        }

        // Handle content images upload
        $contentImages = json_decode($blog->content_images, true) ?? [];

        if ($request->hasFile('contentImage')) {
            foreach ($request->file('contentImage') as $index => $image) {
                if ($image) {
                    // Delete old image if it exists
                    if (isset($contentImages[$index]) && file_exists(public_path($contentImages[$index]))) {
                        unlink(public_path($contentImages[$index]));
                    }
                    // Upload new image
                    $contentImages[$index] = $this->uploadImage($image, 'blogs/contents', 'content_');
                }
            }
        }

        // Calculate reading time
        $wordCount = str_word_count($request->title . ' ' . $request->short_details);
        foreach ($request->contentTitle as $title) {
            $wordCount += str_word_count($title);
        }
        foreach ($request->contentDetails as $detail) {
            $wordCount += str_word_count($detail);
        }
        $timeToRead = ceil($wordCount / 200);

        $blog->update([
            'title' => $request->title,
            'category' => $request->category,
            'thumbnail' => $thumbnail,
            'short_details' => $request->short_details,
            'time_to_read' => $timeToRead,
            'table_of_contents' => json_encode($request->contentTitle),
            'content_images' => json_encode($contentImages),
            'content_details' => json_encode($request->contentDetails),
        ]);

        return redirect()->back()->with('success', 'Blog Post Updated Successfully!');
    }









    public function deleteBlog($id)
    {
        $blog = Blog::findOrFail($id);

        // Delete thumbnail
        if (file_exists(public_path($blog->thumbnail))) {
            unlink(public_path($blog->thumbnail));
        }

        // Delete content images
        $contentImages = json_decode($blog->content_images, true) ?? [];
        foreach ($contentImages as $image) {
            if (file_exists(public_path($image))) {
                unlink(public_path($image));
            }
        }

        $blog->delete();

        return redirect()->back()->with('success', 'Blog Post Deleted Successfully!');
    }










    private function uploadImage($file, $path, $prefix)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $prefix . time() . '_' . uniqid() . '.' . $extension;
        $filePath = $path . '/' . $filename;
        $file->move(public_path($path), $filename);
        return $filePath;
    }
}
