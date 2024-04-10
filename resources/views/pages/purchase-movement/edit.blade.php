@extends('layouts.AdminLTE.index')
@section('title', 'Editar Recepcion de Compras')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => ['purchase-movement.update', $purchase_movement->id], 'method' => 'PUT']) }}
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Editar Recepción de Orden de Compra</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>Numero OC</label>
                                        <input class="form-control" type="text" name="number_oc" id="number_oc" placeholder="Numero OC" autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="ibox float-e-margins" id="div_details">
                <div class="ibox-title">
                    <h3>Items a Recepcionar</h3>
                </div>
                <div class="ibox-content table-responsive no-padding" id="detail_product">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="text-right">Cód</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Presentación</th>
                                {{-- <th class="text-center">Vcto</th> --}}
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">A Recibir</th>
                                <th class="text-center">Recepcionado</th>
                                <th class="text-center">Saldo</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right">SubTotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail"></tbody>
                    </table>
                </div>
            </div>
            <div class="ibox-content pb-0" id="div_deposito">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Deposito</label>
                                {{ Form::select('deposits_id', [], request()->deposits_id, ['class' => 'form-control', 'select2', 'id' => 'deposits_id']) }}
                            </div>
                            <div class="form-group col-md-4">
                                <label>Sucursal</label>
                                {{ Form::select('branch_id', $branches, old('branch_id'), ['placeholder' => 'Seleccione Sucursal', 'class' => 'form-control', 'select2', 'id' => 'branch_id']) }}
                            </div>
                            <div class="form-group col-md-4" id="row_btn_pending_number" style="display: none;">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Observación</label>
                                <textarea class="form-control" name="observation">{{ old('observation') }}</textarea>
                            </div>
                        </div>
                    </div><br><br>
                    <div class="col-lg-12">
                        <div id="div_provider_data">
                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>Proveedor</label>
                                    <input type="text" name="provider_name" value="" id="provider_name" class="form-control" readonly>
                                    <input type="hidden" name="purchases_provider_id" value="" id="purchases_provider_id" class="form-control" readonly>
                                    <input type="hidden" name="ruc" value="" id="ruc" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Telefono</label>
                                    <input type="text" name="phone_label" value="" id="phone_label" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Dirección</label>
                                    <input type="text" name="address_label" value="" id="address_label" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div id="div_invoice_header">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="form-group col-md-9">
                                            <label>Razon Social</label>
                                            <input type="text" name="social_reason" value="" id="social_reason" class="form-control" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Condición</label>
                                            <select name="condition" onchange="show_expiration($(this).val())" class="form-control">
                                                @foreach(config('constants.invoice_condition') as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Numero de Factura</label>
                                            <input class="form-control text-center" type="text" name="invoice_number" onchange="loadStamped();" id="invoice_number" value="" invoice-purchase-mask>
                                            <input type="hidden" name="purchase_id" value="" id="purchase_id" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Fecha Factura</label>
                                            <input class="form-control  text-center date" type="text" name="date" id="date" value="">
                                            <span class="red" id="text_date_validation"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Documento</label>
                                            {{ Form::select('type', $type_vouchers, old('type'), ['data-live-search'=>'true', 'class' => 'form-control', 'id' => 'type']) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Nro.Timbrado</label>
                                            <input class="form-control" type="text" name="stamped" id="stamped" value=""  onkeyup="changeValidationStamped();">
                                            <span class="red" id="text_stamped_validation"></span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Vigencia Timbrado</label>
                                            <input class="form-control  text-center date" type="text" name="stamped_validity" id="stamped_validity" value="">
                                            <span class="red" id="text_stamped_validity_validation"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" style="border-left: 1px solid #e7eaec;">
                                    <div class="row">
                                        <div class="form-group col-md-6 text-center">
                                            <label>Fecha Pago</label>
                                            <input class="form-control text-center date" type="text" name="expiration[]" value="">
                                        </div>
                                        <div class="form-group col-md-6 text-center">
                                            <label>Pago</label>
                                            <input class="form-control" type="text" name="payment_amount[]" value="" period-data-mask>
                                        </div>
                                    </div>
                                    <div class="row" id="row_btn_payment_date">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-block btn-outline btn-primary" id="btn_payment_date"><i class="fa fa-plus"></i> Fecha de Pago</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="ibox-content table-responsive no-padding" id="div_invoice_detail">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">  Cant.</th>
                                        <th class="text-center" width="17%"> Materia Prima</th>
                                        <th class="text-center" width="20%"> Descripcion</th>
                                        <th class="text-center" width="10%"> Precio</th>
                                        <th class="text-center" width="10%"> 10%</th>
                                        <th class="text-center" width="10%"> 5%</th>
                                        <th class="text-center" width="10%"> Exenta</th>
                                        <th class="text-center" width="10%"> Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_product"></tbody>
                            </table>
                        </div>
                        <br>
                        <div id="div_image">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Total factura:</label> <div class="inline-block" id="label_total_invoice">Gs 0.</div>
                                    <input type="hidden" name="total_invoice" value="" id="total_invoice" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Total IVA 5%:</label> <div class="inline-block" id="totales_iva5"> </div>
                                    <input type="hidden" name="total_iva" value="" id="total_iva" class="form-control" readonly>
                                    <input type="hidden" name="total_iva_5" value="" id="total_iva_5" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Total IVA 10%:</label> <div class="inline-block" id="totales_iva10"> </div>
                                    <input type="hidden" name="total_iva_10" value="" id="total_iva_10" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
            <div class="ibox-footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('wish-production') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection

