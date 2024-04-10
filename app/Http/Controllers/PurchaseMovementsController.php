<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchasesMovementsRequest;
use App\Http\Requests\DeletePurchasesMovementsRequest;
use App\Models\Branch;
use App\Models\CalendarPayment;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\DepositRequestingDepartment;
use App\Models\Purchase;
use App\Models\PurchaseMovement;
use App\Models\PurchaseMovementsDetail;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchasesExistence;
use App\Models\PurchasesProductCost;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;

class PurchaseMovementsController extends Controller
{
    public function index()
    {
        $purchases_movements = PurchaseMovement::with('deposit')
                                                ->Active()
                                                ->where('type_movement', 1)
                                                ->where('type_operation', 1)
                                                ->orderBy('id', 'desc');
                                                
        $deposits = Deposit::where('status',1)->pluck('name','id');
        if(request()->s)
        {
            $purchases_movements = $purchases_movements->where('id', request()->s);
        }

        if (request()->purchase_order_number)
        {
            $purchases_movements = $purchases_movements->whereHas('purchases_movement_details.purchases_order_detail.purchases_order', function($query){
                $query->where('number', request()->purchase_order_number);
            });
        }

        if(request()->deposits_id)
        {
            $purchases_movements = $purchases_movements->where('deposits_id', request()->deposits_id);
        }
        $purchases_movements = $purchases_movements->paginate(20);
        return view('pages.purchase-movement.index', compact('purchases_movements','deposits'));
    }

    public function create()
    {
        $branches       = Branch::getAllCached()->pluck('name', 'id');
        $deposits       = Deposit::where('status',1)->get();
        $array_deposits = [];
        foreach ($deposits as $key => $deposit)
        {
            $array_deposits[$deposit->branch_id][$deposit->id] = $deposit->name;
        }
        $type_vouchers = config('constants.type_purchases');
        foreach ($type_vouchers as $key => $value) 
        {
            if (!in_array($key, [1, 2])) 
            {
                unset($type_vouchers[$key]);
            }
        }
        return view('pages.purchase-movement.create', compact('array_deposits', 'branches', 'type_vouchers'));
    }

