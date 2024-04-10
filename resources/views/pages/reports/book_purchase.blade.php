@extends('layouts.AdminLTE.index')

@section('title', ' Libro Compras')

@section('menu_pagina')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            @include('partials.messages')
            <div class="ibox-title">
                @if(request()->date_range)
                    <div class="ibox-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    Exportar <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                {{-- <li><a href="{{ route('reports.purchases_report.xls', request()->query()) }}" id="xls"><i class="fa fa-file-excel"></i> XLS</a></li> --}}
                                <li><a href="{{ route('reports.purchases_report.pdf', request()->query()) }}"><i class="fa fa-file-pdf" ></i> PDF</a></li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            <div class="ibox-content pb-0">
                <form method="GET">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Sucursal</label>
                            {{ Form::select('branch_id', $branches, request()->branch_id, ['placeholder' => 'Sucursal', 'class' => 'form-control selectpicker', 'data-live-search'=>'true', ]) }}
                        </div>
                        <div class="form-group col-md-2">
                            <label>Condición</label>
                            {{ Form::select('condition', config('constants.invoice_condition'), request()->condition, ['data-live-search'=>'true', 'placeholder' => 'Seleccione Condicion', 'class' => 'form-control selectpicker']) }}
                        </div>
                        <div class="form-group col-md-3">
                            <label>Documento</label>
                            {{ Form::select('type[]', config('constants.type_purchases'), request()->type, ['class' => 'form-control selectpicker','data-live-search'=>'true', 'multiple', 'data-actions-box'=>'true', 'data-none-Selected-Text' => 'Seleccione Documento','required']) }}
                        </div>
                        <div class="form-group col-md-3">
                            <label>Fecha</label>
                            <input type="text" name="date_range"  class="form-control date_range text-center"  placeholder="Rango de fecha" value="{{ request()->date_range }}" autocomplete="off" date-range-mask>
                        </div>
                        <div class="form-group col-md-2" style="margin-top: 7mm">
                            <button type="submit" class="btn btn-primary" name="filter" value="1"><i class="fa fa-search"></i></button>
                            @if(request()->filter)
                                <a href="{{ request()->url() }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            @if(request()->date_range)
                <div class="ibox-content table-responsive no-padding">
                    @if(count($purchases) > 0)
                        <div class="col-lg-3 widget style1 navy-bg">
                            <div class="ibox float-e-margins">
                                <div>
                                    <h5>Total Exentas</h5>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <h1 class="no-margins">{{ number_format($purchases_sum->total_excenta, 0, ',', '.') }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 widget style1 navy-bg">
                            <div class="ibox float-e-margins">
                                <div>
                                    <h5>Total IVA 5%</h5>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <h1 class="no-margins">{{ number_format($purchases_sum->total_iva5, 0, ',', '.') }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 widget style1 navy-bg">
                            <div class="ibox float-e-margins">
                                <div>
                                    <h5>Total IVA 10%</h5>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <h1 class="no-margins">{{ number_format($purchases_sum->total_iva10, 0, ',', '.') }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 widget style1 navy-bg">
                            <div class="ibox float-e-margins ">
                                <div>
                                    <h5>IVA 10%</h5>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <h1 class="no-margins">{{ number_format($purchases_sum->amount_iva10, 0, ',', '.') }}</h1>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>SU</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Fecha Compra</th>
                                    <th>Condición</th>
                                    <th>Tipo</th>
                                    <th>Numero</th>
                                    <th>Ruc</th>
                                    <th>Proveedor</th>
                                    <th>Exenta</th>
                                    <th>Total IVA 5%</th>
                                    <th>IVA 5%</th>
                                    <th>Total IVA 10%</th>
                                    <th>IVA 10%</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->branch->abbreviation }}</td>
                                        <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $purchase->date->format('d/m/Y') }}</td>
                                        <td>{{ config('constants.invoice_condition.'. $purchase->condition) }}</td>
                                        <td><span class="label label-{{ config('constants.type_purchases_label.' . $purchase->type) }}">{{ config('constants.type_purchases.'. $purchase->type) }}</span></td>
                                        <td>{{ $purchase->number }}</td>
                                        <td>{{ $purchase->ruc }}</td>
                                        <td>{{ $purchase->provider->name }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->total_excenta, 0, ',', '.') : 0 }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->total_iva5, 0, ',', '.') : 0 }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->amount_iva5, 0, ',', '.') : 0 }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->total_iva10, 0, ',', '.') : 0 }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->amount_iva10, 0, ',', '.') : 0 }}</td>
                                        <td class="text-right">{{ $purchase->status==1 ? number_format($purchase->amount, 0, ',', '.') : 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-right"><b>Total ({{ number_format($purchases_sum->count, 0, ',', '.') }}):</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->total_excenta, 0, ',', '.') }}</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->total_iva5, 0, ',', '.') }}</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->amount_iva5, 0, ',', '.') }}</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->total_iva10, 0, ',', '.') }}</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->amount_iva10, 0, ',', '.') }}</b></td>
                                    <td class="text-right"><b>{{ number_format($purchases_sum->amount, 0, ',', '.') }}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                        {{ $purchases->appends(request()->query())->links() }}
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
@section('layout_css')
    <link rel="stylesheet" href="{{  cached_asset('css/bootstrap-select.min.css') }}">
    <style>
    </style>
@endsection
@section('layout_js')
    <script src="{{ cached_asset('js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();

            $('.date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                cancelLabel: 'Clear'
                }
            });

            $('.date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });
        });
    </script>
@endsection