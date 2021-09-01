<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainCategoriesController extends Controller
{
    //
    public function index() {

        $default_lang = get_default_lang();
        $categories = MainCategory::where('translation_lang', $default_lang)->selection()->get();
        return view('admin.maincategories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.maincategories.create');
    }

    public function store(MainCategoryRequest $request)
    {

        try {
            //return $request;

            $main_categories = collect($request->category);

            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all()) [0];


            $filePath = "";
            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);

            }

            DB::beginTransaction(); // don't execute any operation until reach to commit()

            $default_category_id = MainCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath
            ]);

            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });


            if (isset($categories) && $categories->count()) {

                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath
                    ];
                }

                MainCategory::insert($categories_arr);
            }

            DB::commit(); // execute all the operation

            return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
            DB::rollback();  // fall back all operations
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }

    public function edit ($mainCat_id) {
        $mainCategory = MainCategory::with('categories')->selection()->find($mainCat_id);
        if(!$mainCategory) {
            return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);
        }
        return view('admin.maincategories.edit', compact('mainCategory'));
    }

    public function update($mainCat_id, MainCategoryRequest $request) {

        try {

            $mainCategory = MainCategory::find($mainCat_id);

            if(!$mainCategory) {
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);
            }

            //update data
            $category = array_values($request->category)[0];

            if(!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


            MainCategory::where('id', $mainCat_id)->update([
                'name' => $category['name'],
                'active' => $request->active
            ]);

            //save image


            if($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
                MainCategory::where('id', $mainCat_id)->update([
                    'photo' => $filePath
                ]);
            }

            return redirect()->route('admin.maincategories')->with(['success' => 'تم ألتحديث بنجاح']);

        }catch (\Exception $ex) {

            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }

    public function destroy($id){
        try{

            $main_category = MainCategory::find($id);
            if(!$main_category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $vendors = $main_category->vendors();
            if(isset($vendors) && $vendors->count() > 0)
                return redirect()->route('admin.maincategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);

            $image = Str::after($main_category->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder

            //delete translation
            $main_category->categories()->delete();
            $main_category->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);

        }catch(\Exception $ex){
            return $ex;
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id){
        try{
            $main_category = MainCategory::find($id);
            if(!$main_category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $status = ($main_category->active == 1) ? 0 : 1;

            $main_category->update(['active' => $status]);

            return redirect()->route('admin.maincategories')->with(['success' => ' تم تغيير الحالة بنجاح ']);


        }catch(\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

}
