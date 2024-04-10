<?php

namespace App\Http\Requests;

use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseOrderRequest extends FormRequest
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
            // if(request()->ruc)
            // {
            //     $provider = Proveedor::where('ruc',request()->ruc)->first();
            //     if($provider)
            //     {
            //         $validator->errors()->add('ruc', 'El RUC ya fue registrado, pertenece a '. $provider->name);
            //     }
            // }
        });
    }
}
