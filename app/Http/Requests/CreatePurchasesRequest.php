<?php

namespace App\Http\Requests;

use App\Models\AccountingClosing;
use App\Models\CashBoxDetail;
use App\Models\Purchase;
use App\Models\PurchasesProduct;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreatePurchasesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type'                  => 'required',
            'branch_id'             => 'required',
            'stamped'               => 'required_if:type, 1|required_if:type, 4',
            'payment_amount.*'      => 'required_if:type, 2|required_if:condition, 2',
            'condition'             => 'required',
            'date'                  => 'required|date_format:d/m/Y',
            'purchases_provider_id' => 'required',
            'razon_social'          => 'required',
            'ruc'                   => 'required',
            'total_product'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'stamped.required_if'      => 'El campo :attribute es obligatorio.',
            'number.required_if'       => 'El campo :attribute es obligatorio.',
            'payment_amount.*.required_if' => 'El campo :attribute es obligatorio.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request()->date)
            {
                if (check_date(request()->date))
                {
                    $dat = Carbon::createFromFormat('d/m/Y',request()->date)->format('Y-m-d');

                        if(request()->type == 4)
                        {
                            if(!request()->invoice_id)
                            {
                                $validator->errors()->add('total_payment_method', 'Si es Nota de Crédito debe especificar la Factura.');
                            }
                        }

                        if(request()->date)
                        {
                            $date = Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d');

                            if($date > date('Y-m-d'))
                            {
                                $validator->errors()->add('total_payment_method', 'La Fecha de la Compra no puede superar la Fecha Actual.');
                            }

                            if(request()->stamped_validity)
                            {
                                if(check_date(request()->stamped_validity))
                                {
                                    $stamped_validity = Carbon::createFromFormat('d/m/Y', request()->stamped_validity)->format('Y-m-d');
                                    
                                    if($stamped_validity < $date)
                                    {
                                        $validator->errors()->add('total_payment_method', 'La Fecha vigencia de timbrado no puede ser menor a la Fecha compra.');
                                    }
                                }
                            }
                        }

                        if (request()->detail_product_id)
                        {
                            foreach (request()->detail_product_id as $detail_product_key => $product_id)
                            {
                                $diff = (floatval(request()->detail_product_quantity[$detail_product_key]) * floatval(cleartStringNumber(request()->detail_product_amount[$detail_product_key]))) - floatval(floatVal(cleartStringNumber(request()->detail_total_iva10[$detail_product_key])) + floatVal(cleartStringNumber(request()->detail_total_iva5[$detail_product_key])) + floatval(cleartStringNumber(request()->detail_total_excenta[$detail_product_key])));
                                if ($diff > 1 || $diff < -1)
                                {
                                    $validator->errors()->add('detail_product_amount', 'Subtotal del producto '.request()->detail_product_name[$detail_product_key].' no es correcto.');
                                }
                            }
                        }

                        if(cleartStringNumber(request()->total_product) == 0)
                        {
                            $validator->errors()->add('total_invoice', 'El Total de la Compra no puede ser 0.');
                        }

                        $count_purchases = Purchase::where('provider_id', request()->purchases_provider_id)
                                                    ->where('number', request()->number)
                                                    ->where('stamped', request()->stamped)
                                                    ->where('type', request()->type)
                                                    ->where('status', '<', 2)
                                                    ->count();
                        if($count_purchases > 0)
                        {
                            $validator->errors()->add('voucher_number', 'El número ya existe en la base de datos.');
                        }                  
                                        
                        if(request()->type_payment == 2)
                        {
                            if(sum_array(request()->amount_treasury) == 0)
                            {
                                $validator->errors()->add('total_invoice', 'El Total de pago a Tesoreria no puede ser cero.');
                            }
                            if (request()->amount_treasury)
                            {
                                $diff = sum_array(request()->amount_treasury) - floatval(cleartStringNumber(request()->total_product));
                                if($diff > 1 || $diff < -1)
                                {
                                    $validator->errors()->add('total_invoice', 'El Total de pago a Tesoreria no puede ser mayor a la Compra.');
                                }
                            }
                        }
                        if (sum_array(request()->amount_treasury) > 0)
                        {
                            $diff = (sum_array(request()->amount_treasury)) - floatval(cleartStringNumber(request()->total_product));
                            if($diff > 1 || $diff < -1)
                            {
                                $validator->errors()->add('total_invoice', 'Diferencias en TESORERIA y TOTAL DE COMPRA.');
                            }

                            if (request()->expiration)
                            {
                                foreach (request()->expiration as $key => $expiration)
                                {
                                    if (!check_date($expiration))
                                    {
                                        $validator->errors()->add('expiration', 'Formato incorrecto del campo FECHA PAGO.');
                                    }
                                }
                            }
                        }

                        if(isset(request()->receipts) && cleartStringNumber(request()->total_product) != sum_array(request()->receipts)) 
                        {
                            $validator->errors()->add('receipts', 'Diferencias en Total de Recibos y Total de Compra.');
                        }

                        if(request()->type)
                        {
                            if (in_array(request()->type, [1,4]))
                            {
                                if (strlen(request()->stamped) != 8)
                                {
                                    $validator->errors()->add('stamped', 'Cantidad de digitos del timbrado no es valido.');
                                }
                                if(!check_date(request()->stamped_validity) && request()->stamped_validity)
                                {
                                    $validator->errors()->add('stamped_validity', 'Formato del campo VIGENCIA DE TIMBRADO debe ser d/m/Y');
                                }
                            }
                        }
                }
            }
        });
    }
}
