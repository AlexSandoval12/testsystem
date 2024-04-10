<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePurchaseImageRequest;
use App\Http\Requests\CreatePurchaseOrderRequest;
use App\Models\Articulo;
use App\Models\Branch;
use App\Models\Presentation;
use App\Models\Provider;
use App\Models\PurchaseBudget;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\WishPurchase;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchases_providers = Provider::Filter();
        $order           = PurchaseOrder::with('branch', 'provider')
            ->orderBy('id', 'desc');

        if (request()->o)
        {
            $order = $order->where('ruc', 'LIKE', '%' . request()->o . '%')
                ->orWhere('number', 'LIKE', '%' . request()->o . '%');
        }

        if (request()->invoice_copy)
        {
            $order = $order->where('invoice_copy', request()->invoice_copy);
        }

         $order = $order->paginate(20);
         return view('pages.purchase-order.index', compact('order', 'purchases_providers'));
    }

    public function create()
    {
        $users                  = User::filter();
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $raw_materials          = RawMaterial::Filter();
        $product_presentations  = Presentation::Filter();
        $provider_suggesteds    = NULL;
        $detail_wish_purchase   = NULL;
        $wish_purchases         = NULL;
        if(request()->wish_purchase_ids)
        {
            // Buscar el Detalle de la Solicitud de Compra
            $wish_purchases = WishPurchase::with('wish_purchase_details.material')
                                    ->whereIn('id',request()->wish_purchase_ids)->get();

            
            if($wish_purchases)
            {
                $count                     = 0;
                foreach ($wish_purchases as $wish_purchase) 
                {
                    $requesting_departments_id = $wish_purchase->requesting_departments_id;
                    $requested_by              = $wish_purchase->requested_by;
                   
                    foreach($wish_purchase->wish_purchase_details as $key => $detail)
                    {
                        if($detail->quantity > 0)
                        {
                                $detail_wish_purchase[$key]['id']                        = $detail->id;
                                $detail_wish_purchase[$key]['product_id']                = $detail->material_id ?? 0;
                                $detail_wish_purchase[$key]['product_name']              = $detail->material_id ? $detail->material->description : $detail->description;
                                $detail_wish_purchase[$key]['product_presentation_id']   = $detail->presentation ? $detail->presentation : NULL;
                                $detail_wish_purchase[$key]['product_presentation_name'] = $detail->presentation ? config('constants.presentation.'.$detail->presentation) : NULL;
                                $detail_wish_purchase[$key]['description']               = $detail->description;
                                $detail_wish_purchase[$key]['quantity']                  = $detail->quantity;
                                $detail_wish_purchase[$key]['amount']                    = 0;


                                $count++;
                        }
                    }

                    if($count==0)
                    {
                        $wish_purchase_id             = NULL;
                        $social_reason_id          = NULL;                          
                        $currency_id               = NULL;                          
                        $requesting_departments_id = NULL;
                        $requested_by              = NULL;
                        $detail_wish_purchase         = [];
                    }
                }

            }
        }
        return view('pages.purchase-order.create', compact('users' , 'branches', 'raw_materials', 'product_presentations','provider_suggesteds','detail_wish_purchase','wish_purchases'));
    }

    public function store(CreatePurchaseOrderRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request, &$purchase_order)
            {
                $last_number = PurchaseOrder::orderBy('number', 'desc')->limit(1)->first();
                $last_number = $last_number ? $last_number->number : 0;
                $last_number = $last_number + 1;

                $purchase_order = PurchaseOrder::create([
                    'number'                    => $last_number,
                    'date'                      => $request->date,
                    'ruc'                       => $request->ruc,
                    'branch_id'                 => $request->branch_id,
                    'condition'                 => $request->condition,
                    'provider_id'               => $request->purchases_provider_id,
                    'razon_social'              => $request->razon_social,
                    'phone'                     => $request->phone,
                    'address'                   => $request->address,
                    'status'                    => 1,
                    'observacion'               => $request->observation,
                    'user_id'                   => auth()->user()->id,
                    'amount'                    => $this->parse($request->total_product)
                ]);

                // Grabar los Productos
                foreach($request->detail_product_id as $key => $value)
                {
                    $purchase_order->purchase_order_details()->create([              
                        'material_id'              => $request->detail_product_id[$key],
                        'quantity'                 => $request->detail_product_quantity[$key],
                        'presentation'             => intVal($request->detail_presentation_id[$key]),
                        'description'              => isset($request->detail_product_description[$key]) ? $request->detail_product_name[$key].'('.$request->detail_product_description[$key].')' : $request->detail_product_name[$key],
                        'amount'                   => $this->parse($request->detail_product_amount[$key]),
                        'residue'                  => $this->parse($request->detail_product_amount[$key]),
                    ]);
                }
            });

            return response()->json([
                'success'            => true,
                'purchases_order_id' => $purchase_order->id
            ]);
        }
        abort(404);
    }

    public function show(PurchaseOrder $purchase_order)
    {

        return view('pages.wish-purchase.show', compact('purchase_order'));
    }

    public function edit(PurchaseOrder $purchase_order)
    {
        $users                  = User::filter();
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $raw_materials          = RawMaterial::Filter();
        $product_presentations  = Presentation::Filter();
        $provider_suggesteds    = NULL;

        return view('pages.purchase-order.edit',compact('purchase_order','users','branches','raw_materials','product_presentations','provider_suggesteds'));
    }

