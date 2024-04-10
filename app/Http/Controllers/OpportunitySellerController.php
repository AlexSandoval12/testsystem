<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalesOpportunitySellerRequest;
use App\Http\Requests\ImportSalesOpportunitityBySellerRequest;
use App\Http\Requests\UpdateSalesOpportunitySellerRequest;
use App\Library\CallCenter;
use App\Models\AdditionalService;
use App\Models\Contract;
use App\Models\CrmContact;
use App\Models\CrmProduct;
use App\Models\Enterprise;
use App\Models\HalfContact;
use App\Models\Insurance;
use App\Models\LoginCallCenter;
use App\Models\SalesOpportunityMovement;
use App\Models\SalesOpportunityTracking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\City;
use App\Models\ContactMedium;
use App\Models\Opportunity;

class OpportunitySellerController extends Controller
{
    public function index()
    {
        $opportunity_online =  Opportunity::where('seller_id', auth()->user()->id)->first();

        $half_contacts = $this->getHalfContacts();
        //OPORTUNIDADES NUEVAS
        $opportunities_new = Opportunity::with('contact_medium')
            ->where('status', 1)
            ->where('seller_id', auth()->user()->id);
        if (request()->s) {
            $opportunities_new = $opportunities_new->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }

        if (request()->date_range)
        {
            $from_date  = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[0])->format('Y-m-d 00:00:00');
            $until_date = Carbon::createFromFormat('d/m/Y', explode('-',str_replace(' ', '', request()->date_range))[1])->format('Y-m-d 23:59:59');

            $opportunities_new = $opportunities_new->whereBetween('created_at', [$from_date, $until_date]);
        }

        if (request()->half_contact_id) {
            $opportunities_new = $opportunities_new->whereIn('half_contact_id', request()->half_contact_id);
        }

