<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\MainCategory;
use App\Http\Requests\VendorRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $vendors = Vendor::selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = MainCategory::where('translation_of', 0)->active()->get();
        return view('admin.vendors.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VendorRequest $request)
    {

        try {

            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


            $filePath = "";
            if ($request->has('logo')) {
                $filePath = uploadImage('vendors', $request->logo);

            }


            Vendor::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => $request->password,
                'active' => $request->active,
                'address' => $request->address,
                'logo' => $filePath,
                'category_id' => $request->category_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);


        }catch(\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        try {
            $vendor = Vendor::selection()->find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);

            $categories = MainCategory::where('translation_of', 0)->active()->get();

            return view('admin.vendors.edit', compact('vendor', 'categories'));

        }catch(\Exception $ex){
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(VendorRequest $request, $id)
    {
        //
        try {
            $vendor = Vendor::selection()->find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);

            DB::beginTransaction();

            if($request->has('logo')){
                $filePath = uploadImage('vendor', $request->logo);
                Vendor::where('id', $id)->update([
                    'logo' => $filePath,
                ]);
            }

            if(!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $data = $request->except('_token', 'id', 'logo', 'password');

            if($request->has('password') && !is_null($request->password))
                $data['password'] = $request->password;

            Vendor::where('id', $id)->update($data);

            DB::commit();

            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);

        }catch (\Exception $ex){
            DB::rollBack();
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{

            $vendor = Vendor::find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

            $image = Str::after($vendor->logo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder

            $vendor->delete();
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);

        }catch(\Exception $ex){
            return $ex;
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function changeStatus($id){
        try{
            $vendor = Vendor::find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

            $status = ($vendor->active == 1) ? 0 : 1;

            $vendor->update(['active' => $status]);

            return redirect()->route('admin.vendors')->with(['success' => ' تم تغيير الحالة بنجاح ']);


        }catch(\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
