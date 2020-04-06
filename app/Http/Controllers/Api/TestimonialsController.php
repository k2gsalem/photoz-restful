<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Testimonials;


use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Storage;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (auth()->user()->id == 1) {


            try {
                //Validate the data
                try {
                    $this->validate($request, [
                        'testimonial_name' => ['required', 'string'],
                        'testimonial_title' => ['required', 'string'],
                        'testimonial_desc' => ['nullable', 'string'],
                        'testimonial_date' => ['required', 'date', 'date_format:Y-m-d'],
                        'testimonial_image' => ['required', 'image', 'max:1999'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json($e->errors(), 400);
                }
                $file_to_store = 'noimage.jpg'; //default image

                if ($request->testimonial_image !== null) {
                    $filename = $request->file('testimonial_image')->getClientOriginalName();
                    $file_first = pathinfo($filename, PATHINFO_FILENAME);
                    $extension = $request->file('testimonial_image')->getClientOriginalExtension();

                    $file_to_store = $file_first . '_' . time() . '.' . $extension;
                    $path = $request->file('testimonial_image')->storeAs('public/testimonials_picture', $file_to_store);
                }
                // return dd($request);
                // $user_id = auth()->user()->id;
                $testimonoals = Testimonials::create([
                    'testimonial_name' => $request->testimonial_name,
                    'testimonial_title' => $request->testimonial_title,
                    'testimonial_desc' => $request->testimonial_desc,
                    'testimonial_image' => $file_to_store,
                    'testimonial_date' => $request->testimonial_date,
                ]);

                return response()->json([
                    'status' => "Success",
                    'message' => " Testimonial Created Successfully",
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json(["status" => "failure", "message" => "user is not Admin"], 400);
        }



        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonials $testimonials)
    {
        return 1;
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    // public function edit(Testimonials $testimonials)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->id == 1) {


            try {
                $testimonials = Testimonials::where('id', $id);
                    // ->where('user_id', auth()->user()->id);
                if (count($testimonials->get()) === 0)
                    return response()->json([
                        'success' => false,
                        'message' => 'Testimonial not found or Unauthorized'
                    ], 400);

                $old_photo = Testimonials::where('id', $id)->pluck('testimonial_image')->first();
                try {
                    $this->validate($request, [
                        'testimonial_name' => ['required', 'string'],
                        'testimonial_title' => ['required', 'string'],
                        'testimonial_desc' => ['nullable', 'string'],
                        'testimonial_date' => ['required', 'date', 'date_format:Y-m-d'],
                        'testimonial_image' => ['required', 'image', 'max:1999'],
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json($e->errors(), 400);
                }
                $file_to_store = $old_photo;

                if ($request->testimonial_image !== null) {
                    Storage::delete('public/testimonials_pictures/' . $old_photo);
                    $filename = $request->file('testimonial_image')->getClientOriginalName();
                    $file_first = pathinfo($filename, PATHINFO_FILENAME);
                    $extension = $request->file('testimonial_image')->getClientOriginalExtension();

                    $file_to_store = $file_first . '_' . time() . '.' . $extension;
                    $path = $request->file('testimonial_image')->storeAs('public/testimonials_picture', $file_to_store);
                }
                // return dd($request);
                // $user_id = auth()->user()->id;
                $testimonoals = Testimonials::where('id', $id)->update([
                    'testimonial_name' => $request->testimonial_name,
                    'testimonial_title' => $request->testimonial_title,
                    'testimonial_desc' => $request->testimonial_desc,
                    'testimonial_image' => $file_to_store,
                    'testimonial_date' => $request->testimonial_date,
                ]);

                return response()->json([
                    'status' => "Success",
                    'message' => " Testimonial Updated Successfully",
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json(["status" => "failure", "message" => "user is not Admin"], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Testimonials  $testimonials
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->user()->id == 1) {



            try {
                //Get exact album to be deleted
                $testimonials = Testimonials::where('id', $id);

                if (count($testimonials->get()) === 0)
                    return response()->json([
                        'status' => "failure",
                        'message' => 'Testimonials not found'
                    ], 404);

                //Get cover photo
                $cover = $testimonials->pluck('testimonial_image')->first();

                //Verify user

                    //Delete photos associated with album

                    //Delete from Storage
                    if ($cover !== 'noimage.jpg') {
                        Storage::delete('/public/testimonials_picture/' . $cover);
                    }
                    $testimonials = $testimonials->delete();
                    return response()->json(["status"=>"Success","message"=>"Testimonials Deleted"], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json(["status" => "failure", "message" => "user is not Admin"], 400);
        }
    }
    }

