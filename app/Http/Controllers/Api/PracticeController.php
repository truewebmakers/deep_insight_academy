<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\practice;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageHandleTrait;
use Illuminate\Support\Facades\Storage;

class PracticeController extends Controller
{
    use ImageHandleTrait;
    /**
     * Add Practice.
     */
    public function addPractice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'sub_category_id' => 'required|integer',
            'q_num' => 'required|integer|min:1',
            'audio' => 'file|mimes:mp3,wav',
            'paragraph' => 'nullable|string',
            'image' => 'nullable|string',
            'is_short' => 'boolean|nullable',
            'difficulty' => 'nullable|in:Easy,Medium,Hard',
            'image_type' => 'nullable|in:Bar,Line,Pie,Flow,Table,Map,Pic,Comb',
            'essay_type' => 'nullable|in:Dual Q,Y/N,Open Q',
            'prepare_time' => 'nullable|date_format:H:i:s',
            'test_time' => 'nullable|date_format:H:i:s',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $sub_category = sub_category::findOrFail($request->sub_category_id);
            $practice = new practice();
            $practice->title = trim($request->title);
            $practice->q_num = $request->q_num;
            $practice->sub_category_id = $sub_category->id;
            $practice->main_category_id = $sub_category->main_category_id;
            $practice->prepare_time = $request->prepare_time;
            $practice->test_time = $request->test_time;
            $practice->is_short = $request->is_short;
            $practice->difficulty = $request->difficulty;
            $practice->image_type = $request->image_type;
            $practice->essay_type = $request->essay_type;
            $practice->save();
            if ($sub_category->content_type === 'image') {
                if (!$request->filled('image')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Image is required'
                    ], 400);
                }
                $image = $this->decodeBase64Image($request->image);
                $imageName = 'practice_' . $practice->id . '.' . $image['extension'];
                $imagePath = 'public/practice/image/' . $imageName;
                Storage::put($imagePath, $image['data']);

                $practice->image = 'storage/app/public/practice/image/' . $imageName;
            } else if ($sub_category->content_type === 'text') {
                if (!$request->filled('paragraph')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Paragraph is required'
                    ], 400);
                }
                $practice->paragraph = $request->paragraph;
            } else if ($sub_category->content_type === 'audio') {
                if (!$request->hasFile('audio')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Audio is required'
                    ], 400);
                }
                $audioFile = $request->file('audio');
                $audioName = 'practice_' . $practice->id . '.' . $audioFile->getClientOriginalExtension();
                $audioPath = 'public/practice/audio/' . $audioName;
                Storage::put($audioPath, file_get_contents($audioFile));
                $practice->audio = 'storage/app/public/practice/audio/' . $audioName;
            }
            $practice->save();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Practice added successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to add practice.'
            ], 500);
        }
    }

    public function updatePractice(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'sub_category_id' => 'required|integer',
            'q_num' => 'required|integer|min:1',
            'audio' => 'file|mimes:mp3,wav',
            'paragraph' => 'nullable|string',
            'image' => 'nullable|string',
            'is_short' => 'boolean|nullable',
            'difficulty' => 'nullable|in:Easy,Medium,Hard',
            'image_type' => 'nullable|in:Bar,Line,Pie,Flow,Table,Map,Pic,Comb',
            'essay_type' => 'nullable|in:Dual Q,Y/N,Open Q',
            'prepare_time' => 'nullable|date_format:H:i:s',
            'test_time' => 'nullable|date_format:H:i:s',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $sub_category = sub_category::findOrFail($request->sub_category_id);
            $practice = practice::findOrFail($id);
            $practice->title = trim($request->title);
            $practice->q_num = $request->q_num;
            $practice->sub_category_id = $sub_category->id;
            $practice->main_category_id = $sub_category->main_category_id;
            $practice->prepare_time = $request->prepare_time;
            $practice->test_time = $request->test_time;
            $practice->is_short = $request->is_short;
            $practice->difficulty = $request->difficulty;
            $practice->image_type = $request->image_type;
            $practice->essay_type = $request->essay_type;
            $practice->save();
            if ($sub_category->content_type === 'image') {
                if (!$request->filled('image')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Image is required'
                    ], 400);
                }
                $image = $this->decodeBase64Image($request->image);
                $imageName = 'practice_' . $practice->id . '.' . $image['extension'];
                $imagePath = 'public/practice/image/' . $imageName;
                Storage::put($imagePath, $image['data']);

                $practice->image = 'storage/app/public/practice/image/' . $imageName;
            } else if ($sub_category->content_type === 'text') {
                if (!$request->filled('paragraph')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Paragraph is required'
                    ], 400);
                }
                $practice->paragraph = $request->paragraph;
            } else if ($sub_category->content_type === 'audio') {
                if (!$request->hasFile('audio')) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Audio is required'
                    ], 400);
                }
                $audioFile = $request->file('audio');
                $audioName = 'practice_' . $practice->id . '.' . $audioFile->getClientOriginalExtension();
                $audioPath = 'public/practice/audio/' . $audioName;
                Storage::put($audioPath, file_get_contents($audioFile));
                $practice->audio = 'storage/app/public/practice/audio/' . $audioName;
            }
            $practice->save();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Practice update successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to update practice.'
            ], 500);
        }
    }
    /**
     * Get All Practices by sub category
     */
    public function getAllPracitcesBySubCategoryUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        try {
            $sub_category = sub_category::findOrFail($request->sub_category_id);
            $practices = practice::where('sub_category_id', $sub_category->id)->where('disable', 0)->get()->makeHidden('disable');
            $practices->transform(function ($practice) use ($sub_category) {
                $practice->sub_category_name = $sub_category->name ;
                return $practice;
            });
            return response()->json([
                'status_code' => 200,
                'data' => $practices,
                'message' => 'Practices retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            // print($e);
            return response()->json([
                'status_code' => 404,
                'message' => 'Failed to retrieve practices.'
            ], 404);
        }
    }

    /**
     * Get All Practices by sub category Admin
     */
    public function getAllPracitcesBySubCategoryAdmin(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'sub_category_id' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status_code' => 400,
        //         'message' => $validator->messages()
        //     ], 400);
        // }
        // try {
        //     $sub_category = sub_category::findOrFail($request->sub_category_id);
        //     $practices = practice::where('sub_category_id', $sub_category->id)->where('disable', 0)->get();
        //     return response()->json([
        //         'status_code' => 200,
        //         'data' => $practices,
        //         'message' => 'Practices retrieved successfully'
        //     ], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status_code' => 500,
        //         'message' => 'Failed to retrieve practices.'
        //     ], 500);
        // }

        try {
            $sub_category = sub_category::with('categories')->get();
           // $practices = practice::where('sub_category_id', $sub_category->id)->where('disable', 0)->get();
            return response()->json([
                'status_code' => 200,
                'data' => $sub_category,
                'message' => 'Practices retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to retrieve practices.'
            ], 500);
        }
    }

    /**
     * Get Practice details
     */
    public function getPracitceDetailsUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'practice_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        try {
            $practice = practice::where('id', $request->practice_id)->where('disable', 0)->first();
            if (!$practice) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Practice not found'
                ], 404);
            }
            $sub_category = sub_category::findOrFail($practice->sub_category_id);
            $practice->sub_category_details = $sub_category;

            $practice->makeHidden('disable');
            return response()->json([
                'status_code' => 200,
                'data' => $practice,
                'message' => 'Practice retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to retrieve practice.'
            ], 500);
        }
    }
    public function deletePractice(Request $request,$id)
    {
       $practice = practice::find($id)->delete();
        return response()->json([
            'status_code' => 200,
            'message' => 'Practice delete successfully'
        ], 200);
    }
}
