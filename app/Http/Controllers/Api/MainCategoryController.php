<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\main_category;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainCategoryController extends Controller
{
    /**
     * Add Main Category.
     */
    public function addMainCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $name = trim($request->name);
            $existingCategory = main_category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->first();

            if ($existingCategory) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Category already exists'
                ], 200);
            }

            $main_category = new main_category();
            $main_category->name = $name;
            $main_category->save();
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
     * Update Main Category.
     */
    public function updateMainCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $category = main_category::findOrFail($request->category_id);
            $name = trim($request->name);
            $existingCategory = main_category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->where('id', '!=', $request->category_id)->first();

            if ($existingCategory) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Category already exists'
                ], 200);
            }
            $category->name = $name;
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
     * Delete Main Category.
     */
    public function deleteMainCategory(Request $request)
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
            $category = main_category::findOrFail($request->category_id);
            $subCategoryCount = sub_category::where('main_category_id', $request->category_id)->count();
            if ($subCategoryCount > 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'This category cannot be delete because it has associated sub categories. Please move the sub categories to another category first or delete that sub categories.'
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
     * Get All Main Categories.
     */
    public function getAllMainCategories()
    {
        try {
            $categories = main_category::all();
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

    /**
     * Get All Main Categories With Its Sub Categories.
     */
    public function getAllMainCategoriesWithSubCategories()
    {
        try {
            // $categories = main_category::all();
            $categories = main_category::with('subCategories')->get();
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
