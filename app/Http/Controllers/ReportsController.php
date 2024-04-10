<?php

namespace App\Http\Controllers;

use App\Exports\NombreExport;
use App\Http\Requests\ConfirmInventoryRequest;
use App\Http\Requests\CreatePurchasesProductInventoriesRequest;
use App\Http\Requests\UpdatePurchasesProductInventoriesRequest;
use App\Models\Branch;
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

class ReportsController extends Controller
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

    public function stock_product_purchases_report()
    {
       
        // $deposits = Deposit::whereHas('users', function($query){ $query->where('user_id', auth()->user()->id); })->Filter();
        $deposits = Deposit::where('status',true)->pluck('name','id');

        $purchases_existences = null;

        if(request()->deposit_id)
        {
            $purchases_existences = $this->getStockProductPurchases()->paginate(30);
        }

        return view('pages.reports.stock_reports', compact('deposits','purchases_existences'));
    }

    private function getStockProductPurchases()
    {
        $purchases_existences = PurchasesExistence::with('deposit')
                                                  ->select("purchases_existences.*", DB::raw("SUM(purchases_existences.residue) as existence"))
                                                  ->whereHas('raw_material', function ($query) 
                                                    {
                                                        $query->where('status', true);
                                                    });

        if(request()->deposit_id) 
        {
            $purchases_existences = $purchases_existences->where('deposit_id', request()->deposit_id);
        }

        if(request()->product_id)
        {
            $purchases_existences = $purchases_existences->where('type', request()->product_id);
        }   

        if(request()->product_id == 1)
        {
            return $purchases_existences->groupBy('raw_material_id')->groupBy('price_cost');
        }
        else
        {
            return $purchases_existences->groupBy('raw_material_id');
        }
    }

    public function stock_product_purchases_report_excel()
    {
        $purchases_existences = $this->getStockProductPurchases()->get();
        $excelArray   = [];
        $excelArray[] = [   'Deposito',
                            'Producto',
                            'Existencia',
                            'Costo'
                        ];

        foreach($purchases_existences as $existence)
        {

            $excelArray[] = [
                $existence->deposit->name,
                $existence->raw_material->description,
                $existence->existence ? number_format($existence->existence, 0, '.', '') : 0,
                $existence->price_cost ?  number_format($existence->price_cost, 0, ',', '.') : 0
            ];
        }

        return Excel::download(new NombreExport(collect($excelArray)), 'Reporte Existencia.xlsx');

    }

    public function purchases_report()
    {
        $branches             = Branch::where('status',true)->pluck('name','id');
        $purchases= null;
        $purchases_sum= null;
        if(request()->date_range)
        {
            $purchases = $this->getPurchasesReports()->paginate(20);
            $purchases_sum = $this->getPurchasesReportSum()->first();
        }

        return view('pages.reports.book_purchase', compact('branches', 'purchases',  'purchases_sum'));
    }

    public function getPurchasesReports()
    {
        $purchases  = Purchase::with('branch','note_credits')
                                ->where('type', '<', 5)
                                ->where('status', '<', 2);

        if(request()->date_range)
        {
            $from_date  = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[0])->format('Y-m-d 00:00:00');
            $until_date = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[1])->format('Y-m-d 23:59:59');
            $purchases  = $purchases->whereBetween('date', [$from_date, $until_date]);
        }

        if(request()->branch_id)
        {
            $purchases = $purchases->where('branch_id', request()->branch_id);
        }

        if(request()->condition)
        {
            $purchases = $purchases->where('condition', request()->condition);
        }

        if(request()->type)
        {
            $purchases = $purchases->whereIn('type', request()->type);
        }
        return $purchases->orderBy('date');
    }

    public function getPurchasesReportSum()
    {
        $from_date  = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[0])->format('Y-m-d');
        $until_date = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[1])->format('Y-m-d');
        $book_purchases = Purchase::selectRaw("COUNT(*) as count,
            SUM(IF(status=1, total_excenta, 0)) as total_excenta,
            SUM(IF(status=1, total_iva5, 0)) as total_iva5,
            SUM(IF(status=1, amount_iva5, 0)) as amount_iva5,
            SUM(IF(status=1, total_iva10, 0)) as total_iva10,
            SUM(IF(status=1, amount_iva10, 0)) as amount_iva10,
            SUM(IF(status=1, amount, 0)) as amount")
            ->where('type', '<', 5)
            ->where('status', '<', 2)
            ->whereBetween('date', [$from_date, $until_date]);

        if(request()->branch_id)
        {
            $book_purchases = $book_purchases->where('branch_id', request()->branch_id);
        }


        if(request()->condition)
        {
            $book_purchases = $book_purchases->where('condition', request()->condition);
        }

        if(request()->type)
        {
            $book_purchases = $book_purchases->whereIn('type', request()->type);
        }

        return $book_purchases->orderBy('date');
    }

    public function purchases_report_pdf()
    {
        $purchases = $this->getPurchasesReports()->get();
        $purchases_sum = $this->getPurchasesReportSum()->first();

        //FACTURAS
        //CONTADOO
        $purchase_counted        = $this->getPurchasesReportSum()->where('type', 1)->where('condition', 1)->first();
        $purchase_counted_total  = $purchase_counted->amount ? number_format($purchase_counted->amount, 0, ',','.') : 0;
        //CREDITO
        $purchase_credit         = $this->getPurchasesReportSum()->where('type', 1)->where('condition', 2)->first();
        $purchase_credit_total   = $purchase_credit->amount ? number_format($purchase_credit->amount, 0, ',','.') : 0;

        //NOTAS DE CREDITOS
        $credit_notes           = $this->getPurchasesReportSum()->where('type', 4)->first();


        $pdf = PDF::loadView('pages.reports.book_purchase_pdf', compact('purchases', 'purchase_counted', 'purchase_counted_total', 'purchase_credit', 'purchase_credit_total','credit_notes', 'purchases_sum'))->setPaper('A4','portrait');
        return $pdf->stream();
        // return view('pages.reports.purchases_reports_pdf', compact('purchases'));
    }
}
