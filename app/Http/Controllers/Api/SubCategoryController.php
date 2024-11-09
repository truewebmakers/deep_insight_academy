<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\main_category;
use App\Models\practice;
use App\Models\sub_category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Add Sub Category.
     */
    public function addSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'main_category_id' => 'required',
            'ai_score' => 'boolean',
            'content_type' => 'required|in:image,audio,text',
            'input_type' => 'required|in:text,audio',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $mainCategory = main_category::findOrFail($request->main_category_id);
            $name = trim($request->name);
            $existingCategory = sub_category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->where('main_category_id', $mainCategory->id)->first();

            if ($existingCategory) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Category already exists'
                ], 200);
            }

            $sub_category = new sub_category();
            $sub_category->name = $name;
            $sub_category->main_category_id = $mainCategory->id;
            $sub_category->content_type = $request->content_type;
            $sub_category->input_type = $request->input_type;
            if ($request->has('ai_score')) {
                $sub_category->ai_score = $request->ai_score;
            }
            if ($request->has('description')) {
                $sub_category->description = $request->description;
            }
            $sub_category->save();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Category added successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to add category.'
            ], 500);
        }
    }

    /**
     * Update Sub Category.
     */
    public function updateSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'ai_score' => 'boolean',
            'content_type' => 'in:image,audio,text',
            'input_type' => 'in:audio,text',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $category = sub_category::findOrFail($request->category_id);
            if ($request->filled('name')) {
                $name = trim($request->name);
                $existingCategory = sub_category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->where('id', '!=', $request->category_id)->where('main_category_id', $category->main_category_id)->first();

                if ($existingCategory) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Category already exists'
                    ], 200);
                }
                $category->name = $name;
            }
            if ($request->filled('main_category_id')) {
                $category->main_category_id = $request->main_category_id;
            }
            if ($request->has('ai_score')) {
                $category->ai_score = $request->ai_score;
            }
            if ($request->has('description')) {
                $category->description = $request->description;
            }
            if ($request->filled('content_type')) {
                $category->content_type = $request->content_type;
            }
            if ($request->filled('input_type')) {
                $category->input_type = $request->input_type;
            }
            $category->save();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Category updated successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to update category.'
            ], 500);
        }
    }

    /**
     * Delete Sub Category.
     */
    public function deleteSubCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $category = sub_category::findOrFail($request->category_id);
            $practiceCount = practice::where('sub_category_id', $request->category_id)->count();
            if ($practiceCount > 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'This sub category cannot be delete because it has associated practices. Please move the practices to another sub category first or delete that practices.'
                ], 404);
            }
            $category->delete();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to delete category.'
            ], 500);
        }
    }

    /**
     * Get All Sub Categories.
     */
    public function getAllSubCategoriesOfMainCategory(Request $request)
    {
        try {
            $categories = sub_category::all();
            return response()->json([
                'status_code' => 200,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to retrieve categories.'
            ], 500);
        }
    }
}
