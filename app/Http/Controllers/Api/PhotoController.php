<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Photo;
use App\Album;

class PhotoController extends Controller
{
    //Upload a photo ($request) to Album = $id, authenticated
    //Returns status 201 and photo id if successful
    public function store(Request $request)   //POST
    {

        if (auth()->user()->id == 1) {

            try {
                $album = $request->input('album_id');
                //Get Associated User
                $user = Album::where('id', $album)->pluck('user_id')->first();

                //either album does not exist or user is not authorized
                if ($user === null or $user !== auth()->user()->id) {
                    return response()->json([
                        'status' => "Success",
                        'message' => 'Not Authorized'
                    ], 401);
                }

                //Get Album Size
                $album_size = count(Photo::where('album_id', $album)->get());
                if ($album_size === 1000)    //Album Full
                {
                    return response()->json([
                        'status' => "Success",
                        'message' => 'Album Full'
                    ], 400);
                }

                //Validate the data
                try {
                    $this->validate($request, [
                        'photo_description' => ['nullable', 'string', 'nullable'],
                        'privacy' => ['required', 'integer', 'between:1,3'],
                        'photo' => ['required', 'image', 'max:6144'],
                        'type' => ['required','integer','size:1'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return \response($e->errors(), 400);
                }

                //Save the image
                $filename = $request->file('photo')->getClientOriginalName();
                $file_first = pathinfo($filename, PATHINFO_FILENAME);
                $extension = $request->file('photo')->getClientOriginalExtension();
                $file_to_store = $file_first . '_' . time() . '.' . $extension;
                $path = $request->file('photo')->storeAs('public/photos', $file_to_store);

                $photo = Photo::create([
                    'photo_description' => $request->photo_description,
                    'album_id' => $request->album_id,
                    'privacy' => $request->privacy,
                    'photo' => $file_to_store,
                    'type' =>$request->type,
                    //'geo_location' => $ Get from meta_data
                    //'taken_on' => $Get from meta data
                ]);

                return response()->json([
                    'status' => "Success",
                    'message' => $photo->id ." Photo Created Sucessfully",
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => "Success",
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json(["status" => "failure", "message" => "User Not Authorized"], 400);
        }
    }
    public function storebulk(Request $request)   //POST
    {

        if (auth()->user()->id == 1) {

            try {
                $album = $request->input('album_id');
                //Get Associated User
                $user = Album::where('id', $album)->pluck('user_id')->first();

                //either album does not exist or user is not authorized
                if ($user === null or $user !== auth()->user()->id) {
                    return response()->json([
                        'status' => "Success",
                        'message' => 'Not Authorized'
                    ], 401);
                }

                //Get Album Size
                $album_size = count(Photo::where('album_id', $album)->get());
                if ($album_size === 1000)    //Album Full
                {
                    return response()->json([
                        'status' => "Success",
                        'message' => 'Album Full'
                    ], 400);
                }

                //Validate the data
                try {
                    $this->validate($request, [
                        'photo_description' => ['nullable', 'string', 'nullable'],
                        'privacy' => ['required', 'integer', 'between:1,3'],
                        'photo.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:6144',
                        'type' => ['required','integer','size:1'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return \response($e->errors(), 400);
                }

                //Save the image
                // $filename = $request->file('photo')->getClientOriginalName();
                // $file_first = pathinfo($filename, PATHINFO_FILENAME);
                // $extension = $request->file('photo')->getClientOriginalExtension();
                // $file_to_store = $file_first . '_' . time() . '.' . $extension;
                // $path = $request->file('photo')->storeAs('public/photos', $file_to_store);


                if ($files = $request->file('photo')) {
                   // return dd($files);
                    // Define upload path
                     $destinationPath = public_path('/profile_images/'); // upload path
                     foreach($files as $img) {

                        $filename =$img->getClientOriginalName();
                        $file_first = pathinfo($filename, PATHINFO_FILENAME);
                        $extension = $img->getClientOriginalExtension();
                        $file_to_store = $file_first . '_' . time() . '.' . $extension;
                        $path = $img->storeAs('public/photos', $file_to_store);
                         // Save In Database
                         $photo= Photo::create([

                            'album_id' => $request->album_id,
                            'privacy' => $request->privacy,
                            'photo' => $file_to_store,
                            'type' =>$request->type,
                            //'geo_location' => $ Get from meta_data
                            //'taken_on' => $Get from meta data
                        ]);
                     }

                 }

                // $photo = Photo::create([
                //     'photo_description' => $request->photo_description,
                //     'album_id' => $request->album_id,
                //     'privacy' => $request->privacy,
                //     'photo' => $file_to_store,
                //     'type' =>$request->type,
                //     //'geo_location' => $ Get from meta_data
                //     //'taken_on' => $Get from meta data
                // ]);

                return response()->json([
                    'status' => "Success",
                    'message' => $photo->id ." Photo Created Sucessfully",
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => "Success",
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json(["status" => "failure", "message" => "User Not Authorized"], 400);
        }
    }

    //Update a photo with id =$id, authenticated
    //Returns status 200 for success
    public function update(Request $request, $id)   //PUT
    {
        if (auth()->user()->id == 1) {


        try {
            //Get Album from $id
            $album = Photo::where('id', $id)->where('type', 1)->pluck('album_id')->first();
            //Get Associated User
            $user = Album::where('id', $album)->pluck('user_id')->first();

            //either photo/album does not exist or user is not authorized
            if ($user === null or $user !== auth()->user()->id)
                return response()->json([
                    'success' => false,
                    'message' => 'Photo/Album not found or Not Authorized'
                ], 400);


            $old_photo = Photo::where('id', $id)->pluck('photo')->first();

            //Validate the data
            try {
                $this->validate($request, [
                    'photo_description' => ['nullable', 'string'],
                    'privacy' => ['required', 'integer', 'between:1,3'],
                    'photo' => ['image', 'max:6144'],
                    'type' => ['required','integer', 'size:1'],

                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return \response($e->errors(), 400);
            }

            $file_to_store = $old_photo;

            if ($request->photo !== null) {
                //Delete Old Image
                Storage::delete('public/photos/' . $old_photo);
                //Save the new image
                $filename = $request->file('photo')->getClientOriginalName();
                $file_first = pathinfo($filename, PATHINFO_FILENAME);
                $extension = $request->file('photo')->getClientOriginalExtension();
                $file_to_store = $file_first . '_' . time() . '.' . $extension;
                $path = $request->file('photo')->storeAs('public/photos', $file_to_store);
            }

            $photo = Photo::where('id', $id)->update([
                'photo_description' => $request->photo_description,
                'privacy' => $request->privacy,
                'photo' => $file_to_store,
                'type' => $request->type,
            ]);

            return response()->json(["ststus"=>"Success","message"=>"Photo Updated Successfully"], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    } else {
        return response()->json(["status" => "failure", "message" => "User Not Authorized"], 400);
    }
    }

    //Returns info about the photo if authenticated or public or link accessible
    public function show($id)   //GET
    {
        try {
            $photo = Photo::where('id', $id)->where('type', 1); //match photo id
            //$photo = Photo::find($id);
            if (count($photo->get()) === 0) {
                return response()->json([
                    'status' => "failure",
                    'message' => 'Photo not found'
                ], 404);
            }

            //Get Photo Privacy
            $photo_privacy = $photo->pluck('privacy')->first();
            //Get Associated Album id
            $album =  $photo->pluck('album_id')->first();
            //Get Album Privacy
            $album_privacy = Album::where('id', $album)->pluck('privacy')->first();
            //Get Associated User id
            $user = Album::where('id', $album)->pluck('user_id')->first();

            if ((auth()->check() and auth()->user()->id === $user) or $photo_privacy < 3)
                return response()->json([
                    'status' => "success",
                    'data' => $photo->get()
                ], 200);
            else
                return response()->json([
                    'status' => "failure",
                    'message' => 'Not Authorized'
                ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "failure",
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //Delete the photo with id=$id, authenticated
    //Returns 200 for success
    public function destroy($id)    //DELETE
    {
        if (auth()->user()->id == 1) {


        try {
            $photo = Photo::where('id', $id); //match photo id

            if (count($photo->get()) === 0)
                return response()->json([
                    'status' => "failure",
                    'message' => 'Photo not found'
                ], 404);

            //Get Associated Album id
            $album =  $photo->pluck('album_id')->first();
            //Get Associated User id
            $user = Album::where('id', $album)->pluck('user_id')->first();

            if (auth()->user()->id === $user) {
                //Delete from Storage
                Storage::delete('/public/photos/' . $photo->pluck('photo')->first());
                $photo->delete();
                return response()->json(["status"=>"Success","message"=>"Photo Deleted Sucessfully"], 200);
            } else {
                return response()->json([
                    'status' => "failure",
                    'message' => 'Not Authorized'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    } else {
        return response()->json(["status" => "failure", "message" => "User Not Authorized"], 400);
    }
    }
}
