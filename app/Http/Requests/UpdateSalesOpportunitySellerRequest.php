<?php

namespace App\Http\Requests;

use App\Models\SalesOpportunity;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesOpportunitySellerRequest extends FormRequest
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
        if(!auth()->user()->can('denpro-seller-opportunities.create'))
        {
            return [
                'half_contact_id'       => 'required',
                'fullname'              => 'required',
                'email'                 => 'nullable|email',
                'product'               => 'sometimes',
                'product_amount'        => 'required_with:product',
                'contact_name.*'        => 'required',
                'contact_prefix,*'      => 'required',
                'contact_number,*'      => 'required',
                'message_id'            => 'required_if:half_contact_id,45'
            ];
        }
        else
        {
            return [
                'fullname'              => 'required',
                'dental_office'         => 'required',
                'phone'                 => 'required',
                'prefix_id'             => 'required',
                'type_number'           => 'required',
                'city_id'               => 'required',
                'address'               => 'required',
                'observation'           => 'required'
            ];
        }
    }

    public function messages()
    {
        return [
            'contact_name.*.required'     => 'El campo nombre de contacto es obligatorio.',
            'contact_prefix.*.required'   => 'El campo prefijo es obligatorio.',
            'contact_number.*.required'   => 'El campo número de teléfono es obligatorio.',

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if(!request()->phone && !request()->email && !auth()->user()->can('denpro-seller-opportunities.create'))
            {
                $validator->errors()->add('phone', 'El campo teléfono o email deben ser completados.');
            }

            if (request()->half_contact_id == 45 && request()->message_id)
            {
                $sales_opportunity = SalesOpportunity::where('message_id', request()->message_id)
                                                    ->where('id', '<>', $this->route('opportunity')->id)
                                                    ->where('status', '<>', 30)
                                                    ->first();
                if ($sales_opportunity)
                {
                    $validator->errors()->add('sales_opportunity', 'Ya existe oportunidad de venta con el mismo THINKCHAT ID CHAT.');
                }
            }
        });
    }
}
