<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;

class CreateClienteRequest extends FormRequest
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
            if(request()->ci)
            {
                $client = Client::where('ci',request()->ci)->first();
                if($client)
                {
                    $validator->errors()->add('ci', 'Numero de Cedula ya fue registrada');
                }
            }
        });
    }
}
