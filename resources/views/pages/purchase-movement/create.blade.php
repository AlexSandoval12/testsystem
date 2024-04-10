@extends('layouts.AdminLTE.index')
@section('title', 'Recepcion de Compras')
@section('content')
<div class="row">
    {{ Form::open(['id' => 'form']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agregar Recepción de Orden de Compra</h5>
                        </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label>Numero OC</label>
                                    <input class="form-control" type="text" name="number_oc" id="number_oc" placeholder="Numero OC" autofocus>
                                </div>
                                <div class="form-group col-md-2">
                                    <br>
                                    <button type="button" class="btn btn-primary" name="button_search" id="button_search"><i class="fa fa-search"></i> BUSCAR OC</button>
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
            <div class="ibox-footer" id="div_footer">
                <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                <a href="{{ url('purchases-movements') }}" class="btn btn-sm btn-danger">Cancelar</a>
            </div>
            <div id="modal_expiration_product">

            </div>
        {{ Form::close() }}
</div>
@endsection
@section('layout_css')
<style>
    #div_provider_data, #div_invoice_detail, #div_invoice_header, #div_image, #div_note_credits{
            position: relative;
            margin: auto;
            width: 100%;
            border: 3px solid #C8C4C4;
            padding: 10px;
            border-radius: 5px;
        }
</style>
@endsection

@section('layout_js')
    <script>
        var invoice_items_array = [];
        var array_deposits = {!! json_encode($array_deposits) !!}
        console.log(array_deposits);
        $(document).ready(function ()
        {
            $("select[name='purchases_product_id']").on('change', function(){
                checkLastPurchasesProducts();
            });

            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('purchase-movement-store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('purchase-movement') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#button_search").click(function() {
                Search_OC();
            });

            $("#btn_expiration_date").click(function() {
                AddExpirationDetail();
            });

            $('#number_oc').keypress(function(e){
                if (e.keyCode == 13)
                {
                    $("#button_search").click();
                    e.preventDefault();
                    return false;
                }
            });

            $('#btn_payment_date').click(function(){
                $('<div class="row">'+
                    '<div class="form-group col-md-6 text-center">'+
                        '<input class="form-control text-center date" type="text" name="expiration[]" value="" date-mask autocomplete="off">'+
                    '</div>'+
                    '<div class="form-group col-md-6">'+
                        '<div class="input-group">'+
                            '<input class="form-control" type="text" name="payment_amount[]" value="" period-data-mask>'+
                            '<span class="input-group-btn">'+
                                '<button class="btn btn-warning" type="button"  onclick="$(this).parent().parent().parent().parent().remove()"><i class="fa fa-times"></i></button>'+
                            '</span>'+
                        '</div>'+
                    '</div>'+
                '</div>').insertBefore('#row_btn_payment_date');
                loadDate();

            });

            loadDate();
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

        checkLastPurchasesProducts();
        function checkLastPurchasesProducts()
        {
            var product_id  = $("select[name='purchases_product_id'] option:selected").val();
            var provider_id = $("#purchases_provider_id").val();

            $("#text_last_purchases").html('');

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
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        function addToTable(element)
        {
            var quantity   = element.val();
            var product_id = element.closest('tr').find("input[name='detail_product_id[]']").val();
            var detail_id = element.closest('tr').find("input[name='detail_id[]']").val();
            if(quantity > 0)
            {
                var product_name = element.closest('tr').find("input[name='detail_product_name[]']").val();
                var description = element.closest('tr').find("input[name='detail_product_name[]']").val();
                var mobil_id = element.closest('tr').find("input[name='detail_emergency_mobil_id[]']").val();
                var mobil = element.closest('tr').find("input[name='detail_emergency_mobil[]']").val();
                var amount = element.closest('tr').find("input[name='detail_product_amount[]']").val();
                var type_tax = parseInt(element.closest('tr').find("input[name='detail_type_tax[]']").val());
                var amount_5      = 0;
                var amount_10     = 0;
                var amount_exenta = 0;
                var social_reason_id = $("#social_reason_id").val();
                var provider_id = $("#purchases_provider_id").val();


                $.ajax({
                    url: '{{ route('ajax.purchases-movements') }}',
                    type: "GET",
                    data: { product_name : product_name, amount : amount, social_reason_id : social_reason_id, provider_id : provider_id },
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            if(element)
                            {
                                $('#row_btn_pending_number').show();
                                $('#row_btn_pending_number').append('<button type="button" class="btn btn-block btn-outline btn-primary" id="btn_pending_number">El Proveedor cuenta con una factura<br>nro° '+element.number+', CLICK AQUÍ</button>');
                                $('#btn_pending_number').click(function(){
                                    $('#invoice_number').val(element.number);
                                    $('#purchase_id').val(element.id);
                                    $('#date').val(element.date);
                                    $('#stamped').val(element.stamped);
                                    $('#stamped_validity').val(element.stamped_validity);
                                });

                            }
                        });

                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
                if(description == '')
                {
                    description = product_name;
                }

                if(quantity != '' && product_id != '' && description != '' && amount != '' && product_id && detail_id && typeof product_id != 'undefined' && typeof detail_id != 'undefined')
                {
                    let subtotal = Math.round((parseFloat(amount)) * quantity);

                    switch (type_tax) {
                        case 1: //EXCENTA
                            amount_exenta = subtotal;
                            break;
                        case 2://GRABADA 5
                            amount_5 = subtotal;
                            break;
                        default://GRABADA 10
                            amount_10 = subtotal;
                            break;
                    }
                    if($.inArray(detail_id, invoice_items_array) != '-1')
                    {
                        removeRow(detail_id);
                    }

                    $('#tbody_product').append('<tr>'+
                                                '<input type="hidden" name="order_detail_id[]" value="'+detail_id+'">'+
                                                '<input type="hidden" name="detail_invoice_product_ids[]" value="'+product_id+'">'+
                                                '<input type="hidden" name="detail_price[]" value="' + amount + '">'+
                                                '<input type="hidden" name="detail_quantities[]" value="'+quantity+'">'+
                                                '<input type="hidden" name="detail_total_amount[]" value="'+subtotal+'">'+
                                            '<td width="5%"  class="text-center">'+ quantity+'</td>'+
                                            '<td width="17%" class="text-center">'+ product_name+'</td>'+
                                            '<td width="20%" class="text-center">'+
                                                '<input type="text" name="detail_descriptions[]" value="'+description+'">'+
                                            '</td>'+
                                            '<td width="8%"  class="text-center">'+mobil+'</td>'+
                                            '<td width="10%" class="text-right">'+$.number(amount, 2, ',', '.')+'</td>' +
                                            '<td width="10%" class="text-right">'+
                                                '<input style="width:107px;" type="text" class="text-right" period-data-mask-decimal name="detail_amounts[]" onkeyup="calculateIva();" value="'+$.number(amount_10, 2, ',', '.')+'">'+
                                            '</td>' +
                                            '<td width="10%" class="text-right">'+
                                                '<input style="width:107px;" type="text" class="text-right" period-data-mask-decimal name="detail_amounts_5[]" onkeyup="calculateIva();" value="'+$.number(amount_5, 2, ',', '.')+'">'+
                                            '</td>' +
                                            '<td width="10%" class="text-right">'+
                                                '<input style="width:107px;" type="text" class="text-right" period-data-mask-decimal name="detail_amounts_exenta[]" onkeyup="calculateIva();" value="'+$.number(amount_exenta, 2, ',', '.')+'">'+
                                            '</td>' +
                                            '<td width="10%" class="text-right subtotal"></td>'+
                                        '</tr>');
                    calculateIva();
                    clearOptions();
                    loadPeriodDataMaskDecimal();
                    invoice_items_array.push(detail_id);
                }
                else
                {
                    alert('Hay campos vacios favor completar');
                }
            }
            else
            {
                removeRow(detail_id);
            }
        }

        function calculateIva()
        {
            var grandTotal_10 = 0;
            var grandTotal_5 = 0;
            var grandTotal_Excenta = 0;
            var detail_amounts_subtotal = 0;
            var total_invoice = 0;

            $('input[name^="detail_amounts[]"]').each(function ()
            {
                grandTotal_10      += +parseFloat($(this).val().replace(/\./g, '').replace(',', '.'));
                grandTotal_5       += +parseFloat($(this).closest('tr').find('input[name^="detail_amounts_5[]"]').val().replace(/\./g, '').replace(',', '.'));
                grandTotal_Excenta += +parseFloat($(this).closest('tr').find('input[name^="detail_amounts_exenta[]"]').val().replace(/\./g, '').replace(',', '.'));

                detail_amounts_subtotal = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) +
                                          parseFloat($(this).closest('tr').find('input[name^="detail_amounts_5[]"]').val().replace(/\./g, '').replace(',', '.')) +
                                          parseFloat($(this).closest('tr').find('input[name^="detail_amounts_exenta[]"]').val().replace(/\./g, '').replace(',', '.'));

                $(this).closest('tr').find('input[name="detail_total_amount[]"]').val(detail_amounts_subtotal);
                $(this).closest('tr').find('td.subtotal').html($.number(detail_amounts_subtotal, 2, ',', '.'));

            });

            $('input[name^="detail_total_amount[]"]').each(function ()
            {
                total_invoice += +$(this).val().replace(/\./g, '').replace(',', '.');
            });

            total_iva5 = grandTotal_5 / 21;
            total_iva10 = grandTotal_10 / 11;
            total_iva = total_iva10 + total_iva5;

            $('#label_total_iva').html('Gs. '+$.number(total_iva, 0, ',', '.'));
            $("#totales_iva5").html('Gs. '+$.number(total_iva5, 2, ',', '.'));
            $("#totales_iva10").html('Gs. '+$.number(total_iva10, 2, ',', '.'));
            $("#label_total_invoice").html('Gs. '+$.number(total_invoice, 0, ',', '.'));
            $('#total_iva').val(total_iva);
            $('#total_iva_5').val($.number(total_iva5, 2, ',', '.'));
            $('#total_iva_10').val($.number(total_iva10, 2, ',', '.'));
            $('#total_invoice').val($.number(total_invoice, 2, ',', '.'));
        }

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

        function clearOptions()
        {
            $('#description').val('');
            $('#amount').val(0);
            $('#amount_5').val(0);
            $('#amount_exenta').val(0);
            $('#discount_amount').val(0);
        }

        function removeRow(detail_id)
        {
            invoice_items_array = jQuery.grep(invoice_items_array, function(value) {
                return value != detail_id;
            });

            $('input[name^="order_detail_id[]"]').each(function ()
            {
                if($(this).val() == detail_id)
                {
                    $(this).parent().remove();
                }
            });

            calculateIva();
        }

        changeStatus();
        function changeStatus()
        {
            $("#div_details, #div_deposito, #div_footer").hide();

            $("#number_oc").prop("readonly", false);
            $("#button_search").show();
        }
        // Search_OC();
        function Search_OC()
        {
            var number_oc        = $("#number_oc").val();
            // var number_oc        = 13165;
            // var social_reason_id = 1;
            var conteo           = 0;
            console.log(number_oc)
            $('#tbody_detail').html('');

            if(number_oc != '')
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.purchases-products-movements') }}',
                    type: "GET",
                    data: { number_oc : number_oc},
                    success: function(data) {
                        $.each(data.items, function(index, element) {
                            $('#tbody_detail').append('<tr>' +
                                '<td class="text-right">' + element.product_id + '</td>' +
                                '<td>' + element.product_name + '</td>' +
                                '<td>' + element.presentation + '</td>' +
                                // '<td><button type="button" '+(element.expiration_need ? "" : "disabled")+'  data-product_id="'+element.product_id+'" data-product_name="'+element.product_name+'" data-quantity="'+element.quantity+'" data-target="#detail_product_expiration" class="btn btn-outline btn-primary" id="btn-detail-product-expiration" onClick="load_Modal_Expiration(this);"><i class="fas fa-plus"></i></button></td>'+
                                '<td class="text-center">' + $.number(element.quantity, 0, ',', '.') + '</td>' +
                                '<td><input style="width:50px;" type="text" name="detail_product_quantity[]" onchange="addToTable($(this))" value="" autocomplete="off"></td>' +
                                '<td class="text-center">' + $.number(element.received, 0, ',', '.') + '</td>' +
                                '<td class="text-center">' + $.number(element.residue, 0, ',', '.')  + '</td>' +
                                '<td class="text-right">'  + $.number(element.amount, 0, ',', '.')   + '</td>' +
                                '<input type="hidden" name="detail_product_amount[]" value="' + parseFloat(element.amount) + '">' +
                                '<td class="text-right">'  + $.number(element.subtotal, 0, ',', '.') + '</td>' +
                                '<input type="hidden" name="detail_id[]" value="' + element.id + '">' +
                                '<input type="hidden" name="detail_product_id[]" value="' + element.product_id + '">' +
                                '<input type="hidden" name="quantity_product[]" value="' + element.quantity + '">' +
                                '<input type="hidden" name="detail_pending_reception[]" value="' + element.pending_reception + '">' +
                                '<input type="hidden" name="detail_product_name[]" value="' + element.product_name + '">' +
                                '<input type="hidden" name="detail_emergency_mobil_id[]" value="' + element.mobil_id + '">' +
                                '<input type="hidden" name="detail_emergency_mobil[]" value="' + element.mobil_name + '">' +
                                '<input type="hidden" name="detail_type_tax[]" value="' + element.type_tax + '">' +
                                '<input type="hidden" name="detail_presentation[]" value="' + element.presentation_id + '">' +
                            '</tr>');
                                $('#phone_label').val(data.phone);
                                $('#provider_name').val(data.provider_fullname);
                                $('#purchases_provider_id').val(data.provider_id);
                                $('#ruc').val(data.ruc);
                                $('#social_reason').val(data.social_reason);
                                $('#phone_label').val(data.phone);
                                $('#address_label').val(data.address);
                            conteo++;
                        });

                        if(conteo>0)
                        {
                            $('#branch_id').val(data.branch_id).trigger('change');
                            $("#div_details, #div_deposito, #div_footer").show();

                            $("#number_oc").prop("readonly", true);
                            $("#button_search").hide();
                            $("[select2]").select2({
                                language: 'es'
                            });
                            load_deposit();
                        }else
                        {
                            swal({
                                title: "SISTEMA",
                                text: "No existen productos a recibir!!",
                                icon: "info",
                                button: "OK",
                            });
                            return false;
                        }
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });

                loadProductsSelect();
            }
            else
            {
                swal({
                    title: "SISTEMA",
                    text: "Hay campos vacíos",
                    icon: "warning",
                    button: "OK",
                });
                return false;
            }
        }

        function load_deposit()
        {
            $('#deposits_id').html('');
            $.each(array_deposits, function(index, element){
                $.each(element, function(index2, element2){
                    $('#deposits_id').append('<option value="'+index2+'">'+element2+'</option>');
                });
            });
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

        function loadProductsSelect()
        {
            $("#div_emergency_mobile").hide();
            $("#emergency_mobile").val('');

            $("#purchases_product_id").select2({
                language: 'es',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('ajax/purchases-products') }}?purchase_order_number="+$("#number_oc").val(),
                    dataType: 'json',
                    method: 'GET',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term, social_reason_id: $('#social_reason_id').val() };
                    },
                    success: function(data) {
                        $(data.items).each(function(index, element) {

                            if(element.requires_mobile==1)
                            {
                                $("#div_emergency_mobile").show();
                                $("#emergency_mobile").val(element.requires_mobile);
                            }
                        });
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
                $('#purchases_product_id option:selected').attr('data-accounting-plan', data_item.accounting_plan_id);
                $('#purchases_product_id option:selected').attr('data-requires-mobile', data_item.requires_mobile);
                $('#purchases_product_id option:selected').attr('data-text', data_item.name);
            });
        }

        function loadStamped()
        {
            var invoice_number = $('#invoice_number').val();
            var provider_id = $('#purchases_provider_id').val();
            $('#stamped, #stamped_validity').val('');
            if(invoice_number && provider_id)
            {
                $.ajax({
                    url: '{{ route('provider-stamped.search') }}',
                    type: "GET",
                    data: { purchase_number: invoice_number, provider_id:provider_id},
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

        function load_Modal_Expiration(button)
        {
            let rows_qty        = 0;
            let $button         = $(button);
            let product_id      = $button.attr('data-product_id');
            let product_name    = $button.attr('data-product_name');
            let quantity        = $button.attr('data-quantity');

            console.log(product_id, product_name, quantity);
            // Agregar evento click al botón de agregar fecha de vencimiento
            if (document.getElementById('detail_product_expiration_'+product_id) == null)
            {
                $('#modal_expiration_product').append(
                    '<div class="modal fade" id="detail_product_expiration_'+product_id+'" tabindex="-1" role="dialog" aria-labelledby="detail_product_expiration_label" aria-hidden="true">'+
                        '<div class="modal-dialog modal-lg" role="document">'+
                            '<div class="modal-content">'+
                                '<div class="row">'+
                                    '<div class="col-md-12">'+
                                        '<table class="table table-hover">'+
                                            '<thead>'+
                                                '<tr>'+
                                                    '<th>Cod</th>'+
                                                    '<th>Producto</th>'+
                                                    '<th>Cantidad</th>'+
                                                    '<th>Vencimiento</th>'+
                                                    '<th></th>'+
                                                '</tr>'+
                                            '</thead>'+
                                            '<tbody id="tbody_expiration_detail_'+product_id+'">'+
                                                '<tr>'+
                                                    '<td><input type="hidden" value="'+product_id+'" id="exp_product_id" name="exp_product_id[]" ></input>'+product_id+'</td>'+
                                                    '<td><input type="hidden" value="'+product_name+'" id="exp_product_name" name="exp_product_name_'+product_id+'[]" ></input>'+product_name+'</td>'+
                                                    '<td><input type="text" id="exp_product_quantity" name="exp_product_quantity_'+product_id+'[]" value="'+quantity+'" ></input></td>'+
                                                    '<td class="text-center"><input type="text" date-mask class="date" name="exp_date_'+product_id+'[]" value=""></input></td>' +
                                                    '<td><button type="button" class="btn btn-block btn-outline btn-primary" onclick="CloneExpirationR(this);"  id="btn_expiration_date"><i class="fa fa-plus"></i></button></td>'+
                                                '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                    '</div>'
                );

                $('#detail_product_expiration_'+product_id).modal('show');
            }
            else
            {
                $('#detail_product_expiration_'+product_id).modal('show');
            }
            // $('#tbody_expiration_detail').html('');
            loadDate();

        }

        function RemoveExpirationR(button)
        {
            // Verificar si es la primera fila
            if ($(button).closest("tr").is(":first-child")) {
                return;
            }
            $(button).closest("tr").remove();
        }

        function CloneExpirationR(button)
        {
            let exp_product_quantity = $('#exp_product_quantity').val();
            let exp_product_quantity_int = parseInt(exp_product_quantity);
            if(exp_product_quantity_int > exp_product_quantity) {
                alert("La cantidad ingresada no puede ser mayor a la cantidad inicial.");
                return;
            }
            let button_product_id = $(button).closest('tr').find("input[name='exp_product_id[]']").val();
            let $tr = $(button).closest('tr');
            let $clone = $tr.clone();
            $clone.find('input[type="date"]').val('');
            $clone.find('input[type="text"]').val('');
            if($tr.is(":first-child"))
            {
                $clone.append('<td><button class="btn btn-warning" type="button"  onclick="RemoveExpirationR(this);"><i class="fa fa-times"></i></button></td>');
            }
            $('#tbody_expiration_detail_'+button_product_id).append($clone);
            loadDate();
        }
    </script>
@endsection

