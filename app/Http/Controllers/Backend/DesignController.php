<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Design;
use App\Models\Image;
use App\Models\SubCategory;
use App\Models\Video;
use App\Traits\fileUploadTrait;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    use fileUploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.design.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required'],
            'thumbnail' => ['image', 'max:5000'],
            'image.*' => ['image'],
            'video.*' => ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'],
            'category_id' => ['required', 'integer'],
            'status' => ['required'],
        ]);

        if ($request->has('thumbnail')) {

            $thumbnail = $this->fileUplaod($request, 'myDisk', 'thumbnails', 'thumbnail');
        }

        $design = Design::create([
            'thumbnail' => $thumbnail,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'status' => $request->status,
        ]);

        if ($request->has('video')) {
            $videos = $this->filesUplaod($request, 'myDisk', 'videos', 'video');
            foreach ($videos as $video) {
                Video::create([
                    'design_id' => $design->id,
                    'name' => $video,
                ]);
            }
        }

        if ($request->has('image')) {
            $images = $this->filesUplaod($request, 'myDisk', 'images', 'image');
            foreach ($images as $image) {
                Image::create([
                    'design_id' => $design->id,
                    'name' => $image,
                ]);
            }
        }
        return redirect()->route('admin.show-designs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $design = Design::with('images', 'videos')->findOrFail($id);
        $categories = Category::all();
        $subCategories = SubCategory::where('category_id', $design->category_id)->get();
        return view('admin.design.edit', compact('design', 'categories', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required'],
            'thumbnail' => ['image', 'max:5000'],
            'image.*' => ['image'],
            'video.*' => ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'],
            'category_id' => ['required', 'integer'],
            'category_id' => ['sometimes', 'integer'],
        ]);

        $design = Design::findOrFail($id);
        $updatedDesignData = $request->except('video', 'image');
        if ($request->has('thumbnail')) {
            $oldImagePath = $design->thumbnail;
            $updatedDesignData['thumbnail'] = $this->fileUpdate($request, 'myDisk', 'thumbnails', 'thumbnail', $oldImagePath);
        }

        if ($request->has('video')) {
            $videos = $this->filesUplaod($request, 'myDisk', 'videos', 'video');
            foreach ($videos as $video) {
                Video::create([
                    'design_id' => $design->id,
                    'name' => $video,
                ]);
            }
        }

        if ($request->has('image')) {
            $images = $this->filesUplaod($request, 'myDisk', 'images', 'image');
            foreach ($images as $image) {
                Image::create([
                    'design_id' => $design->id,
                    'name' => $image,
                ]);
            }
        }

        $design->update($updatedDesignData);

        toastr('Added successfully');
        return redirect()->route('admin.show-designs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $design = Design::findOrFail($id);
        Video::where('design_id', $design->id)->delete();
        Image::where('design_id', $design->id)->delete();
        $design->delete();
        return response(['status' => 'success', 'message' => 'Deleted successfully']);
    }

    //change status using ajax request _________________________________________________________
    public function changeStatus(Request $request)
    {
        $design = Design::findOrFail($request->id);

        $request->status == "true" ? $design->status = 'active' : $design->status = 'inactive';
        $design->save();

        return response(['message' => 'Status has been updated']);
    }

    //delete Product Images using ajax____________________________________________________________
    public function deleteDesignImage(Request $request)
    {
        $design_image = Image::findOrFail($request->image_id);
        $design_image->delete();
        $this->deleteFile('myDisk', $design_image->name);
    }

    //delete Product Images using ajax____________________________________________________________
    public function deleteDesignVideo(Request $request)
    {
        $design_video = Video::findOrFail($request->video_id);
        $design_video->delete();
        $this->deleteFile('myDisk', $design_video->name);
    }
}
