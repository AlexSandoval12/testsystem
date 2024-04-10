@extends('layouts.AdminLTE.index')

@section('title', ' Reporte de Existencias')

@section('menu_pagina')

@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form method="GET">
                            @if(request()->deposit_id)
                                <div class="ibox-tools">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                            Exportar <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="{{ route('reports.stock-product-purchases-xls', request()->query()) }}"><i class="fa fa-file-excel"></i> XLS</a></li>
                                            {{-- <li><a href="{{ route('reports.stock-product-details-xls', request()->query()) }}"><i class="fa fa-file-excel"></i> DETALLADO</a></li> --}}
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                    <div class="ibox-content pb-0">
                        @include('partials.messages')
                        <form method="GET">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Deposito</label>
                                    {{ Form::select('deposit_id', $deposits, old('deposit_id', request()->deposit_id), ['placeholder' => 'Seleccione Deposito', 'class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-md-5">
                                    <label>Producto</label>
                                    {{ Form::select('product_id', config('constants.product'), request()->product_id, ['placeholder' => 'Seleccione Producto', 'class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    <br>
                                    <button type="submit" class="btn btn-primary" name="filter"><i class="fa fa-search"></i></button>
                                    @if(request()->deposit_id)
                                        <a href="{{ url('reports/stock-product-purchases') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>                              
                            </div>
                        </form>
                    </div>
                    @if(request()->deposit_id)
                        <div class="ibox-content no-padding">
                            @if($purchases_existences->count() > 0)                                
                                <table class="table table-hover table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Código</th>
                                            <th class="text-center">Nombre de Producto</th>
                                            <th class="text-center">Existencia</th>
                                            <th class="text-center">Costo</th>
                                        </tr>                                        
                                    </thead>
                                    <tbody>
                                        @foreach($purchases_existences as $existence)
                                            <tr>
                                                <td class="text-center">{{ $existence->raw_material_id }}</td>
                                                <td class="text-center">{{ $existence->raw_material->description }}</td>
                                                <td class="text-center">{{ number_format($existence->existence, 0, ',', '.') }}</td>
                                                <td class="text-center">Gs. {{ $existence->price_cost ? number_format($existence->price_cost, 0, ',', '.') : 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>                                
                                {{ $purchases_existences->appends(request()->query())->links() }}
                            @else
                                <div class="alert alert-danger" role="alert">
                                    No se encontraron Registros.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection
@section('layout_js')
    <script>
        $(document).ready(function() {
            $('.date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                  cancelLabel: 'Clear'
                }
            });

            $('.date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('#enterprise_id, #type_receipt, #type_payment').on('change', function(){
                getSellers();
            });
        });
    </script>
@endsection