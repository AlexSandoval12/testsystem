<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCrmProductRequest;
use App\Http\Requests\CreateOpportunityTrackingRequest;
use App\Http\Requests\CreateSalesOpportunityRequest;
use App\Http\Requests\CreateSalesOpportunityTrackingRequest;
use App\Http\Requests\DeleteSalesOpportunityRequest;
use App\Http\Requests\ImportSaleOpportunitiesRequest;
use App\Http\Requests\UpdateSalesOpportunityRequest;
use App\Http\Requests\CreateSalesOpportunityFileRequest;
use App\Library\CallCenter;
use App\Library\NextLeadUser;
use App\Models\AdditionalService;
use App\Models\Branch;
use App\Models\CallCenterCall;
use App\Models\CallCenterCallPhone;
use App\Models\CallCenterPauseMotive;
use App\Models\City;
use App\Models\Ciudad;
use App\Models\ContactMedium;
use App\Models\ContractPromotion;
use App\Models\CrmProduct;
use App\Models\LastSeller;
use App\Models\LoginCallCenter;
use App\Models\Opportunity;
use App\Models\SalesOpportunity;
use App\Models\SalesOpportunityRejectionMotive;
use App\Models\SalesOpportunityTracking;
use App\Models\SalesOpportunityFile;
use App\Models\SellerTeam;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class OpportunityController extends Controller
{
    public function index()
    {
        $half_contacts = $this->getHalfContacts();
        $status = $this->getStatus();
        $sellers = $this->getSellers();
        $opportunities = Opportunity::with('seller', 'branch', 'contact_medium')
            ->where('status', '<', 30);
        if (request()->s) {
            $opportunities = $opportunities->where(function ($query) {
                $query->orWhere('fullname', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('email', 'LIKE', '%' . request()->s . '%')
                    ->orWhere('document_number', 'LIKE', '%' . request()->s . '%');
            });
        }
      

        if (request()->seller_id) {
            $opportunities = $opportunities->where('seller_id', request()->seller_id);
        }
        if (request()->half_contact_id) {
            $opportunities = $opportunities->whereIn('half_contact_id', request()->half_contact_id);
        }
        if (request()->status) {
            $opportunities = $opportunities->whereIn('status', request()->status);
        }
        if (auth()->user()->seller_supervisor && !auth()->user()->can('sales-opportunity-all.index')) {
            $team_leaders_id = User::where('status', true)->where('seller', true)->where('seller_supervisor_id', auth()->user()->id)->get()->pluck('id')->toArray();
            $opportunities = $opportunities->where(function ($query) use ($team_leaders_id) {
                $query->whereHas('seller', function ($query2) {
                    $query2->where('seller_supervisor_id', auth()->user()->id);
                })->orWhereHas('seller', function ($query3) use ($team_leaders_id) {
                    $query3->whereIn('seller_supervisor_id', $team_leaders_id);
                });
            });
        }
        $opportunities = $opportunities->orderBy('id', 'desc')->paginate(20);
        return view('pages.opportunity.index', compact('opportunities', 'half_contacts', 'status', 'sellers'));
    }

    public function create()
    {
        $sellers = $this->getSellers();
        $half_contacts = $this->getHalfContacts();
        // $insurances = $this->getInsurances();
        $branches =  $this->getBranches();
        // $contract_promotions = $this->getContractPromotions();
        $seller_teams = SellerTeam::whereExists(function ($query) {
                                            $query->select(DB::raw(1))
                                                ->from('seller_team_users')
                                                ->whereRaw("seller_team_users.seller_team_id = seller_teams.id");
                                        })->where('type_team', 1)->where('status', true)->pluck('name', 'id');

        return view('pages.opportunity.create', compact('half_contacts',  'branches', 'seller_teams', 'sellers'));
    }

    public function store(CreateSalesOpportunityRequest $request)
    {
        DB::transaction(function () use ($request)
        {
            Opportunity::create([
                'branch_id'             => $request->branch_id,
                'contact_medium_id'     => $request->contact_medium_id,
                'fullname'              => $request->fullname,
                'phone'                 => $request->phone,
                'email'                 => $request->email,
                'document_number'       => $request->document_number,
                // 'insurance_id'          => $request->insurance_id,
                // 'contract_promotion_id' => $request->contract_promotion_id,
                'lead'                  => $request->lead,
                'type_plan'             => $request->type_plan,
                'amount'                => $request->amount,
                'observation'           => $request->observation,
                'user_id'               => auth()->user()->id,
                'seller_id'             => $request->seller_id,
                'status'                => 1,
                'contract_type'         => $request->contract_type,
            ]);

        });

        toastr()->success('Agregado exitosamente');

        return redirect('opportunity');
    }

    public function import()
    {
        $enterprises = $this->getEnterprises();
        $half_contacts = $this->getHalfContacts();
        $insurances = $this->getInsurances();
        $contract_promotions = $this->getContractPromotions();
        $branches = $this->getBranches();
        $seller_teams = SellerTeam::whereExists(
            function ($query) {
                $query->select(DB::raw(1))
                    ->from('seller_team_users')
                    ->whereRaw("seller_team_users.seller_team_id = seller_teams.id");
            }
        )->whereIn('type_team',[1,2])->where('status', true)->pluck('name', 'id');

        return view('pages.sales-opportunity.import', compact('half_contacts', 'enterprises', 'insurances', 'branches', 'contract_promotions', 'seller_teams'));
    }

    public function import_post(ImportSaleOpportunitiesRequest $request)
    {

        $excel = Excel::load(request()->file('file'))->get();
        $import_success = false;
        $fields_nulls = false;
        $main_fiedls = false;
        foreach ($excel as $row)
        {
            if ($row[0] == null or $row[1] == null)
            {
                $main_fiedls = true;
                break;
            }

            if (!$request->half_contact_id)
            {
                if ($row[5] == null)
                {
                    $fields_nulls = true;
                    break;
                }
            }
        }
        if ($fields_nulls == false && $main_fiedls == false)
        {
            foreach ($excel as $key => $row2)
            {
                $concat_teams = '';
                foreach ($request->teams as $key => $value) {
                    $concat_teams .= ($key == 1 ? '' : '_') . $value;
                }

                $next_seller_id = NextLeadUser::next_seller_id($request->enterprise_id, $request->teams, $concat_teams, $request->seller_type);

                if($next_seller_id)
                {
                    SalesOpportunity::create([
                        'enterprise_id'         => $request->enterprise_id,
                        // 'branch_id'             => $request->branch_id ? $request->branch_id : $row2[6],
                        'branch_id'             => $request->branch_id ? $request->branch_id : null,
                        'half_contact_id'       => $request->half_contact_id ? $request->half_contact_id : $row2[5],
                        'fullname'              => $row2[0],
                        'phone'                 => $row2[1],
                        'email'                 => isset($row2[2]) ? $row2[2] : null,
                        'contact_name'          => $row2[7],
                        'document_number'       => $row2[3],
                        'insurance_id'          => $request->insurance_id,
                        'contract_promotion_id' => $request->contract_promotion_id,
                        'lead'                  => $row2[4],
                        'type_plan'             => $request->type_plan,
                        'observation'           => $row2[8] ? $row2[8] : $request->observation,
                        'user_id'               => auth()->user()->id,
                        'seller_id'             => $next_seller_id,
                        'status'                => 1,
                        'creator'               => 2
                    ]);

                    LastSeller::updateorcreate(
                        [
                            'enterprise_id'     => $request->enterprise_id,
                            'seller_type'       => $request->seller_type,
                            'distribution_type' => 1,
                            'teams_concat'      => $concat_teams

                        ],
                        [
                            'last_seller'  => $next_seller_id
                        ]
                    );
                    $import_success = true;
                }
                else
                {
                    $import_success = false;
                }
                // $variable_update = $request->seller_city==1 ? 'last_seller_asu_opportunity_id' : 'last_seller_int_opportunity_id';
                // Enterprise::findOrFail($request->enterprise_id)->update([$variable_update => $next_seller_id]);
            }
            if($import_success == true)
            {
                toastr()->success('Importado exitosamente');
            }
            else
            {
                toastr()->warning('Características de vendedor selccionados no existe coincidencia en base de datos.');

            }
        }
        else
        {
            if ($fields_nulls == true)
            {
                toastr()->warning('No se puede importar, campo Medio de contacto no encontrado');
            }
            else
            {
                toastr()->warning('Campo Nombre Completo y/o Telefono vacios.');
            }
        }

        return redirect('sales-opportunity');
    }

    public function show(Opportunity $opportunity)
    {
        if (auth()->user()->seller_supervisor and $opportunity->seller->seller_supervisor_id != auth()->user()->id and $opportunity->seller_id != auth()->user()->id && !auth()->user()->can('sales-opportunity-all.index')) {
            toastr()->warning('Codigo de oportunidad no existe');
            return redirect('opportunity');
        }

        // $opportunity_products = CrmProduct::where('opportunity_id', $opportunity->id)->get();
        // $call_center_call = CallCenterCall::where('sales_opportunity_id', $opportunity->id)->where('status', 1)->first();
        $cities = Ciudad::pluck('ciudad', 'id');
        // Buscar los Motivos de Pausa de las Llamadas
        $con = 0;
        $call_center_login = false;
        
        //MODIFICACION SOLICITADA POR NIMIA TICKET 10278 31/03/2022
        if (auth()->user()->can('sales-opportunity-trackings.corporative-view'))
        {
            $actions = config('constants.opportunity-actions');
            unset($actions[9], $actions[10],$actions[12],$actions[13],$actions[14]);
        }
        else
        {
            $actions = array_diff_key(config('constants.opportunity-actions'), array_flip([1,3,4,5,6,8,9,10,11]));
            // unset($actions[3], $actions[4], $actions[5], $actions[6], $actions[7], $actions[8]);
        }

        return view('pages.opportunity.show', compact('opportunity', 'cities', 'call_center_login', 'con', 'actions'));
    }

    public function tracking_store(CreateOpportunityTrackingRequest $request, Opportunity $opportunity)
    {
        DB::transaction(function () use ($request, $opportunity) {
            // $next_closer_id = $this->getNextCloserId($opportunity->enterprise_id);
            $sales_opportunity_tracking = $opportunity->trackings()->create([
                'attended'          => $request->attended ? $request->attended : null,
                'contact_form'      => $request->contact_form ? $request->contact_form : null,
                'action'            => $request->action_id,
                'not_attended'      => $request->not_attended,
                'call_again'        => $request->call_again,
                'closer'            => $request->closer,
                'sold'              => $request->sold,
                'reject'            => ($request->radio == 3 || $request->reject) ? true : NULL,
                'observation'       => $request->observation,
                'status'            => 1,
                'user_id'           => auth()->user()->id,
                'scheduled'         => $request->scheduled ? ($request->scheduled == 2 ? 0 : 1) : 0
            ]);

            if ($opportunity->status == 20) {
                $opportunity->update(['status' => 5]);
            }
            if ($request->radio == 1) {
                // asignar a cerrador de venta
                $opportunity->update([
                    'status' => 10,
                    // 'closer_id' => $next_closer_id,
                    'closed_in' => 1,
                    'city_id'   => $request->city_id,
                    'deadline'  => $request->deadline,
                    'address'   => $request->address,
                    'closed_at' => date('Y-m-d H:i:s'),
                ]);

                // Enterprise::findOrFail($opportunity->enterprise_id)->update(['last_closer_opportunity_id' => $next_closer_id]);
            } elseif ($request->radio == 2) {
                // vendido
                $opportunity->update([
                    'status' => 15,
                    'selled_at' => date('Y-m-d H:i:s')
                ]);
                // foreach ($opportunity->crm_products as $product)
                // {
                //     if (in_array($product->id, $request->selected))
                //     {
                //         $product->update(['sold' => true]);
                //     }
                // }
                // if ($request->product_selected) {
                //     foreach ($request->product_selected as $key => $value) {
                //         CrmProduct::create([
                //             'opportunity_id'    => $opportunity->id,
                //             'additional_service_id' => $request->product[$value],
                //             'paramedic'         => ($request->product[$value] == 13 && $request->paramedic) ? true : null,
                //             'ambulance'         => ($request->product[$value] == 13 && $request->ambulance) ? true : null,
                //             'amount'            => $request->product_amount[$value],
                //             'sold'              => 1,
                //             'observation'       => $request->product_observation[$value]
                //         ]);
                //     }
                // }
            } elseif ($request->radio == 3 || $request->reject) {
                // rechazado
                $opportunity->update([
                    'status' => 20,
                    'rejected_at' => date('Y-m-d H:i:s'),
                    'rejected_motive_id' => $request->rejected_motive_id
                ]);
                if (in_array(3, auth()->user()->enterprises->pluck('id')->toArray()) || in_array(6, auth()->user()->enterprises->pluck('id')->toArray())) {
                    $opportunity->update([
                        'attended' => 2
                    ]);
                }
            } elseif ($request->radio == 4) {
                // $branch = auth()->user()->branch->id;
                // switch ($branch) {
                //     case 1:
                //         $city_id = 2;
                //         break;
                //     case 2:
                //         $city_id = 26;
                //         break;
                //     case 3:
                //         $city_id = 43;
                //         break;
                //     default:
                //         $city_id = 1;
                //         break;
                // }
                //asignar cierre en clinica
                $opportunity->update([
                    'status' => 10,
                    'closed_in' => 2,
                    'city_id'   => $request->city_id,
                    'deadline'  => $request->deadline,
                    'closed_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                if ($opportunity->status == 1) {
                    // en proceso
                    $opportunity->update([
                        'status' => 5,
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $opportunity->update(['online_call' => 2, 'scheduled'=> $request->schedule ? ($request->scheduled == 2 ? 0 : 1) : 0]);



            // Numeros a los que llamar
            // Estado de los numeros llamados
            // 1 - Pendiente
            // 2 - No Contestado
            // 3 - Contestado

            // Llamador Predictivo
            // $call_center = new CallCenter();

            // $call_center_call = CallCenterCall::where([
            //     'queue'       => auth()->user()->call_center_queue,
            //     'agent'       => auth()->user()->call_center_agent,
            //     'internal'    => auth()->user()->call_center_internal,
            //     'user_id'     => auth()->user()->id,
            //     'sales_opportunity_id' => $opportunity->id,
            //     'status'      => 1
            // ])
            //     ->first();
            // if ($call_center_call) {
            //     $callCenterCallPhones = CallCenterCallPhone::where(['number_phone'  => $opportunity->phone, 'call_center_calls_id' => $call_center_call->id])->get();
            //     $callCenterCallPhone = CallCenterCallPhone::where(['number_phone'   => $opportunity->phone, 'call_center_calls_id' => $call_center_call->id, 'status' => 1])->first();
            //     foreach ($callCenterCallPhones as $key => $value) {
            //         $value->update(['sales_opportunity_tracking_id' => $sales_opportunity_tracking->id]);
            //     }

            //     if ($request->attended == 1) {
            //         $call_center_call->update(['status' => 3]);
            //         $callCenterCallPhone->update(['status' => 3]);
            //     }

            //     if ($request->attended == 2) {
            //         $call_center_call->update(['status' => 2]);
            //         $callCenterCallPhone->update(['status' => 2]);
            //     }

            //     // Cortar la Llamada en Curso
            //     $status = $call_center->status_agent(auth()->user()->call_center_agent);
            //     foreach ($status as $key => $value) {
            //         if ($key == "remote_chan" and $value != null) {
            //             $call_center->hang_call($value);
            //         }
            //     }
            // }
        });

        toastr()->success('Agregado exitosamente');
        return response()->json(['success' => true]);
    }

    public function store_files(CreateSalesOpportunityFileRequest $request, SalesOpportunity $opportunity)
    {
        DB::transaction(function () use ($request, $opportunity) {
            $files = $request->file('files');

            foreach ($files as $key => $file) {
                $file = $file->store('salesopportunity-files');

                $filename = basename($file);

                $opportunity->files()->create([
                    'file' => $filename,
                    'description' => $request->description,
                    'user_id' => auth()->user()->id
                ]);
            }
        });

        toastr()->success('Agregado exitosamente');

        return response()->json(['success' => true]);
    }

    public function download_file(SalesOpportunityFile $files)
    {
        $path = storage_path('app/salesopportunity-files/' . $files->file);
        if (request()->show) {
            $files = File::get($path);
            $mime_type = File::mimeType($path);

            $response = response()->make($files, 200);

            $response->header('Content-Type', $mime_type);

            return $response;
        }

        return response()->download($path);
    }

    public function edit(SalesOpportunity $opportunity)
    {
        $enterprises = $this->getEnterprises();
        $half_contacts = $this->getHalfContacts();
        $insurances = $this->getInsurances();
        $branches =  $this->getBranches();
        $contract_promotions = $this->getContractPromotions();
        return view('pages.sales-opportunity.edit', compact('opportunity', 'enterprises', 'half_contacts', 'insurances', 'branches', 'contract_promotions'));
    }

    public function update(UpdateSalesOpportunityRequest $request, SalesOpportunity $opportunity)
    {
        $opportunity->update([
            'half_contact_id' => $request->half_contact_id,
            'fullname'        => $request->fullname,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'document_number'  => $request->document_number,
            'insurance_id'    => $request->insurance_id,
            'contract_promotion_id' => $request->contract_promotion_id,
            'lead'            => $request->lead,
            'amount'          => $request->amount,
            'type_plan'       => $request->type_plan,
            'observation'     => $request->observation,
            'contract_type'   => $request->contract_type
        ]);

        toastr()->success('Modificado exitosamente');

        return redirect('sales-opportunity');
    }

    public function delete(SalesOpportunity $opportunity)
    {
        return view('pages.sales-opportunity.delete', compact('opportunity'));
    }

    public function destroy(DeleteSalesOpportunityRequest $request, SalesOpportunity $opportunity)
    {
        $opportunity->update([
            'status' => 30,
            'deleted_reason' => $request->motive,
            'deleted_user_id' => auth()->user()->id,
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);

        toastr()->success('Eliminado exitosamente');

        return redirect('sales-opportunity');
    }

    public function ajax_add_product_crm(CreateCrmProductRequest $request)
    {
        if (request()->ajax()) {
            $opportunity = SalesOpportunity::find($request->modal_opportunity_id);
            CrmProduct::create([
                'opportunity_id'    => $opportunity->id,
                'additional_service_id' => $request->modal_product_id,
                'paramedic'         => $request->modal_product_id == 13 ? ($request->modal_paramedic ? true : false) : false,
                'ambulance'         => $request->modal_product_id == 13 ? ($request->modal_ambulance ? true : false) : false,
                'amount'            => $request->modal_product_amount,
                'sold'              => 1,
                'observation'       => $request->modal_product_observation
            ]);

            return response()->json([
                'success' => true
            ]);
        }

        abort(404);
    }

    public function sales_board()
    {
        $enterprises = $this->getEnterprises();
        $supervisors = $this->getSupervisors();
        $opportunities = null;
        $sellers = null;
        if (request()->enterprise_id && request()->from_date && request()->until_date) {
            $sellers = User::orderBy('first_name')
                ->where('seller', true)
                ->where('status', true)
                ->where('enterprise_id', request()->enterprise_id);
            if (request()->supervisor_id) {
                $sellers = $sellers->where('seller_supervisor_id', request()->supervisor_id);
            }
            $sellers = $sellers->orderBy('last_name')
                ->get();

            $opportunities = $this->getSalesBoard();
        }

        return view('pages.sales-opportunity.sales-board', compact('opportunities', 'enterprises', 'sellers', 'supervisors'));
    }

    private function getSalesBoard()
    {
        $sales = SalesOpportunity::with('seller')
            ->selectRaw('seller_id, status, COUNT(status) as total')
            ->where('status', '<=', 10)
            ->groupBy('status', 'seller_id')
            ->get();

        $from_date_tracking = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
        $without_trackings = SalesOpportunity::with('seller')
            ->selectRaw('seller_id, COUNT(*) as quantity')
            ->where('status', 5)
            ->whereNotExists(function ($query) use ($from_date_tracking) {
                $query->select(DB::raw(1))
                    ->from('sales_opportunity_trackings')
                    ->whereRaw("sales_opportunity_trackings.sales_opportunity_id = sales_opportunities.id and sales_opportunity_trackings.status = 1 AND sales_opportunity_trackings.reassigned is null")
                    ->where('sales_opportunity_trackings.created_at', '>=', $from_date_tracking);
            })
            ->groupBy('seller_id')
            ->get();


        $with_trackings = SalesOpportunity::with('seller')
            ->selectRaw('seller_id, COUNT(*) as quantity')
            ->where('status', 5)
            ->whereExists(function ($query) use ($from_date_tracking) {
                $query->select(DB::raw(1))
                    ->from('sales_opportunity_trackings')
                    ->whereRaw("sales_opportunity_trackings.sales_opportunity_id = sales_opportunities.id and sales_opportunity_trackings.status = 1 AND sales_opportunity_trackings.reassigned is null")
                    ->where('sales_opportunity_trackings.created_at', '>=', $from_date_tracking);
            })
            ->groupBy('seller_id')
            ->get();
        $array = [];
        foreach ($sales as $key => $sale) {
            $array[$sale->seller_id][$sale->status] = $sale->total;
        }

        foreach ($without_trackings as $key => $value) {
            $array[$value->seller_id]['without_trackings'] = $value->quantity;
        }

        foreach ($with_trackings as $key => $value) {
            $array[$value->seller_id]['with_trackings'] = $value->quantity;
        }

        $from_date = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d 00:00:00');
        $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d 23:59:59');
        $sales_two = SalesOpportunity::with('seller')
            ->selectRaw('seller_id, status, COUNT(status) as total')
            ->whereRaw('(status = 15 AND selled_at BETWEEN ? AND ?) OR (status = 20 AND rejected_at BETWEEN ? AND ?)', [$from_date, $until_date, $from_date, $until_date])
            ->groupBy('status', 'seller_id')
            ->get();

        foreach ($sales_two as $key => $sale) {
            $array[$sale->seller_id][$sale->status] = $sale->total;
        }

        return $array;
    }

    public function tracking_board()
    {
        $enterprises = $this->getEnterprises();
        $supervisors = $this->getSupervisors();
        $trackings   = collect([]);
        if (request()->enterprise_id && request()->from_date && request()->until_date) {
            $trackings = collect($this->getTrackingBoard());
        }

        return view('pages.sales-opportunity.tracking-board', compact('trackings', 'enterprises', 'supervisors'));
    }

    private function getTrackingBoard()
    {
        $from_date  = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d 00:00:00');
        $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d 23:59:59');

        // Traer vendedores
        $sellers = User::orderBy('first_name')
            ->where('seller', true)
            ->where('status', true)
            ->where('enterprise_id', request()->enterprise_id);
        if (request()->supervisor_id) {
            $sellers = $sellers->where('seller_supervisor_id', request()->supervisor_id);
        }
        $sellers = $sellers->orderBy('last_name')
            ->get();

        // Gestiones Realizadas - Gestiones Negativas - Gestiones Positivas
        $trackings = SalesOpportunityTracking::select(DB::raw('COUNT(*) as count, attended, user_id'))
            ->where('status', true)
            ->whereBetween('created_at', [$from_date, $until_date])
            ->groupBy('user_id', 'attended')
            ->get();

        $tc = [];
        foreach ($trackings as $key => $tracking) {
            $tc[$tracking->user_id][$tracking->attended ? $tracking->attended : 1] = $tracking->count;
        }
        // Volver a Llamar sacados
        $total_call_agains = SalesOpportunityTracking::select(DB::raw('COUNT(*) as count, user_id'))
            ->where('status', true)
            ->where('call_again', '!=', '0000-00-00')
            ->whereNotNull('call_again')
            ->whereBetween('created_at', [$from_date, $until_date])
            ->groupBy('user_id')
            ->get();
        //volver a llamar a gestionar
        $total_call_again_to_tracking = SalesOpportunityTracking::select(DB::raw('COUNT(*) as count, user_id'))
            ->where('status', true)
            ->where('call_again', '!=', '0000-00-00')
            ->whereBetween('call_again', [$from_date, $until_date])
            ->whereNotNull('call_again')
            ->groupBy('user_id')
            ->get();
        $tca = [];
        $tca2 = [];
        foreach ($total_call_agains as $key => $total_call_again) {
            $tca[$total_call_again->user_id] = $total_call_again->count;
        }

        foreach ($total_call_again_to_tracking as $key => $call_again) {
            $tca2[$call_again->user_id] = $call_again->count;
        }

        // Carteras Negativas - Carteras Positivas
        $total_att_hols = SalesOpportunityTracking::select(DB::raw('COUNT(*) as count, user_id, MIN(attended) as attended, sales_opportunity_id'))
            ->where('status', true)
            ->whereBetween('created_at', [$from_date, $until_date])
            ->groupBy('user_id', 'sales_opportunity_id')
            ->get();
        $tah = [];
        foreach ($total_att_hols as $key => $total_att_hol) {
            if (!isset($tah[$total_att_hol->user_id][$total_att_hol->attended ? $total_att_hol->attended : 1])) {
                $tah[$total_att_hol->user_id][$total_att_hol->attended ? $total_att_hol->attended : 1] = 0;
            }
            $tah[$total_att_hol->user_id][$total_att_hol->attended ? $total_att_hol->attended : 1]++;
        }
        $trackings_array = [];
        foreach ($sellers as $key => $seller) {
            $positive_collections = isset($tc[$seller->id][1]) ? $tc[$seller->id][1] : 0;
            $negative_collections = isset($tc[$seller->id][2]) ? $tc[$seller->id][2] : 0;
            $call_again = isset($tca[$seller->id]) ? $tca[$seller->id] : 0;
            $call_again_to_tracking = isset($tca2[$seller->id]) ? $tca2[$seller->id] : 0;
            $positive_holders = isset($tah[$seller->id][1]) ? $tah[$seller->id][1] : 0;
            $negative_holders = isset($tah[$seller->id][2]) ? $tah[$seller->id][2] : 0;

            // $trackings_array[$key]['id'] = $seller->id;
            $trackings_array[$key]['fullname'] = $seller->fullname;
            $trackings_array[$key]['total_collections'] = $positive_collections + $negative_collections;
            $trackings_array[$key]['total_positive_collections'] = $positive_collections;
            $trackings_array[$key]['total_negative_collections'] = $negative_collections;
            $trackings_array[$key]['total_call_again'] = $call_again;
            $trackings_array[$key]['total_holders'] = $positive_holders + $negative_holders;
            $trackings_array[$key]['total_positive_holders'] = $positive_holders;
            $trackings_array[$key]['total_negative_holders'] = $negative_holders;
            $trackings_array[$key]['total_call_again_to_tracking'] = $call_again_to_tracking;
        }

        return $trackings_array;
    }

    public function sales_board_corporative()
    {
        $supervisors = $this->getSupervisors();
        $enterprises = $this->getEnterprises()->filter(function ($value, $key) {
            if ($key != 7) {
                return $value;
            }
        });
        if (request()->from_date && request()->until_date) {
            $sales_opportunities = collect($this->getSalesBoardCorporative());
        }

        return view('pages.sales-opportunity.sales-board-corporative', compact('sales_opportunities', 'enterprises', 'supervisors'));
    }

    private function getSalesBoardCorporative()
    {
        $from_date  = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d 00:00:00');
        $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d 23:59:59');

        //UNIDADES DE NEGOCIO
        $enterprises = $this->getEnterprises()->filter(function ($value, $key) {
            if ($key != 7) {
                return $value;
            }
        });
        // Traer vendedores
        $sellers = User::orderBy('first_name')
            ->where('seller', true)
            ->where('status', true)
            ->whereIn('enterprise_id', [3, 6]);
        if (request()->supervisor_id) {
            $sellers = $sellers->where('seller_supervisor_id', request()->supervisor_id);
        }
        $sellers = $sellers->orderBy('last_name')
            ->get();
        $sales_opportunities = [];
        foreach ($sellers as $key => $seller) {
            $sales_opportunities[$seller->id]['fullname'] = $seller->fullname;
            $sales_opportunities[$seller->id]['total_quantity'] = 0;
            $sales_opportunities[$seller->id]['total_amount'] = 0;
            foreach ($enterprises as $enterprise_id => $enterprise) {
                $sales_opportunities[$seller->id]['oportunidades'][$enterprise_id]['quantity'] = 0;
                $sales_opportunities[$seller->id]['oportunidades'][$enterprise_id]['amount'] = 0;
            }
        }

        //OPORTUNIDAES ACEPTADAS
        $opportunities = SalesOpportunity::selectRaw('sales_opportunities.seller_id, 
                                                        sales_opportunities.enterprise_id, 
                                                        SUM(crm_products.amount) as amount')
            ->leftJoin('crm_products', 'sales_opportunities.id', '=', 'crm_products.opportunity_id')
            ->where('status', 15)
            ->whereBetween('selled_at', [$from_date, $until_date])
            ->whereHas('seller', function ($query) {
                $query->where('status', true)->whereIn('enterprise_id', [3, 6]);
            })->groupBy('sales_opportunities.id', 'sales_opportunities.seller_id', 'sales_opportunities.enterprise_id')->get();
        foreach ($opportunities as $key => $value) {
            $sales_opportunities[$value->seller_id]['oportunidades'][$value->enterprise_id]['quantity']++;
            $sales_opportunities[$value->seller_id]['oportunidades'][$value->enterprise_id]['amount'] += $value->amount;
            $sales_opportunities[$value->seller_id]['total_quantity']++;
            $sales_opportunities[$value->seller_id]['total_amount'] += $value->amount;
        }

        return $sales_opportunities;
    }

    public function tracking_board_corporative()
    {
        $supervisors = $this->getSupervisors();
        $trackings   = Collect([]);
        if (request()->from_date && request()->until_date) {
            $trackings = $this->getTrackingBoardCorporative();
        }
        return view('pages.sales-opportunity.tracking-board-corporative', compact('trackings', 'supervisors'));
    }

    private function getTrackingBoardCorporative()
    {

        $from_date  = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d 00:00:00');
        $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d 23:59:59');

        // Traer vendedores
        $sellers = User::orderBy('first_name')
            ->where('seller', true)
            ->where('status', true)
            ->whereIn('enterprise_id', [3, 6, 7]);
        if (request()->supervisor_id) {
            $sellers = $sellers->where('seller_supervisor_id', request()->supervisor_id);
        }
        $sellers = $sellers->orderBy('last_name')
            ->get();

        $sales_opportunities = SalesOpportunity::selectRaw("COUNT(*) AS cantidad, users.id AS seller_id, 
                                                        CONCAT(users.first_name,' ',users.last_name) AS fullname,
                                                        sales_opportunities.status")
            ->join('users', 'sales_opportunities.seller_id', '=', 'users.id')
            ->whereIn('users.enterprise_id', [3, 6, 7])
            ->where('users.status', 1)
            ->where('users.seller', true)
            ->groupBy('sales_opportunities.seller_id')
            ->groupBy('sales_opportunities.status')->get();
        $sales_opportunity_trackings = SalesOpportunityTracking::SelectRaw('COUNT(*) AS cantidad, action, sales_opportunities.seller_id')
            ->join('sales_opportunities', 'sales_opportunity_trackings.sales_opportunity_id', '=', 'sales_opportunities.id')
            ->join('users', 'sales_opportunities.seller_id', '=', 'users.id')
            ->where('sales_opportunity_trackings.created_at', '>=', $from_date)
            ->where('sales_opportunity_trackings.created_at', '<=', $until_date)
            ->where('sales_opportunity_trackings.status', 1)
            ->where('users.status', 1)
            ->where('users.seller', 1)
            ->whereIn('users.enterprise_id', [3, 6, 7])
            ->groupBy('sales_opportunities.seller_id')->groupBy('sales_opportunity_trackings.action')->get();
        $trackings = [];
        foreach ($sales_opportunities as $key => $value) {
            $trackings[$value->seller_id]['fullname'] = $value->fullname;
            if (!array_key_exists('cartera_total', $trackings[$value->seller_id])) {
                $trackings[$value->seller_id]['cartera_total'] = 0;
            }
            $trackings[$value->seller_id]['cartera_total'] += $value->cantidad;
            $trackings[$value->seller_id]['estado'][$value->status] = $value->cantidad;
        }

        foreach ($sales_opportunity_trackings as $key => $value) {
            if (!array_key_exists('all_trackings', $trackings[$value->seller_id])) {
                $trackings[$value->seller_id]['all_trackings'] = 0;
            }
            $trackings[$value->seller_id]['trackings'][$value->action] = $value->cantidad;
            $trackings[$value->seller_id]['all_trackings'] += $value->cantidad;
        }
        return $trackings;
    }

    public function report_list()
    {
        // FILTROS DE BUSQUEDA
        $enterprises   = $this->getEnterprises();
        $half_contacts = $this->getHalfContacts();
        $supervisors   = $this->getSupervisors();
        $status        = $this->getStatus();
        $sellers       = $this->getSellers();
        $insurances    = $this->getInsurances();
        $closers       = $this->getClosers();
        $seller_teams  = $this->getSellerTeams();
        $opportunities = collect();
        $opportunities_qty = 0;
        $cities = City::GetAllCached()->sortBy('name')->pluck('name', 'id');

        if (request()->date_range) {
            $opportunities = $this->getReportOpportunities()->paginate(20);
            $opportunities_qty = $this->getReportOpportunities()->count();
        }

        return view('pages.sales-opportunity.report-list', compact('opportunities', 'enterprises', 'half_contacts', 'supervisors', 'status', 'sellers', 'opportunities_qty', 'insurances', 'closers', 'cities', 'seller_teams'));
    }

    public function report_list_pdf()
    {
        $opportunities = $this->getReportOpportunities()->get();
        $pdf = PDF::loadView('pages.sales-opportunity.report-list-pdf', compact('opportunities'));
        return $pdf->stream();
    }

    public function report_list_excel()
    {
        $opportunities = $this->getReportOpportunities()->get();
        $excelArray = [];

        if (auth()->user()->can('physical-legal-opportunities.create')) {
            $excelArray[] = [
                'Fecha creacion',
                'Fecha procesada',
                'Fecha Aceptada',
                'Fecha rechazada',
                'UN',
                'Medio',
                'Nombre Completo',
                'Teléfono',
                'Email',
                'Nro.Documento',
                'Estado',
                'Vendedor',
                'Motivo de Rechazo',
                'Observación de rechazo',
                'Id Oportunidad',
                'Id Vendedor'
            ];
        } else {
            $excelArray[] = [
                'Fecha creacion',
                'Fecha procesada',
                'Fecha cerrada',
                'Fecha venta cajón',
                'Fecha vendida',
                'Fecha rechazada',
                'UN',
                'Medio',
                'Nombre Completo',
                'Teléfono',
                'Email',
                'Nro.Documento',
                'Ciudad',
                'Zona',
                'Lead',
                'Form_id',
                'Ad_id',
                'Seguro',
                'Tipo de Plan',
                'Estado',
                'Vendedor',
                'Cerrador',
                'Observación de reasignación',
                'Motivo de Rechazo',
                'Observación de rechazo',
                'Gestiones Positivas',
                'Gestiones Negativas',
                'Estado Ult. Gestion',
                'Reasignado',
                'Id Oportunidad',
                'Id Vendedor'
            ];
        }

        foreach ($opportunities as $opportunity) {
            if (auth()->user()->can('physical-legal-opportunities.create')) {
                $excelArray[] = [
                    $opportunity->created_at->format('d/m/Y H:i:s'),
                    $opportunity->processed_at ? $opportunity->processed_at->format('d/m/Y H:i:s') : '',
                    $opportunity->selled_at ? $opportunity->selled_at->format('d/m/Y H:i:s') : '',
                    $opportunity->rejected_at ? $opportunity->rejected_at->format('d/m/Y H:i:s') : '',
                    $opportunity->enterprise->abbreviation,
                    $opportunity->half_contact->name,
                    $opportunity->fullname,
                    $opportunity->phone,
                    $opportunity->email,
                    $opportunity->document_number,
                    $opportunity->status == 15 ? 'Aceptado' : config('constants.sales-opportunity-status.' . $opportunity->status),
                    $opportunity->seller->fullname,
                    $opportunity->rejected_motive ? $opportunity->rejected_motive->name : '',
                    $opportunity->rejected_motive && $opportunity->trackings->where('reject', 1)->first() ? $opportunity->trackings->where('reject', 1)->first()->observation : '',
                    $opportunity->id,
                    $opportunity->seller_id
                ];
            } else {
                $excelArray[] = [
                    $opportunity->created_at->format('d/m/Y H:i:s'),
                    $opportunity->processed_at ? $opportunity->processed_at->format('d/m/Y H:i:s') : '',
                    $opportunity->closed_at ? $opportunity->closed_at->format('d/m/Y H:i:s') : '',
                    $opportunity->drawer_sale_at ? $opportunity->drawer_sale_at->format('d/m/Y H:i:s') : '',
                    $opportunity->selled_at ? $opportunity->selled_at->format('d/m/Y H:i:s') : '',
                    $opportunity->rejected_at ? $opportunity->rejected_at->format('d/m/Y H:i:s') : '',
                    $opportunity->enterprise->abbreviation,
                    $opportunity->half_contact->name,
                    $opportunity->fullname,
                    $opportunity->phone,
                    $opportunity->email,
                    $opportunity->document_number,
                    $opportunity->city_id ? $opportunity->City->name : '-',
                    $opportunity->address,
                    $opportunity->lead,
                    $opportunity->form_id,
                    $opportunity->ad_id,
                    $opportunity->insurance ? $opportunity->insurance->name : '',
                    $opportunity->type_plan ? config('constants.type_plan.' . $opportunity->type_plan) : '',
                    config('constants.sales-opportunity-status.' . $opportunity->status),
                    $opportunity->seller->fullname,
                    $opportunity->closer ? $opportunity->closer->fullname : ($opportunity->trackings->where('reassigned', 1)->first() ? $opportunity->trackings->where('reassigned', 1)->first()->user->fullname : ''),
                    $opportunity->trackings &&  $opportunity->trackings->where('reassigned', 1)->first() ? $opportunity->trackings->where('reassigned', 1)->first()->observation : '',
                    $opportunity->rejected_motive ? $opportunity->rejected_motive->name : '',
                    $opportunity->rejected_motive && $opportunity->trackings->where('reject', 1)->first() ? $opportunity->trackings->where('reject', 1)->first()->observation : '',
                    $opportunity->trackings ? $opportunity->trackings->where('attended', 1)->count() : '0',
                    $opportunity->trackings ? $opportunity->trackings->where('attended', 2)->count() : '0',
                    $opportunity->trackings && $opportunity->trackings()->orderBy('id', 'desc')->first() ? config('constants.attended.' . $opportunity->trackings()->orderBy('id', 'desc')->first()->attended) : '',
                    $opportunity->status == 5 && $opportunity->closed_in > 0 ? ($opportunity->closed_in == 1 ? 'Desde Cerrador' : 'Desde Sucursal') : '',
                    $opportunity->id,
                    $opportunity->seller_id
                ];
            }
        }

        Excel::create('Oportunidades de Venta', function ($excel) use ($excelArray) {
            $excel->sheet('sheet1', function ($sheet) use ($excelArray) {
                $sheet->fromArray($excelArray, null, 'A1', false, false);
            });
        })->export('xlsx');
    }

    private function getReportOpportunities()
    {
        $from_date  = Carbon::createFromFormat('d/m/Y', explode('-', str_replace(' ', '', request()->date_range))[0])->format('Y-m-d 00:00:00');
        $until_date = Carbon::createFromFormat('d/m/Y', explode('-', str_replace(' ', '', request()->date_range))[1])->format('Y-m-d 23:59:59');

        if (request()->date_range_type) {
            switch (request()->date_range_type) {
                case 1:
                    $field = 'created_at';
                    break;
                case 2:
                    $field = 'selled_at';
                    break;
                default:
                    $field = 'rejected_at';
                    break;
            }
        }

        $opportunities = SalesOpportunity::with('half_contact', 'enterprise', 'trackings')
            ->whereBetween($field, [$from_date, $until_date])
            ->where(function ($query) {
                $query->whereIn('enterprise_id', auth()->user()->enterprises->pluck('id'))
                    ->orWhereNull('enterprise_id');
            });

        if (request()->enterprise_id) {
            $opportunities = $opportunities->whereIn('enterprise_id', request()->enterprise_id);
        }

        if (request()->extra_filters) {
            if (in_array(1, request()->extra_filters)) {
                $opportunities = $opportunities->whereHas('half_contact', function ($query) {
                    $query->where('type', 1);
                });
            }
        }

        if (request()->half_contact_id) {
            if (auth()->user()->can('sales-opportunity.report-list-agency')) {
                $opportunities = $opportunities->whereIn('half_contact_id', [3, 11, 30, 31]);
            }
            $opportunities = $opportunities->whereIn('half_contact_id', request()->half_contact_id);
        } else {
            if (auth()->user()->can('sales-opportunity.report-list-agency')) {
                $opportunities = $opportunities->whereIn('half_contact_id', [3, 11, 30, 31]);
            }
        }
        if (request()->seller_id) {
            $opportunities = $opportunities->where('seller_id', request()->seller_id);
        }

        if (request()->city_id) {
            $opportunities = $opportunities->where('city_id', request()->city_id);
        }

        if (request()->supervisor_id) {
            $opportunities = $opportunities->whereHas('seller', function ($query) {
                $query->whereIn('seller_supervisor_id', request()->supervisor_id);
            });
        }

        if (request()->closer_id) {
            $opportunities = $opportunities->where('closer_id', request()->closer_id);
        }

        if (request()->insurance_id) {
            $opportunities = $opportunities->where('insurance_id', request()->insurance_id);
        }
        if (request()->type_plan) {
            $opportunities = $opportunities->whereIn('type_plan', request()->type_plan);
        }
        if (request()->status) {
            $opportunities = $opportunities->whereIn('status', request()->status);
        } else {
            $opportunities = $opportunities->where('status', '<', 30);
        }

        if (request()->seller_team_id) {
            $opportunities = $opportunities->whereHas('seller', function ($query) {
                $query->whereIn('seller_team_id', request()->seller_team_id);
            });
        }

        return $opportunities->orderBy('id', 'desc');
    }

    public function report_grouped()
    {
        // FILTROS DE BUSQUEDA
        $enterprises   = $this->getEnterprises();
        $half_contacts = $this->getHalfContacts();
        $supervisors   = $this->getSupervisors();
        $status        = $this->getStatus();
        $sellers       = $this->getSellers();
        $seller_teams  = $this->getSellerTeams();
        $opportunities = Collect([]);
        $opportunities_creator  = Collect([]);
        $opportunities_sellers  = Collect([]);
        $array_status_name      = [];
        $array_status_count     = [];
        $array_sellers_name     = [];
        $array_sellers_count    = [];
        $array_creator_name     = [];
        $array_creator_count    = [];
        if (request()->from_date && request()->until_date)
        {
            $from_date = Carbon::createFromFormat('d/m/Y', request()->from_date)->format('Y-m-d 00:00:00');
            $until_date = Carbon::createFromFormat('d/m/Y', request()->until_date)->format('Y-m-d 23:59:59');

            $opportunities = SalesOpportunity::with('half_contact')->selectRaw('COUNT(*) as count, status')
                ->where('status', '<', 30)
                ->whereBetween('created_at', [$from_date, $until_date]);
            if (request()->enterprise_id) {
                $opportunities = $opportunities->where('enterprise_id', request()->enterprise_id);
            }
            if (request()->half_contact_id) {
                $opportunities = $opportunities->where('half_contact_id', request()->half_contact_id);
            }
            if (request()->supervisor_id) {
                $opportunities = $opportunities->whereHas('seller', function ($query) {
                    $query->where('seller_supervisor_id', request()->supervisor_id);
                });
            }
            if (request()->seller_id) {
                $opportunities = $opportunities->where('seller_id', request()->seller_id);
            }
            if (request()->status) {
                $opportunities = $opportunities->where('status', request()->status);
            }
            if (request()->seller_team_id) {
                $opportunities = $opportunities->whereHas('seller', function ($query) {
                    $query->where('seller_team_id', request()->seller_team_id);
                });
            }

            // if(request()->creator)
            // {
            //     $opportunities = $opportunities->where('creator', request()->creator);
            // }
            $opportunities = $opportunities->groupBy('status')->get();

            $array_status_name = [];
            $array_status_count = [];
            foreach ($opportunities as $key => $opportunity) {
                $array_status_name[] = config('constants.sales-opportunity-status.' . $opportunity->status);
                $array_status_count[] = $opportunity->count;
            }

            $opportunities_creator = SalesOpportunity::selectRaw('COUNT(*) as count, creator')
                ->where('status', '<', 30)
                ->whereBetween('created_at', [$from_date, $until_date]);
            if (request()->enterprise_id) {
                $opportunities_creator = $opportunities_creator->where('enterprise_id', request()->enterprise_id);
            }
            if (request()->half_contact_id) {
                $opportunities_creator = $opportunities_creator->where('half_contact_id', request()->half_contact_id);
            }
            if (request()->supervisor_id) {
                $opportunities_creator = $opportunities_creator->whereHas('seller', function ($query) {
                    $query->where('seller_supervisor_id', request()->supervisor_id);
                });
            }
            if (request()->seller_id) {
                $opportunities_creator = $opportunities_creator->where('seller_id', request()->seller_id);
            }
            if (request()->status) {
                $opportunities_creator = $opportunities_creator->where('status', request()->status);
            }
            if (request()->seller_team_id) {
                $opportunities_creator = $opportunities_creator->whereHas('seller', function ($query) {
                    $query->where('seller_team_id', request()->seller_team_id);
                });
            }

            // if(request()->creator)
            // {
            //     $opportunities_creator = $opportunities_creator->where('creator', request()->creator);
            // }
            $opportunities_creator = $opportunities_creator->groupBy('creator')->get();

            $array_creator_name = [];
            $array_creator_count = [];
            foreach ($opportunities_creator as $key => $opportunity) {
                $array_creator_name[] = config('constants.sales-opportunity-creator.' . $opportunity->creator);
                $array_creator_count[] = $opportunity->count;
            }



            $opportunities_sellers = SalesOpportunity::with('seller')
                ->selectRaw('COUNT(*) as count, seller_id')
                ->where('status', '<', 30)
                ->whereBetween('created_at', [$from_date, $until_date]);
            if (request()->enterprise_id) {
                $opportunities_sellers = $opportunities_sellers->where('enterprise_id', request()->enterprise_id);
            }
            if (request()->half_contact_id) {
                $opportunities_sellers = $opportunities_sellers->where('half_contact_id', request()->half_contact_id);
            }
            if (request()->supervisor_id) {
                $opportunities_sellers = $opportunities_sellers->whereHas('seller', function ($query) {
                    $query->where('seller_supervisor_id', request()->supervisor_id);
                });
            }
            if (request()->seller_id) {
                $opportunities_sellers = $opportunities_sellers->where('seller_id', request()->seller_id);
            }
            if (request()->status) {
                $opportunities_sellers = $opportunities_sellers->where('status', request()->status);
            }

            if (request()->seller_team_id) {
                $opportunities_sellers = $opportunities_sellers->whereHas('seller', function ($query) {
                    $query->where('seller_team_id', request()->seller_team_id);
                });
            }
            // if(request()->creator)
            // {
            //     $opportunities_sellers = $opportunities_sellers->where('creator', request()->creator);
            // }
            $opportunities_sellers = $opportunities_sellers->groupBy('seller_id')->orderBy('count', 'desc')->get();

            $array_sellers_name = [];
            $array_sellers_count = [];
            foreach ($opportunities_sellers as $key => $seller) {
                $array_sellers_name[] = $seller->seller->fullname;
                $array_sellers_count[] = $seller->count;
            }
        }

        return view('pages.sales-opportunity.report-grouped', compact('enterprises', 'half_contacts', 'sellers', 'supervisors', 'array_status_name', 'array_status_count', 'opportunities', 'array_sellers_name', 'array_sellers_count', 'opportunities_sellers', 'opportunities_creator', 'array_creator_name', 'array_creator_count', 'status', 'seller_teams'));
    }

    public function ajax_change_days_call_again()
    {
        Setting::where('id', 1)->update(['days_call_again_leads' => request()->days]);
        return response()->json(['success' => true]);
    }

    public function ajax_clients()
    {
        $results = [];
        $services_type_doctors = $this->getServicesTypeDoctors();

        if (request()->ajax()) {
            return response()->json($results);
        }
        abort(404);
    }

    private function getStatus()
    {
        $status = config('constants.sales-opportunity-status');
        if (auth()->user()->can('physical-legal-opportunities.create')) {
            return Arr::except($status, [30, 10, 13]);
        } else {
            return Arr::except($status, [30]);
        }
    }

    private function getRejectedMotives()
    {
        $motive =  SalesOpportunityRejectionMotive::where('status', true);
        if (!auth()->user()->can('denpro-seller-opportunities.create')) {
            if (in_array(3, auth()->user()->enterprises->pluck('id')->toArray()) || in_array(6, auth()->user()->enterprises->pluck('id')->toArray())) {
                $motive = $motive->where('type', 2);
            } else {
                $motive = $motive->where('type', 1);
            }
        } elseif (auth()->user()->can('denpro-seller-opportunities.create')) {
            $motive = $motive->where('type', 3)->orWhere('id',10);
        }
        return $motive->orderBy('name')->pluck('name', 'id');
    }

    // private function getInsurances()
    // {
    //     return Insurance::GetAllCached()
    //         ->sortBy('name')
    //         ->pluck('name', 'id');
    // }

    private function getHalfContacts()
    {
        if (auth()->user()->can('sales-opportunity.report-list-agency')) {
            return ContactMedium::where('status', true)->whereIn('id', [3, 11, 30, 31])->orderBy('name')->pluck('name', 'id');
        } else {
            return ContactMedium::where('status',true)->pluck('name', 'id');
        }
    }

    private function getClosers()
    {
        return User::where('status', true)
            ->where('sales_closer', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->pluck('fullname', 'id');
    }

    private function getSupervisors()
    {
        return User::orderBy('first_name')
            ->orderBy('last_name')
            ->where('seller_supervisor', true)
            ->where('status', true)
            ->get()
            ->pluck('fullname', 'id');
    }

    private function getSellers()
    {
        return User::orderBy('name')
            ->orderBy('name')
            ->where('seller', true)
            ->where('active', true)
            ->get()
            ->pluck('name', 'id');
    }

    private function getBranches()
    {
        return Branch::where('status',1)->pluck('name', 'id');
    }

    private function getContractPromotions()
    {
        return ContractPromotion::where('status', true)->pluck('name', 'id');
    }

    private function getSellerTeams()
    {
        return SellerTeam::where('status', true)->pluck('name', 'id');
    }

    private function getAdditionalServices()
    {
        return  AdditionalService::where('status', true)->whereIn('enterprise_id', [3, 6])->orderBy('enterprise_id', 'asc')->get();
    }
}
