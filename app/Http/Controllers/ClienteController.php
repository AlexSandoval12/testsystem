<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClienteRequest;
use App\Models\Ciudad;
use App\Models\Client;
use App\Models\Departamento;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function index()
    {  
        $clientes = Client::orderBy('first_name')->paginate(20);
        return view('pages.cliente.index' ,compact('clientes'));       
    } //

    public function create()
    {
        $departamentos = Departamento::pluck('departamento','id');
        $nation = Nationality::pluck('name','id');
        return view('pages.cliente.create', compact('departamentos','nation'));
   }

    public function store(CreateClienteRequest $request)
    {
        DB::transaction(function() use ($request)
        {
            $cliente = Client::create([  
                'first_name'   => $request->name,
                'last_name'    => $request->apellido,
                'address'      => $request->address,
                'ruc'          => $request->ruc,
                'document_number' => $request->document_number,
                'ciudades'       => $request->ciudades,
                'departamentos'=> $request->departamentos,
                'neighborhood'=> $request->neighborhood,
                'razon_social'=> $request->razon_social,
                'civil_status'=> $request->civil_status,
                'gender'=> $request->gender,
                'observation'=> $request->observation,
                'nationality_id' => $request->nationalities_id,
                 ]);
                 
        });
        return redirect('cliente'); 
    }

    public function show($clientes)
    {   
        $clientes = Client::find($clientes);
        $departamento = Departamento::all();
        $ciudades = Ciudad::whereDepartamento_id($clientes->departamento['id'])->get();
        
        return view('pages.clientes.create', compact(['cliente','departamentos','ciudadades']));
    }
    public function ajax_nationalities()
    {
        if(request()->ajax())
        {
            $nation = Nationality::where('nationalities_id',request()->nationalities_id)->get();
            $result = [];
            foreach ($nation as $key => $nations) 
            {
                $result[$key]['id'] = $nations->id;    
                $result[$key]['name'] = $nations->name;
            }
            return $result;
        }
    }

    public function ajax_department()
    {
        if(request()->ajax())
        {
            $nation = Ciudad::where('departamentos_id',request()->departamento_id)->get();
            Log::info($nation);
            $result = [];
            foreach ($nation as $key => $nations) 
            {
                $result[$key]['id'] = $nations->id;    
                $result[$key]['name'] = $nations->ciudad;
            }
            return $result;
        }
    }

    public function ajax_clients()
    {
        if(request()->ajax())
        {
            $results = [];
            $key = 0;
            $cliente = Client::where('first_name','LIKE','%'. request()->q .'%')->orWhere('last_name','LIKE','%'. request()->q .'%')
                                ->orWhere('ruc','LIKE','%'. request()->q .'%')->orWhere('document_number','LIKE','%'. request()->q .'%')->first();
            if($cliente)
            {
                $results['items'][$key]['id']                  = $cliente->id;
                $results['items'][$key]['text']                = $cliente->first_name .' '.$cliente->last_name . ' | Ruc : ' . $cliente->ruc;
                $results['items'][$key]['name']                = $cliente->first_name .' '.$cliente->last_name;
                $results['items'][$key]['ruc']                 = $cliente->ruc;
                $results['items'][$key]['razon_social']        = $cliente->razon_social;
                $results['items'][$key]['document_number']     = $cliente->document_number;
                $results['items'][$key]['address']             = $cliente->address;
                $results['items'][$key]['phone']               = $cliente->phone ?? '';
            }
            return response()->json($results);                   
        }
    }
}