public function update(PurchaseOrder $purchase_order)
{
    if(request()->all())
    {
        DB::transaction(function() use ($purchase_order)
        {
            $purchase_order->update([
                'branch_id'                 => request()->branch_id,
                'condition'                 => request()->condition,
                'status'                    => 1,
                'observacion'               => request()->observation,
                'amount'                    => $this->parse(request()->total_product)
            ]);

            // Grabar los Productos
            $purchase_order->purchase_order_details()->delete();
            foreach(request()->detail_product_id as $key => $value)
            {

                $purchase_order->purchase_order_details()->create([              
                    'material_id'              => request()->detail_product_id[$key],
                    'quantity'                 => request()->detail_product_quantity[$key],
                    'presentation'             => request()->detail_presentation_id[$key],
                    'description'              => isset(request()->detail_product_description[$key]) ? request()->detail_product_name[$key].'('.request()->detail_product_description[$key].')' : '',
                    'amount'                   => $this->parse(request()->detail_product_amount[$key]),
                    'residue'                  => $this->parse(request()->detail_product_amount[$key]),
                ]);
            }
        });


        return redirect('purchase-order');
    }
}

    public function charge_purchase_budgets(PurchaseOrder $wish_purchase)
    {
        return view('pages.wish-purchase.purchase_budgets',compact('wish_purchase'));
    }

    public function charge_purchase_budgets_store(PurchaseOrder $wish_purchase, CreatePurchaseImageRequest $request)
    {

        if (request()->ajax()) {
            if ($request->hasFile('files')) {
                $wish_purchase->purchase_budgets()->delete();

                foreach ($request->file('files') as $key => $input_file) {
                    $file = $input_file;

                    $dir = 'storage/wish_purchases_budgets';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    if ($file) {
                        $filename = $this->uploadSignature($file);
                    }

                    $wish_purchase->purchase_budgets()->create([
                        'name' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            return response()->json(['success' => true]);
        }
    }

    // private function uploadSignature($file)
    // {
    //     $signature_name = Str::random(40) . '.' . $file->getClientOriginalExtension();

    //     $destinationPath = 'storage/wish_purchases_budgets/' . $signature_name;

    //     if ($file->move(public_path('storage/wish_purchases_budgets'), $signature_name)) {
    //         Image::make($destinationPath)
    //             ->orientate()
    //             ->save($destinationPath);
    //     }

    //     return $signature_name;
    // }
    // public function confirm_purchase_budgets(PurchaseOrder $wish_purchase)
    // {

    //     $wish_purchases = $wish_purchase->purchase_budgets()->get();

    //     return view('pages.wish-purchase.confirm-purchase-budgets',compact('wish_purchase'));
    // }

    // public function confirm_purchase_budgets_store(PurchaseBudget $purchase_budget)
    // {
    //     $text = 'Presupuesto Aprobado';
    //     // APROBAR EL PRESUPUESTO
    //     if(request()->type == 1)
    //     {
    //         $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>2]);
    //         $purchase_budget->wish_purchase->update(['status' => 5]);
    //     }
    //     //BORRAR EL PRESUPUESTO
    //     elseif(request()->type == 2)
    //     {
    //         $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>3]);
    //         $text = 'Presupuesto Rechazado';

    //     }
    //     // RECHAZAR EL PRESUPUESTO
    //     elseif(request()->type == 3)
    //     {
    //         $text = 'Presupuesto Borrado';
    //         $purchase_budget->delete();
    //     }

    //     if(request()->url)
    //     {
    //         return redirect(request()->url);
    //     }
    //     else
    //     {
    //         return redirect('wish-purchase');
    //     }
    // }

    // public function wish_purchase_budgets_approved(PurchaseOrder $wish_purchase)
    // {
    //     $purchase_budgets = $wish_purchase->purchase_budgets()->where('status',2)->get();

    //     return view('pages.wish-purchase.wish-purchase-budgets-approved',compact('wish_purchase','purchase_budgets'));
    // }

    private function parse($value)
    {
        return str_replace(',', '.',str_replace('.', '', $value));
    }
}
