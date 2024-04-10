@extends('layouts.sistema')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">                
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Editar Inventario de Productos</h5>
                        <div class="ibox-tools">
                            @permission('closing-inventory-stock.reload-existence')
                                <a href="{{ url('inventories/' . $purchases_product_inventory->id .'/edit?reload_actual_existence=1') }}" class="btn btn-primary btn-xs"><i class="fa fa-sync"></i> Precargar Saldos</a>
                            @endpermission
                            <a href="{{ url('inventories') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                        </div>
                    </div>
                    {{ Form::open(['route' => ['inventories.update', $purchases_product_inventory->id], 'method' => 'PUT']) }}
                        <div class="ibox-content table-responsive">
                            @include('partials.messages')
                            @if(count($purchases_products) > 0)
                                <table class="table table-condensed table-hover table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Cód</th>
                                            <th class="text-center">Nombre del Producto</th>
                                            <th class="text-center">Categoria</th>
                                            <th class="text-center">Precio Costo</th>
                                            <th class="text-center">Existencia</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchases_products as $purchases_product)
                                            <tr>
                                                <td class="text-right">{{ $purchases_product->id }}</td>
                                                <td>{{ $purchases_product->name }}</td>
                                                <td>{{ $purchases_product->category_name }}</td>   
                                                <td>
                                                    <input class="form-control" type="text" name="old_cost_product[{{ $purchases_product->id }}]" value="{{ (isset($inventory_old_cost[$purchases_product->id]) && $inventory_old_cost[$purchases_product->id] > 0) ? number_format($inventory_old_cost[$purchases_product->id], 0, ',', '.') : (isset($cost_product[$purchases_product->id]) ? number_format($cost_product[$purchases_product->id], 0, ',', '.') : 0) }}" period-data-mask readonly>
                                                </td>                                                                                                  
                                                <td align="center">{{ isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0 }}
                                                    <input type="hidden" name="old_existences[{{ $purchases_product->id }}]" value="{{ isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0 }}">
                                                </td> 
                                                <td>
                                                    @if(request()->reload_actual_existence)
                                                        <input class="form-control" type="text" name="new_existence[{{ $purchases_product->id }}]" value="{{ isset($existences[$purchases_product->id]) ? $existences[$purchases_product->id] : 0 }}">
                                                    @else
                                                        <input class="form-control" type="text" name="new_existence[{{ $purchases_product->id }}]" value="{{ isset($inventory_existences[$purchases_product->id]) ? $inventory_existences[$purchases_product->id] : 0 }}">
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if((!isset($inventory_old_cost[$purchases_product->id]) || $inventory_old_cost[$purchases_product->id] == 0) && (!isset($cost_product[$purchases_product->id]) || $cost_product[$purchases_product->id] == 0) && isset($inventory_existences[$purchases_product->id]) && $inventory_existences[$purchases_product->id] > 0)
                                                        <b style="color:red;">Precio costo a regularizar</b>
                                                    @endif
                                                </td>                                                                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table> 
                                <div class="row">                                        
                                    <div class="form-group col-md-12">
                                        <label>Observación</label>
                                        <textarea class="form-control" name="observation">{{ old('observation') }}</textarea>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger" role="alert">
                                    No se encontraron Registros.
                                </div>
                            @endif
                        </div>             
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                            <a href="{{ url('inventories') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>                                
                    {{ Form::close() }} 
                </div>                               
            </div>
        </div>
    </div>
@endsection
