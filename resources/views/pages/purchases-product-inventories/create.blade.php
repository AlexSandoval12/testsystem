@extends('layouts.AdminLTE.index')

@section('title', ' Inventario')

@section('menu_pagina')

@section('content')
<div class="row">
            <div class="col-lg-12">                
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Agregar Inventario de Productos</h5>
                        <div class="ibox-tools">
                            <a href="{{ url('inventories') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                        </div>
                    </div>
                    <div class="ibox-content pb-0">                        
                        <div class="row">
                            <form method="GET">
                                <div class="form-group col-sm-4">
                                    {{ Form::select('deposit_id', $deposits, request()->deposit_id, ['placeholder' => 'Seleccione Deposito', 'class' => 'form-control', 'select2', 'required']) }}
                                </div>
                                    <div class="form-group col-md-2">
                                        <select class="form-control selectpicker"name="extra_filters[]" data-none-selected-text="Filtros Extras">
                                            <option value="1" {{ in_array(1, (request()->extra_filters ? request()->extra_filters : [])) ? 'selected' : '' }}>Precargar saldos</option>
                                        </select>
                                    </div>
                                <div class="form-group col-sm-2">
                                    <button type="submit" class="btn btn-primary" name="filter" value="1"><i class="fa fa-search"></i></button>
                                    @if(request()->filter)
                                        <a href="{{ url('inventories/create') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(request()->filter)
                        @if(request()->deposit_id)
                            {{ Form::open(['route' => 'inventories.store']) }}  
                                <input type="hidden" name="deposit_id" value="{{ request()->deposit_id }}">
                                <div class="ibox-content">
                                    @include('partials.messages')
                                    <div class="row">                                        
                                        <div class="form-group col-md-6">
                                            <label>Observación</label>
                                            <textarea class="form-control" name="observation">{{ old('observation') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="ibox-content table-responsive no-padding">
                                    @if(count($purchases_products) > 0)
                                        <table class="table table-condensed table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Cód</th>
                                                    <th class="text-center">Nombre del Producto</th>
                                                    <th class="text-center">Precio Costo</th>
                                                    <th class="text-center">Existencia</th>
                                                    <th class="text-center">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($purchases_products->chunk(300) as $chunk)
                                                    @foreach($chunk as $key => $purchases_product)
                                                        <tr>
                                                            <td class="text-right">{{ $purchases_product->id }}</td>
                                                            <td>{{ $purchases_product->description }}</td>
                                                            <td>
                                                                <input class="form-control" type="text" name="old_cost_product[{{ $purchases_product->id }}]" value="{{ isset($cost_product[$purchases_product->id]) ? number_format($cost_product[$purchases_product->id], 0, ',', '.') : 0 }}" period-data-mask readonly>
                                                            </td>                                                                                                  
                                                            <td class="text-right">{{ isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0 }}</td> 
                                                            <td>
                                                                <input class="form-control" type="text" name="product_id[{{ $purchases_product->id }}]" value="{{ 
                                                                    old('product_id') ?  
                                                                    old('product_id.'.$purchases_product->id) : 
                                                                    (request()->extra_filters ? 
                                                                        (in_array(1,request()->extra_filters) ? 
                                                                            (isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0) : 0) : 0) }}">
                                                                <input type="hidden" name="old_existences[{{ $purchases_product->id }}]" value="{{ isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0 }}">
                                                            </td>                                                                                                   
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-danger" role="alert">
                                            No se encontraron Registros.
                                        </div>
                                    @endif
                                </div>             
                                <div class="ibox-footer">
                                    <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                                    <a href="{{ url('inventories/create') }}" class="btn btn-sm btn-danger">Cancelar</a>
                                </div>                                
                            {{ Form::close() }} 
                        @else   
                            <div class="alert alert-danger" role="alert">
                                Faltan datos!!!
                            </div>
                        @endif 
                    @endif 
                </div>                               
            </div>
        </div>
@endsection