<?php
namespace App\Services;

use App\Jobs\AccountingMovementsJob;
use App\Models\AccountingPlanUnion;
use App\Models\CalendarPayment;
use App\Models\PaymentServicesAuthorization;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseNoteCredit;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchasesAccountingPlan;
use App\Models\PurchasesCollect;
use App\Models\PurchasesDetail;
use App\Models\PurchasesOrderDetail;
use Carbon\Carbon;

class PurchasesService
{
	public function store($request, $purchase, $user_id)
	{
        if ($purchase)
        {
            $purchase->update([
                'condition'                 => $request->condition,
                'branch_id'                 => $request->branch_id,
                'number'                    => $request->number,
                'date'                      => $request->date,
                'change'                    => $request->change,
                'stamped'                   => $request->stamped,
                'stamped_validity'          => $request->stamped_validity ? ($request->electronic_document == 1 ? NULL : $request->stamped_validity   ) : NULL,
                'observation'               => $request->observation,
                'amount'                    => cleartStringNumber($request->total_product),
                'total_excenta'             => cleartStringNumber($request->total_excenta_final),
                'total_iva5'                => cleartStringNumber($request->total_iva5_final),
                'total_iva10'               => cleartStringNumber($request->total_iva10_final),
                'amount_iva5'               => cleartStringNumber($request->amount_iva5),
                'amount_iva10'              => cleartStringNumber($request->amount_iva10),
                'user_id'                   => $user_id,
                'status'                    => 1,
            ]);
            $purchase->purchases_details()->delete();
        }
        else
        {
            $purchase = Purchase::create([
                'date'                  => $request->date,
                'branch_id'             => $request->branch_id,
                'stamped'               => $request->stamped,
                'type'                  => $request->type,
                'condition'             => $request->condition,
                'number'                => $request->number,
                'provider_id'           => $request->purchases_provider_id,
                'razon_social'          => $request->razon_social,
                'ruc'                   => $request->ruc,
                'phone'                 => $request->phone,
                'address'               => $request->address,
                'observation'           => $request->observation,
                'stamped_validity'      => $request->stamped_validity ? ($request->electronic_document == 1 ? NULL : $request->stamped_validity   ) : NULL,
                'amount'                => cleartStringNumber($request->total_product),
                'total_excenta'         => cleartStringNumber($request->total_excenta_final),
                'total_iva5'            => cleartStringNumber($request->total_iva5_final),
                'total_iva10'           => cleartStringNumber($request->total_iva10_final),
                'amount_iva5'           => cleartStringNumber($request->amount_iva5),
                'amount_iva10'          => cleartStringNumber($request->amount_iva10),
                'change'                => $request->change,
                'status'                => true,
                'user_id'               => $user_id,
            ]);
        }

        // Nota de Credito
        if ($request->type == 4)
        {
            $amount = cleartStringNumber($request->total_product);

            PurchaseNoteCredit::create([
                'purchase_id'         => $purchase->id,
                'purchase_invoice_id' => $request->invoice_id
            ]);

            // Buscar las Facturas que esten en Tesoreria
            // $purchases_pendings = PurchasesCollect::with('purchase.purchases_accounting_plans')
            //     ->where('purchase_id', $request->invoice_id)
            //     ->where('residue', '>', 0)
            //     ->orderBy('expiration')
            //     ->get();
            // foreach ($purchases_pendings as $pendings)
            // {
            //     if ($amount > 0)
            //     {
            //         $amount_final = $amount > $pendings->residue ? $pendings->residue : $amount;

            //         $purchase->purchases_collect_payments()->create([
            //             'purchases_collect_id' => $pendings->id,
            //             'amount'               => $amount_final
            //         ]);

            //         $pendings->decrement('residue', $amount_final);

            //         $amount = $amount - $amount_final;
            //     }
            // }
        }

        foreach($request->detail_product_id as $key => $value)
        {
            $amount_product = cleartStringNumber($request->detail_product_amount[$key]);
            $purchase_detail = PurchaseDetail::create([
                'purchase_id'               => $purchase->id,
                'material_id'      => $request->detail_product_id[$key],
                'purchases_order_detail_id' => $request->detail_product_orders_id[$key] ? $request->detail_product_orders_id[$key] : NULL,
                'description'               => $request->detail_product_name[$key],
                'quantity'                  => $request->detail_product_quantity[$key],
                'amount'                    => $amount_product,
                'excenta'                   => cleartStringNumber($request->detail_total_excenta[$key]),
                'iva5'                      => cleartStringNumber($request->detail_total_iva5[$key]),
                'iva10'                     => cleartStringNumber($request->detail_total_iva10[$key])
            ]);

            if ($request->detail_product_orders_id[$key])
            {
                $purchases_order_detail = PurchaseOrderDetail::find($request->detail_product_orders_id[$key]);
                $purchases_order_detail->decrement('residue', $request->detail_product_quantity[$key]);
            }

            //SI PUEDE EDITAR MALLA CONTABLE DE PRODUCTOS
            // if (auth()->user()->can('purchases-products.accounting-entries'))
            // {
            //     $accounting_plan_union = AccountingPlanUnion::where('social_reason_id', $request->social_reason_id)
            //                                                 ->where('accountable_type', 'App\\Models\\PurchasesProduct')
            //                                                 ->where('accountable_id', $request->detail_product_id[$key])
            //                                                 ->first();
            //     if (!$accounting_plan_union)
            //     {
            //         AccountingPlanUnion::create([
            //                                         'accountable_id'     => $request->detail_product_id[$key],
            //                                         'accountable_type'   => 'App\Models\PurchasesProduct',
            //                                         'social_reason_id'   => $request->social_reason_id,
            //                                         'accounting_plan_id' => $request->detail_accounting_plan[$key]
            //                                     ]);
            //     }
            //     //SI EL PRODUCTO ES STOCKEABLE
            //     if ($purchase_detail->purchases_product->purchases_category->stockeable)
            //     {
            //         $accounting_plan_union = AccountingPlanUnion::where('social_reason_id', $request->social_reason_id)
            //                                                     ->where('accountable_type', 'App\\Models\\PurchasesProduct_Stock')
            //                                                     ->where('accountable_id', $request->detail_product_id[$key])
            //                                                     ->first();
            //         if (!$accounting_plan_union)
            //         {
            //             AccountingPlanUnion::create([
            //                                             'accountable_id'     => $request->detail_product_id[$key],
            //                                             'accountable_type'   => 'App\Models\PurchasesProduct_Stock',
            //                                             'social_reason_id'   => $request->social_reason_id,
            //                                             'accounting_plan_id' => $request->detail_baccounting_plan[$key]
            //                                         ]);
            //         }
            //     }
            // }
        }
        if(sum_array($request->amount_treasury) > 0)
        {
            foreach ($request->expiration as $expiration_key => $expiration) 
            {
                 $purchases_collect = PurchasesCollect::create([
                                         'purchase_id' => $purchase->id,
                                         'number'     => $expiration_key + 1,
                                         'expiration' => $expiration,
                                         'amount'     => cleartStringNumber($request->amount_treasury[$expiration_key]),
                                         'residue'    => cleartStringNumber($request->amount_treasury[$expiration_key])
                                     ]);
                //SI ES PROVEEDORE
                CalendarPayment::create([
                    'date'                  =>  Carbon::createFromFormat('d/m/Y', $expiration)->format('Y-m-d'),
                    'purchase_id'           => $purchase->id,
                    'provider_id'           => $request->purchases_provider_id,
                    'description'           => $purchase->observation,
                    'amount'                => $request->amount_treasury[$expiration_key],
                    'user_id'               => auth()->user()->id,
                    'status'                => 1,
                    'purchase_collect_id'   => $purchases_collect->id
                ]);                
            }
        }

        // GENERAR ASIENTOS CONTABLES
        // AccountingMovementsJob::dispatch(4, $purchase->id, $purchase->user_id);

        return $purchase;
	}

    private function parse($value)
    {
        return str_replace(',', '.',str_replace('.', '', $value));
    }
}