<?php

namespace App\Http\Controllers;

use App\Exports\NombreExport;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProveedorRequest;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ProveedorController extends Controller
{
    public function index()
    {
        $providers = $this->getProvider();
        $providers = $providers->paginate(20);
        return view('pages.proveedor.index' ,compact('providers'));
    }

    public function create()
    {
        return view('pages.proveedor.create');
    }

    public function edit(Provider $provider_id)
    {
        return view('pages.proveedor.edit',compact('provider_id'));
    }

    public function store(CreateProveedorRequest $request)
    {
        DB::transaction(function() use ($request)
        {
            $proveedor = Provider::create([
                                        'name'       => $request->name,
                                        'ruc'        => $request->ruc,
                                        'address'     => $request->address,
                                        'phone'      => $request->phone]);
        });
        return redirect('provider');
    }

    public function update(Provider $provider_id)
    {
            $provider_id->update([
                                'name'       => request()->name,
                                'ruc'        => request()->ruc,
                                'address'     => request()->address,
                                'phone'      => request()->phone]);

        return redirect('provider');
    }

    public function export_xls()
    {
        $providers = $this->getProvider()->get();
        $excelArray = [];
        $excelArray[] = [
            'Nombre',
            'RUC',
            'Direccion.',
            'Telefono',
        ];

        foreach ($providers as $provider) {
            $excelArray[] = [
                $provider->name,
                $provider->ruc,
                $provider->address,
                $provider->phone,
            ];
        }

        return Excel::download(new NombreExport(collect($excelArray)), 'Proveedores.xlsx');

    }

    private function getProvider()
    {
        $providers = Provider::orderBy('name');

        if(request()->s)
        {
            $providers = $providers->where('name', 'like', '%'.request()->s.'%')->orwhere('ruc', 'like', '%'.request()->s.'%');
        }
        return $providers;
    }

    public function ajax_providers()
    {
        $purchases_providers = Provider::orderBy('name')
                                                ->where(function ($query)
                                                {
                                                    $query->Where('name', 'LIKE', '%' . request()->q . '%')
                                                          ->orWhere('ruc', 'LIKE', '%' . request()->q . '%');
                                                })
                                                ->where('status', true)
                                                ->get();
        $results = [];
        foreach ($purchases_providers as $key => $purchases_provider)
        {
            $results['items'][$key]['id']                  = $purchases_provider->id;
            $results['items'][$key]['text']                = $purchases_provider->name . '| Ruc : ' . $purchases_provider->ruc;
            $results['items'][$key]['name']                = $purchases_provider->name;
            $results['items'][$key]['ruc']                 = $purchases_provider->ruc;
            $results['items'][$key]['dv']                  = $purchases_provider->dv;
            $results['items'][$key]['address']             = $purchases_provider->address;
            $results['items'][$key]['phone']               = $purchases_provider->phone1.' '.$purchases_provider->phone2;
            $results['items'][$key]['type_iva']            = $purchases_provider->type_iva ? $purchases_provider->type_iva : 3;
            $results['items'][$key]['bank_account']        = $purchases_provider->bank ? ($purchases_provider->bank->name.' - '.$purchases_provider->bank_account) : '';
            // $results['items'][$key]['bank_id']             = $purchases_provider->bank_id;
            // $results['items'][$key]['bank_account_number'] = $purchases_provider->bank_account;
            // $results['items'][$key]['days_of_grace']       = $purchases_provider->days_of_grace;

            // Buscar el Ultimo Timbrado del Proveedor
            // $purchases_ringing = Purchase::whereIn('type', [1,4])
            //                                ->Active()
            //                                ->orderBy('id', 'desc')
            //                                ->where('purchases_provider_id', $purchases_provider->id)
            //                                ->limit(1)
            //                                ->first();
            // if($purchases_ringing)
            // {
            //     $results['items'][$key]['stamped']          = $purchases_ringing->stamped;
            //     $results['items'][$key]['stamped_validity'] = $purchases_ringing->stamped_validity->format('d/m/Y');
            // }else
            // {
            //     $results['items'][$key]['stamped']          = '';
            //     $results['items'][$key]['stamped_validity'] = '';
            // }
        }
        return response()->json($results);
    }

    public function ajax_providers_purchases()
    {
         if(request()->ajax())
         {
             // Buscar las Ultimas 5 Compras
             $results   = [];
             $purchases = Purchase::orderBy('id', 'desc')
                                     ->where('status', true)
                                     ->where('type', '<', 5)
                                     ->where('provider_id', request()->purchases_provider_id)
                                     ->limit(6)
                                     ->get();
             foreach ($purchases as $key => $purchase)
             {
                 $results['purchases'][$key]['id']         = $purchase->id;
                 $results['purchases'][$key]['date']       = $purchase->date->format('d/m/Y');
                 $results['purchases'][$key]['type']       = config('constants.type_purchases.'. $purchase->type);
                 $results['purchases'][$key]['type_label'] = config('constants.type_purchases_label.' . $purchase->type);
                 $results['purchases'][$key]['number']     = $purchase->number;
                 $results['purchases'][$key]['amount']     = number_format($purchase->amount, 0, ',', '.');
             }

        //     // Buscar las Facturas Pendientes de Pago
             $residue = DB::raw("SUM(b.residue) as residue");

             $purchases_pendings = Purchase::from('purchases as a')->select("a.*", $residue)
                                 ->join('purchases_collects as b', 'a.id', '=', 'b.purchase_id')
                                 ->where('a.type', '<', 3)
                                 ->where('a.provider_id', request()->purchases_provider_id)
                                 ->where('a.status', true)
                                 ->where('b.residue', '>', 0)
                                 ->groupBy('a.id')
                                 ->get();
             foreach ($purchases_pendings as $key => $purchases_pending)
             {
                 $results['pendings'][$key]['id']         = $purchases_pending->id;
                 $results['pendings'][$key]['date']       = $purchases_pending->date->format('d/m/Y');
                 $results['pendings'][$key]['type']       = config('constants.type_purchases.'. $purchases_pending->type);
                 $results['pendings'][$key]['type_label'] = config('constants.type_purchases_label.' . $purchases_pending->type);
                 $results['pendings'][$key]['number']     = $purchases_pending->number;
                 $results['pendings'][$key]['amount']     = intVal($purchases_pending->residue);
             }

        //     // Buscar las Ordenes de Compra Pendientes y Ultimos Pagos y Anticipos
             if(request()->social_reason_id)
             {
                 $purchases_orders = PurchaseOrder::select("purchase_orders.*")
                                                   ->join('purchase_order_details', 'purchase_order_details.purchases_order_id', '=', 'purchase_orders.id')
                                                   ->where('purchase_orders.provider_id', request()->purchases_provider_id)
                                                   ->where('purchase_orders.status', true)
                                                   ->where('purchases_order_details.residue', '>', 0)
                                                   ->groupBy('purchase_orders.id')
                                                   ->get();

                 foreach ($purchases_orders as $key => $purchases_order)
                 {
                     $results['orders'][$key]['id']        = $purchases_order->id;
                     $results['orders'][$key]['date']      = $purchases_order->date->format('d/m/Y');
                     $results['orders'][$key]['condition'] = config('constants.invoice_condition.'. $purchases_order->condition);
                     $results['orders'][$key]['number']    = $purchases_order->number;
                 }

        //         // Ultimos Pagos del Proveedor
                 $payments = Purchase::orderBy('id', 'desc')
                                         ->where('status', true)
                                         ->where('type',  5)
                                         ->where('provider_id', request()->purchases_provider_id)
                                         ->limit(6)
                                         ->get();
                 foreach ($payments as $key => $payment)
                 {
                     $results['payments'][$key]['id']     = $payment->id;
                     $results['payments'][$key]['date']   = $payment->date->format('d/m/Y');
                     $results['payments'][$key]['number'] = $payment->number;
                     $results['payments'][$key]['amount'] = number_format($payment->amount, 0, ',', '.');
                 }

        //         // Anticipos del Proveedor
                 $advances = Purchase::where('status', true)
                                     ->where('type',  5)
                                     ->where('provider_id', request()->purchases_provider_id)
                                     ->get();
                 $total_advances=0;
                 foreach ($advances as $key => $advance)
                 {
                     $results['advances'][$key]['id']                 = $advance->id;
                     $results['advances'][$key]['date']               = $advance->date->format('d/m/Y');
                     $results['advances'][$key]['number']             = $advance->number;
                     $results['advances'][$key]['amount']             = number_format($advance->advance, 0, ',', '.');
                     $total_advances = $total_advances + $advance->advance;
                 }

                 if($total_advances>0)
                 {
                     $results['advances_total'][0]['amount'] = number_format($total_advances, 0, ',', '.');
                 }
             }

             return response()->json($results);
         }
         abort(404);
    }
}
