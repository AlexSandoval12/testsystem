<?php

namespace App\Http\Requests;

use App\Models\Provider;
use Illuminate\Foundation\Http\FormRequest;

class CreateProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) 
        {
            if(request()->ruc)
            {
                $provider = Provider::where('ruc',request()->ruc)->first();
                if($provider)
                {
                    $validator->errors()->add('ruc', 'El RUC ya fue registrado, pertenece a '. $provider->name);
                }
            }
        });
    }
}
