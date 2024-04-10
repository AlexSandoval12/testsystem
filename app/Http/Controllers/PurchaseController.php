<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchasesRequest;
use App\Http\Requests\DeletePurchasesRequest;
use App\Http\Requests\UpdatePurchasesRequest;
use App\Jobs\AccountingMovementsJob;
use App\Models\AccountingEntry;
use App\Models\AccountingPlan;
use App\Models\Branch;
use App\Models\CalendarPayment;
use App\Models\CashBox;
use App\Models\CashBoxDetail;
use App\Models\CostCenter;
use App\Models\Currency;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchasesAccountingPlan;
use App\Models\PurchasesCollectPayment;
use App\Models\PurchasesCostCenter;
use App\Models\PurchasesDetail;
use App\Models\PurchasesNoteCredit;
use App\Models\PurchasesOrderDetail;
use App\Models\PurchasesProvider;
use App\Models\PurchasesCollect;
use App\Models\SocialReason;
use App\Services\PurchasesService;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;


class PurchaseController extends Controller
{
    public function index()
    {
        $providers = Provider::Filter();
        $purchases           = Purchase::with('provider')
            ->where('type', '!=', 5)
            ->orderBy('id', 'desc');
        if (request()->s)
        {
            $purchases = $purchases->where('razon_social', 'LIKE', '%' . request()->s . '%')
                ->orWhere('ruc', 'LIKE', '%' . request()->s . '%')
                ->orWhere('number', 'LIKE', '%' . request()->s . '%');
        }

        if (request()->invoice_copy)
        {
            $purchases = $purchases->where('invoice_copy', request()->invoice_copy);
        }

        if (request()->provider_id)
        {
            $purchases = $purchases->where('provider_id', request()->provider_id);
        }
        $purchases = $purchases->whereIn('status', [1,2])->paginate(20);
        
        return view('pages.purchase.index', compact('purchases', 'providers'));
    }

    public function create()
    {
        $branches          = Branch::GetAllCached()->sortBy('name')->pluck('name', 'id');
        // $cash_boxes        = CashBox::where('status', true)->where('small_box', true)->pluck('name', 'id');

        $invoice_copy = config('constants.invoice_copy');
        unset($invoice_copy[0]);

        return view('pages.purchase.create', compact('branches', 'invoice_copy'));
    }

    public function store(CreatePurchasesRequest $request)
    {
        if (request()->ajax())
        {
            DB::transaction(function () use ($request, &$purchase)
            {
                $purchase = (New PurchasesService)->store($request, null, auth()->user()->id);

                toastr()->success('Agregado exitosamente');
            });

            return response()->json([
                'success'     => true,
                'purchase_id' => $purchase->id
            ]);
        }
        abort(404);
    }

    public function edit(Purchase $purchase)
    {
        $branches          = Branch::where('status', true)->pluck('name', 'id');
        $cost_centers      = CostCenter::where('social_reason_id', $purchase->social_reason_id)
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $invoice_copy      = config('constants.invoice_copy');
        unset($invoice_copy[0]);
        $cash_boxes        = CashBox::where('status', true)->where('small_box', true)->pluck('name', 'id');
        $purchases_accounting_plans = PurchasesAccountingPlan::where('purchase_id', $purchase->id)->where('type', 0)->get();
        $purchases_accounting_plan  = PurchasesAccountingPlan::where('purchase_id', $purchase->id)->where('type', 1)->first();
        $provider_accounting_plan_id = null;
        $accounting_plan_union = $purchase->purchases_provider->accounting_plan_unions->where('social_reason_id', $purchase->social_reason_id)->first();
        if ($accounting_plan_union)
        {
            $provider_accounting_plan_id =  $accounting_plan_union->accounting_plan_id;
        }
        $array_accounting_plans = AccountingPlan::orderBy('number')
                                             ->orderBy('name')
                                             ->where('social_reason_id', $purchase->social_reason_id)
                                             ->where('setteable', true)
                                             ->get()->pluck('fullname', 'id');

        return view('pages.purchases.edit', compact('purchase', 'cost_centers', 'branches', 'invoice_copy', 'cash_boxes', 'purchases_accounting_plans', 'purchases_accounting_plan', 'array_accounting_plans', 'provider_accounting_plan_id'));
    }

