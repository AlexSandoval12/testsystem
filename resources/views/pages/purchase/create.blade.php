@extends('layouts.AdminLTE.index')
@section('title', 'Compras')
@section('content')
{{ Form::open(['id' => 'form']) }}
<div class="row">
    <div class="col-lg-7">
        <div class="ibox float-e-margins">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="my-0">Datos del Proveedor</h4>
                </div>
                <div class="panel-body pb-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group col-md-12">
                                <label>Proveedor</label>
                                <select class="form-control" name="purchases_provider_id" id="purchases_provider_id"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group col-md-4">
                                <label>RUC</label>
                                <input type="text" name="ruc" value="{{ old('ruc') }}" id="ruc" class="form-control">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Telefono</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" id="phone" class="form-control">
                            </div>
                            <div class="form-group col-md-5">
                                <label>Dirección</label>
                                <input type="text" name="address" value="{{ old('address') }}" id="address" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Razón Social Proveedor</label>
                                <input type="text" name="razon_social" value="{{ old('razon_social') }}" id="razon_social" class="form-control">
                            </div>
                            <div class="form-group col-md-12" id="div_details_advances_providers">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <h4 class="my-0">Anticipos del Proveedor</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-condensed table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">OP.Nro</th>
                                                    <th class="text-center">Importe</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_details_advances_providers"></tbody>
                                            <tfoot class="bold">
                                                <tr>
                                                    <td colspan="3" class="text-right">Total</td>
                                                    <td class="text-right"><b id="total_advance"></b></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="accounting_account_provider_id" id="accounting_account_provider_id" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="ibox float-e-margins">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">Orden de Compra</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2">Ultimas Compras</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3">Pendientes Pago</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body table-responsive">
                            <div id="div_purchase_order_spinner" style="display: none;">
                                <h2 class="text-center my-3"><i class="fa fa-spinner fa-spin"></i>Cargando...</h2>
                            </div>
                            <div class="row" id="div_orders_purchases">
                                <div class="col-md-12">
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Numero</th>
                                                <th class="text-center">Condición</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_detail_orders_purchases"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" id="div_dont_have_orders_purchases" style="display: none;">
                                <div class="col-md-12">
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Numero</th>
                                                <th class="text-center">Condición</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">NO TIENE ORDENES DE COMPRAS</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane">
                        <div class="panel-body table-responsive">
                            <div class="row" id="div_last_purchases">
                                <div class="col-md-12">
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-center">Numero</th>
                                                <th class="text-center">Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_detail_last_purchases"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane">
                        <div class="panel-body table-responsive">
                            <div class="row" id="div_purchases_pendings">
                                <div class="col-md-12">
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-center">Numero</th>
                                                <th class="text-center">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_detail_purchases_pendings"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="lender_receipts" style="display: none;">
    <div class="col-lg-7">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Recibos</h5>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-condensed table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Nro. Recibo</th>
                                    <th class="text-center">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_detail_lender_payments"></tbody>
                            <tfoot class="bold">
                                <tr>
                                    <td class="text-right" colspan="2">Totales</td>
                                    <td class="text-right"><b id="div_total_receipts"></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Datos de la Compra</h5>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Sucursal</label>
                        {{ Form::select('branch_id', $branches, old('branch_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'id' => 'branch_id']) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>Documento</label>
                        {{ Form::select('type', config('constants.type_purchases'), old('type'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'id' => 'type']) }}
                    </div>
                    <div class="form-group col-md-3">
                        <label>Numero</label>
                        <input class="form-control" type="text" name="number" id="number" value="{{ old('number') }}" invoice-purchase-mask>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Fecha Compra</label>
                        <input class="form-control date" type="text" name="date" id="date" value="{{ old('date') }}" onblur="changeValidationDate();">
                        <span class="red" id="text_date_validation"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Nro.Timbrado</label>
                        <input class="form-control" type="text" name="stamped" id="stamped" value="{{ old('stamped') }}" onkeyup="changeValidationStamped();">
                        <span class="red" id="text_stamped_validation"></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Vigencia Timbrado</label>
                        <input class="form-control date" type="text" name="stamped_validity" id="stamped_validity" value="{{ old('stamped_validity') }}" onblur="changeValidationDate();">
                        <span class="red" id="text_stamped_validity_validation"></span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Condición</label>
                        {{ Form::select('condition', config('constants.invoice_condition'), old('condition'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'id' => 'condition', "onChange" => "changeCondition();"]) }}
                    </div>
                    {{-- <div class="form-group col-md-3" id="div_quota">
                        <label>Cant. Cuota</label>
                        <input class="form-control" type="text" name="number" id="quota" value="{{ old('quota') }}">
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="div_note_credits">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Buscar el Numero de Factura que afecta la Nota de Crédito</h5>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Nro. Factura</label>
                        <select class="form-control" name="invoice_number" id="invoice_number" style="width: 100%"></select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Condición</label>
                        <input class="form-control" type="text" name="invoice_condition" id="invoice_condition" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Fecha</label>
                        <input class="form-control" type="text" name="invoice_date" id="invoice_date" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Monto Factura</label>
                        <input class="form-control" type="text" name="invoice_amount" id="invoice_amount" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Saldo Factura</label>
                        <input class="form-control" type="text" name="invoice_residue" id="invoice_residue" readonly>
                    </div>
                    <input type="hidden" name="invoice_id" id="invoice_id">
                </div>
            </div>
            <div class="ibox-content table-responsive" id="detail_product_invoice">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">Cód</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Subtotal</th>
                            <th class="text-center">Exenta</th>
                            <th class="text-center">IVA 5%</th>
                            <th class="text-center">IVA 10%</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_detail_invoice"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="div_spinner" style="display: none;">
    <h2 class="text-center my-3"><i class="fa fa-spinner fa-spin"></i>Cargando...</h2>
</div>
<div id="div_detail_products">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h3>Items a Comprar</h3>
        </div>
        <div class="ibox-content pb-0">
            <div class="row">
                <div class="form-group col-md-3">
                    <label>Producto</label>
                    <select class="form-control" name="purchases_product_id" id="purchases_product_id" style="width: 100%"></select>
                    <span class="red" id="text_last_purchases"></span>
                </div>
                <div class="form-group col-md-3">
                    <label>Descripción</label>
                    <input class="form-control" type="text" name="products_description" id="products_description" value="{{ old('products_description') }}" placeholder="Descripción">
                </div>
                <div class="form-group col-md-1">
                    <label>Cantidad</label>
                    <input class="form-control" type="text" name="products_quantity" value="{{ old('products_quantity') }}" placeholder="Cantidad">
                </div>
                <div class="form-group col-md-2">
                    <label>Monto</label>
                    <input class="form-control" type="text" name="products_amount" value="{{ old('products_amount') }}" placeholder="Monto" period-data-mask-decimal>
                </div>
                <div class="form-group col-md-1">
                    <label>Agregar</label>
                    <button type="button" class="btn btn-success" id="button_add_product"><i class="fa fa-plus"></i></button>
                </div>
                <input type="hidden" name="emergency_mobile" id="emergency_mobile">
                <input type="hidden" name="type_iva" id="type_iva">
            </div>
        </div>
        <div class="ibox-content table-responsive no-padding">
            <table class="table table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th class="text-center" width="5%">Cód</th>
                        <th class="text-center" width="20%">Producto</th>
                        <th class="text-center" width="5%">OC</th>
                        <th class="text-center" width="5%">Cantidad</th>
                        <th class="text-center" width="9%">Precio</th>
                        <th class="text-center" width="9%">Exenta</th>
                        <th class="text-center" width="9%">IVA 5%</th>
                        <th class="text-center" width="9%">IVA 10%</th>
                        <th class="text-center" width="5%"></th>
                    </tr>
                </thead>
                <tbody id="tbody_detail"></tbody>
                <tfoot class="bold">
                    <tr>
                        <td colspan="6" id="totales_iva"></td>
                        <td class="text-right">Sub-Totales</td>
                        <td id="total_excenta" class="text-right"></td>
                        <td id="total_iva5" class="text-right"></td>
                        <td id="total_iva10" class="text-right"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-right"><b><h3>Total de Compra</h3></b></td>
                        <td class="text-right" bgcolor= "#E0F8F7"><b><h3 id="total_purchases"></h3></b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <input type="hidden" name="total_excenta_final" id="total_excenta_final" value="0">
            <input type="hidden" name="total_iva5_final" id="total_iva5_final" value="0">
            <input type="hidden" name="total_iva10_final" id="total_iva10_final" value="0">
            <input type="hidden" name="total_product" id="total_product" value="0">
            <input type="hidden" name="amount_iva5" id="amount_iva5" value="0">
            <input type="hidden" name="amount_iva10" id="amount_iva10" value="0">
        </div>
    </div>
    <div class="ibox float-e-margins">
        <div class="ibox-content pb-0">
            <div class="row">
                <div class="col-md-7">
                    {{-- <div class="row">
                        <div class="form-group col-md-12">
                            <label>Caja Chica</label>
                            {{ Form::select('cash_box_id', $cash_boxes, old('cash_box_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => 'Seleccione Caja Chica', 'id' => 'cash_box_id']) }}
                        </div>
                    </div> --}}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Seleccione el Pago</label><br>
                                <input type="radio" name="type_payment" value="1" {{ 'checked' }} onClick="changeOtherAccounting(1);" id="type_payment_1"> NO PAGO
                                <input type="radio" name="type_payment" value="2" {{ 'checked' }} onClick="changeOtherAccounting(0);" id="type_payment_2"> PAGO
                            </div>
                            <div class="form-group col-md-3 text-center">
                                <label>Fecha Pago</label>
                                <input class="form-control text-center date" type="text" name="expiration[]" value="" autocomplete="off">
                                <span class="red" id="text_days_of_grace"></span>
                            </div>
                            <div class="form-group col-md-5 text-center">
                                <label>Monto a Pagar en Tesoreria</label>
                                <input class="form-control  text-right" type="text" name="amount_treasury[]" value="" period-data-mask-decimal autocomplete="off">
                            </div>
                            <div class="row" id="row_btn_payment_date" >
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-block btn-outline btn-primary" id="btn_payment_date"><i class="fa fa-plus"></i> Fecha de Pago</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Observación</label>
                            <textarea class="form-control" name="observation" >{{ old('observation') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ibox-footer">
            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
            <a href="{{ url('purchase') }}" class="btn btn-sm btn-danger">Cancelar</a>
        </div>
    </div>
</div>
{{ Form::close() }}
@endsection
@section('layout_js')
    <script>
        var dv = 0;
        var counter = 0;
        var total_receipts = 0;
        var form_ready = false;
        var invoice_items_array            = [];
        var accounting_accounts_array      = [];
        var other_accounting_account_array = [];
        var advances_array                 = [];
        $("#div_details_advances_providers, #detail_accounting_seat, #div_social_reason_name, #div_note_credits, #detail_product_invoice").hide();
        $("#div_social_reason").show();
        $("#text_date_validation, #text_stamped_validity_validation, #tbody_detail_invoice, #text_days_of_grace").html('');

        $(document).ready(function ()
        {
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('purchase.store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('purchase') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $(".date").datepicker({
                format: 'dd/mm/yyyy',
                language: 'es',
                autoclose: true,
                todayBtn: true,
                todayBtn: "linked",
                daysOfWeekDisabled: [0]
            });

            $("[period-data-mask]").inputmask({
                alias: 'decimal',
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true,
                allowMinus: false,
                rightAlign: false,
                digits: 0,
                removeMaskOnSubmit: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });

            $(".date").datepicker({
                format: 'dd/mm/yyyy hh:ii',
                language: 'es',
                autoclose: true,
                todayBtn: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });

            $("#purchases_provider_id").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('ajax/purchases_providers') }}",
                    dataType: 'json',
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.items
                        };
                    }
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (repo) {
                    if (repo.loading) return repo.text;
                    var markup = repo.name + "<br>" + "<i class='fa fa-id-card'></i> " + repo.ruc ;
                    return markup;
                },
                templateSelection: function (repo) {
                    return repo.name + '|' + repo.ruc;
                }
            }).on("select2:select", function (e) {
                $('#text_days_of_grace').html('');
                var data_item = e.params.data;
                $('#razon_social').val(data_item.name);
                $('#ruc').val(data_item.ruc);
                $('#phone').val(data_item.phone);
                $('#address').val(data_item.address);
                // $('#stamped').val(data_item.stamped);
                // $('#stamped_validity').val(data_item.stamped_validity);
                dv = data_item.dv;

                if(data_item.days_of_grace) $('#text_days_of_grace').html(data_item.days_of_grace + ' Días de gracia.');
                $('#div_purchase_order_spinner').show();
                changeLastPurchases();
                // load_payment_services_authorizations();
            });

            $("#invoice_number").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: '{{ url('ajax/purchases/note-credits') }}',
                    dataType: 'json',
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            purchases_provider_id: $('#purchases_provider_id').val(),
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.items
                        };
                    }
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (repo) {
                    if (repo.loading) return repo.text;
                    var markup = repo.text;
                    return markup;
                },
                templateSelection: function (repo) {
                    return repo.text;
                }
            }).on("select2:select", function (e) {
                var data_item = e.params.data;

                $('#invoice_condition').val(data_item.condition);
                $('#invoice_date').val(data_item.date);
                $('#invoice_id').val(data_item.id);
                $('#invoice_amount').val($.number(data_item.total, 0, ',', '.'));
                $('#invoice_residue').val($.number(data_item.residue, 0, ',', '.'));

                // Buscar Productos de la Factura
                $("#tbody_detail_invoice").html('');
                $("#detail_product_invoice").show();
                $.each(data_item.products, function(index, value)
                {
                    $('#tbody_detail_invoice').append('<tr>' +
                        '<td class="text-right">' + value.id + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td class="text-right">' + value.quantity + '</td>' +
                        '<td class="text-right">' + value.amount + '</td>' +
                        '<td class="text-right">' + value.subtotal + '</td>' +
                        '<td class="text-right">' + value.excenta + '</td>' +
                        '<td class="text-right">' + value.iva5 + '</td>' +
                        '<td class="text-right">' + value.iva10 + '</td>' +
                    '</tr>');
                });

            });

            $("#purchases_product_id").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('ajax/raw-material') }}",
                    dataType: 'json',
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data, params) {
                        return { results: data.items };
                    }
                },
                escapeMarkup: function (markup) { return markup; },
                templateResult: function (repo) {
                    if (repo.loading) return repo.text;
                    var markup = repo.name;
                    return markup;
                },
                templateSelection: function (repo) {
                    return repo.name;
                }
            }).on("select2:select", function (e) {
                var data_item = e.params.data;
                $('#purchases_product_id option:selected').attr('data-text', data_item.name);
            });


            $("#button_add_product").click(function() {
                addProduct();
            });

            $("#button_add_payment_method").click(function() {
                addPaymentMethod();
            });

            $("#button_other_accounting_account").click(function() {
                addOtherAccountingAccount();
            });

            $("select[name='purchases_product_id']").on('change', function(){
                checkLastPurchasesProducts();
            });

            $("select[name='type']").on('change', function(){
                ChangeTypePurchase();
            });

            $("#social_reason_id").on('change', function(){
                $('#div_spinner').show();
            });

            $("#number").on('change', function(){
                loadStamped();
            });

            $(".date").datepicker({
                format: 'dd/mm/yyyy',
                language: 'es',
                autoclose: true,
                todayBtn: true,
                todayBtn: "linked"
            });

            $('#btn_payment_date').click(function(){
                $('<div class="row" >'+
                    '<div class="form-group col-md-3 text-center" style="margin-left: 66mm">'+
                        '<input class="form-control text-center date" type="text" name="expiration[]" value="" autocomplete="off">'+
                    '</div>'+
                    '<div class="form-group col-md-5" style="margin-left: -2mm">'+
                        '<div class="input-group">'+
                            '<input class="form-control" type="text" name="amount_treasury[]" value="" period-data-mask-decimal autocomplete="off">'+
                            '<span class="input-group-btn">'+
                                '<button class="btn btn-warning" type="button"  onclick="$(this).parent().parent().parent().parent().remove()"><i class="fa fa-times"></i></button>'+
                            '</span>'+
                        '</div>'+
                    '</div>'+
                '</div>').insertBefore('#row_btn_payment_date');
                loadDate();

            });
        });

        function loadDate()
        {
            $(".date").datepicker({
                format: 'dd/mm/yyyy',
                language: 'es',
                autoclose: true,
                todayBtn: true,
                todayBtn: "linked",
                daysOfWeekDisabled: [0]
            });

            $("[period-data-mask]").inputmask({
                alias: 'decimal',
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true,
                allowMinus: false,
                rightAlign: false,
                digits: 0,
                removeMaskOnSubmit: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });

            $(".date").datepicker({
                format: 'dd/mm/yyyy hh:ii',
                language: 'es',
                autoclose: true,
                todayBtn: true,
            });

            $("[date-mask]").inputmask({
                alias: 'date'
            });
        }

        function loadStamped()
        {
            var purchase_number = $('#number').val();
            var provider_id = $('#purchases_provider_id').val();

            $('#stamped, #stamped_validity').val('');
            if(purchase_number && provider_id)
            {
                $.ajax({
                    url: '{{ route('provider-stamped.search') }}',
                    type: "GET",
                    data: { purchase_number, provider_id},
                    success: function(data) {
                        if(data.stamped) $('#stamped').val(data.stamped);
                        if(data.stamp_validity) $('#stamped_validity').val(data.stamp_validity);
                    },
                    error: function(data) {
                        laravelErrorMessages(data);
                    }
                });
            }
        }
        // changeCondition()
        // function changeCondition()
        // {
        //     var condition = $("#condition option:selected").val();
        //     if(condition == 1)
        //     {
        //         $("#div_quota").hide();
        //     }
        //     else
        //     {
        //         $("#div_quota").show();
        //     }
        // }

        function loadPeriodDataMaskDecimal()
        {
            $("[period-data-mask-decimal]").inputmask({
                alias: 'decimal',
                groupSeparator: '.',
                radixPoint: ',',
                autoGroup: true,
                allowMinus: false,
                rightAlign: true,
                digits: 2,
                removeMaskOnSubmit: true,
            });
        }

        function ChangeTypePurchase()
        {
            $("#div_note_credits").hide();

            if($('#type').val() == 4)
            {
                $("#div_note_credits").show();
            }
        }

        function ChangeCostCentersCheck()
        {
            var grandSelected        = 0;
            var grandValorUnitario   = 0;
            var grandPercentageTotal = 100;
            var grandQuantity        = 0;
            $('input[name^="cost_centers_check[]"]').each(function ()
            {
                if(+$(this).prop('checked'))
                {
                    grandSelected++;
                }
            });

            $('input[name^="cost_centers_check[]"]').each(function ()
            {
                var in_cost_centers = $(this).closest('tr').find("input[name='cost_centers[]']");
                in_cost_centers.val('');
            });

            if(grandSelected>0)
            {
                grandValorUnitario = Math.round(100 / grandSelected);
                $('input[name^="cost_centers_check[]"]').each(function ()
                {
                    if(+$(this).prop('checked'))
                    {
                        grandQuantity = grandValorUnitario > grandPercentageTotal ? grandPercentageTotal : grandValorUnitario;
                        var in_cost_centers = $(this).closest('tr').find("input[name='cost_centers[]']");
                        in_cost_centers.val(grandQuantity);

                        grandPercentageTotal = grandPercentageTotal - grandQuantity
                    }
                });
            }
            ChangeCostCentersCheckTotal();
        }

        function ChangeCostCentersCheckTotal()
        {
            var grandTotal = 0;
            $("input[name*='cost_centers[]']").each(function(e)
            {
                var in_cost_centers = +$(this).val().replace(/\./g, '');
                grandTotal = grandTotal + in_cost_centers;
            });
            $("#total_cost_centers").val(grandTotal);
        }

        function ChangeSelectAllCostCenters(t)
        {
            if ($(t).prop('checked'))
            {
                $('input[name^="cost_centers_check[]"]').each(function ()
                {
                    var check_cost_centers = $(this).closest('tr').find("input[name='cost_centers_check[]']");
                    check_cost_centers.attr("checked", this.checked=true);
                });
            }else
            {
                $('input[name^="cost_centers_check[]"]').each(function ()
                {
                    var check_cost_centers = $(this).closest('tr').find("input[name='cost_centers_check[]']");
                    check_cost_centers.attr("checked", this.checked=false);

                    var in_cost_centers = $(this).closest('tr').find("input[name='cost_centers[]']");
                    in_cost_centers.val('');
                });
            }
            ChangeCostCentersCheck();
        }

        function Condition()
        {
            if($("select[name='condition']").val()==1)
            {
                $('#way_to_pay').show();
            }else
            {
                $('#way_to_pay').hide();
            }
        }

        function changeValidationDate()
        {
            if($("#type").val() != 2)
            {
                $("#text_date_validation, #text_stamped_validity_validation").html('');

                var date1 = $("#date").val().split("/");
                var date = new Date(parseInt(date1[2]),parseInt(date1[1]-1),parseInt(date1[0]));

                var date2 = $("#stamped_validity").val().split("/");
                var stamped_validity = new Date(parseInt(date2[2]),parseInt(date2[1]-1),parseInt(date2[0]));

                if(date != '' && stamped_validity != '')
                {
                    if(date > stamped_validity)
                    {
                        $("#text_date_validation").html('La fecha de Compra no puede ser mayor a la fecha de Vigencia');
                        $("#text_stamped_validity_validation").html('La fecha de Vigencia no puede ser menor a la fecha de Compra');
                    }
                }
            }
        }

        changeValidationStamped();
        function changeValidationStamped()
        {
            if($("#type").val() != 2)
            {
                $("#text_stamped_validation").html('');
                var Max_Length = 8;
                var length     = $("#stamped").val().length;
                if (length > 0)
                {
                    if (length < Max_Length)
                    {
                        $("#text_stamped_validation").html("Faltan "+ (Max_Length-length) + " numeros");
                    }

                    if (length > Max_Length)
                    {
                        $("#text_stamped_validation").html("Ha superado la cantidad de numeros");
                    }
                }
            }
        }

        checkLastPurchasesProducts();
        function checkLastPurchasesProducts()
        {
            var product_id  = $("select[name='purchases_product_id'] option:selected").val();
            var provider_id = $("#purchases_provider_id").val();

            $("#text_last_purchases").html('');
            $("#div_emergency_mobile").hide();
            $("#emergency_mobile").val('');

            if(product_id > 0 && provider_id > 0)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.products-purchases-last') }}',
                    type: "GET",
                    data: { purchases_product_id:product_id, purchases_provider_id:provider_id },
                    success: function(data) {
                        if(data.total_count > 0)
                        {
                            $('#text_last_purchases').html('<b>Ultimas Compras:</b>');
                            $(data.items).each(function(index, element) {
                                $('#text_last_purchases').append('<br><b>Fecha : </b> ' + element.date + ' <b>P : </b>' + element.price + ' <b>C : </b>' + element.quantity);
                            });
                        }
                        else
                        {
                            $('#text_last_purchases').html('');
                        }

                        $(data.products).each(function(index, element) {
                            if(element.requires_mobile==1)
                            {
                                $("#div_emergency_mobile").show();
                                $("#emergency_mobile").val(element.requires_mobile);
                                $("#emergency_mobile_id").select2({
                                    language: 'es'
                                });
                            }

                            $("#type_iva").val(element.type_iva);
                        });
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        changeOtherAccounting(0);
        function changeOtherAccounting(type)
        {
            $('#div_other_accounting_account').hide();
            var social_reason_id = $("#social_reason_id").val();

            if(social_reason_id > 0)
            {
                if(type==1)
                {
                    $('#div_other_accounting_account').show();
                    $("#other_accounting_account_id").select2({
                        language: 'es'
                    });
                }
            }
        }

        changeLastPurchases();
        function changeLastPurchases()
        {
            $('#div_last_purchases, #div_purchases_pendings, #div_orders_purchases, #div_other_accounting_account, #div_details_advances_providers').hide();
            $('#accounting_account_provider, #accounting_account_provider_id').val();

            var purchases_provider_id = $("#purchases_provider_id").val();
            var social_reason_id      = $("#social_reason_id").val();
            var conteo_purchases      = 0;
            var conteo_pendings       = 0;
            var conteo_orders         = 0;
            var conteo_advance        = 0;
            advances_array = [];

            if(purchases_provider_id > 0)
            {
                $.ajax({
                    url: '{{ route('ajax.providers-purchases') }}',
                    type: "GET",
                    data: { purchases_provider_id : purchases_provider_id, social_reason_id : social_reason_id },
                    success: function(data) {
                        $('#tbody_detail_last_purchases, #tbody_detail_purchases_pendings, #tbody_details_advances_providers').html('');

                        $(data.purchases).each(function(index, element) {
                            $('#tbody_detail_last_purchases').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-center"><span class="label label-' + element.type_label + '">' + element.type +'</td>' +
                                '<td class="text-right"><a target="_blank" href="{{ url('purchase') }}/' + element.id + '">' + element.number +'</td>' +
                                '<td class="text-right">' + element.amount +'</td>' +
                            '</tr>');

                            conteo_purchases++;
                        });

                        if(conteo_purchases > 0 )
                        {
                            $('#div_last_purchases').show();
                        }

                        $(data.accounting).each(function(index, element) {
                            $('#accounting_account_provider').val(element.name);
                            $('#accounting_account_provider_id').val(element.id);
                        });

                        $(data.pendings).each(function(index, element) {
                            $('#tbody_detail_purchases_pendings').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-center"><span class="label label-' + element.type_label + '">' + element.type +'</td>' +
                                '<td class="text-right"><a target="_blank" href="{{ url('purchase') }}/' + element.id + '">' + element.number +'</td>' +
                                '<td class="text-right">' + element.amount +'</td>' +
                            '</tr>');

                            conteo_pendings++;
                        });

                        if(conteo_pendings > 0)
                        {
                            $('#div_purchases_pendings').show();
                        }
                        $(data.orders).each(function(index, element) {
                            $('#tbody_detail_orders_purchases').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-center">' + element.number +'</td>' +
                                '<td class="text-center">' + element.condition +'</td>' +
                                '<td class="text-center"><a href="javascript:;" onClick="changeOrdersDetailProducts('+ element.id +');"><i class="fa fa-info-circle"> Productos</i></a></td>' +
                                '<input type="text" name="detail_order_id[]" value=s"' + element.id + '">' +
                                '<input type="text" name="detail_invoice_number[]" value="' + element.invoice_number + '">' +
                                '<input type="text" name="detail_invoice_date[]" value="' + element.invoice_date + '">' +
                                '<input type="text" name="detail_date_payment[]" value="' + element.date_payment + '">' +
                            '</tr>');

                            conteo_orders++;
                        });

                        if(conteo_orders > 0 )
                        {
                            $('#div_orders_purchases').show();
                        }
                        else
                        {
                            $('#div_dont_have_orders_purchases').show();
                        }
                        $('#div_purchase_order_spinner').hide();

                        $(data.advances).each(function(index, element) {
                            $('#tbody_details_advances_providers').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-right"><a target="_blank" href="{{ url('purchases-payments') }}/' + element.id + '">' + element.number +'</td>' +
                                '<td class="text-right">' + element.amount + '</td>' +
                                '<td class="text-center"><input type="checkbox" name="advances_providers_check[]" value= "' + element.id + '" onClick="ChangeAdvancesProvidersCheck();"></td>' +
                            '</tr>');

                            advances_array[index] = {accounting_plan_id: element.accounting_plan_id, accounting_plan_name: element.accounting_plan, provider_amount: element.amount, purchase_id: element.id } ;
                            conteo_advance++;
                        });

                        if(conteo_advance>0)
                        {
                            $("#div_details_advances_providers").show();

                            $(data.advances_total).each(function(index, element) {
                                $("#total_advance").html(element.amount);
                            });
                        }
                    },
                    error: function(data) {
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        function ChangeAdvancesProvidersCheck()
        {
            changeOtherAccounting(1);
            $("#type_payment_1").prop("checked", true);

            $("input[name='detail_other_accounting_enabled[]']").each(function(index, element){
                if(element.value === 'false')
                {
                    $(this).parent().parent().remove();
                }
            });
            $("input[name='advances_providers_check[]']").each(function(index, element){
                if($(this).is(':checked'))
                {
                    var purchase_id = this.value;
                    $.each(advances_array, function (index_advance, element_advance){
                        if(purchase_id == element_advance.purchase_id)
                        {
                            addToTableOtherAccountingAccount(element_advance.accounting_plan_id, element_advance.accounting_plan_name, element_advance.provider_amount.replaceAll('.', ''), false);
                            other_accounting_account_array.push(element_advance.accounting_plan_id)
                        }
                    });
                }
                else
                {
                }
            });
        }

        function changeOrdersDetailProducts(id)
        {
            if (form_ready == true)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.products-purchases-orders') }}',
                    type: "GET",
                    data: { id : id },
                    success: function(data)
                    {
                        if(data.total_count > 0)
                        {
                            console.log(data);
                            $(data.items).each(function(index, element)
                            {
                                addToTable(element.id,
                                element.name,
                                element.price,
                                element.quantity,
                                element.type_iva,
                                element.number_order,
                                element.id_order,
                                element.accounting_plan,
                                element.emergency_mobile,
                                element.emergency_mobile_id,
                                element.emergency_mobile_name,
                                element.stockeable,
                                element.accounting_plan_sales);
                            });

                            $("#number").val(data.invoice_number);
                            $("#date").val(data.invoice_date);
                            $("#expiration").val(data.date_payment);
                            $("#condition").val(data.invoice_condition);
                            $("#currency_id").val(data.invoice_currency_id);
                            $("#branch_id").val(data.invoice_branch_id);
                            $("#stamped").val(data.invoice_stamped);
                            $("#stamped_validity").val(data.invoice_stamp_validity);

                            $('.selectpicker').selectpicker('refresh');
                        }
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
            else
            {
                swal({
                    title: "SISTEMA",
                    text: "El formulario esta cargándose, favor aguarde.",
                    icon: "warning",
                    button: "OK",
                });
            }
        }

        function addProduct()
        {
            var product_name          = $("#purchases_product_id option:selected").data('text');
            var product_id            = $("#purchases_product_id option:selected").val();
            var product_description   = $("#products_description").val();
            var product_amount        = $("input[name='products_amount']").val().replace(/\./g, '').replace(',', '.');
            var product_quantity      = $("input[name='products_quantity']").val().replace(/\./g, '');
            var product_type_iva      = $("#type_iva").val();
            var accounting_plan       = $("#purchases_product_id option:selected").data('accounting-plan');
            var baccounting_plan      = $("#purchases_product_id option:selected").data('baccounting-plan');
            var stockeable            = $("#purchases_product_id option:selected").data('stockeable');
            var emergency_mobile      = $("#emergency_mobile").val();
            var emergency_mobile_id   = $("#emergency_mobile_id").val();
            var emergency_mobile_name = emergency_mobile_id ? $("select[name='emergency_mobile_id'] option:selected").text() : '';
            product_quantity          = (product_quantity > 0 ? product_quantity : 1);

            if(product_amount!='' && product_id!='' && product_quantity!='' && $.isNumeric(product_id))
            {
                var description = product_description ? product_description : product_name;
                addToTable(product_id, description, product_amount, product_quantity, product_type_iva, '', '',accounting_plan, emergency_mobile, emergency_mobile_id, emergency_mobile_name, stockeable, baccounting_plan);

                $("#emergency_mobile_id").val('');
                $("#emergency_mobile").val('');
                $("#products_description, #type_iva").val('');
                $("input[name='products_amount']").val('');
                $("input[name='products_quantity']").val('');
                $("#text_last_purchases").html('');
            }
            else
            {
                alert('Hay campos vacíos');
                return false;
            }
        }

        function addToTable(id, name, amount, quantity, type_iva, number_orders, id_orders, accounting_plan, emergency_mobile, emergency_mobile_id, emergency_mobile_name, stockeable, baccounting_plan)
        {
            counter++;
            var subtotal      = 0;
            var total_excenta = 0;
            var total_iva5    = 0;
            var total_iva10   = 0;

            subtotal = quantity * amount;

            invoice_items_array.push(id);

            if(($("#type").val() == 2) || ($("#type").val() == 3))
            {
                total_excenta = subtotal;
                total_iva5    = 0;
                total_iva10   = 0;
            }
            else
            {
                // Evaluar el IVA para insertar en el Detalle
                if(type_iva==1)
                {
                    total_excenta = subtotal;
                    total_iva5    = 0;
                    total_iva10   = 0;
                }

                if(type_iva==2)
                {
                    total_excenta = 0;
                    total_iva5    = subtotal;
                    total_iva10   = 0;
                }

                if(type_iva==3)
                {
                    total_excenta = 0;
                    total_iva5    = 0;
                    total_iva10   = subtotal;
                }
            }//aca
            $('#tbody_detail').append('<tr>' +
                '<td width="5%">' + counter + '</td>' +
                '<td width="5%" class="text-right">' + id + '<input type="hidden" name="detail_product_id[]" value="' + id + '"></td>' +
                '<td width="20%">' + name + ( emergency_mobile==1 ? '<br><i><b><span class="red">'+ emergency_mobile_name +'</span></b></i>' : '' ) + 
                    '<input type="hidden" name="detail_product_name[]" value="' + name + '">'+
                '</td>' + 
                '<td width="5%" class="text-center">' + number_orders + ' <input type="hidden" name="detail_product_orders_id[]" value="' + id_orders + '"></td>' +
                '<td width="5%" class="text-center">' + $.number(quantity, 0, ',', '.') + '<input type="hidden" name="detail_product_quantity[]" onkeyup="changeReCalculo();" value="' + quantity + '"></td>' +
                '<td width="9%" class="text-right"><input type="text" name="detail_product_amount[]" onkeyup="changeReCalculo();" value="' + amount + '" period-data-mask-decimal></td>' +
                '<td width="9%" class="text-right"><input type="text" name="detail_total_excenta[]"  onkeyup="calculateGrandTotal();" value="' + total_excenta + '" period-data-mask-decimal></td>' +
                '<td width="9%" class="text-right"><input type="text" name="detail_total_iva5[]" onkeyup="calculateGrandTotal();" value="' + total_iva5 + '" period-data-mask-decimal></td>' +
                '<td width="9%" class="text-right"><input type="text" name="detail_total_iva10[]"  onkeyup="calculateGrandTotal();" value="' + total_iva10 + '" period-data-mask-decimal></td>' +
                '<td with="5%" class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '<input type="hidden" name="detail_type_iva[]" value="' + type_iva + '">' +
            '</tr>');
            $("#select_"+counter).select2({
                                    language: 'es'
                                });
            $("#select2_"+counter).select2({
                                    language: 'es'
                                });
            change_load_accounting_account(counter, accounting_plan, baccounting_plan);
            loadPeriodDataMaskDecimal();
            calculateGrandTotal();
            changeReCalculo()
        }

        function removeRowOtherAccountingAccount(t, other_accounting_account_id)
        {
            $(t).parent().parent().remove();
            other_accounting_account_array.splice($.inArray(other_accounting_account_id, other_accounting_account_array), 1 );
        }

        function changeReCalculo()
        {
            $("input[name*='detail_product_quantity']").each(function(e) {
                var quantity                = +$(this).val().replace(',', '');
                var amount                  = $(this).closest('tr').find("input[name='detail_product_amount[]']").val().replace(/\./g, '').replace(',', '.');
                var type_iva                = $(this).closest('tr').find("input[name='detail_type_iva[]']").val();
                // var td_detail_total_excenta = $(this).closest('tr').find("#td_detail_total_excenta");
                // var td_detail_total_iva5    = $(this).closest('tr').find("#td_detail_total_iva5");
                // var td_detail_total_iva10   = $(this).closest('tr').find("#td_detail_total_iva10");

                var in_detail_total_excenta = $(this).closest('tr').find("input[name='detail_total_excenta[]']");
                var in_detail_total_iva5    = $(this).closest('tr').find("input[name='detail_total_iva5[]']");
                var in_detail_total_iva10   = $(this).closest('tr').find("input[name='detail_total_iva10[]']");

                var subtotal                = 0;
                var total_excenta           = 0;
                var total_iva5              = 0;
                var total_iva10             = 0;

                subtotal = quantity * amount;

                // Evaluar el IVA para insertar en el Detalle
                if(type_iva==1)
                {
                    total_excenta = subtotal;
                    total_iva5    = 0;
                    total_iva10   = 0;
                }

                if(type_iva==2)
                {
                    total_excenta = 0;
                    total_iva5    = subtotal;
                    total_iva10   = 0;
                }

                if(type_iva==3)
                {
                    total_excenta = 0;
                    total_iva5    = 0;
                    total_iva10   = subtotal;
                }

                in_detail_total_excenta.val($.number(total_excenta, 2,',', '.'));
                in_detail_total_iva5.val($.number(total_iva5, 2,',', '.'));
                in_detail_total_iva10.val($.number(total_iva10, 2,',', '.'));

                // td_detail_total_excenta.html($.number(total_excenta, 2, ',', '.'));
                // td_detail_total_iva5.html($.number(total_iva5, 2, ',', '.'));
                // td_detail_total_iva10.html($.number(total_iva10, 2, ',', '.'));
            });
            calculateGrandTotal();
        }

        function removeRow(t, product_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(product_id, invoice_items_array), 1 );
            calculateGrandTotal();
        }

        calculateGrandTotal();
        function calculateGrandTotal()
        {
            var grandTotal_Excenta = 0;
            var grandTotal_Iva5    = 0;
            var grandTotal_Iva10   = 0;
            var grandTotal_Factura = 0;
            var iva_5              = 0;
            var iva_10             = 0;
            var total_iva          = 0;
            var aux = 0;
            var total_qty          = 0;
            $('input[name^="detail_product_quantity[]"]').each(function ()
            {
                total_qty += +$(this).val().replace(/\./g, '').replace(',', '.');
            });

            $('input[name^="detail_total_excenta[]"]').each(function ()
            {
                grandTotal_Excenta += +$(this).val().replace(/\./g, '').replace(',', '.');
            });
            $("#total_excenta").html('<b>' + $.number(grandTotal_Excenta, 2, ',', '.') + '</b>');

            $('input[name^="detail_total_iva5[]"]').each(function ()
            {
                grandTotal_Iva5 += +$(this).val().replace(/\./g, '').replace(',', '.');
            });
            $("#total_iva5").html('<b>' + $.number(grandTotal_Iva5, 2, ',', '.') + '</b>');

            $('input[name^="detail_total_iva10[]"]').each(function ()
            {
                grandTotal_Iva10 += +$(this).val().replace(/\./g, '').replace(',', '.');
            });
            $("#total_iva10").html('<b>' + $.number(grandTotal_Iva10, 2, ',', '.') + '</b>');
            grandTotal_Factura = grandTotal_Excenta + grandTotal_Iva5 + grandTotal_Iva10;
            iva_5              = grandTotal_Iva5 / 21;
            iva_10             = grandTotal_Iva10 / 11;
            total_iva          = iva_5 + iva_10;

            $("#total_purchases").html($.number(grandTotal_Factura, 2, ',', '.'));
            $("#totales_iva").html('IVA 5 % ' + $.number(iva_5, 2, ',', '') + ' -  IVA 10 % ' + $.number(iva_10, 2, ',', '.'));
            $("#total_excenta_final").val($.number(grandTotal_Excenta, 2, ',', ''));
            $("#total_iva5_final").val($.number(grandTotal_Iva5, 2, ',', ''));
            $("#total_iva10_final").val($.number(grandTotal_Iva10, 2, ',', ''));
            $("#total_product").val($.number(grandTotal_Factura, 2, ',', ''));
            $("#amount_iva5").val($.number(iva_5, 2, ',', ''));
            $("#amount_iva10").val($.number(iva_10, 2, ',', ''));

            if(grandTotal_Factura > 0)
            {
                Condition();
                $("[select2]").select2();
                $('#amount_treasury').val(grandTotal_Factura);
            }
            else
            {
                if (total_qty == 0)
                {
                    $('#way_to_pay').hide();
                }
            }
        }
    </script>
@endsection

