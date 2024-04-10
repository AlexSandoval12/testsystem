<?php

namespace App\Http\Requests;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;

class CreateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
            if(request()->name)
            {
                $provider = Brand::where('name',request()->name)->first();
                if($provider)
                {
                    $validator->errors()->add('name', 'El nombre de la marca ya fue registrado');
                }
            }
        });
    }
}
