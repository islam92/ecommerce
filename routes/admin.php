<?php

use App\Http\Controllers\Admin\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MainCategoriesController;
use App\Http\Controllers\Admin\VendorsController;
use App\Http\Controllers\Admin\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
define('PAGINATION_COUNT',10);
Route::group(['namespace' => 'Admin', 'middleware' => 'auth:admin'], function() {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');


    ##########################Begin Languages Routes##############################

    Route::group(['prefix' => 'languages'], function(){
        Route::get('/', [LanguageController::class, 'index'])->name('admin.languages');
        Route::get('create', [LanguageController::class, 'create']) -> name('admin.languages.create');
        Route::post('store', [LanguageController::class, 'store']) -> name('admin.languages.store');

        Route::get('edit/{id}', [LanguageController::class, 'edit']) -> name('admin.languages.edit');
        Route::post('update/{id}', [LanguageController::class, 'update']) -> name('admin.languages.update');

        Route::get('delete/{id}', [LanguageController::class, 'destroy']) -> name('admin.languages.delete');
    });

    ##########################end Languages Routes##############################

    ######################### Begin Main Categoris Routes ########################
    Route::group(['prefix' => 'main_categories'], function () {
        Route::get('/', [MainCategoriesController::class, 'index']) -> name('admin.maincategories');
        Route::get('create', [MainCategoriesController::class, 'create']) -> name('admin.maincategories.create');
        Route::post('store', [MainCategoriesController::class, 'store']) -> name('admin.maincategories.store');
        Route::get('edit/{id}', [MainCategoriesController::class, 'edit']) -> name('admin.maincategories.edit');
        Route::post('update/{id}', [MainCategoriesController::class, 'update']) -> name('admin.maincategories.update');
        Route::get('delete/{id}', [MainCategoriesController::class, 'destroy']) -> name('admin.maincategories.delete');
        Route::get('changeStatus/{id}', [MainCategoriesController::class, 'changeStatus']) -> name('admin.maincategories.status');

    });
    ######################### End  Main Categoris Routes  ########################

    ######################### Begin Sub Categoris Routes ########################
        Route::group(['prefix' => 'sub_categories'], function () {
            Route::get('/', [SubCategoriesController::class, 'index']) -> name('admin.subcategories');
            Route::get('create', [SubCategoriesController::class, 'create']) -> name('admin.subcategories.create');
            Route::post('store', [SubCategoriesController::class, 'store']) -> name('admin.subcategories.store');
            Route::get('edit/{id}', [SubCategoriesController::class, 'edit']) -> name('admin.subcategories.edit');
            Route::post('update/{id}', [SubCategoriesController::class, 'update']) -> name('admin.subcategories.update');
            Route::get('delete/{id}', [SubCategoriesController::class, 'destroy']) -> name('admin.subcategories.delete');
            Route::get('changeStatus/{id}', [SubCategoriesController::class, 'changeStatus']) -> name('admin.subcategories.status');

        });

    ######################### End  Sub Categoris Routes  ########################


    ######################### Begin Main Vendor Routes ########################
    Route::group(['prefix' => 'vendors'], function () {
        Route::get('/', [VendorsController::class, 'index']) -> name('admin.vendors');
        Route::get('create', [VendorsController::class, 'create']) -> name('admin.vendors.create');
        Route::post('store', [VendorsController::class, 'store']) -> name('admin.vendors.store');
        Route::get('edit/{id}', [VendorsController::class, 'edit']) -> name('admin.vendors.edit');
        Route::post('update/{id}', [VendorsController::class, 'update']) -> name('admin.vendors.update');
        Route::get('delete/{id}', [VendorsController::class, 'destroy']) -> name('admin.vendors.delete');
        Route::get('changeStatus/{id}', [VendorsController::class, 'changeStatus']) -> name('admin.vendors.status');

    });
    ######################### End  Main Vendor Routes  ########################
});



Route::group(['namespace' => 'Admin', 'middleware' => 'guest:admin'], function() {
    Route::get('login', [LoginController::class, 'getLogin'])->name('get.admin.login');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});


 ########################### test part routes #####################

 Route::get('subcateory',function (){

    $mainCategory = \App\Models\MainCategory::find(31);
    return    $mainCategory -> subCategories;
});

Route::get('maincategory',function (){

  $subcategory = \App\Models\SubCategory::find(1);

  return $subcategory -> mainCategory;


});
