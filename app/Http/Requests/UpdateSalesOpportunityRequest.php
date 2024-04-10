<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesOpportunityRequest extends FormRequest
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
            'enterprise_id'         => 'required',
            'half_contact_id'       => 'required',
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
        });
    }
}