        if (request()->leads_reassigned) {
            $opportunities_new = $opportunities_new->whereHas('sales_movements', function ($query) {
                if (request()->date) {
                    $query->where('created_at', 'like', Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d') . '%');
                }
                $query->where('new_id', auth()->user()->id)->where('type', 1)->orderBy('id', 'desc');
            });
        }

        if (request()->enterprise_id) {
            $opportunities_new = $opportunities_new->whereIn('enterprise_id', request()->enterprise_id);
        }
        if (auth()->user()->can('physical-legal-opportunities.create')) {
            $opportunities_new = $opportunities_new->orderByDesc('created_at');
        } else {
            $opportunities_new = $opportunities_new->orderByDesc('created_at');
        }

        $opportunities_new = $opportunities_new->paginate(20);

        //OPORTUNIDADES EN PROCESO
        // $setting = Setting::find(1);
        // $from_date = Carbon::now()->subDays($setting->days_call_again_leads)->format('Y-m-d 00:00:00');
        $opportunities_process = Opportunity::with('trackings')
                                                ->where('status', 5)
                                                ->where('seller_id', auth()->user()->id);
        if (auth()->user()->can('physical-legal-opportunities.create') or auth()->user()->can('denpro-seller-opportunities.create'))
        {
            $from_date_tracking = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
            $opportunities_process = $opportunities_process->whereExists(function ($query) use ($from_date_tracking) {
                                                    $query->select(DB::raw(1))
                                                          ->from('opportunity_trackings')
                                                          ->whereRaw("opportunity_trackings.opportunity_id = opportunities.id and opportunity_trackings.status = 1 AND opportunity_trackings.reassigned is null")
                                                          ->where('opportunity_trackings.created_at', '>=', $from_date_tracking);
                                                });
        }
        else
        {
            $opportunities_process = $opportunities_process->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('opportunity_trackings')
                    ->whereRaw("opportunity_trackings.opportunity_id = opportunities.id and opportunity_trackings.status = 1 and opportunity_trackings.call_again >= CURDATE()");
            })->orderBy('id');
        }
        // $opportunities_process->whereNotExists(function($query) use ($from_date)
        //     {
        //         $query->select(DB::raw(1))
        //             ->from(DB::raw('call_center_calls'))
        //               ->whereRaw('sales_opportunities.id = call_center_calls.sales_opportunity_id')
        //               ->where('call_center_calls.user_id', auth()->user()->id)
        //               ->where('call_center_calls.created_at', '>=', $from_date);
        //     });
        $opportunities_process = $opportunities_process->groupBy('id');

        if (request()->s) {
            $opportunities_process = $opportunities_process->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }
   
        if (request()->date_range)
        {
            $opportunities_process = $opportunities_process->whereBetween('created_at', [$from_date, $until_date]);
        }

        if (request()->half_contact_id) {
            $opportunities_process = $opportunities_process->whereIn('half_contact_id', request()->half_contact_id);
        }
        if (request()->leads_reassigned) {
            $opportunities_process = $opportunities_process->whereHas('sales_movements', function ($query) {
                if (request()->date) {
                    $query->where('created_at', 'like', Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d') . '%');
                }
                $query->where('new_id', auth()->user()->id)->where('type', 1)->orderBy('id', 'desc');
            });
        }
        if (request()->enterprise_id) {
            $opportunities_process = $opportunities_process->whereIn('enterprise_id', request()->enterprise_id);
        }

        $opportunities_process = $opportunities_process->paginate(20);
        //  $opportunities_process = $opportunities_process->get()->sortBy('first_call_again');

        // OPORTUNIDADES PENDIENTES DE CIERRE
        $opportunities_closer = Opportunity::with('contact_medium', 'closer',)
            ->where('status', 10)
            ->where('seller_id', auth()->user()->id)
            ->orderBy('id');
        if (request()->s) {
            $opportunities_closer = $opportunities_closer->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }

        if (request()->date_range)
        {
            $opportunities_closer = $opportunities_closer->whereBetween('created_at', [$from_date, $until_date]);
        }

        if (request()->half_contact_id) {
            $opportunities_closer = $opportunities_closer->whereIn('half_contact_id', request()->half_contact_id);
        }
        if (request()->leads_reassigned) {
            $opportunities_closer = $opportunities_closer->whereHas('sales_movements', function ($query) {
                if (request()->date) {
                    $query->where('created_at', 'like', Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d') . '%');
                }
                $query->where('new_id', auth()->user()->id)->where('type', 1)->orderBy('id', 'desc');
            });
        }

        $opportunities_closer = $opportunities_closer->paginate(20);

        //OPORTUNIDADES SIN SEGUIMIENTO
        if (auth()->user()->can('physical-legal-opportunities.create')) {
            $from_date_tracking = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
            $without_trackings = Opportunity::with('trackings')
                ->where('status', 5)
                ->where('seller_id', auth()->user()->id)
                ->whereNotExists(function ($query) use ($from_date_tracking) {
                    $query->select(DB::raw(1))
                        ->from('sales_opportunity_trackings')
                        ->whereRaw("sales_opportunity_trackings.sales_opportunity_id = sales_opportunities.id and sales_opportunity_trackings.status = 1 AND sales_opportunity_trackings.reassigned is null")
                        ->where('sales_opportunity_trackings.created_at', '>=', $from_date_tracking);
                })->groupBy('id')->orderByDesc('created_at');
        } else {
            $without_trackings = Opportunity::with('trackings')
                ->where('status', 5)
                ->where('seller_id', auth()->user()->id)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('opportunity_trackings')
                        ->whereRaw("opportunity_trackings.opportunity_id = opportunities.id and opportunity_trackings.status = 1 and opportunity_trackings.call_again >= CURDATE()");
                })->groupBy('id')->orderBy('id');
        }

        if (request()->s) {
            $without_trackings = $without_trackings->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }

        if (request()->date_range)
        {
            $without_trackings = $without_trackings->whereBetween('created_at', [$from_date, $until_date]);
        }

        if (request()->half_contact_id) {
            $without_trackings = $without_trackings->whereIn('half_contact_id', request()->half_contact_id);
        }

        if (request()->leads_reassigned) {
            $without_trackings = $without_trackings->whereHas('sales_movements', function ($query) {
                if (request()->date) {
                    $query->where('created_at', 'like', Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d') . '%');
                }
                $query->where('new_id', auth()->user()->id)->where('type', 1)->orderBy('id', 'desc');
            });
        }

        if (request()->enterprise_id) {
            $without_trackings = $without_trackings->whereIn('enterprise_id', request()->enterprise_id);
        }
        $opportunities_without_trackings_count = $without_trackings->get()->count();
        // $opportunities_without_trackings = $without_trackings->limit(20)->get();
        $opportunities_without_trackings = $without_trackings->paginate(20);

        //VENTAS CAJON
        $opportunities_drawer_sales = Opportunity::where('status', 13)->where('seller_id', auth()->user()->id)->get();

        //OPORTUNIDADES CERRADAS 
        $from_date = Carbon::now()->firstOfMonth();
        $closed_opportunities = Opportunity::where('status', 15)->where('seller_id', auth()->user()->id);

        // if (!in_array(3, auth()->user()->enterprises->pluck('id')->toArray()) && !in_array(6, auth()->user()->enterprises->pluck('id')->toArray()))
        // {
        //     $closed_opportunities = $closed_opportunities->where('selled_at', '>=', $from_date);
        // }

        if (request()->date_range)
        {
            $closed_opportunities = $closed_opportunities->whereBetween('created_at', [$from_date, $until_date]);
        }
        
        if (request()->half_contact_id) {
            $closed_opportunities = $closed_opportunities->whereIn('half_contact_id', request()->half_contact_id);
        }

        $closed_opportunities_qty = $closed_opportunities->get()->count();
        $closed_opportunities = $closed_opportunities->orderBy('selled_at', 'desc')->paginate(20);

        $contracts = null;
        // Contract::selectRaw('contracts.id, contracts.number, contracts.date, contracts.quotas_amount, (SELECT COUNT(*) FROM contract_fees WHERE contract_fees.contract_id = contracts.id AND contract_fees.number <= 3 AND contract_fees.residue > 0 AND contract_fees.expiration < CURDATE()) as cuotas, CONCAT(clients.first_name, " ", clients.last_name) as account_holder_fullname, clients.document_number as account_holder_document_number, enterprises.abbreviation as enterprise_abbreviation, enterprises.label as enterprise_label, debit_entities.name as contractingentity_debitentity_name, contracts.contract_type as contract_contract_type, contracts.amount')
        //     ->leftJoin('clients', 'contracts.account_holder_id', '=', 'clients.id')
        //     ->leftJoin('enterprises', 'contracts.enterprise_id', '=', 'enterprises.id')
        //     ->leftJoin('contracting_entities', 'contracting_entities.contract_id', '=', 'contracts.id')
        //     ->leftJoin('debit_entities', 'contracting_entities.debitentity_id', '=', 'debit_entities.id')
        //     ->where('contracts.status', 5)
        //     ->whereRaw('(enterprise_id = 1 OR enterprise_id = 2)')
        //     ->whereRaw('(SELECT COUNT(*) FROM contract_fees WHERE contract_fees.contract_id = contracts.id AND contract_fees.number <= 3 AND contract_fees.residue > 0) > 0')
        //     ->where('contracts.seller_id', auth()->user()->id)
        //     ->orderBy('contracts.number', 'asc')
        //     ->get();


        $call_center_login = false;
        $pause_motives     = [];
        if (auth()->user()->call_center_queue and auth()->user()->call_center_agent and auth()->user()->call_center_internal) {
            $login = LoginCallCenter::where([
                'queue'    => auth()->user()->call_center_queue,
                'agent'    => auth()->user()->call_center_agent,
                'internal' => auth()->user()->call_center_internal,
                'user_id'  => auth()->user()->id
            ])
                ->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
                ->first();
            if ($login) {
                $call_center_login = true;
            }
        }

        //OPORTUNIDADES RECHAZADAS
        $opportunities_rejected = Opportunity::with('contact_medium', 'sales_movements')
            ->where('status', 20)
            ->where('seller_id', auth()->user()->id)
            ->orderBy('id');
        if (request()->s) {
            $opportunities_rejected = $opportunities_rejected->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }

        if (request()->date_range)
        {
            $opportunities_rejected = $opportunities_rejected->whereBetween('created_at', [$from_date, $until_date]);
        }

        if (request()->half_contact_id) {
            $opportunities_rejected = $opportunities_rejected->whereIn('half_contact_id', request()->half_contact_id);
        }
        if (request()->leads_reassigned) {
            $opportunities_rejected = $opportunities_rejected->whereHas('sales_movements', function ($query) {
                if (request()->date) {
                    $query->where('created_at', 'like', Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d') . '%');
                }
                $query->where('new_id', auth()->user()->id)->where('type', 1)->orderBy('id', 'desc');
            });
        }

        if (request()->enterprise_id) {
            $opportunities_rejected = $opportunities_rejected->whereIn('enterprise_id', request()->enterprise_id);
        }

        $opportunities_rejected = $opportunities_rejected->paginate(20);

        // if ($opportunity_online) {
        //     return redirect('sales-opportunity/' . $opportunity_online->id);
        // }elseif (auth()->user()->online_user > 0) {
        //     return  redirect("call-center-lead/call?type=" . auth()->user()->online_user);
        // } else {
            return view('pages.opportunity-seller.index', compact('opportunities_closer', 'opportunities_process', 'opportunities_new', 'opportunities_without_trackings', 'contracts', 'half_contacts', 'call_center_login', 'opportunity_online', 'opportunities_drawer_sales', 'opportunities_without_trackings_count', 'closed_opportunities', 'opportunities_rejected', 'closed_opportunities_qty'));
        // }
    }

    public function create()
    {
        $cities = City::GetAllCached()->pluck('name', 'id');
        $half_contacts = $this->getHalfContacts();
        $insurances = $this->getInsurances();
        $types_phone_prefixes = config('constants.type-phone-prefixes');
        $type_numbers = config('constants.type-number');
        $enterprises = [];
        $additional_services = $this->getAdditionalServices();
        if (auth()->user()->can('physical-legal-opportunities.create'))
        {
            $enterprises = $this->getEnterprises();
        }
        return view('pages.sales-opportunity-seller.create', compact( 'cities','half_contacts', 'insurances', 'types_phone_prefixes', 'type_numbers', 'enterprises', 'additional_services'));
    }

    public function create_import()
    {
        return view('pages.sales-opportunity-seller.import');
    }

    public function store(CreateSalesOpportunitySellerRequest $request)
    {
        DB::transaction(function () use ($request) {
            $opportunity = SalesOpportunity::create([
                'client_type'               => $request->client_type ? $request->client_type : true,
                'enterprise_id'             => $request->enterprise_id ? $request->enterprise_id : auth()->user()->enterprises->first()->id,
                'branch_id'                 => auth()->user()->branch_id,
                'half_contact_id'           => $request->half_contact_id ? $request->half_contact_id : 36,
                'fullname'                  => $request->fullname,
                'prefix'                    => $request->prefix_id,
                'number_without_prefix'     => $request->phone,
                'phone'                     => $request->prefix_id . $request->phone,
                'email'                     => $request->email,
                'document_number'           => $request->document_number,
                'ruc'                       => $request->ruc ? $request->ruc : null,
                'insurance_id'              => $request->insurance_id,
                'lead'                      => $request->lead,
                'type_plan'                 => $request->type_plan,
                'observation'               => $request->observation,
                'user_id'                   => auth()->user()->id,
                'seller_id'                 => auth()->user()->id,
                'status'                    => 1,
                'creator'                   => 1,
                'contract_type'             => $request->contract_type,
                'dental_office'             => $request->dental_office,
                'city_id'                   => $request->city_id,
                'address'                   => $request->address,
                'location'                  => $request->addresses_locations,
                'scheduled'                 => $request->scheduled = 2 ? 0 : 1,
                'message_id'                => $request->message_id
            ]);

            if (auth()->user()->can('physical-legal-opportunities.create'))
            {
                if ($request->contact_name) {
                    foreach ($request->contact_name as $key => $value) {
                        CrmContact::create([
                            'opportunity_id'        => $opportunity->id,
                            'contact_name'          => $value,
                            'contact_charge'        => $request->contact_charge[$key],
                            'email'                 => $request->contact_email[$key],
                            'prefix'                => $request->contact_prefix[$key],
                            'number_without_prefix' => $request->contact_number[$key],
                            'number'                => $request->contact_prefix[$key] . $request->contact_number[$key]
                        ]);
                    }
                }
            }
        });
        toastr()->success('Agregado exitosamente');

        return response()->json(['success' => true]);
    }

    public function store_import(ImportSalesOpportunitityBySellerRequest $reques)
    {
        $excel = Excel::load(request()->file('file'))->get();
        $fiels_nulls = false;
        $last_enterprise = Enterprise::orderBy('id', 'desc')->first()->id;
        $last_half_contact = HalfContact::orderBy('id', 'desc')->first()->id;
        $prefixs = config('constants.phone-prefixes');
        if ($excel[0][0][1]) {
            foreach ($excel[0] as $row) {
                if ($row[0] == null or $row[1] == null or ($row[3] == null and $row[4] == null)  or  $row[5] == null or  $row[6] == null) {
                    $fiels_nulls = true;
                    break;
                }
                if ($row[5] > $last_enterprise) {
                    $fiels_nulls = true;
                    break;
                }
                if ($row[6] > $last_half_contact) {
                    $fiels_nulls = true;
                    break;
                }
            }
        } else {
            foreach ($excel as $row) {
                if ($row[0] == null or $row[1] == null or ($row[3] == null and $row[4] == null)  or  $row[5] == null or  $row[6] == null) {
                    $fiels_nulls = true;
                    break;
                }
                if ($row[5] > $last_enterprise) {
                    $fiels_nulls = true;
                    break;
                }
                if ($row[6] > $last_half_contact) {
                    $fiels_nulls = true;
                    break;
                }
            }
        }
        if ($fiels_nulls == false) {
            if ($excel[0][0][1]) {
                foreach ($excel[0] as $row) {
                    if (substr(strval($row[3]), 0, 3)  == '595') {
                        $phone = strval($row[3]);
                    } else {
                        $phone = substr(strval($row[3]), 0, 1)  == '0' ? strval($row[3]) : '0' . strval($row[3]);
                    }
                    $prefix = substr($phone, 0, 4);
                    if (!array_key_exists($prefix, $prefixs)) {
                        $prefix = substr($phone, 0, 3);
                        if (!array_key_exists($prefix, $prefixs)) {
                            $prefix = null;
                        }
                    }
                    SalesOpportunity::create([
                        'enterprise_id'   => $row[5],
                        'branch_id'       => 1,
                        'half_contact_id' => $row[6],
                        'fullname'        => $row[1],
                        'prefix'          => $prefix,
                        'number_without_prefix' =>  $prefix ? str_replace($prefix, '', $phone) : null,
                        'phone'           => $phone,
                        'email'           => $row[4],
                        'document_number' => $row[2],
                        'observation'     => $row[7],
                        'user_id'         => auth()->user()->id,
                        'seller_id'       => auth()->user()->id,
                        'status'          => 1,
                        'creator'         => 1
                    ]);
                }
            } else {
                foreach ($excel as $row) {
                    if (substr(strval($row[3]), 0, 3)  == '595') {
                        $phone = strval($row[3]);
                    } else {
                        $phone = substr(strval($row[3]), 0, 1)  == '0' ? strval($row[3]) : '0' . strval($row[3]);
                    }
                    $prefix = substr($phone, 0, 4);
                    if (!array_key_exists($prefix, $prefixs)) {
                        $prefix = substr($phone, 0, 3);
                        if (!array_key_exists($prefix, $prefixs)) {
                            $prefix = null;
                        }
                    }

                    SalesOpportunity::create([
                        'enterprise_id'   => $row[5],
                        'branch_id'       => 1,
                        'half_contact_id' => $row[6],
                        'fullname'        => $row[1],
                        'prefix'          => $prefix,
                        'number_without_prefix' =>  $prefix ? str_replace($prefix, '', $phone) : null,
                        'phone'           => $phone,
                        'email'           => $row[4],
                        'document_number' => $row[2],
                        'observation'     => $row[7],
                        'user_id'         => auth()->user()->id,
                        'seller_id'       => auth()->user()->id,
                        'status'          => 1,
                        'creator'         => 1
                    ]);
                }
            }
            toastr()->success('Importado exitosamente');
        } else {
            toastr()->warning('No se puede importar debido a campos vacios');
        }

        return redirect('sales-opportunity-seller/create-import');
    }

    public function edit(SalesOpportunity $opportunity)
    {
        session(['return_url' => ['sales_opportunity_sellers' => url()->previous()]]);
        $cities = City::GetAllCached()->pluck('name', 'id');
        $half_contacts = $this->getHalfContacts();
        $insurances = $this->getInsurances();
        $types_phone_prefixes = config('constants.type-phone-prefixes');
        $type_numbers = config('constants.type-number');
        $enterprises = $this->getEnterprises();
        $additional_services = $this->getAdditionalServices();
        $opportunity_products = CrmProduct::where('opportunity_id', $opportunity->id)->get();
        return view('pages.sales-opportunity-seller.edit', compact('cities', 'opportunity', 'half_contacts', 'insurances', 'types_phone_prefixes', 'type_numbers', 'enterprises', 'additional_services', 'opportunity_products'));
    }

    public function update(UpdateSalesOpportunitySellerRequest $request, SalesOpportunity $opportunity)
    {
        DB::transaction(function () use ($request, $opportunity) {
            $opportunity->update([
                'client_type'               => $request->client_type ? $request->client_type : true,
                'half_contact_id'           => $request->half_contact_id ? $request->half_contact_id : 36,
                'fullname'                  => $request->fullname,
                'prefix'                    => $request->prefix_id,
                'number_without_prefix'     => $request->phone,
                'phone'                     => $request->prefix_id . $request->phone,
                'email'                     => $request->email,
                'document_number'           => $request->document_number,
                'ruc'                       => $request->ruc ? $request->ruc : null,
                'insurance_id'              => $request->insurance_id,
                'lead'                      => $request->lead,
                'type_plan'                 => $request->type_plan,
                'observation'               => $request->observation,
                'contract_type'             => $request->contract_type,
                'dental_office'             => $request->dental_office,
                'city_id'                   => $request->city_id,
                'address'                   => $request->address,
                'location'                  => $request->addresses_locations,
                'message_id'                => $request->message_id
            ]);

            if (auth()->user()->can('physical-legal-opportunities.create')) {
                $opportunity->crm_contacts()->delete();
                if ($request->contact_name) {
                    foreach ($request->contact_name as $key => $value) {
                        CrmContact::create([
                            'opportunity_id'        => $opportunity->id,
                            'contact_name'          => $value,
                            'contact_charge'        => $request->contact_charge[$key],
                            'email'                 => $request->contact_email[$key],
                            'prefix'                => $request->contact_prefix[$key],
                            'number_without_prefix' => $request->contact_number[$key],
                            'number'                => $request->contact_prefix[$key] . $request->contact_number[$key]
                        ]);
                    }
                }
                $opportunity->crm_products()->delete();
                if ($request->product) {
                    foreach ($request->product as $key => $value) {
                        CrmProduct::create([
                            'opportunity_id'    => $opportunity->id,
                            'additional_service_id' => $value,
                            'amount'            => $request->product_amount[$key]
                        ]);
                    }
                }
            }
        });

        toastr()->success('Modificado exitosamente');

        return response()->json(['success' => true, 'return_url' => is_string(request()->session()->get('return_url')) ? request()->session()->get('return_url') : request()->session()->get('return_url')['sales_opportunity_sellers']]);
    }

    // public function delete(SalesOpportunity $opportunity)
    // {
    //     return view('pages.sales-opportunity.delete', compact('opportunity'));
    // }

    // public function destroy(DeleteSalesOpportunityRequest $request, SalesOpportunity $opportunity)
    // {
    //     $opportunity->update([
    //         'status' => 30,
    //         'deleted_reason' => $request->motive,
    //         'deleted_user_id' => auth()->user()->id,
    //         'deleted_at' => date('Y-m-d H:i:s'),
    //     ]);

    //     toastr()->success('Eliminado exitosamente');

    //     return redirect('sales-opportunity');
    // }

    public function download_matriz()
    {
        $excelArray = [];
        $excelArray[] = [
            'Tipo cliente',
            'Nombre Cliente',
            'Nro documento',
            'Telefono',
            'Email',
            'Unidad de negocio',
            'Medio de contacto',
            'Observación'
        ];

        for ($i = 1; $i <= 3; $i++) {
            $excelArray[$i] = [
                1,
                'testeo' . $i,
                '123123',
                '994123123',
                'testeo' . $i . '@gmail.com',
                3,
                2
            ];
        }

        $enterprises[] = [
            'Codigo',
            'Descripción'
        ];
        foreach ($this->getEnterprises() as $key => $value) {
            $enterprises[] = [
                $key,
                $value
            ];
        }

        $half_contacts[] = [
            'Codigo',
            'Descripción'
        ];
        foreach ($this->getHalfContacts() as $key => $value) {
            $half_contacts[] = [
                $key,
                $value
            ];
        }

        $client_type[0] = [
            'Codigo',
            'Descripción'
        ];
        foreach (config('constants.client-type') as $key => $value) {
            $client_type[] = [
                $key,
                $value
            ];
        }

        Excel::create('Oportunidades por vendedor', function ($excel) use ($excelArray, $enterprises, $half_contacts, $client_type) {
            $excel->sheet('sheet1', function ($sheet) use ($excelArray) {
                $sheet->fromArray($excelArray, null, 'A1', false, false);
            });
            $excel->sheet('Tipo cliente', function ($sheet) use ($client_type) {
                $sheet->fromArray($client_type, null, 'A1', false, false);
            });
            $excel->sheet('Unidades de negocio', function ($sheet) use ($enterprises) {
                $sheet->fromArray($enterprises, null, 'A1', false, false);
            });
            $excel->sheet('Medios de contacto', function ($sheet) use ($half_contacts) {
                $sheet->fromArray($half_contacts, null, 'A1', false, false);
            });
        })->export('xlsx');
    }

    public function get_closed_opportunities()
    {
        if (request()->ajax())
        {
            $opportunities = SalesOpportunity::where('seller_id', request()->seller_id)
                                            ->where('status', 15)
                                            ->where('document_number', request()->document_number)
                                            ->where('contract_id', null)
                                            ->orderBy('id')->get()->each->setAppends([]);
            return response()->json($opportunities);
        }

        abort(404);
    }

    private function getHalfContacts()
    {
        return ContactMedium::where('status',1)
            ->pluck('name', 'id');
    }

    // private function getInsurances()
    // {
    //     return Insurance::GetAllCached()
    //         ->sortBy('name')
    //         ->pluck('name', 'id');
    // }

    private function getEnterprises()
    {
        return Enterprise::GetAllCached()->sortBy('name')->pluck('name', 'id');
    }

    private function getAdditionalServices()
    {
        return  AdditionalService::select('service', 'id', 'enterprise_id')->where('status', true)->whereIn('enterprise_id', [3, 6])->orderBy('service', 'image', 'asc')->get()->toArray();
    }
}
