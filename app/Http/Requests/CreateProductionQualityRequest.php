<?php

namespace App\Http\Requests;

use App\Models\ProductionQuality;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateProductionQualityRequest extends FormRequest
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
        ];
    }

    public function withValidator($validator)
    {
        // Log::info(request()->all());

        // $validator->after(function ($validator)
        // {
        //     if(!request()->{"quality_id0"} || !request()->{"cantidad_controlada0"})
        //     {
        //         $validator->errors()->add('ruc', 'La primera Etapa no puede mandar vacio');
        //     }
        //     if(!request()->{"quality_id1"} || !request()->{"cantidad_controlada1"})
        //     {
        //         $validator->errors()->add('ruc', 'La segunda Etapa no puede mandar vacio');
        //     }
        //     if(!request()->{"quality_id2"} || !request()->{"cantidad_controlada2"})
        //     {
        //         $validator->errors()->add('ruc', 'La tercera Etapa no puede mandar vacio');
        //     }
        // });
    }
}