    public function store(CreatePurchasesMovementsRequest $request) 
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            { 
                $purchases_movement = PurchaseMovement::create([ 'deposit_id'      => $request->deposits_id,   
                                                                  'observation'      => $request->observation, 
                                                                  'invoice_number'   => $request->invoice_number,
                                                                  'invoice_date'     => $request->date,
                                                                  'date_payment'     => $request->expiration[0],                     
                                                                  'type_operation'   => 1,
                                                                  'type_movement'    => 1,                                   
                                                                  'status'           => true,
                                                                  'branch_id'        => $request->branch_id,
                                                                  'invoice_condition'=> $request->condition,
                                                                  'invoice_stamped'  => $request->stamped,
                                                                  'stamp_validity'   => $request->stamped_validity,
                                                                  'reason_deleted'   => $request->reason_deleted,
                                                                  'user_id'          => auth()->user()->id,
                                                                  'recived_person'   => auth()->user()->id
                                                                ]);

                foreach($request->detail_product_id as $key => $product_id)
                {
                    $product_quantity = $request->detail_product_quantity[$key];
                    if($product_quantity && $product_quantity != '' and $product_quantity != 0) 
                    {
                        // Solo productos que sean mercaderias
                        $purchases_product = RawMaterial::find($product_id);
                        $affects_stock     =  false;
                        if($purchases_product->type == 2 && $purchases_product->purchases_category->stockeable == true) 
                        {
                            $affects_stock     = true;
                        }

                            $purchases_movement_details = $purchases_movement->purchases_movement_details()->create([ 
                                'raw_materials_id'          => $product_id,
                                'purchases_order_detail_id' => $request->detail_id[$key],
                                'quantity'                  => $product_quantity,
                                'affects_stock'             => $affects_stock 
                            ]);

                            $quantity_received = PurchaseMovementsDetail::where('purchases_order_detail_id', $request->detail_id[$key])
                            ->whereHas('purchases_movement', function($query){
                            $query->where('status', 1)
                            ->where('type_movement', 1)//SI ES ENTRADA
                            ->where('type_operation', 1);//SI ES RECEPCION
                            })->sum('quantity');
                            $purchases_order_detail = PurchaseOrderDetail::find($request->detail_id[$key]);
                            $purchases_order_detail->update(['quantity_received' => $quantity_received]);
                            //EL CAMPO RESIDUE HACE REFERENCIA A LA CANTIDAD DE PRODUCTO QUE AUN NO FUE PAGADA
                            $purchases_order_detail->increment('residue', intval($product_quantity));


                            $price_cost_iva = 0;
                            if($purchases_order_detail)
                            {
                            $price_cost = $purchases_order_detail->amount;
                            if($purchases_product->type_iva==1)
                            {
                                $price_cost_iva = $purchases_order_detail->amount;
                            }

                            if($purchases_product->type_iva==2)
                            {
                                $price_cost_iva = $purchases_order_detail->amount * 1.05;
                            }

                            if($purchases_product->type_iva==3)
                            {
                                $price_cost_iva = $purchases_order_detail->amount * 1.1;
                            }
                            }
                            $purchases_existence = PurchasesExistence::create([ 
                                'deposit_id'           => $request->deposits_id, 
                                'type'                 => 1, 
                                'raw_material_id'      => $product_id,
                                'quantity'             => $product_quantity,
                                'residue'              => $product_quantity,
                                'price_cost'           => $price_cost,
                                'price_cost_iva'       => $price_cost_iva 
                            ]);


                                $purchases_movement_details->update([
                                    'price_cost'    => $price_cost
                                ]);
                            //ACTUALIZACION DE COSTO PROMEDIO
                            $existences = PurchasesExistence::where('raw_material_id',  $product_id)
                                        ->where('residue', '>', 0)
                                        ->get()           
                                        ->sum('residue');

                            $existence_costs = PurchasesExistence::selectRaw('sum(residue) as total_quantity, price_cost,raw_material_id')
                                            ->where('raw_material_id', $product_id)
                                            ->where('residue', '>', 0)
                                            ->groupBy('price_cost','raw_material_id')
                                            ->get();
                            $cost_array = [];
                            $n_cost = null;
                            foreach ($existence_costs as $key => $cost) 
                            {
                                if(isset($cost_array[($cost->raw_material_id)]))
                                {
                                    $cost_array[$cost->raw_material_id] += $cost->total_quantity * $cost->price_cost;
                                }
                                else
                                {
                                    $cost_array[$cost->raw_material_id] = $cost->total_quantity * $cost->price_cost;
                                }
                            }

                            if($existences) 
                            {
                                $new_cost = ($cost_array[$product_id] / $existences);
                                $raw_material = RawMaterial::where('id',$product_id)->update(['average_cost' =>$new_cost]);
                            }
                    }
                }
                //CARGA DE FACTURA
                $pending_purchase = Purchase::where('id',request()->purchase_id)->first();
                if($pending_purchase)
                {
                   $pending_purchase->update([
                                            'date'                  => $request->date,
                                            'social_reason_id'      => $request->social_reason_id,
                                            'branch_id'             => $request->branch_id,
                                            'stamped'               => $request->stamped,
                                            'type'                  => $request->type,
                                            'condition'             => $request->condition,
                                            'number'                => $request->invoice_number,
                                            'purchases_provider_id' => $request->purchases_provider_id,
                                            'razon_social'          => $request->social_reason,
                                            'ruc'                   => $request->ruc,
                                            'phone'                 => $request->phone_label,
                                            'address'               => $request->address_label,
                                            'observation'           => $request->observation,
                                            'stamped_validity'      => $request->stamped_validity,
                                            'amount'                => $this->parse($request->total_invoice),
                                            'total_excenta'         => $this->array_sum($request->detail_amounts_exenta),
                                            'total_iva5'            => $this->array_sum($request->detail_amounts_5),
                                            'total_iva10'           => $this->array_sum($request->detail_amounts),
                                            'amount_iva5'           => $request->total_iva_5 ?  $this->parse($request->total_iva_5) : 0,
                                            'amount_iva10'          => $request->total_iva_10 ?  $this->parse($request->total_iva_10) : 0,
                                            'currency_id'           => $request->currency_id,
                                            'change'                => 1,
                                            'cash_box_id'           => null,
                                            'accounting_plan_id'    => $request->type_payment == 1 ? $request->other_accounting_account_id : null,
                                            'status'                => 4,//Autorizado por RRHH
                                            'user_id'               => auth()->user()->id,
                                            'received_user_id'      => auth()->user()->id,
                                            'received_date'         => date('Y-m-d H:i:s'),
                                            'invoice_copy'          => 0,
                                            'request_json'          => json_encode($request->all()),
                                            'first_expiration'      => $first_expiration,
                                            'provider_type'         => 3 //proveedores
                                        ]);

                                        
                    $purchases_movement->update(['purchase_id' => $pending_purchase->id]);
                    foreach ($request->order_detail_id as $index => $order_detail_id)
                    {
                        $pending_purchase->purchases_details()->update([
                            'purchases_product_id'      => $request->detail_invoice_product_ids[$index],
                            'purchases_order_detail_id' => $order_detail_id,
                            'description'               => $request->detail_descriptions[$index],
                            'accounting_plan_id'        => null,
                            'quantity'                  => $request->detail_quantities[$index],
                            'amount'                    => $request->detail_price[$index],
                            'excenta'                   => $this->parse($request->detail_amounts_exenta[$index]),
                            'iva5'                      => $this->parse($request->detail_amounts_5[$index]),
                            'iva10'                     => $this->parse($request->detail_amounts[$index])
                        ]);
                    }

                    if($purchases_movement->purchases_movement_details()->first())
                    {
                        $cost_centers = $purchases_movement->purchases_movement_details()->first()->purchases_order_detail->purchases_order->purchases_order_cost_centers;
                        if ($cost_centers->count() > 0)
                        {
                            foreach($cost_centers as $key => $cost_center)
                            {
                                $pending_purchase->purchases_cost_centers()->update([
                                    'cost_center_id' => $cost_center->cost_center_id,
                                    'amount'         => 0,
                                    'percentage'     => $cost_center->percentage
                                ]);
                            }
                        }
                    }

                    foreach ($request->expiration as $expiration_key => $expiration)
                    {
                        //Agendar Pago
                        CalendarPayment::create([
                            'social_reason_id' => $pending_purchase->social_reason_id,
                            'date'             => $expiration,
                            'type_account'     => 3, //Porveedores
                            'type_scheduler'   => 5, //OCASIONAL
                            'purchase_id'      => $pending_purchase->id,
                            'purchases_provider_id' => $request->purchases_provider_id,
                            'description'      => $pending_purchase->observation,
                            'amount'           => $this->parse($request->payment_amount[$expiration_key]),
                            'user_id'          => auth()->user()->id,
                            'currency_id'      => $request->currency_id,
                            'status'           => 3
                        ]);
                    }
                }
                else
                {
                    $purchase = Purchase::create([
                                            'date'                  => $request->date,
                                            'branch_id'             => $request->branch_id,
                                            'stamped'               => $request->stamped,
                                            'type'                  => $request->type,
                                            'condition'             => $request->condition,
                                            'number'                => $request->invoice_number,
                                            'provider_id' => $request->purchases_provider_id,
                                            'razon_social'          => $request->social_reason,
                                            'ruc'                   => $request->ruc,
                                            'phone'                 => $request->phone_label,
                                            'address'               => $request->address_label,
                                            'observation'           => $request->observation,
                                            'stamped_validity'      => $request->stamped_validity,
                                            'amount'                => $this->parse($request->total_invoice),
                                            'total_excenta'         => $this->array_sum($request->detail_amounts_exenta),
                                            'total_iva5'            => $this->array_sum($request->detail_amounts_5),
                                            'total_iva10'           => $this->array_sum($request->detail_amounts),
                                            'amount_iva5'           => $request->total_iva_5 ?  $this->parse($request->total_iva_5) : 0,
                                            'amount_iva10'          => $request->total_iva_10 ?  $this->parse($request->total_iva_10) : 0,
                                            'status'                => 4,//Autorizado por RRHH
                                            'user_id'               => auth()->user()->id,
                                        ]);

                    $purchases_movement->update(['purchase_id' => $purchase->id]);
                    foreach ($request->order_detail_id as $index => $order_detail_id)
                    {
                        $purchase->purchase_details()->create([
                            'material_id'      => $request->detail_invoice_product_ids[$index],
                            'description'               => $request->detail_descriptions[$index],
                            'quantity'                  => $request->detail_quantities[$index],
                            'amount'                    => $request->detail_price[$index],
                            'excenta'                   => $this->parse($request->detail_amounts_exenta[$index]),
                            'iva5'                      => $this->parse($request->detail_amounts_5[$index]),
                            'iva10'                     => $this->parse($request->detail_amounts[$index])
                        ]);
                    }

                    foreach ($request->expiration as $expiration_key => $expiration)
                    {
                        //Agendar Pago
                        CalendarPayment::create([
                            'date'             => $expiration,
                            'purchase_id'      => $purchase->id,
                            'provider_id'      => $request->purchases_provider_id,
                            'description'      => $purchase->observation,
                            'amount'           => $this->parse($request->payment_amount[$expiration_key]),
                            'user_id'          => auth()->user()->id,
                            'status'           => 3
                        ]);
                    }
                }
                
                // toastr()->success('Agregado exitosamente');
            });

            return response()->json([
                'success' => true
            ]);
        }
        abort(404);
    }

    public function show(PurchaseMovement $purchase_movement)
    {

        $purchase_movement->load(['purchases_movement_details', 
                'purchases_movement_details.raw_material', 
                'purchases_movement_details.purchases_order_detail']);
        
        return view('pages.purchase-movement.show', compact('purchase_movement'));
    }

    public function edit(PurchaseMovement $purchase_movement)
    {
        $branches       = Branch::getAllCached()->pluck('name', 'id');
        $deposits       = Deposit::where('status',1)->get();
        $type_vouchers = config('constants.type_purchases');
        

        return view('pages.purchase-movement.edit',compact('purchase_movement','branches','deposits','type_vouchers'));
    }

