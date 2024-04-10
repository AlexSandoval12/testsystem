<?php

namespace App\Http\Requests;

use App\Models\Proveedor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateProductionControlRequest extends FormRequest
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
            foreach(request()->detail_stage_id as $key => $value)
            {
                // Log::info(request()->all());
                // Log::info(!request()->{"stage_id".$value});
                
                // if(!request()->{"stage_id".$value} || !request()->{"cantidad_controlada".$value})
                // {
                //     $validator->errors()->add('ruc', 'La primera Etapa no puede mandar vacio');
                // }
                // if(!request()->{"stage_id".$value} || !request()->{"cantidad_controlada".$value})
                // {
                //     $validator->errors()->add('ruc', 'La segunda Etapa no puede mandar vacio');
                // }
                // if(!request()->{"stage_id".$value} || !request()->{"cantidad_controlada".$value})
                // {
                //     $validator->errors()->add('ruc', 'La tercera Etapa no puede mandar vacio');
                // }
            }
        });
    }
}