    public function update(UpdatePurchasesRequest $request, Purchase $purchase)
    {
        if (request()->ajax())
        {
            DB::transaction(function () use ($request, $purchase)
            {
                $purchase->update([
                    'branch_id'             => $request->branch_id,
                    'received_user_id'      => auth()->user()->id,
                    'received_date'         => date('Y-m-d H:i:s'),
                    'condition'             => $request->condition,
                    'stamped'               => $request->stamped,
                    'stamped_validity'      => $request->stamped_validity,
                    'observation'           => $request->observation,
                    'cash_box_id'           => $request->cash_box_id
                ]);

                // //Agendar Pago
                // if(in_array($request->type, [1,3])) 
                // {

                //     $old_calendar_payment = CalendarPayment::where('status', 1)
                //         ->where('purchase_id', $purchase->id)
                //         ->where('purchases_provider_id', $purchase->purchases_provider_id)
                //         ->first();

                //     if($old_calendar_payment) $old_calendar_payment->update(['status' => 7, 'user_rescheduled_id' => auth()->user()->id]);

                //     if($request->expiration) 
                //     {
                //         CalendarPayment::create([
                //             'social_reason_id' => $purchase->social_reason_id,
                //             'date'             => $request->expiration,
                //             'type_account'     => 3, //Porveedores
                //             'type_scheduler'   => 5, //Pago de factura
                //             'purchase_id'      => $purchase->id,
                //             'purchases_provider_id' => $purchase->purchases_provider_id,
                //             'description'      => ' - ',
                //             'amount'           => cleartStringNumber($purchase->amount),
                //             'currency_id'      => $purchase->currency_id,
                //             'last_calendar_payment_id' => $old_calendar_payment ? $old_calendar_payment->id : null,
                //             'user_id'          => auth()->user()->id,
                //             'status'           => 1
                //         ]);
                //     }                    
                // }

                if($request->invoice_copy)
                {
                    $purchase->update(['invoice_copy' => $request->invoice_copy]);
                }

                foreach ($request->detail_purchase_detail_id as $key => $value)
                {
                    $purchases_detail = PurchasesDetail::find($value);

                    if ($purchases_detail)
                    {
                        $purchases_detail->update(['accounting_plan_id' => $request->detail_accounting_plan[$key]]);
                    }
                }

                foreach ($request->detail_cost_center_id as $key => $value)
                {
                    $purchases_cost_center = PurchasesCostCenter::find($value);

                    if ($purchases_cost_center)
                    {
                        $purchases_cost_center->update(['cost_center_id' => $request->detail_cost_center[$key]]);
                    }
                }

                //Se instancia para verificar si cuenta con accounting plan / purchase collect
                $purchases_accounting_plans = PurchasesAccountingPlan::where('purchase_id', $purchase->id)->delete();

                if ($request->purchases_collect_id)
                {
                    foreach ($request->purchases_collect_id as $collect_key => $collect_id)
                    {
                        $purchases_collect =  PurchasesCollect::where('id', $collect_id)->whereRaw('amount = residue')->first();
                        if ($purchases_collect)
                        {
                            $purchases_collect->update(['expiration' => $request->expiration[$collect_key]]);

                            $collect_payment_amount = PurchasesCollectPayment::whereHas('purchase', function($query){ $query->where('status', 1); })
                                                                        ->where('purchases_collect_id', $purchases_collect->id)->sum('amount');
                            $purchases_collect->update([
                                                        'amount'  => cleartStringNumber($request->amount_treasury[$collect_key]),
                                                        'residue' => cleartStringNumber($request->amount_treasury[$collect_key]) - $collect_payment_amount
                                                       ]);

                            CalendarPayment::where('purchases_collect_id', $purchases_collect->id)
                                            ->whereIn('status', [1,3])
                                            ->update([
                                                    'date'   => Carbon::createFromFormat('d/m/Y', $request->expiration[$collect_key])->format('Y-m-d'),
                                                    'amount' => cleartStringNumber($request->amount_treasury[$collect_key])
                                                    ]);
                        }
                    }

                    $purchases_collects = PurchasesCollect::where('purchase_id', $purchase->id)->whereRaw('amount = residue')->get();
                }


                $purchase->purchases_accounting_plans()->create([
                    'accounting_plan_id' => $request->accounting_account_provider_id,
                    'amount'             => PurchasesCollect::where('purchase_id', $purchase->id)->sum('amount'),
                    'type'               => true
                ]);

                // if ($request->amount_treasury > 0)
                // {
                //     $total_amount_treasury = $request->amount_treasury;
                //     $expiration = $request->condition == 1 ? $request->date : $request->expiration;

                //     foreach ($purchases_collects as $collect_key => $purchase_collect)
                //     {
                //         $collect_amount = $total_amount_treasury > 0 ? ($purchase_collect->amount > $total_amount_treasury ? $total_amount_treasury : $purchase_collect->amount) : 0;
                //         if ($collect_amount > 0)
                //         {
                //             $purchase_collect->update(['amount' => $collect_amount, 'residue' => $collect_amount]);
                //             $total_amount_treasury -= $collect_amount;
                //         }
                //         $collect_payment_amount = PurchasesCollectPayment::whereHas('purchase', function($query){ $query->where('status', 1); })
                //                                                     ->where('purchases_collect_id', $purchase_collect->id)->sum('amount');
                //         $purchase_collect->update(['residue' => intVal($purchase_collect->amount - $collect_payment_amount)]);
                //     }

                //     $purchase->purchases_accounting_plans()->create([
                //         'accounting_plan_id' => $request->accounting_account_provider_id,
                //         'amount'             => $request->amount_treasury,
                //         'type'               => true
                //     ]);
                // }

                if (isset($request->detail_other_accounting_account_id))
                {
                    foreach ($request->detail_other_accounting_account_id as $key => $value)
                    {
                        $purchase->purchases_accounting_plans()->create([
                            'accounting_plan_id' => $value,
                            'amount'             => $request->detail_other_accounting_account_amount[$key],
                            'type'               => false
                        ]);
                    }

                    // Descontar los Anticipos del Proveedor
                    if ($request->advances_providers_check)
                    {
                        $data_filter = '';
                        foreach ($request->advances_providers_check as $key => $value)
                        {
                            $data_filter = $data_filter . $value . ',';
                        }

                        $advances = Purchase::where('status', true)
                            ->where('type',  5)
                            ->where('purchases_provider_id', $request->purchases_provider_id)
                            ->where('social_reason_id', $request->social_reason_id)
                            ->where('advance', ">", 0)
                            ->whereIn('id', [$data_filter])
                            ->get();
                    }
                    else
                    {
                        $advances = Purchase::where('status', true)
                            ->where('type',  5)
                            ->where('purchases_provider_id', $request->purchases_provider_id)
                            ->where('social_reason_id', $request->social_reason_id)
                            ->where('advance', ">", 0)
                            ->get();
                    }

                    $residue_advance = $request->total_not_payment;
                    foreach ($advances as $advance)
                    {
                        if ($residue_advance > 0)
                        {
                            $amount = $advance->advance < $residue_advance ? $advance->advance : $residue_advance;

                            $purchase->purchases_advance()->create([
                                'purchase_advance_id' => $advance->id,
                                'amount'              => $amount
                            ]);

                            $advance->decrement('advance', $amount);

                            $residue_advance = $residue_advance - $amount;
                        }
                    }
                }

                // Actulizar el Asiento Contable
                $accounting_entry = AccountingEntry::where([
                    'fromable_type' => 'App\Models\Purchase',
                    'fromable_id'   => $purchase->id
                ])->first();
                
                if ($accounting_entry)
                {
                    AccountingMovementsJob::dispatch(37, $purchase->id, $purchase->user_id);
                }

                toastr()->success('Editado exitosamente');
            });

            return response()->json(['succes' => 'true']);
        }
        abort(404);
    }

