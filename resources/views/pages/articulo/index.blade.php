@extends('layouts.AdminLTE.index')
@section('title', 'Articulo ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-tools">
                <div class="btn-group pull-right">
                    <a href="{{ url('articulo/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <table class="table table-striped table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="text-center">Codigo Barra</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articulos as $articulo)
                        <tr>
                            <td>{{ $articulo->name }}</td>
                            <td class="text-center">{{ $articulo->barcode }}</td>
                            <td class="text-center">{{ number_format($articulo->price,0,',','.') }}</td>
                            <td class="text-center">
                                <a href="{{ url('articulo/' . $articulo->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                                <a href="{{ url('articulo/' . $articulo->id) }}"><i class="fa fa-info-circle"></i></a>
                                <a href="{{ url('articulo/' . $articulo->id . '/pdf') }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Generar Codigo de Barras"><i class="fa fa-barcode"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
