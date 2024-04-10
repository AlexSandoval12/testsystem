<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateSalesOpportunityRequest extends FormRequest
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
            'branch_id'             => 'required',
            'contact_medium_id'       => 'required',
            'fullname'              => 'required',
            'email'                 => 'nullable|email'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if(!request()->phone && !request()->email)
            {
                $validator->errors()->add('phone', 'El campo teléfono o email deben ser completados.');
            }

            if (!request()->seller_id && !request()->teams)
            {
                $validator->errors()->add('seller','Debe elegir Equipo o Vendedor, seleccionar solo uno.');
            }

            if (request()->seller_id && request()->teams)
            {
                $validator->errors()->add('teams','No puede elegir Equipo y Vendedor, seleccionar solo uno.');
            }

        });
    }
}
