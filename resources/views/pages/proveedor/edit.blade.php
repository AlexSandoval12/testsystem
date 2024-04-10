@extends('layouts.AdminLTE.index')
@section('title', 'Editar Proveedor')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => ['provider.update', $provider_id->id], 'method' => 'PUT']) }}
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Nombre</label><br>
                        <input id="name" class="form-control" name="name" type="text"  value="{{ old('name', $provider_id->name) }}">
                        </div>
                    <div class="form-group col-md-4">
                        <label>RUC</label><br>
                        <input id="ruc" name="ruc" class="form-control" type="text"  value="{{ old('name', $provider_id->ruc) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Telefono</label>
                        <input id="phone" name="phone" class="form-control" type="text"  value="{{ old('name', $provider_id->phone) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Direccion</label>
                        <input id="address" name="address" class="form-control" type="text"  value="{{ old('name', $provider_id->address) }}">
                    </div>
                </div>
            </div>
            <div class="ibox-footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('provider') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
