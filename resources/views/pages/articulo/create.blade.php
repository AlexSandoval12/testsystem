@extends('layouts.AdminLTE.index')
@section('title', 'AGREGAR ARTICULO ')
@section('content')
{{ Form::open(['route' => 'articulo.store']) }}
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nombre</label><br>
                        <input id="name" class="form-control" name="name" type="text" value="{{--{{ old('name', $articulo->name) }}--}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Cod Barra</label><br>
                        <input id="barcode" name="barcode" class="form-control" type="text" value="{{--{{ old('name', $articulo->name) }}--}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Precio</label><br>
                        <input id="price" name="price" class="form-control" type="text" value="{{--{{ old('name', $articulo->name) }}--}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Marca</label>
                        {{ Form::select('brand_id', $brand ,request()->brand_id, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'placeholder'  => 'Seleccione un equipo']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="ibox-footer">
            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
            <a href="{{ url('articulo') }}" class="btn btn-sm btn-danger">Cancelar</a>
        </div>
    </div>
</div>
{{ Form::close() }}
@endsection
@section('layout_js')
<script>
 </script>
@endsection
@section('layout_css')
<style>
</style>
@endsection