<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where('status',1)->get();
        return view('pages.brand.index',compact('brands'));
    }

    public function create()
    {
        return view('pages.brand.create');
    }

    public function store(CreateBrandRequest $request)
    {
        Brand::create([
            'name' => request()->name,
            'status' => 1
        ]);
        

        $this->flashMessage('check', 'La Marca fue registrado correctamente', 'success');

        return redirect()->route('brand');
    }
    public function edit(Brand $brands)
    {
        return view('pages.brand.edit',compact('brands'));
    }

    public function update(Brand $brands)
    {
            $brands->update([
                                'name'       => request()->name,
                            ]);
                            
                                
                                

        return redirect('brand');
    }
}
