@extends('layouts.AdminLTE.index')
@section('title', 'Compras')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="row">
                    <div class="ibox-content pull-right">
                        <a href="{{ url('purchase/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                    {{-- @endpermission --}}
                    </div>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="s" placeholder="Buscar" value="{{ request()->s }}">
                        </div>
                        <div class="form-group col-md-3">
                            {{ Form::select('provider_id', $providers, request()->provider_id, ['placeholder' => 'Seleccione Proveedor', 'class' => 'form-control', 'select2']) }}
                        </div>
                        {{-- <div class="form-group col-md-2">
                            {{ Form::select('invoice_copy', config('constants.invoice_copy'), request()->invoice_copy, ['class' => 'form-control', 'placeholder' => 'Seleccione estado', 'select2']) }}
                        </div> --}}
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary" name="filter" value="1"><i class="fa fa-search"></i></button>
                            @if(request()->filter)
                                <a href="{{ url('purchases') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Fecha</th>
                            <th>Condición</th>
                            <th>Tipo</th>
                            <th>Numero</th>
                            <th>Ruc</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->branch->name }}</td>
                                <td>{{ $purchase->date->format('d/m/Y') }}</td>
                                <td>{{ config('constants.invoice_condition.'. $purchase->condition) }}</td>
                                <td><span class="label label-{{ config('constants.type_purchases_label.' . $purchase->type) }}">{{ config('constants.type_purchases.'. $purchase->type) }}</span></td>
                                <td>{{ $purchase->number }}</td>
                                <td>{{ $purchase->ruc }}</td>
                                <td>{{ $purchase->provider->name }}</td>
                                <td class="text-right">{{ number_format($purchase->amount, 2, ',', '.') }}</td>
                                <td>
                                    <span class="label label-{{ config('constants.purchase-status-label.' . $purchase->status) }}">{{ config('constants.purchase-status.'. $purchase->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('purchase/' . $purchase->id) }}"><i class="fa fa-info-circle"></i></a>
                                    <a href="{{ url('purchase/' . $purchase->id . '/pdf') }}" target="_blank"><i class="fa fa-file"></i></a>
                                    <a href="{{ url('purchase/' . $purchase->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>

                                    {{-- @permission('purchases.delete') --}}
                                        @if($purchase->status == 1)
                                            <a href="{{ url('purchases/' . $purchase->id . '/delete') }}"><i class="fa fa-trash"></i></a>
                                        @endif
                                    {{-- @endpermission --}}
                                    {{-- @permission('purchases.edit') --}}
                                        @if($purchase->status == 1)
                                            <a href="{{ url('purchases/' . $purchase->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                        @endif
                                    {{-- @endpermission --}}
                                    @if($purchase->file)
                                        <a href="{{ url('purchases-provider-invoice/') }}/{{ $purchase->id }}/download?show=1" target="_blank"><i class="fa fa-search"></i></a>
                                        <a href="{{ url('purchases-provider-invoice/') }}/{{ $purchase->id }}/download" target="_blank"><i class="fa fa-download"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $purchases->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

