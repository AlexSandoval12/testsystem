@extends('layouts.AdminLTE.index')

@section('title', ' Inventario')

@section('menu_pagina')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Inventario</h5>
                <div class="ibox-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-2"><b>ID:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->id }}</div>
                </div>  
                <div class="row">
                    <div class="col-md-2"><b>Fecha:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->date->format('d/m/Y') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-2"><b>Deposito:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->deposit->name }}</div>
                </div>                        
                <div class="row">
                    <div class="col-md-2"><b>Observacion:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->observation }}</div>
                </div>                        
                <div class="row mt-2">
                    <div class="col-md-2"><b>Usuario Creación:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->user->fullname }}</div>
                </div>
                <div class="row">
                    <div class="col-md-2"><b>Fecha Creación:</b></div>
                    <div class="col-md-10">{{ $purchases_product_inventory->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">                
    <div class="col-md-12">
        <div class="ibox-content table-responsive no-padding">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Cód</th>
                        <th>Producto</th>
                        <th class="text-right">Cantidad encontrada</th>
                        <th class="text-right">Existencia Actual</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases_product_inventory->purchases_product_inventory_details as $details)
                        <tr>
                            <td>{{ $details->product_id }}</td>
                            <td>{{ $details->purchases_product->name }}</td>
                            <td class="text-right">{{ number_format($details->quantity, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->existence, 0, ',', '.') }}</td>
                            <td><span class="label label-{{ $details->quantity > $details->existence ? 'primary' : ($details->quantity == $details->existence ? '' : 'danger') }}">{{ $details->quantity > $details->existence ? 'Entrada' : ($details->quantity == $details->existence ? 'Sin Movimiento' : 'Salida') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection