<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PurchasesProductInventory;
use App\Models\Deposit;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;

class CreatePurchasesProductInventoriesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'deposit_id'       => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) 
        {
            // Validar que haya productos
            $count=0;
            $count_price=0;
            foreach(request()->product_id AS $product_id => $quantity)
            {
                if($quantity != '')
                {
                    $count++;
                    // if($product_id == 865)
                    // {
                    //     Log::info(request()->old_existences);

                    // }
                    // if($quantity != request()->old_existences[$product_id])
                    // {
                    //     if($quantity > request()->old_existences[$product_id])
                    //     {
                    //         if(request()->old_cost_product[$product_id] == '' or request()->old_cost_product[$product_id] == '0') 
                    //         {
                    //             $count_price++;
                    //         }
                    //     }                        
                    // }
                }                
            }

            if($count==0)
            {
                $validator->errors()->add('total_payment_method', 'No existe producto seleccionado!!!');
            }

            // if($count_price>0)
            // {
            //     $validator->errors()->add('total_payment_method', 'Debe especificar el costo del producto!!!');
            // }  

            $purchases_product_inventory = Inventory::where('deposit_id',request()->deposit_id)
                 ->where('status', 2)->first();

            if($purchases_product_inventory)     
            {
                $validator->errors()->add('deposit_id', 'Existe un inventario en proceso del deposito '. Deposit::findOrFail(request()->deposit_id)->name);
            }
        });
    }
}
