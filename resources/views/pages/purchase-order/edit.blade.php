@extends('layouts.AdminLTE.index')
@section('title', 'Editar Orden de Compra')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => ['purchase-order.update', $purchase_order->id], 'method' => 'PUT']) }}
            <div class="ibox-content">
                @include('partials.messages')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Editar Orden de Compra</h5>
                            </div>
                            <div class="ibox-content pb-0">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Solicitado por</label>
                                        <input class="form-control" type="text" name="requested_by" value="{{ old('requested_by',$purchase_order->user->name) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Condición</label>
                                        {{ Form::select('condition', config('constants.invoice_condition'), old('condition',$purchase_order->condition), ['class' => 'form-control', 'select2']) }}
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Sucursal</label>
                                        {{ Form::select('branch_id', $branches, old('branch_id',$purchase_order->branch_id), ['class' => 'form-control', 'select2', 'id' => 'branch_id']) }}
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Fecha</label>
                                        <input class="form-control" type="text" name="date" value="{{ old('date', $purchase_order->date) }}"  readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Cambio</label>
                                        <input class="form-control" type="text" name="change" id="change" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Datos del Proveedor @if($provider_suggesteds) <small>Proveedores Sugeridos: {{implode(', ', $provider_suggesteds)}}</small> @endif</h5>
                            </div>
                        <div class="ibox-content pb-0">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Proveedor</label>
                                    <input type="text" value="{{ $purchase_order->provider->name }}"   class="form-control" readonly>
                                    <input type="hidden" name="purchases_provider_id" id="purchases_provider_id" value="{{ old('purchases_provider_id',$purchase_order->provider_id) }}"  class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>RUC</label>
                                    <input type="text" name="ruc" value="{{ old('ruc',$purchase_order->ruc) }}" id="ruc" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Telefono</label>
                                    <input type="text" name="phone" value="{{ old('phone',$purchase_order->phone) }}" id="phone" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Razón Social</label>
                                    <input type="text" name="razon_social" value="{{ old('razon_social',$purchase_order->razon_social) }}" id="razon_social" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Dirección</label>
                                    <input type="text" name="address" value="{{ old('address',$purchase_order->address) }}" id="address" class="form-control" readonly>
                                </div>
                                <input type="hidden" name="type_iva" id="type_iva">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="ibox float-e-margins">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1">Ultimas Compras</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-2">Pendientes Pago</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
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
                                <div id="tab-2" class="tab-pane">
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
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3>Items a Comprar</h3>
                </div>
                <div class="ibox-content pb-0">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Producto</label>
                            {{ Form::select('raw_materials_id', $raw_materials, old('raw_materials_id'), ['id' => 'raw_materials_id', 'placeholder' => 'Seleccione Materia Prima', 'class' => 'form-control', 'select2']) }} 
                            <span class="red" id="text_last_purchases"></span>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Presentación</label>
                            {{ Form::select('product_presentations_id', config('constants.presentation'), old('product_presentations_id'), ['id' => 'product_presentations_id', 'placeholder' => 'Presentación', 'class' => 'form-control', 'select2']) }}
                            <span class="red" id="text_last_purchases"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Descripción</label>
                            <input class="form-control" type="text" name="products_description" value="{{ old('products_description') }}" placeholder="Concepto Diferenciado">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Cantidad</label>
                            <input class="form-control" type="text" name="products_quantity" value="{{ old('products_quantity') }}" placeholder="Cantidad">
                        </div>
                        <div class="form-group col-md-2">
                            <label>SubTotal</label>
                            <input class="form-control" type="text" name="products_amount" value="{{ old('products_amount') }}" placeholder="Monto" period-data-mask-decimal>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Agregar</label>
                            <button type="button" class="btn btn-success" id="button_add_product"><i class="fa fa-plus"></i></button>
                        </div>
                        <input type="hidden" name="emergency_mobile" id="emergency_mobile">
                    </div>
                </div>
                <div class="ibox-content table-responsive no-padding" id="detail_product">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-right">Cód</th>
                                <th>Producto</th>
                                <th>Presentación</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right">SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail"></tbody>
                        <tfoot class="bold">
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-right">Sub-Totales</td>
                                <td id="total_order" class="text-right"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <input type="hidden" name="total_product" id="total_product" value="0">
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <div class="form-group col-md-7">
                        <label>Observación</label>
                        <textarea class="form-control" name="observation" rows="18">{{ old('observation') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-7">
                        <label>Imagen de la OC</label>
                        <input type="file" class="form-control" name="signature_image">
                    </div>
                </div>
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
@section('layout_js')

    <script>
        var counter = 0;
        var invoice_items_array = [];

        $(document).ready(function ()
        {
            changeLastPurchases();
            @foreach($purchase_order->purchase_order_details as $order)

            addToTable(
                        {{$order->material_id}},
                        '{{$order->raw_material->description}}' ,
                        {{$order->amount}}, 
                        {{$order->quantity}},
                        {{$order->presentation}}, 
                        '{{config('constants.presentation.' . $order->presentation)}}', 
                        );

            @endforeach

            $("#button_add_product").click(function() {
                addProduct();
            });

            $("select[name='raw_materials_id']").on('change', function(){
                checkLastPurchasesProducts();
            });

            $("#social_reason_id").on('change', function(){
                ChangeListAccountingAccounts();
            });
        });


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

        checkLastPurchasesProducts();
        function checkLastPurchasesProducts()
        {
            var product_id  = $("select[name='raw_materials_id']").val();
            var provider_id = $("#purchases_provider_id").val();

            $("#div_emergency_mobile").hide();
            $("#text_last_purchases").html('');

            if(product_id > 0 && provider_id > 0)
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('ajax.products-purchases-last') }}',
                    type: "GET",
                    data: { raw_materials_id:product_id, purchases_provider_id:provider_id },
                    success: function(data) {
                        if(data.total_count > 0)
                        {
                            $('#text_last_purchases').html('<b>Ultimas Compras:</b>');
                            $(data.items).each(function(index, element) {
                                $('#text_last_purchases').append('<br><b>Fecha: </b> ' + element.date + ' <b>Precio: </b>' + element.price + ' <b>Cantidad: </b>' + element.quantity);
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
                        });
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        ChangeListAccountingAccounts();
        function ChangeListAccountingAccounts()
        {
            var social_reason_id = $("#social_reason_id").val();
            var counter_cost     = 1;
            $("#tbody_detail_cost_centers").html('');
            $("#div_detail_cost_centers").hide();
        }


        changeLastPurchases();
        function changeLastPurchases()
        {
            $('#div_last_purchases, #div_purchases_pendings').hide();

            var purchases_provider_id = $("#purchases_provider_id").val();
            var conteo_purchases      = 0;
            var conteo_pendings       = 0;

            if(purchases_provider_id > 0)
            {
                $.ajax({
                    url: '{{ route('ajax.providers-purchases') }}',
                    type: "GET",
                    data: { purchases_provider_id : purchases_provider_id },
                    success: function(data) {
                        $('#tbody_detail_last_purchases, #tbody_detail_purchases_pendings').html('');

                        $(data.purchases).each(function(index, element) {
                            $('#tbody_detail_last_purchases').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-center"><span class="label label-' + element.type_label + '">' + element.type +'</td>' +
                                '<td class="text-right"><a target="_blank" href="{{ url('purchases') }}/' + element.id + '">' + element.number +'</td>' +
                                '<td class="text-right">' + element.amount +'</td>' +
                            '</tr>');

                            conteo_purchases++;
                        });

                        if(conteo_purchases > 0 )
                        {
                            $('#div_last_purchases').show();
                        }

                        $(data.pendings).each(function(index, element) {
                            $('#tbody_detail_purchases_pendings').append('<tr>' +
                                '<td class="text-center">' + element.date + '</td>' +
                                '<td class="text-center"><span class="label label-' + element.type_label + '">' + element.type +'</td>' +
                                '<td class="text-right"><a target="_blank" href="{{ url('purchases') }}/' + element.id + '">' + element.number +'</td>' +
                                '<td class="text-right">' + element.amount +'</td>' +
                            '</tr>');

                            conteo_pendings++;
                        });

                        if(conteo_pendings > 0 )
                        {
                            $('#div_purchases_pendings').show();
                        }
                    },
                    error: function(data) {
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        function addProduct()
        {
            var product_name              = $("select[name='raw_materials_id'] option:selected").text();
            var product_id                = $("select[name='raw_materials_id']").val();
            var product_description       = $("#products_description").val();
            var product_presentation_id   = $("#product_presentations_id").val();
            var product_presentation_name = $("select[name='product_presentations_id'] option:selected").text();
            var product_amount            = $("input[name='products_amount']").val().replace(/\./g, '').replace(/\,/g, '.');
            var product_quantity          = $("input[name='products_quantity']").val().replace(/\./g, '');
            var product_type_iva          = $("#type_iva").val();
            var emergency_mobile          = $("#emergency_mobile").val();
            var emergency_mobile_id       = $("#emergency_mobile_id").val();
            var emergency_mobile_name     = emergency_mobile_id ? $("select[name='emergency_mobile_id'] option:selected").text() : '';
            product_quantity              = (product_quantity > 0 ? product_quantity : 1);

            if(product_amount!='' && product_id!='' && product_quantity!='' && product_presentation_id!='')
            {
                if($.inArray(product_id, invoice_items_array) != '-1')
                {
                    if(confirm('Ya existe el producto, desea continuar?'))
                    {
                        var description = product_description ? product_description : product_name;
                        // var amount      = ((product_amount*1000)/1000) / product_quantity;
                        var amount      = parseFloat((product_amount / product_quantity).toFixed(2));

                        addToTable(product_id, description, amount, product_quantity, product_presentation_id, product_presentation_name, emergency_mobile, emergency_mobile_id, emergency_mobile_name, '');
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    var description = product_description ? product_description : product_name;
                    // var amount      = ((parseFloat(product_amount)*1000)/1000) / product_quantity;
                    var amount      = parseFloat((product_amount / product_quantity).toFixed(2));

                    addToTable(product_id, description, amount, product_quantity, product_presentation_id, product_presentation_name, emergency_mobile, emergency_mobile_id, emergency_mobile_name, '');
                }

                $('#raw_materials_id, #emergency_mobile_id, #product_presentations_id').val(null).trigger('change');
                $("#emergency_mobile").val('');
                $("#products_description").val('');
                $("input[name='products_amount']").val('');
                $("input[name='products_quantity']").val('');
                $("#text_last_purchases").html('');

                // $('#raw_materials_id').focus();
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

        function addToTable(id, name, amount, quantity, presentation_id, presentation_name)
        {
            counter++;

            var subtotal = quantity * amount;
            invoice_items_array.push(id);

            $('#tbody_detail').append('<tr>' +
                    '<td>' + counter + '</td>' +
                    '<td class="text-right">' + id +' <input type="hidden" name="detail_product_id[]" value="' + id + '"></td>' +
                    '<td>' + name +  ' <input type="hidden" name="detail_product_name[]" value="' + name + '"></td>' +
                    '<td>' + presentation_name + ' <input type="hidden" name="detail_presentation_id[]" value="' + presentation_id + '"></td>' +
                    '<td class="text-right"><input type="text" class="form-control" name="detail_product_quantity[]" onkeyup="changeReCalculo();" value="' + $.number(quantity, 0, ',', '.') + '"></td>' +
                    '<td class="text-right"><input type="text" class="form-control" name="detail_product_amount[]" onkeyup="changeReCalculo();" value="' + $.number(amount, 2, ',', '.') + '" period-data-mask-decimal></td>' +
                    '<td class="text-right" id="td_detail_subtotal">' + $.number(subtotal, 2, ',', '.') +'</td>' +
                    '<input type="hidden" name="detail_product_subtotal[]" value="' + subtotal + '">'+
                    '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>');

            loadPeriodDataMaskDecimal();
            calculateGrandTotal();
        }

        function removeRow(t, product_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(product_id, invoice_items_array), 1 );
            calculateGrandTotal();
            counter--;
        }

        calculateGrandTotal();
        function calculateGrandTotal()
        {
            var grandTotal    = 0;
            var grandQuantity = 0;

            $('input[name^="detail_product_subtotal[]"]').each(function ()
            {
                grandTotal = grandTotal + parseFloat($(this).val().replace(',', '.'));
            });

            $('input[name^="detail_product_quantity[]"]').each(function ()
            {
                grandQuantity = grandQuantity + parseFloat($(this).val().replace(/\./g, '').replace(',', '.'));
            });

            $("#total_order").html('<b>' + $.number(grandTotal, 2, ',', '.') + '</b>');
            $("#total_product").val($.number(grandTotal, 2, ',', ''));

            if(grandQuantity > 0)
            {
                $('#detail_product').show();
                $("[select2]").select2();
            }
            else
            {
                $('#detail_product').hide();
            }
        }

        function changeReCalculo()
        {
            $("input[name*='detail_product_quantity']").each(function(e) {
                var quantity           = +$(this).val().replace(',', '');
                var amount             = $(this).closest('tr').find("input[name='detail_product_amount[]']").val().replace(/\./g, '').replace(',', '.');
                var td_detail_subtotal = $(this).closest('tr').find("#td_detail_subtotal");
                var in_detail_subtotal = $(this).closest('tr').find("input[name='detail_product_subtotal[]']");
                var subtotal           = 0;

                subtotal = amount * quantity;
                td_detail_subtotal.html($.number(subtotal, 2, ',', '.'));
                in_detail_subtotal.val($.number(subtotal, 2, ',', ''));
            });
            calculateGrandTotal();
        }

    </script>
@endsection
