@extends('layouts.AdminLTE.index')

@section('title', ' Inventario')

@section('menu_pagina')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Inventario de Productos</h5>
                <div class="ibox-tools">                            
                        <a href="{{ url('inventories/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="form-group col-md-2">
                            <input class="form-control" placeholder="Desde Fecha" type="text" name="from_date" value="{{ request()->from_date }}" date-mask>
                        </div>
                        <div class="form-group col-md-2">
                            <input class="form-control" placeholder="Hasta Fecha" type="text" name="until_date" value="{{ request()->until_date }}" date-mask>
                        </div>
                        <div class="form-group col-sm-4">
                            {{ Form::select('deposit_id', $deposits, request()->deposit_id, ['placeholder' => 'Seleccione Deposito', 'class' => 'form-control', 'select2']) }}
                        </div>
                        <div class="form-group col-sm-2">
                            <button type="submit" class="btn btn-primary" name="filter" value="1"><i class="fa fa-search"></i></button>
                            @if(request()->filter)
                                <a href="{{ url('inventories') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Deposito</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases_product_inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->id }}</td>
                                <td>{{ $inventory->date->format('d/m/Y') }}</td>
                                <td>{{ $inventory->deposit->name }}</td>                                        
                                <td><span class="label label-{{ config('constants.purchase-product-inventories-status-label.' .$inventory->status) }}">{{ config('constants.purchase-product-inventories-status.' .$inventory->status) }}</span></td>                                        
                                <td class="text-right">
                                    <a href="{{ url('inventories/' . $inventory->id) }}" data-toggle="tooltip" data-placement="top" title="Ver Detalle"><i class="fa fa-info-circle"></i></a>
                                    <a href="{{ url('inventories/' . $inventory->id . '/pdf') }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Exportar a PDF"><i class="fa fa-file-pdf"></i></a>                                           
                                    <a href="{{ url('inventories/' . $inventory->id . '/xls') }}" target="_blank" data-toggle="tooltip" data-placement="top" title="Exportar a excel"><i class="fa fa-file-excel"></i></a>   
                                    @if($inventory->status == 2)
                                            <a href="{{ url('inventories/' . $inventory->id .'/edit') }}" data-toggle="tooltip" data-placement="top" title="Editar Inventario"><i class="fa fa-pencil-alt"></i></a> 

                                            <a href="{{ url('inventories/' . $inventory->id . '/confirm-inventory') }}" data-toggle="tooltip" data-placement="top" title="Confirmación de Inventario" onclick="return confirm('¿Desea confirmar el Inventario?');"><i class="fa fa-check"></i></a>  
                                            <a href="{{ url('inventories/' . $inventory->id . '/delete') }}" data-toggle="tooltip" data-placement="top" title="Eliminar Inventario" onclick="return confirm('¿Desea eliminar Inventario?');"><i class="fa fa-trash"></i></a>  
                                    @endif 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $purchases_product_inventories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('layout_js')
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection