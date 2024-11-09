<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MainCategoryController;
use App\Http\Controllers\Api\PracticeController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('admin/login', [AdminController::class, 'adminLogin']);
Route::prefix('admin')->group(function () {
    //Practice Main Category
    Route::post('practice/maincategory/add', [MainCategoryController::class, 'addMainCategory']);
    Route::post('practice/maincategory/update', [MainCategoryController::class, 'updateMainCategory']);
    Route::post('practice/maincategory/delete', [MainCategoryController::class, 'deleteMainCategory']);
    Route::get('practice/maincategory/getall', [MainCategoryController::class, 'getAllMainCategories']);

    //Practice Sub Category
    Route::post('practice/subcategory/add', [SubCategoryController::class, 'addSubCategory']);
    Route::post('practice/subcategory/update', [SubCategoryController::class, 'updateSubCategory']);
    Route::post('practice/subcategory/delete', [SubCategoryController::class, 'deleteSubCategory']);
    Route::get('practice/subcategory/getall', [SubCategoryController::class, 'getAllSubCategoriesOfMainCategory']);

    //Practice
    Route::post('practice/add', [PracticeController::class, 'addPractice']);
    Route::post('practice/update/{id}', [PracticeController::class, 'updatePractice']);
    Route::post('practice/delete/{id}', [PracticeController::class, 'deletePractice']);
    Route::get('practice/getall', [PracticeController::class, 'getAllPracitcesBySubCategoryAdmin']);
});

Route::post('user/register', [UserController::class, 'signup']);
Route::post('user/login', [UserController::class, 'login']);
Route::get('user/categories/getall', [MainCategoryController::class, 'getAllMainCategoriesWithSubCategories']);
Route::prefix('user')->middleware(['auth:user', 'scope:user'])->group(function () {
    Route::get('practice/getall', [PracticeController::class, 'getAllPracitcesBySubCategoryUser']);
    Route::get('practice/get', [PracticeController::class, 'getPracitceDetailsUser']);
});
