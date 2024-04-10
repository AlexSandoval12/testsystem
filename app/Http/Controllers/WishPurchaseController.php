<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePurchaseImageRequest;
use App\Http\Requests\CreateWishPurchaseRequest;
use App\Models\Branch;
use App\Models\Presentation;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseBudget;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\WishPurchase;
use App\Models\WishPurchaseDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class WishPurchaseController extends Controller
{
    public function index()
    {
        $purchases_providers = Provider::Filter();
        $purchases           = WishPurchase::with('branch', 'provider')
            ->orderBy('id', 'desc');

        if (request()->p)
        {
            $purchases = $purchases->where('number', 'LIKE', '%' . request()->p . '%');
        }

        if (request()->invoice_copy)
        {
            $purchases = $purchases->where('invoice_copy', request()->invoice_copy);
        }

        $purchases = $purchases->paginate(20);
        return view('pages.wish-purchase.index', compact('purchases', 'purchases_providers'));
    }

    public function create()
    {
        $users                  = User::filter();
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $raw_materials          = RawMaterial::Filter();
        $product_presentations  = Presentation::Filter();

        return view('pages.wish-purchase.create', compact('users' , 'branches', 'raw_materials', 'product_presentations'));
    }

    public function store(CreateWishPurchaseRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request, &$wish_purchase)
            {
                $last_number = WishPurchase::orderBy('number', 'desc')->limit(1)->first();
                $last_number = $last_number ? $last_number->number : 0;
                $last_number = $last_number + 1;

                $wish_purchase = WishPurchase::create([
                    'number'                    => $last_number,
                    'date'                      => $request->date,
                    'branch_id'                 => $request->branch_id,
                    'observation'               => $request->observation,
                    'status'                    => 1,
                    'user_id'                   => auth()->user()->id
                ]);

                // Grabar los Productos
                foreach($request->detail_product_id as $key => $value)
                {

                    $wish_purchase->wish_purchase_details()->create([
                        'material_id'              => $request->detail_product_id[$key],
                        'quantity'                 => $request->detail_product_quantity[$key],
                        'wish_purchase_id'         => $wish_purchase->id,
                        'deposit_id'               => 1,
                        'presentation'             => $request->detail_presentation_id[$key],
                        'description'              => isset($request->detail_product_description[$key]) ? $request->detail_product_name[$key].'('.$request->detail_product_description[$key].')' : $request->detail_product_name[$key],
                    ]);
                }
            });

            return response()->json([
                'success'            => true,
                'purchases_order_id' => $wish_purchase->id
            ]);
        }
        abort(404);
    }
    public function edit(WishPurchase $wish_purchase)
    {
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $raw_materials          = RawMaterial::Filter();
        $product_presentations  = Presentation::Filter();
        return view('pages.wish-purchase.edit',compact('wish_purchase','branches','raw_materials','product_presentations'));
    }

    public function update(WishPurchase $wish_purchase)
    {
        if(request()->all())
        {
            DB::transaction(function() use ($wish_purchase)
            {
                $wish_purchase->update([
                    'branch_id'                 => request()->branch_id,
                    'observation'               => request()->observation,
                    'user_id'                   => auth()->user()->id
                ]);

                $wish_purchase->wish_purchase_details()->delete();
                // Grabar los Productos
                foreach(request()->detail_product_id as $key => $value)
                {

                    $wish_purchase->wish_purchase_details()->create([
                        'material_id'              => request()->detail_product_id[$key],
                        'quantity'                 => request()->detail_product_quantity[$key],
                        'wish_purchase_id'         => $wish_purchase->id,
                        'deposit_id'               => 1,
                        'presentation'             => request()->detail_presentation_id[$key],
                        'description'              => isset(request()->detail_product_description[$key]) ? request()->detail_product_name[$key].'('.request()->detail_product_description[$key].')' : request()->detail_product_name[$key],
                    ]);
                }
            });

            return redirect('wish-purchase');
        }
    }


    public function show_multiple()
    {
        if(request()->wish_purchase_ids)
        {
            $wish_purchases = WishPurchase::whereIn('id',request()->wish_purchase_ids)->get();
            $raw_materials     = RawMaterial::Filter();

            return view('pages.wish-purchase.show_multiple', compact('wish_purchases','raw_materials'));
        }
        else
        {
            toastr()->warning('Debe seleccionar una solicitud aprobada');
            return back();
        }

    }
    public function show_multiple_submit()
    {
        if(request()->ajax())
        {
            $message = null;
            $status = true;
            $detail = WishPurchaseDetail::find(request()->detail_id);
            if($detail && request()->aproved_quantity)
            {
                if($detail->quantity < request()->aproved_quantity)
                {
                    $message = 'La cantidad Aprobada supera al Saldo Actual: '.$detail->residue;
                    $status = false;
                }
                else
                {
                    $detail->update([
                        'material_id'       => request()->product_id,
                        'presentation '     => request()->presentation]);
                    //EL RESIDUO DEBE RESTAR RECIEN CUANDO SE APRUEBA LA SOLICITUD
                    // $detail->decrement('residue',request()->aproved_quantity);
                }
                return response()->json(['success'=>$status, 'message'=> $message, 'restocking_id'=> $detail->wish_purchase_id]);
    
            }
            else
            {
                return response()->json(['success'=>$status, 'message'=> 'no entro']);
            }
        }
    }

    public function show(WishPurchase $wish_purchase)
    {

        return view('pages.wish-purchase.show', compact('wish_purchase'));
    }

    public function charge_purchase_budgets(WishPurchase $wish_purchase)
    {
        return view('pages.wish-purchase.purchase_budgets',compact('wish_purchase'));
    }

    public function charge_purchase_budgets_store(WishPurchase $wish_purchase, CreatePurchaseImageRequest $request)
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

    private function uploadSignature($file)
    {
        $signature_name = Str::random(40) . '.' . $file->getClientOriginalExtension();

        $destinationPath = 'storage/wish_purchases_budgets/' . $signature_name;

        if ($file->move(public_path('storage/wish_purchases_budgets'), $signature_name)) {
            Image::make($destinationPath)
                ->orientate()
                ->save($destinationPath);
        }

        return $signature_name;
    }
    public function confirm_purchase_budgets(WishPurchase $wish_purchase)
    {

        $wish_purchases = $wish_purchase->purchase_budgets()->get();

        return view('pages.wish-purchase.confirm-purchase-budgets',compact('wish_purchase'));
    }

    public function confirm_purchase_budgets_store(PurchaseBudget $purchase_budget)
    {
        $text = 'Presupuesto Aprobado';
        // APROBAR EL PRESUPUESTO
        if(request()->type == 1)
        {
            $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>1]);
            $purchase_budget->wish_purchase->update(['status' => 2]);
        }
        //BORRAR EL PRESUPUESTO
        elseif(request()->type == 2)
        {
            $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>0]);
            $text = 'Presupuesto Rechazado';

        }
        // RECHAZAR EL PRESUPUESTO
        elseif(request()->type == 3)
        {
            $text = 'Presupuesto Borrado';
            $purchase_budget->delete();
        }

        if(request()->url)
        {
            return redirect(request()->url);
        }
        else
        {
            return redirect('wish-purchase');
        }
    }

    public function wish_purchase_budgets_approved(WishPurchase $wish_purchase)
    {
        $purchase_budgets = $wish_purchase->purchase_budgets()->where('status',2)->get();

        return view('pages.wish-purchase.wish-purchase-budgets-approved',compact('wish_purchase','purchase_budgets'));
    }

    // public function pdf(WishPurchase $wish_purchase)
    // {
    //      return PDF::loadView('pages.wish-purchase.pdf', compact('wish_purchase'))
    //         ->setPaper('A4', 'portrait')
    //         ->stream();

    // }

    public function searchProviderStamped()
    {
        $invoice_number = explode('-', request()->purchase_number);
        $invoice_number = $invoice_number[0] . '-' . $invoice_number[1];

        $purchase = Purchase::select("purchases.*", DB::raw("DATE_FORMAT(stamped_validity, '%d/%m/%Y') stamp_validity"))
            ->where('provider_id', request()->provider_id)
            ->where('number', 'like', ['%'.$invoice_number.'%'])
            ->whereIn('status', [1,3,4])
            ->orderBy('id', 'DESC')
            ->first();

        return  response()->json($purchase);
    }

    public function pdf(WishPurchase $restocking)
    {
        return PDF::loadView('pages.wish-purchase.pdf', compact('restocking'))
                    ->setPaper([0, 0, 250, 100], 'portrait')
                    // ->setPaper([0,0,300,300], 'portrait')
                    ->stream();
    }
    private function parse($value)
    {
        return str_replace(',', '.',str_replace('.', '', $value));
    }
    

}