    public function show(Purchase $purchase)
    {
        return view('pages.purchase.show', compact('purchase'));
    }

    public function pdf(Purchase $purchase)
    {
        return PDF::loadView('pages.purchase.pdf', compact('purchase'))
            ->setPaper('A4', 'portrait')
            ->stream();
    }

    public function delete(Purchase $purchase)
    {
        return view('pages.purchase.delete', compact('purchase'));
    }

    public function delete_submit(DeletePurchasesRequest $request, Purchase $purchase)
    {
        DB::transaction(function () use ($purchase, $request)
        {
            $purchase->update([
                'status'         => 2,
                'date_deleted'   => date('Y-m-d H:i:s'),
                'reason_deleted' => $request->motive,
                'user_deleted'   => auth()->user()->id
            ]);

            foreach ($purchase->purchases_details as $details)
            {
                if ($details->purchases_order_detail)
                {
                    $purchases_order_detail = PurchasesOrderDetail::find($details->purchases_order_detail_id);
                    $total                  = $purchases_order_detail->quantity * $purchases_order_detail->amount;

                    if ($purchases_order_detail->amount_purchases != $total)
                    {
                        if ($purchases_order_detail->residue != $purchases_order_detail->quantity)
                        {
                            $details->purchases_order_detail->increment('residue', $details->quantity);
                        }
                    }
                }
            }

            foreach ($purchase->purchases_advance as $details)
            {
                Purchase::where('id', $details->purchase_advance_id)->increment('advance', $details->amount);
            }

            // MOVIMIENTO DE CAJA
            if ($purchase->cash_box_detail_id)
            {
                CashBoxDetail::where('id', $purchase->cash_box_detail_id)->update(['status' => 2]);
            }

            if ($purchase->payment_services_authorizations)
            {
                foreach ($purchase->payment_services_authorizations as $payment_service)
                {
                    $payment_service->update(['purchase_id' => null]);
                }
            }

            // GENERAR ASIENTOS CONTABLES
            AccountingMovementsJob::dispatch(6, $purchase->id, $purchase->user_id);

            toastr()->success('Eliminado exitosamente');
        });

        return redirect('purchases');
    }

