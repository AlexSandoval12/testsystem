<?php

namespace App\Http\Requests;

use App\Models\AccountingClosing;
use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\PurchaseMovements;
use App\Models\PurchasesProduct;
use App\Models\PurchasesProductInventory;
use App\Models\RawMaterial;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreatePurchasesMovementsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'social_reason_id' => 'required_if:type_movement,1',
            'number_oc'        => 'required_if:type_movement,1',
            'deposits_id'      => 'required',
            'exit_motive_id'   => 'required_if:type_movement,2',
            'recived_person'   => 'required_if:type_movement,2',
            'invoice_stamped'  => 'nullable|numeric',
            'expiration.*'     => 'required',
            'payment_amount.*' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'invoice_stamped.numeric'   => "Timbrado debe ser numérico.",
            'expiration.*.required'     => "El campo FECHA PAGO es requerido.",
            'payment_amount.*.required' => "El campo CUOTA es requerido."
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request()->deposits_id)
            {
                $deposit = Deposit::findOrFail(request()->deposits_id);
                // $accounting_closing = AccountingClosing::where('from_date','<=',  date('Y-m-d'))->where('until_date','>=', date('Y-m-d'))->where('social_reason_id', $deposit->enterprise->social_reason_id)->where('stock_status',1)->where('status',1)->first();

                if($deposit)
                {   
                    if (request()->purchases_provider_id && request()->invoice_number && request()->stamped)
                    {
                        $count_purchases = Purchase::where('provider_id', request()->purchases_provider_id)//ese es el modelo de FACTURA COMPRA (PURCHASE) tenemos que crear todo ese verdad la tabla,etc y si aun no existe la tavla si
                                                    ->where('number', request()->invoice_number)
                                                    ->where('stamped', request()->stamped)
                                                    ->where('type', 1)
                                                    ->whereIn('status', [1,3,4])
                                                    ->count();
                        if($count_purchases > 0 && !request()->purchase_id)
                        {
                            $validator->errors()->add('voucher_number', 'El NUMERO DE FACTURA ya existe en la base de datos.');
                        }
                    }

                    if(request()->branch_id == '')
                    {
                        $validator->errors()->add('total_invoice', 'El campo SUCURSAL no puede quedar vacio.');

                    }

                    if(request()->invoice_number == '')
                    {
                        $validator->errors()->add('total_invoice', 'El campo NUMERO DE FACTURA no puede quedar vacio.');

                    }

                    if(request()->date == '')
                    {
                        $validator->errors()->add('total_invoice', 'El campo FECHA DE FACTURA no puede quedar vacio.');

                    }

                    if(request()->stamped == '')
                    {
                        $validator->errors()->add('total_invoice', 'El campo NRO. DE TIMBRADO no puede quedar vacio.');

                    }

                    if(request()->stamped_validity == '')
                    {
                        $validator->errors()->add('total_invoice', 'El campo VIGENCIA DE TIMBRADO no puede quedar vacio.');

                    }

                    if (request()->type_movement == 1) 
                    {
                        $conteo=0;
                        foreach (request()->detail_product_id as $key => $value)
                        {
                            if(request()->detail_product_quantity[$key] != '' OR request()->detail_product_quantity[$key] != '0')
                            {
                                $conteo++;
                            }

                            if(request()->detail_product_quantity[$key] > request()->quantity_product[$key])
                            {
                                $validator->errors()->add('total_invoice', 'El campo A recibir no puede ser mayor al campo cantidad.');
                            }
                        }

                        if($conteo==0)
                        {
                            $validator->errors()->add('total_invoice', 'No se registro producto recepcionado');
                        }
                    }

                    if(!request()->detail_invoice_product_ids)
                    {
                        $validator->errors()->add('total_invoice', 'El detalle de la Factura no puede quedar vacio.');
                    }
                    else
                    {
                        foreach (request()->detail_product_quantity as $key => $value)
                        {
                            if ($value)
                            {
                                if ($value > request()->detail_pending_reception[$key])
                                {
                                    $validator->errors()->add('detail_pending_reception', 'Cantidad recibida del producto '.request()->detail_product_name[$key].' supera saldo.');
                                }

                                if(!in_array(request()->detail_product_id[$key], request()->detail_invoice_product_ids))
                                {
                                    $validator->errors()->add('detail_invoice_product_ids', 'El producto '.request()->detail_product_name[$key].' no existe en el detalle de la factura');
                                }
                            }
                        }
                        
                        foreach (request()->order_detail_id as $invoice_key => $order_detail_id)
                        {
                            if(!in_array($order_detail_id, request()->detail_id))
                            {
                                $validator->errors()->add('detail_id', 'El producto '.request()->detail_descriptions[$invoice_key].' no se cargo cantidad a recepcionar');
                            }

                            if (request()->detail_quantities[$invoice_key] != request()->detail_product_quantity[array_search($order_detail_id,request()->detail_id)])
                            {
                                $validator->errors()->add('detail_quantities', 'La cantidad del producto '.request()->detail_descriptions[$invoice_key].' no coincide con la cantidad a recepcionar.');
                            }

                            if (request()->detail_price[$invoice_key] != request()->detail_product_amount[array_search($order_detail_id,request()->detail_id)])
                            {
                                $validator->errors()->add('detail_price', 'El Precio del producto '.request()->detail_descriptions[$invoice_key].' no coincide con el precio a recepcionar.');
                            }

                            if (($this->parse(request()->detail_amounts[$invoice_key]) + $this->parse(request()->detail_amounts_5[$invoice_key]) + $this->parse(request()->detail_amounts_exenta[$invoice_key])) != $this->parse(request()->detail_total_amount[$invoice_key]))
                            {
                                $validator->errors()->add('subtotal', 'Importe grabada del producto '. request()->detail_descriptions[$invoice_key] .' no coincide con subtotal.');
                            }
                        }
                        // $purchases_product_inventory = PurchasesProductInventory::where('deposit_id', request()->deposits_id)->where('status', 2)->first();

                        // if($purchases_product_inventory)     
                        // {
                        //     $validator->errors()->add('deposit_id', 'Existe un inventario en proceso del deposito '. $deposit->name);
                        // }
                    }

                    if (request()->payment_amount)
                    {
                        $total_quota = 0;
                        foreach (request()->payment_amount as $key => $payment_amount)
                        {
                            $total_quota += $this->parse($payment_amount);
                        }

                        if ($this->parse(request()->total_invoice) != $total_quota)
                        {
                            $validator->errors()->add('total_quota', 'TOTAL CUOTA no es igual a TOTAL FACTURA.');
                        }
                    }
                    // if (request()->expiration)
                    // {
                    //     foreach (request()->expiration as $expiration_key => $expiration)
                    //     {
                    //         if (check_date($expiration))
                    //         {
                    //             if (Carbon::createFromFormat('d/m/Y', $expiration)->isSunday())
                    //             {
                    //                 $validator->errors()->add('expiration', 'La fecha '.$expiration.' es domingo.');
                    //             }
                    //         }
                    //         else
                    //         {
                    //             $validator->errors()->add('expiration', 'FECHA DE PAGO no tiene el formato requerido DD/MM/YYYY');
                    //         }
                    //     }
                    // }

                    if(request()->detail_quantities && request()->detail_price && request()->total_invoice)
                    {
                        $calc = 0;
                        $total = 0;
                        foreach(request()->detail_quantities as $key => $value) 
                        {
                            if(isset(request()->detail_price[$key]))
                            {
                                $calc = round($value * request()->detail_price[$key]); 
                                $total+= $calc; 
                            }
                        }
                        if(round($this->parse(request()->total_invoice)) > $total)
                        {
                            $validator->errors()->add('total_invoice', 'El Total Factura supera a la Orden de Compra');
                        }
                    }

                    foreach (request()->detail_product_id as $key => $value) 
                    {
                        $purchases_product = RawMaterial::find($value);

                        if($purchases_product->requires_expiration)
                        {
                            if(isset(request()->{'exp_product_quantity_'.$value}) && array_sum(request()->{'exp_product_quantity_'.$value}) != intval(request()->detail_product_quantity[$key]))
                            {
                                $validator->errors()->add('exp_product_quantity', 'La Cantidad en Vencimientos del Producto: ' . $purchases_product->name . ' esta mal configurado');
                            }
                            //VERIFICAR SI EL PRODUCTO ES STOCKEABLE Y SI NO AGREGO UN VENCIMIENTO
                            if(!isset(request()->{'exp_date_'.$value}))
                            {
                                $validator->errors()->add('exp_date_', 'Debe agregar un vencimiento al Producto: ' . $purchases_product->name);   
                            }
    
                            //VERIFICAR SI NO SE CARGARON VALORES NULOS
                            if(isset(request()->{'exp_date_'.$value}) && in_array(null, request()->{'exp_date_'.$value}, true))
                            {
                                $validator->errors()->add('null_exp_date_', 'Vencimientos del Producto: '.$purchases_product->name . ' estan mal configurados');   
                            }   
                        }
                    }
                }
                else
                {
                    $validator->errors()->add('closing', 'Cierre contable vigente');
                }
            }

            // dd(request()->all());

        });
    }

    private function parse($value)
    {
        return intVal(str_replace(',', '.',str_replace('.', '', $value)));
    }
}
