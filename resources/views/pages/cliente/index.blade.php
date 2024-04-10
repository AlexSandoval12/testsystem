@extends('layouts.AdminLTE.index')
@section('title', 'Cliente ')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="ibox float-e-margins">
      <div class="ibox-tools">
        <a href="{{ url('cliente/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Agregar</a> 
      </div> 
        <table class="table table-striped table-condensed table-hover">
          <thead>
              <tr>
                <th>Nombre</th> 
                <th class="text-center">Apellido</th>
                <th class="text-center">RUC</th>
                <th class="text-center">Direccion</th>
                <th class="text-center">C.I</th> 
                <th class="text-center">Estado</th> 
                <th class="text-center">Acciones</th> 
              </tr>
          </thead>
          <tbody>
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->first_name }}</td>
                    <td class="text-center">{{ $cliente->last_name }}</td>
                    <td class="text-center">{{  $cliente->ruc}}</td>
                    <td class="text-center">{{ $cliente->address }}</td>
                    <td class="text-center">{{  number_format($cliente->document_number, 0, ',', '.') }}</td>
                    <td class="text-center"><span class="label label-{{ config('constants.status-label.' . $cliente->status) }}">{{ config('constants.status.' . $cliente->status) }}</td>
                    <td class="text-center">
                      <a href="{{ url('branches/' . $cliente->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        </table>
      </div>
  </div>
</div>                    
@endsection