public function update(PurchaseMovement $request, $id)
{
    if($request->ajax())
    {
        DB::transaction(function() use ($request, $id)
        {
            $detail = PurchaseMovementsDetail::findOrFail($id);

            $detail->update([
                'material_id' => $request->detail_product_id,
                'quantity' => $request->detail_product_quantity,
                'presentation' => $request->detail_presentation_id,
                'description' => isset($request->detail_product_description) ? $request->detail_product_name.'('.$request->detail_product_description.')' : $request->detail_product_name,
            ]);
        });

        return response()->json(['success' => true]);
    }
}

    public function delete(PurchaseMovement $purchases_movement)
    {
        return view('pages.purchases-movements.delete', compact('purchases_movement'));
    }

    public function delete_submit(DeletePurchasesMovementsRequest $request, PurchaseMovement $purchases_movement)
    {
        DB::transaction(function() use ($purchases_movement, $request)
        {
            $purchases_movement->update([ 'status'         => 2,
                                          'date_deleted'   => date('Y-m-d H:i:s'),
                                          'reason_deleted' => $request->motive,
                                          'user_deleted'   => auth()->user()->id ]);

            foreach($purchases_movement->purchases_movement_details as $details) 
            {            
                if($details->purchases_order_detail)
                {
                    $details->purchases_order_detail->decrement('quantity_received', $details->quantity);
                    $details->purchases_order_detail->decrement('residue', $details->quantity);
                }   
                
                if($details->affects_stock)
                {
                    $purchases_existence = PurchasesExistence::where([ 'id' => $details->purchases_existence_id ])->first();
                    if($purchases_existence)
                    {
                        $purchases_existence->update(['residue' => $purchases_existence->residue - $details->quantity]);
                    }

                    //ACTUALIZACION DE COSTO PROMEDIO 31/08/2021
                    $existences = PurchasesExistence::where('purchases_product_id',  $details->purchases_product_id)
                                        ->where('social_reason_id', $details->purchases_existence->social_reason_id)
                                        ->where('residue', '>', 0)  
                                        ->get();
                    $product_quantity = 0;
                    $total_product_cost  = 0;
                    foreach($existences as $key => $existence)
                    {
                        $total_product_cost += $existence->price_cost * $existence->residue;
                        $product_quantity += intVal($existence->residue);
                    }

                    if($product_quantity > 0)
                    {
                        $purchase_product_cost = PurchasesProductCost::where('purchases_product_id',  $details->purchases_product_id)
                        ->where('social_reason_id', $details->purchases_existence->social_reason_id)
                        ->update([
                            'price_cost' => ($total_product_cost / $product_quantity),
                            'quantity' => $product_quantity]);
                    }

                }

                $details->update([ 'purchases_order_detail_id' => NULL ]);
            }

            //ELIMINAR SI TIENE ANCLADO LA PREFACTURA
            if ($purchases_movement->purchase)
            {
                $purchases_movement->purchase->update([
                                                        'status'         => 2,
                                                        'date_deleted'   => date('Y-m-d H:i:s'),
                                                        'reason_deleted' => $request->motive,
                                                        'user_deleted'   => auth()->user()->id
                                                      ]);
            }

            $purchases_movement->purchase->calendar_payments()->update(['status' => 10]);

            toastr()->success('Eliminado exitosamente');
        });
        return redirect('purchases-movements');
    }

    public function ajax_purchases_products_movements()
    {
        if(request()->ajax())
        {
            $results = [];        
            $purchases_order_details = PurchaseOrderDetail::with('purchase_order', 'raw_material')
                                                            ->select("purchase_order_details.*")
                                                            ->join('purchase_orders', 'purchase_order_details.purchases_order_id', '=', 'purchase_orders.id')
                                                            ->where('purchase_orders.status', true)
                                                            ->where('purchase_orders.number', request()->number_oc)
                                                            ->get();
            
            foreach ($purchases_order_details as $key => $order_detail)
            {
                if($order_detail->quantity !=( $order_detail->quantity_received + $order_detail->quantity_cereada))
                {
                    $results['items'][$key]['id']           = $order_detail->id;
                    $results['items'][$key]['product_id']   = $order_detail->material_id;
                    $results['items'][$key]['product_name'] = $order_detail->raw_material->description;
                    $results['items'][$key]['quantity']     = $order_detail->quantity;
                    $results['items'][$key]['received']     = $order_detail->quantity_received;
                    $results['items'][$key]['residue']      = $order_detail->residue;
                    $results['items'][$key]['presentation_id']      = $order_detail->raw_material->presentation->id;
                    $results['items'][$key]['presentation']      = $order_detail->raw_material->presentation->name;
                    $results['items'][$key]['pending_reception']      = $order_detail->quantity - ($order_detail->quantity_received + $order_detail->quantity_cereada);
                    $results['items'][$key]['amount']       = $order_detail->amount;
                    $results['items'][$key]['subtotal']     = $order_detail->amount * $order_detail->quantity;
                }

                $results['ruc']                 = $order_detail->purchase_order->ruc;
                $results['provider_id']         = $order_detail->purchase_order->provider_id;
                $results['provider_fullname']   = $order_detail->purchase_order->provider->name;
                $results['phone']               = $order_detail->purchase_order->phone;
                $results['social_reason']       = $order_detail->purchase_order->razon_social;
                $results['address']             = $order_detail->purchase_order->address;
                $results['branch_id']           = $order_detail->purchase_order->branch_id;
            }         
    
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_purchases_products_order_details()
    {
        if(request()->ajax())
        {
            $results = []; 
            $count   = 0;       
            $purchases_order_details = PurchaseOrderDetail::with('purchases_order', 'purchases_product', 'purchases_product_presentation')
                                                            ->select("purchases_order_details.*")
                                                            ->join('purchases_order', 'purchases_order_details.purchase_order_id', '=', 'purchases_order.id')
                                                            ->where('purchases_order.status', true)
                                                            ->where('purchases_order.number', request()->number_oc)
                                                            ->get();
            foreach ($purchases_order_details as $key => $order_detail)
            {
                if($order_detail->quantity_received > 0)
                {
                    $existence         = 0;                    
                    $product_existence = PurchasesExistence::where('residue', '>', 0)
                                                            ->where('deposit_id', request()->deposit_id)
                                                            ->where('raw_material_id', $order_detail->raw_material_id);
                    if($product_existence)
                    {
                        $existence = $product_existence->get()->sum('residue');
                    }

                    if($existence > 0)
                    { 
                        $results['items'][$key]['id']                    = $order_detail->purchases_product_id;                        
                        $results['items'][$key]['name']                  = $order_detail->purchases_product->name;                        
                        $results['items'][$key]['quantity']              = $order_detail->quantity_received > $existence ? $existence : $order_detail->quantity_received;
                        $count++;
                    }
                }                
            }

            $results['total_count'][0] = $count ;
            
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_product_existences()
    {
        if(request()->ajax())
        {
            $results           = [];
            $product_existence = PurchasesExistence::Select('id','purchases_product_id', 'expiration_date', DB::raw('SUM(residue) as total_residue'))->where('residue', '>', 0)
                                                    ->where('deposit_id', request()->deposit_id)
                                                    ->GroupBy('id','expiration_date')
                                                    ->where('purchases_product_id', request()->purchases_product_id)->limit(10);

            $deposit = Deposit::find(request()->deposit_id);
            if(request()->expiration_date)
            {
                foreach($product_existence->orderBy('expiration_date','DESC')->get() as $purchases_existence)
                {
                    if($purchases_existence->purchases_product)
                    {

                        $results['items'][$purchases_existence->id] = 
                        [
                            'id' => $purchases_existence->id,
                            'product_id' => $purchases_existence->purchases_product_id,
                            'product_name' => $purchases_existence->purchases_product ? $purchases_existence->purchases_product->name : '',
                            'alert_string' => $purchases_existence->expiration_date < now() ? 'alert-danger' : (now()->diffInDays($purchases_existence->expiration_date, false) < 8 ? 'alert-warning' : ''),
                            'expiration_date' => $purchases_existence->expiration_date ? $purchases_existence->expiration_date->format('d/m/Y') : '',
                            'total_residue' => $purchases_existence->total_residue,
                        ];
                    }

                }
            }
            else
            {
                if($product_existence)
                {
                    $product_cost = PurchasesProductCost::where('purchases_product_id',  request()->purchases_product_id)
                                                                                ->where('social_reason_id', $deposit->enterprise->social_reason_id)
                                                                                ->first();
                    $results['items'][0]['price_cost']  = $product_cost ? $product_cost->price_cost : 0;
                    $results['items'][0]['existence']   = $product_existence->get()->sum('total_residue');
                }else
                {
                    $results['items'][0]['price_cost'] = 0;
                    $results['items'][0]['existence']  = 0;
                }
            }
                                   
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_change_requesting_department()
    {
        if(request()->ajax())
        {
            $results           = [];
            $deposit = Deposit::find(request()->deposit_id);
            $requesting_departments = DepositRequestingDepartment::where('deposit_id',$deposit->id)->get();
            //dd($requesting_departments);
            if($requesting_departments)
            {
                foreach ($requesting_departments as $key => $requesting_department)
                {
                    //dd($requesting_department->purchases_requesting_department->name);
                    $results[$key]['id']   = $requesting_department->requesting_department_id;
                    $results[$key]['name'] = $requesting_department->purchases_requesting_department->name;
                }
            }

            return response()->json($results);
        }
        abort(404);
    }

    private function parse($value)
    {
        return intVal(str_replace(',', '.',str_replace('.', '', $value)));
    }

    private function array_sum($array)
    {
        $total = 0;
        foreach ($array as $key => $value)
        {
            $total += intVal(str_replace(',', '.',str_replace('.', '', $value)));
        }
        return $total;
    }

    public function ajax_purchases_movements()
    {
        // if(request()->ajax())
        // {
        //     $results = [];       
        //     $purchases = Purchase::where('purchases_provider_id', request()->provider_id)
        //                                             ->where('social_reason_id', request()->social_reason_id)
        //                                             ->where('amount', request()->amount)
        //                                             ->where('type', 1)
        //                                             ->whereIn('status',[3,4])
        //                                             ->get();
        //     foreach ($purchases as $key => $purchase)
        //     {
        //         $results['items'][$key]['id']           = $purchase->id;
        //         $results['items'][$key]['product_id']   = $purchase->purchases_product_id;
        //         $results['items'][$key]['number']   = $purchase->number;
        //         $results['items'][$key]['stamped']   = $purchase->stamped;
        //         $results['items'][$key]['stamped_validity']   = $purchase->stamped_validity->format('d-m-Y');
        //         $results['items'][$key]['date']   = $purchase->date->format('d-m-Y');
        //     }         
    
        //     return response()->json($results);
        // }
        // abort(404);
    }
}