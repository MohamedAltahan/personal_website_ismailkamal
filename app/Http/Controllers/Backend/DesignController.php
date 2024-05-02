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
            'thumbnail' => ['image', 'max:20000'],
            'video_thumbnail' => ['image', 'max:20000'],
            'image.*' => ['image'],
            'video' => ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'],
            'category_id' => ['required', 'integer'],
            'status' => ['required'],
        ]);

        if ($request->has('thumbnail')) {
            $thumbnail = $this->fileUplaod($request, 'myDisk', 'thumbnails', 'thumbnail');
        } else {
            $thumbnail = null;
        }

        $design = Design::create([
            'thumbnail' => $thumbnail,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'status' => $request->status,
        ]);

        //uplaod video_thumbnail
        if ($request->has('video_thumbnail')) {
            $video_thumbnail = $this->fileUplaod($request, 'myDisk', 'video_thumbnail', 'video_thumbnail');
        } else {
            $video_thumbnail = null;
        }

        if ($request->has('video')) {
            $video = $this->fileUplaod($request, 'myDisk', 'videos', 'video');
            Video::create([
                'design_id' => $design->id,
                'name' => $video,
                'video_thumbnail' => $video_thumbnail,
                'at_home' => 'no'
            ]);
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

        toastr('Saved successfully');
        return  redirect()->route('admin.design.edit', $design->id);
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
            'thumbnail' => ['image', 'max:20000'],
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

        //uplaod video_thumbnail
        if ($request->has('video_thumbnail')) {
            $video_thumbnail = $this->fileUplaod($request, 'myDisk', 'video_thumbnail', 'video_thumbnail');
        } else {
            $video_thumbnail = null;
        }

        if ($request->has('video')) {
            $video = $this->fileUplaod($request, 'myDisk', 'videos', 'video');
            Video::create([
                'design_id' => $design->id,
                'name' => $video,
                'video_thumbnail' => $video_thumbnail,
                'at_home' => 'no'
            ]);
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

        $this->deleteFile('myDisk', $design->thumbnail);

        //delete videos from files then form database
        foreach ($design->videos as $video) {
            $this->deleteFile('myDisk', $video->video_thumbnail);
            $this->deleteFile('myDisk', $video->name);
        }
        Video::where('design_id', $design->id)->delete();

        //delete photos from files then form database
        foreach ($design->images as $image) {
            $this->deleteFile('myDisk', $image->name);
        }
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

    //delete video thumbnail using ajax____________________________________________________________
    public function deleteVideoThumbnail(Request $request)
    {
        $video = Video::findOrFail($request->video_id);
        $this->deleteFile('myDisk', $video->video_thumbnail);
        $video->update(['video_thumbnail' => null]);
    }

    //delete video using ajax____________________________________________________________
    public function deleteDesignVideo(Request $request)
    {
        $design_video = Video::findOrFail($request->video_id);
        $this->deleteFile('myDisk', $design_video->name);
        $this->deleteFile('myDisk', $design_video->video_thumbnail);
        $design_video->delete();
    }

    //updateVideoThumbnail______________________________________________________________________
    public function updateVideoThumbnail(Request $request, $id)
    {
        if ($request->has('video_thumbnail')) {
            $video_thumbnail = $this->fileUplaod($request, 'myDisk', 'video_thumbnail', 'video_thumbnail');
        }
        $video = Video::findOrFail($id);
        $video->update(['video_thumbnail' => $video_thumbnail]);

        if ($video->at_home) {
            return redirect()->back();
        }
        return  redirect()->route('admin.design.edit', $video->design);
    }
}
