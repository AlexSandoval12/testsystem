<?php

namespace App\Http\Requests;

use App\Models\Articulo;
use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductionOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)
        {
            foreach (request()->detail_product_id as $key => $value) 
            {
                // dd(request()->all());
                // $articulo = Articulo::find($value);
                // if(!request()->{"detail_material_quantity_$value"})
                // {
                //     $validator->errors()->add('detail_material_quantity_', 'EL producto '.$articulo->name.' no esta configurado');

                // }
            }
        });
    }
}
