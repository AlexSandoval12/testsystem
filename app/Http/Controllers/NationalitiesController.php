<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNationalitiesRequest;
use App\Models\Ciudad;
use App\Models\Nationality;
use Illuminate\Http\Request;

class NationalitiesController extends Controller
{
    public function index()
    {
        $nations = Nationality::where('status',1)->get();
        return view('pages.nationalities.index',compact('nations'));
    }

    public function create()
    {
        return view('pages.nationalities.create');
    }

    public function store(CreateNationalitiesRequest $request)
    {
        Nationality::create([
            'name' => request()->name,
            'status' => 1
        ]);

        $this->flashMessage('check', 'La Nacionalidad fue registrado correctamente', 'success');

        return redirect()->route('nationalities');
    }

    public function ajax_department()
    {
        if(request()->ajax())
        {
            $ciudades = Ciudad::where('departamentos_id',request()->departamento_id)->get();
            $result = [];
            foreach ($ciudades as $key => $ciudad) 
            {
                $result[$key]['id'] = $ciudad->id;    
                $result[$key]['ciudad'] = $ciudad->ciudad;
                $result[$key]['departamento_id'] = $ciudad->departamentos_id;
            }
            return $result;
        }
    }
}
