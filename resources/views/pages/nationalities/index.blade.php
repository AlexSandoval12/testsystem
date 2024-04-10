@extends('layouts.AdminLTE.index')
@section('title', 'Nacionalidad ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-tools">
                <div class="btn-group pull-right">
                    <a href="{{ url('nationalities/create') }}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <table class="table table-striped table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Nacionalidad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nations as $nation)
                        <tr>
                            <td>{{ $nation->name }}</td>
                            <td class="text-center"><span class="label label-{{ config('constants.status-label.' . $nation->status) }}">{{ config('constants.status.' . $nation->status) }}</td>
                            <td class="text-center">
                                <a href="{{ url('nationalities/' . $nation->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
