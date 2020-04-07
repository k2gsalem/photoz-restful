<?php

namespace App\Http\Controllers\Api;

use App\Photo;
use App\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{

    public function store(Request $request)
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
                        //'photo_description' => ['nullable', 'string', 'nullable'],
                        'privacy' => ['required', 'integer', 'between:1,3'],
                        'path' => ['required', 'string'],
                        'type' => ['required','integer', 'size:2'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return \response($e->errors(), 400);
                }

                //Save the image


                $photo = Photo::create([
                   // 'photo_description' => $request->photo_description,
                    'album_id' => $request->album_id,
                    'privacy' => $request->privacy,
                    'path' => $request->path,
                    'type' => $request->type,
                ]);

                return response()->json([
                    'status' => "Success",
                    'message' => $photo->id ." Video Created Sucessfully",
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


    public function show($id)
    {
        try {
            $photo = Photo::where('id', $id)->where('type', 2); //match photo id
            //$photo = Photo::find($id);
            if (count($photo->get()) === 0) {
                return response()->json([
                    'status' => "failure",
                    'message' => 'Video not found'
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


    public function update(Request $request, $id)
    {
        if (auth()->user()->id == 1) {


            try {
                //Get Album from $id
                $album = Photo::where('id', $id)->where('type', 2)->pluck('album_id')->first();
                //Get Associated User
                $user = Album::where('id', $album)->pluck('user_id')->first();

                //either photo/album does not exist or user is not authorized
                if ($user === null or $user !== auth()->user()->id)
                    return response()->json([
                        'success' => false,
                        'message' => 'Video/Album not found or Not Authorized'
                    ], 400);


                $old_photo = Photo::where('id', $id)->pluck('path')->first();

                //Validate the data
                try {
                    $this->validate($request, [
                        'type' => ['required','integer', 'size:2'],
                        'privacy' => ['required', 'integer', 'between:1,3'],
                        'path' => ['required', 'string'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return \response($e->errors(), 400);
                }

                $file_to_store = $old_photo;



                $photo = Photo::where('id', $id)->update([
                    'privacy' => $request->privacy,
                    'path' => $request->path,
                    'type' => $request->type,
                ]);

                return response()->json(["ststus"=>"Success","message"=>"Video Updated Successfully"], 200);
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


    public function destroy($id)
    {
        if (auth()->user()->id == 1) {


            try {
                $photo = Photo::where('id', $id)->where('type',2); //match photo id

                if (count($photo->get()) === 0)
                    return response()->json([
                        'status' => "failure",
                        'message' => 'Video not found'
                    ], 404);

                //Get Associated Album id
                $album =  $photo->pluck('album_id')->first();
                //Get Associated User id
                $user = Album::where('id', $album)->pluck('user_id')->first();

                if (auth()->user()->id === $user) {
                    //Delete from Storage
                    // Storage::delete('/public/photos/' . $photo->pluck('photo')->first());
                    $photo->delete();
                    return response()->json(["status"=>"Success","message"=>"Video Deleted Sucessfully"], 200);
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