    //CARGA DE FACTURAS DE PROVEEDORES
    public function provider_invoices_load()
    {
        $social_reasons    = SocialReason::GetAllCached()->pluck('razon_social', 'id');
        $currencies        = Currency::GetAllCached()->pluck('name', 'id');
        return view('pages.purchases.provider-invoice-load', compact('social_reasons', 'currencies'));
    }

    public function get_provider_ruc()
    {
        if (request()->ajax())
        {
            $data = explode("-", request()->ruc);
            if (array_key_exists(1, $data))
            {
                $provider = PurchasesProvider::where('ruc', $data[0])->where('dv', $data[1])->first();
                if ($provider)
                {
                    $products = $provider->products->pluck('name', 'id');
                    return response()->json(['success' => 'true', 'data' => $provider, 'products' => $products]);
                }
                else
                {
                    return response()->json(['success' => 'false']);
                }
            }
        }
        abort(404);
    }

    public function invoice_download(Purchase $purchase)
    {
        $path = storage_path('app/invoices/' . $purchase->file);

        if (request()->show)
        {
            $file = File::get($path);
            $mime_type = File::mimeType($path);

            $response = response()->make($file, 200);

            $response->header('Content-Type', $mime_type);

            return $response;
        }

        return response()->download($path);
    }

    public function ajax_purchases_note_credits()
    {
        if (request()->ajax())
        {
            $purchases = Purchase::where('type', 1)
                ->where('number', request()->q)
                ->where('provider_id', request()->purchases_provider_id)
                ->whereIn('status', [1, 4])
                ->get();
            $results = [];
            if ($purchases->count() > 0)
            {
                foreach ($purchases as $key => $purchase)
                {
                    $results['items'][$key]['id']        = $purchase->id;
                    $results['items'][$key]['text']      = $purchase->number;
                    $results['items'][$key]['total']     = $purchase->amount;
                    $results['items'][$key]['date']      = $purchase->date->format('d/m/Y');
                    $results['items'][$key]['condition'] = config('constants.invoice_condition.' . $purchase->condition);

                    foreach ($purchase->purchase_details as $key2 => $detail_products)
                    {
                        $results['items'][$key]['products'][$key2]['id']              = $detail_products->material_id;
                        $results['items'][$key]['products'][$key2]['name']            = $detail_products->material->description;
                        $results['items'][$key]['products'][$key2]['quantity']        = number_format($detail_products->quantity, 0, ',', '.');
                        $results['items'][$key]['products'][$key2]['amount']          = number_format($detail_products->amount, 0, ',', '.');
                        $results['items'][$key]['products'][$key2]['subtotal']        = number_format($detail_products->amount * $detail_products->quantity, 0, ',', '.');
                        $results['items'][$key]['products'][$key2]['excenta']         = number_format($detail_products->excenta, 0, ',', '.');
                        $results['items'][$key]['products'][$key2]['iva5']            = number_format($detail_products->iva5, 0, ',', '.');
                        $results['items'][$key]['products'][$key2]['iva10']           = number_format($detail_products->iva10, 0, ',', '.');
                    }
                }
            }
            return response()->json($results);
        }
        abort(404);
    }

