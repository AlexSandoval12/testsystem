<?php

namespace App\Http\Requests;

use App\Models\ProductionStage;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductionStageRequest extends FormRequest
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
                // $provider = ProductionStage::where('name',request()->name)->first();
                // if($provider)
                // {
                //     $validator->errors()->add('name', 'El nombre de la marca ya fue registrado');
                // }
            }
        });
    }
}
