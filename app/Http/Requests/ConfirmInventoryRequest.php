<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmInventoryRequest extends FormRequest
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
            //
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) 
        {
            // $count = 0;
            // $product_name = '';
            // foreach ($this->route('inventories')->purchases_product_inventory_details as $key => $detail)
            // {
            //     if ($detail->existence != $detail->quantity && (!$detail->old_cost || $detail->old_cost == 0))
            //     {
            //         $count++;
            //         $product_name = $detail->purchases_product->name;
            //         $validator->errors()->add('total_payment_method', 'Debe especificar el costo del producto '.$detail->purchases_product->name.'!!!');
            //     }
            // }

            // if ($count > 0)
            // {
            //     toastr()->warning('Es necesario actualizar costo del producto '.$product_name);
            // }   
        });
    }
}