    public function searchProviderStamped()
    {
        $invoice_number = explode('-', request()->purchase_number);
        $invoice_number = $invoice_number[0] . '-' . $invoice_number[1];

        $purchase = Purchase::select("purchases.*", DB::raw("DATE_FORMAT(stamped_validity, '%d/%m/%Y') stamp_validity"))
            ->where('purchases_provider_id', request()->provider_id)
            ->where('number', 'like', ['%'.$invoice_number.'%'])
            ->whereIn('status', [1,3,4])
            ->orderBy('id', 'DESC')
            ->first();

        return  response()->json($purchase);
    }

    public function ajax_purchases_invoice()
    {
        if(request()->ajax())
        {
            $results = [];

            if(request()->purchase_id)
            {
                $purchase_note_credit = PurchasesNoteCredit::where('purchase_id',request()->purchase_id)->first();

                if($purchase_note_credit)
                {
                    $key = 0;
                    $results['head'][$key]['invoice_number']  = $purchase_note_credit->purchase_invoice->number;
                    $results['head'][$key]['date']            = $purchase_note_credit->purchase_invoice->date->format('d/m/Y');
                    $results['head'][$key]['amount']          = number_format($purchase_note_credit->purchase_invoice->amount,0,',','.');
                    $results['head'][$key]['condition']       = config('constants.invoice_condition.'.$purchase_note_credit->purchase_invoice->condition);
                    foreach($purchase_note_credit->purchase_invoice->purchases_details as $details)
                    {
                        $key++;
                        $results['details'][$key]['id']             = $details->id;
                        $results['details'][$key]['cod']             = $details->purchases_product_id;
                        $results['details'][$key]['product_name']    = $details->purchases_product->name;
                        $results['details'][$key]['description']     = $details->description;
                        $results['details'][$key]['accounting_plan'] = $details->accounting_plan ? $details->accounting_plan->fullname : '';
                        $results['details'][$key]['quantity']        = number_format($details->quantity, 0, ',', '.');
                        $results['details'][$key]['amount']          = number_format($details->amount, 2, ',', '.');
                        $results['details'][$key]['details']         = number_format($details->amount*$details->quantity, 2, ',', '.');
                        $results['details'][$key]['excenta']         = number_format($details->excenta, 2, ',', '.');
                        $results['details'][$key]['iva5']            = number_format($details->iva5, 2, ',', '.');
                        $results['details'][$key]['iva10']           = number_format($details->iva10, 0, ',', '.');
                    }
                }
            }
            return response()->json($results);
        }
        else
        {
            abort(404);
        }
    }
}
