<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmInventoryRequest;
use App\Http\Requests\CreatePurchasesProductInventoriesRequest;
use App\Http\Requests\UpdatePurchasesProductInventoriesRequest;
use App\Models\Deposit;
use App\Models\Inventory;
use App\Models\PriceUpdateLog;
use App\Models\Purchase;
use App\Models\PurchaseMovement;
use App\Models\PurchasesExistence;
use App\Models\PurchasesMovement;
use App\Models\PurchasesOrderDetail;
use App\Models\PurchasesProduct;
use App\Models\PurchasesProductBrand;
use App\Models\RawMaterial;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchasesProductInventoriesController extends Controller
{
    public function index()
    {
        $deposits = Deposit::where('status',true)->get();
        $purchases_product_inventories = Inventory::with('deposit')
            ->orderBy('id', 'desc')->whereIn('status', [1,2]);

        if(request()->filter)
        {
            if(request()->deposit_id)
            {
                $purchases_product_inventories = $purchases_product_inventories->where('deposit_id', request()->deposit_id);
            }

            if(request()->from_date)
            {
                $from_date = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d');
                $purchases_product_inventories = $purchases_product_inventories->where('date', '>=', $from_date);
            }

            if(request()->until_date)
            {
                $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d');
                $purchases_product_inventories = $purchases_product_inventories->where('date', '<=', $until_date);
            }
        }
        
        $purchases_product_inventories = $purchases_product_inventories->paginate(20);
        return view('pages.purchases-product-inventories.index', compact('purchases_product_inventories', 'deposits'));
    }

    public function create()
    {        
        $deposits = Deposit::where('status',true)->pluck('name','id');
        $deposit_destiny      = Deposit::where('id', '<>', auth()->user()->deposit_id)->where('status',true)->get();
        $purchases_products   = '';
        $existences           = [];

        if(request()->filter and request()->deposit_id)
        {
            $purchases_products = RawMaterial::where('status', true)
                                                    ->orderBy('description');
            $purchases_products = $purchases_products->get();

            $purchases_existences = PurchasesExistence::with('deposit')
                                                    ->select("purchases_existences.*", DB::raw("SUM(purchases_existences.residue) as existence"))
                                                    ->whereHas('raw_material', function ($query) {
                                                                $query->where('status', true);})
                                                    ->where('deposit_id', request()->deposit_id)
                                                    ->where('residue', '>', 0)
                                                    ->groupBy('raw_material_id')
                                                    ->get();
            foreach ($purchases_existences as $purchases_existence) 
            {
                $existences[$purchases_existence->raw_material_id] = $purchases_existence->existence;
            }           
        }

        return view('pages.purchases-product-inventories.create', compact( 'deposit_destiny', 'existences', 'purchases_products', 'deposits'));
    }

    public function store(CreatePurchasesProductInventoriesRequest $request)
    {
        DB::transaction(function() use ($request)
        {
            // Inventario
            $purchases_product_inventory = Inventory::create([ 'date'        => date('Y-m-d'),
                                                                               'social_reason_id'  => $request->social_reason_id,                                                                       
                                                                               'purchases_category_id'  => $request->purchases_category_id,                                                                       
                                                                               'deposit_id'  => $request->deposit_id,    
                                                                               'observation' => $request->observation,
                                                                               'status'      => 2,
                                                                               'user_id'     => auth()->user()->id ]);

            foreach($request->product_id AS $product_id => $quantity)
            {
                if($quantity != '')
                {
                    $purchases_product_inventory->purchases_product_inventory_details()->create([ 
                                                                                              'material_id' => $product_id,
                                                                                              'quantity'   => $quantity,
                                                                                              'existence'  => $request->old_existences[$product_id],
                                                                                              'old_cost'   => $request->old_cost_product[$product_id]
                                                                                          ]);
                }
            }


            toastr()->success('Agregado exitosamente');
        });

        return redirect('inventories');
    }

    public function show(PurchasesProductInventory $purchases_product_inventory)
    {   
        $purchases_product_inventory->load(['purchases_product_inventory_details.purchases_product']);
        return view('pages.purchases-product-inventories.show', compact('purchases_product_inventory'));
    }

    public function edit(PurchasesProductInventory $purchases_product_inventory)
    {

        $purchases_products = PurchasesProduct::with('purchases_category')->where('type', 2)
                                                ->where('status', true)
                                                ->orderBy('name')
                                                ->where('purchases_category_id', $purchases_product_inventory->purchases_category_id)
                                                ->get(); 
        // Buscar el ultimo Costo del Producto
        $product_cost = PurchasesProductCost::where('social_reason_id', $purchases_product_inventory->social_reason_id)
                ->whereHas('purchases_product', function ($query) {
                    $query->where('status', true);
                })
                ->get();
        $cost_product = [];
        foreach($product_cost as $cost)
        {                
            $cost_product[$cost->purchases_product_id] = $cost->price_cost;
        }

        $cost_product = collect($cost_product);

        $purchases_existences = PurchasesExistence::with('purchases_product.purchases_category', 'deposit')
                                                ->select("purchases_existences.*", DB::raw("SUM(purchases_existences.residue) as existence"))
                                                ->whereHas('purchases_product', function ($query) {
                                                            $query->where('status', true);})
                                                ->where('deposit_id', $purchases_product_inventory->deposit_id)
                                                ->where('social_reason_id', $purchases_product_inventory->social_reason_id)
                                                ->where('residue', '>', 0)
                                                ->groupBy('purchases_product_id')
                                                ->get();
    
        foreach ($purchases_existences as $purchases_existence) 
        {
            $existences[$purchases_existence->purchases_product_id] = $purchases_existence->existence;
        }   

        foreach($purchases_product_inventory->purchases_product_inventory_details as $key => $detail)
        {
            $inventory_existences[$detail->product_id] = $detail->quantity;
            $inventory_old_cost[$detail->product_id] = $detail->old_cost;
        }

        return view('pages.purchases-product-inventories.edit', compact('purchases_product_inventory', 'purchases_products', 'cost_product', 'existences', 'inventory_existences', 'inventory_old_cost'));
    }

    public function update(UpdatePurchasesProductInventoriesRequest $request, PurchasesProductInventory $purchases_product_inventory)
    {
        DB::transaction(function() use ($request, $purchases_product_inventory)
        {
            $purchases_product_inventory->update(['observation' => $request->observation]);

            $purchases_product_inventory->purchases_product_inventory_details()->delete();
            foreach($request->new_existence AS $product_id => $quantity)
            {
                if($quantity != '')
                {
                    $purchases_product_inventory->purchases_product_inventory_details()->create([ 'product_id' => $product_id,
                                                                                              'quantity'   => $quantity,
                                                                                              'existence'  => $request->old_existences[$product_id],
                                                                                              'old_cost'   => $request->old_cost_product[$product_id]
                                                                                          ]);
                }
            }

            toastr()->success('Inventario actualizado exitosamente');
        });

        return redirect('inventories');
    }

    public function confirm_inventory(ConfirmInventoryRequest $request, Inventory $purchases_product_inventory)
    {
        foreach($purchases_product_inventory->purchases_product_inventory_details as $key => $detail)
        {
            
            // $purchases_existence  = PurchasesExistence::where('raw_material_id', $detail->raw_material_id)
            //     ->where('deposit_id', $purchases_product_inventory->deposit_id)
            //     ->where('residue', '>', 0)
            //     ->sum('residue');
            // if( $detail->quantity != $purchases_existence) 
            // {
            //     toastr()->error('Faltan configurar precios de los productos.');

            //     return redirect('inventories');            
            // }
        }
        DB::transaction(function() use ($purchases_product_inventory)
        {
            foreach($purchases_product_inventory->purchases_product_inventory_details as $key => $detail)
            {
                // Entrada de Producto
                if($detail->quantity > $detail->existence)
                {
                    $quantity_final = $detail->quantity - $detail->existence;
                    $dividendo =  11;
                    $price_cost_iva = $detail->old_cost - ($detail->old_cost / $dividendo);
                    
                    $purchases_movement = PurchaseMovement::create([ 'deposits_id'                  => $purchases_product_inventory->deposit_id,
                                                                    'observation'                    => $purchases_product_inventory->observation,
                                                                    'type_operation'                 => 8,
                                                                    'type_movement'                  => 1,                                   
                                                                    'status'                         => true,
                                                                    'inventory_id'                   => $purchases_product_inventory->id,
                                                                    'user_id'                        => auth()->user()->id 
                                                                ]);
                    
                    $purchases_existence = PurchasesExistence::create([ 'deposit_id'           => $purchases_product_inventory->deposit_id, 
                                                                        'raw_material_id' => $detail->material_id,
                                                                        'quantity'             => $quantity_final,
                                                                        'residue'              => $quantity_final,
                                                                        'price_cost'           => $price_cost_iva,
                                                                        'price_cost_iva'       => $detail->old_cost
                                                                    ]);
                        
                    $purchases_movement->purchases_movement_details()->create([ 'raw_material_id'   => $detail->material_id,
                                                                                'quantity'               => $quantity_final,
                                                                                'purchases_existence_id' => $purchases_existence->id,
                                                                                'affects_stock'          => true ]);
                }

                // Salida de Producto
                $transfers_sending = NULL;
                if($detail->quantity < $detail->existence)
                {
                    $quantity_final = $detail->existence - $detail->quantity;
                    $quantity_process   = $quantity_final;
                    $product_existences = PurchasesExistence::where('residue', '>', 0)
                                                            ->where('deposit_id', $purchases_product_inventory->deposit_id)
                                                            ->where('raw_material_id', $detail->material_id)
                                                            ->orderBy('id')
                                                            ->get();

                        $purchases_movement = PurchaseMovement::create(['deposits_id'    => $purchases_product_inventory->deposit_id,
                                                                        'observation'    => $purchases_product_inventory->observation,
                                                                        'type_operation' => 8,
                                                                        'type_movement'  => 2,                                   
                                                                        'status'         => true,
                                                                        'user_id'        => auth()->user()->id ]); 
                                                                        
                        foreach($product_existences as $product_existence)
                        {
                            if($quantity_process > 0)
                            {
                                $quantity_residue = $quantity_process > $product_existence->residue ? $product_existence->residue : $quantity_process;
                                $movement_detail = $purchases_movement->purchases_movement_details()->create([ 
                                                                                            'raw_material_id'   => $detail->material_id,
                                                                                            'quantity'               => $quantity_residue,
                                                                                            'price_cost'             =>  0,
                                                                                            'purchases_existence_id' => $product_existence->id,
                                                                                            'affects_stock'          => true ]);

                                $product_existence->update(['residue' => $product_existence->residue - $quantity_residue]);

                                $quantity_process = $quantity_process - $quantity_residue;
                            }                        
                        }                            
                }
            }
        });

        $purchases_product_inventory->update(['status' => 1]);

        toastr()->success('Inventario confirmado exitosamente');

        return redirect('inventories');
    }

    public function delete(PurchasesProductInventory $purchases_product_inventory)
    {
        $purchases_product_inventory->update(['status' => 3]);
        toastr()->success('Eliminado exitosamente');
        return redirect('inventories');
    }

    public function xls(PurchasesProductInventory $purchases_product_inventory)
    {  
        $excelArray   = [];
        $excelArray[] = [ 'Código',
                          'Producto',
                          'Movimiento',
                          'Cantidad encontrada',
                          'Costo',
                          'SubTotal' ];

        // foreach($purchases_product_inventory->purchases_movements as $purchases_movement)
        // {
        //     foreach($purchases_movement->purchases_movement_details as $detail)
        //     {
        //         $excelArray[] = [ $detail->purchases_product_id,
        //                           $detail->purchases_product->name,
        //                           config('constants.type_movement_purchases_movements.' . $purchases_movement->type_movement),
        //                           $detail->quantity,
        //                           $detail->purchases_existence ? number_format($detail->purchases_existence->price_cost_iva, 0, '.', '') : 0,
        //                           $detail->purchases_existence ? number_format($detail->purchases_existence->price_cost_iva * $detail->quantity, 0, '.', '') : 0 ];
        //     }
        // }

        foreach($purchases_product_inventory->purchases_product_inventory_details as $detail)
        {                
            $excelArray[] = [ $detail->product_id,
                            $detail->purchases_product->name,
                            $detail->quantity > $detail->existence ? 'Entrada' : ($detail->quantity == $detail->existence ? 'Sin Movimiento' : 'Salida'),
                            intVal($detail->quantity),
                            intVal($detail->old_cost),
                            intVal($detail->quantity * $detail->old_cost)
                            ];
        }

        Excel::create('Inventario Productos', function($excel) use ($excelArray) {
            $excel->sheet('sheet1', function($sheet) use ($excelArray) {
                $sheet->fromArray($excelArray, null, 'A1', false, false);
            });
        })->export('xlsx');
    }

    public function pdf(PurchasesProductInventory $purchases_product_inventory)
    {                  
        return PDF::loadView('pages.purchases-product-inventories.pdf', compact('purchases_product_inventory'))
                    ->setPaper('A5', 'portrait')                  
                    ->stream();
    }

    private function getExistences($product_id, $social_reason_id)
    {
        //ACTUALIZACION DE COSTO PROMEDIO 31/08/2021
        $existences = PurchasesExistence::where('purchases_product_id', $product_id)
                    ->where('social_reason_id', $social_reason_id)
                    ->where('residue', '>', 0)
                    ->get();
        $results = [];
        $results['product_costs'] = 0;
        $results['product_quantity'] = 0;
                    
        foreach($existences as $key => $existence)
        {
            $results['product_costs'] += $existence->price_cost * $existence->residue;
            $results['product_quantity'] += intVal($existence->residue);
        }

        return $results;
    }
}
