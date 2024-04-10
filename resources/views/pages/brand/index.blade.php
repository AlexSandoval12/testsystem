@extends('layouts.AdminLTE.index')
@section('title', 'Marca ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-tools">
                <div class="btn-group pull-right">
                    <a href="{{ url('brand/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <table class="table table-striped table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                        <tr>
                            <td>{{ $brand->name }}</td>
                            <td class="text-center"><span class="label label-{{ config('constants.status-label.' . $brand->status) }}">{{ config('constants.status.' . $brand->status) }}</td>
                            <td class="text-center">
                                <a href="{{ url('brand/' . $brand->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
