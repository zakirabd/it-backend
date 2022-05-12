<?php

namespace App\Http\Controllers;

use App\Images;
use Illuminate\Http\Request;

use App\Helpers\UploadHelper;

use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->keyword != ''){
            return Images::orderBy('created_at', 'DESC')
            ->orwhere('title', 'like', "%{$request->keyword}%")
            ->take(20 * $request->page)->get();
        }else{
            return Images::orderBy('created_at', 'DESC')->take(20 * $request->page)->get();
        }
        //  ->orwhere('email', 'like', "%{$this->request->keyword}%")
        // return Images::orderBy('created_at', 'DESC')->take(20 * $request->page)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $images = new Images();

        $images->title = $request->title;

        if ($request->hasFile('image_url')) {


            $images->image_url = UploadHelper::imageUpload($request->file('image_url'), 'avatars');


        }
        $images->save();

        return response()->json(['msg' => 'Image Saved Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Images  $images
     * @return \Illuminate\Http\Response
     */
    public function show(Images $images)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Images  $images
     * @return \Illuminate\Http\Response
     */
    public function edit(Images $images)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Images  $images
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Images $images)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Images  $images
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Images::findOrFail($id);


        if ($image->image_url) {


            Storage::delete('public/' . $image->image_url);


        }


        $image->delete();


        return response()->json(['msg' => 'Image has been deleted successfully.']);
    }
}
