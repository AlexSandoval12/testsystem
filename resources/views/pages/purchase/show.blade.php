@extends('layouts.AdminLTE.index')
@section('title', 'Mostrar Compra')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="ibox-tools">
                    <a href="{{ url('purchase') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3"><b>Sucursal:</b></div>
                            <div class="col-md-9">{{ $purchase->branch->name }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Tipo:</b></div>
                            <div class="col-md-9"><span class="label label-{{ config('constants.type_purchases_label.' . $purchase->type) }}">{{ config('constants.type_purchases.'. $purchase->type) }}</span></div>
                        </div>                                
                        <div class="row">
                            <div class="col-md-3"><b>Número:</b></div>
                            <div class="col-md-9">{{ $purchase->number }}</div>
                        </div>
                        @if($purchase->type == 4)
                            @if(count($purchase->note_credits)>0)
                                <div class="row mt-2">
                                    <div class="col-md-3"><b>Nro.Factura:</b></div>
                                    <div class="col-md-9">
                                        @foreach ($purchase->note_credits as $note_credit)
                                            {{ $note_credit->purchase_invoice->number }}<br>
                                        @endforeach                                            
                                    </div>
                                </div>
                            @endif
                        @endif 
                        <div class="row">
                            <div class="col-md-3"><b>Condición:</b></div>
                            <div class="col-md-9">{{ config('constants.invoice_condition.'. $purchase->condition) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Nro. Timbrado:</b></div>
                            <div class="col-md-9">{{ $purchase->stamped }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Vigencia Timbrado:</b></div>
                            <div class="col-md-9">{{ $purchase->stamped_validity ? $purchase->stamped_validity->format('d/m/Y') : '' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Fecha Compra:</b></div>
                            <div class="col-md-9">{{ $purchase->date->format('d/m/Y') }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3"><b>Id Proveedor:</b></div>
                            <div class="col-md-9">{{ number_format($purchase->provider_id, 0, ',', '.') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Proveedor:</b></div>
                            <div class="col-md-9">{{ $purchase->provider->name }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Ruc:</b></div>
                            <div class="col-md-9">{{ $purchase->ruc }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Monto:</b></div>
                            <div class="col-md-9">{{ number_format($purchase->amount, 2, ',', '.') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>OP:</b></div>
                            <div class="col-md-9">{{ $purchase->payment_order }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Archivo:</b></div>
                            <div class="col-md-9">
                                @if($purchase->file)
                                    <a href="{{ url('purchases-provider-invoice/') }}/{{ $purchase->id }}/download?show=1" target="_blank"><i class="fa fa-search"></i></a>
                                    <a href="{{ url('purchases-provider-invoice/') }}/{{ $purchase->id }}/download" target="_blank"><i class="fa fa-download"></i></a>
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Observación:</b></div>
                            <div class="col-md-9">{{ $purchase->observation }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Estado:</b></div>
                            <div class="col-md-9"><span class="label label-{{ config('constants.purchase-status-label.' . $purchase->status) }}">{{ config('constants.purchase-status.'. $purchase->status) }}</span></div>
                        </div>
                        @if($purchase->status == 2)
                            <div class="row mt-2">
                                <div class="col-md-3"><b>Usuario Eliminación:</b></div>
                                <div class="col-md-9">{{ $purchase->user_delete->fullname }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Fecha Borrado</b></div>
                                <div class="col-md-9">{{ $purchase->date_deleted->format('d/m/Y') }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Motivo borrado</b></div>
                                <div class="col-md-9">{{ $purchase->reason_deleted }}</div>
                            </div>
                        @endif
                        <div class="row mt-2">
                            <div class="col-md-3"><b>Usuario Creación:</b></div>
                            <div class="col-md-9">{{ $purchase->user->fullname }}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"><b>Fecha Creación:</b></div>
                            <div class="col-md-9">{{ $purchase->created_at->format('d/m/Y h:i:s') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($purchase->type == 1)
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <H3>Detalle de Pago</H3>
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th width="10%" class="text-center">Fecha</th>
                                                <th width="60%" class="text-center">Comprobante</th>
                                                <th width="25%" class="text-center">Monto</th>
                                            </tr>
                                        </thead>
                                        {{-- <tbody>
                                            @foreach($purchase->purchases_collects as $collect)
                                                @if($collect->purchases_collect_payments()->count() > 0)
                                                    @foreach($collect->purchases_collect_payments as $payment)
                                                        @if($payment->purchase->status == 1)
                                                            <tr>
                                                                <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                                                                <td class="text-right">{{ $payment->purchase->date->format('d/m/Y') }}</td>
                                                                <td>{{ config('constants.type_purchases.' . $payment->purchase->type) }} #{{ $payment->purchase->number }}</td>
                                                                <td class="text-right">{{ number_format($payment->amount, 2, ',', '.') }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="4">NO SE REGISTRAN PAGOS</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-center"><b>Saldo</b></td>
                                                <td colspan="2" class="text-center"><b>{{ number_format($purchase->purchases_collects()->sum('residue'), 2,',','.') }} {{ $purchase->currency->abbreviation }}</b></td>
                                            </tr>
                                        </tfoot> --}}
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
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
                        <th>Descripción</th>
                        <th>OC</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>SubTotal</th>
                        <th>Excenta</th>
                        <th>IVA 5%</th>
                        <th>IVA 10%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->purchase_details as $details)
                        <tr>
                            <td>{{ $details->material_id }}</td>
                            <td>
                                {{ $details->material->description }}
                            </td>
                            <td>{{ $details->description }}</td>
                            <td>{{ $details->purchases_order_detail_id ? $details->purchases_order_detail->purchases_order->number : '' }}</td>
                            <td class="text-right">{{ number_format($details->quantity, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->amount, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->amount*$details->quantity, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->excenta, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->iva5, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($details->iva10, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-right"><b>Total:</b></td>
                        <td class="text-right"><b>{{ number_format($purchase->purchase_details->sum('excenta'), 2, ',', '.') }}</b></td>
                        <td class="text-right"><b>{{ number_format($purchase->purchase_details->sum('iva5'), 2, ',', '.') }}</b></td>
                        <td class="text-right"><b>{{ number_format($purchase->purchase_details->sum('iva10'), 2, ',', '.') }}</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

