@extends('layouts.AdminLTE.index')
@section('title', 'Proveedor')
@section('content')
<div class="row">
    {{ Form::open(['id' => 'form']) }}
        <div class="ibox float-e-margins">
            @include('partials.messages')
            <div class="ibox-title">
                <h5>Editar Compra</h5>
            </div>
            <div class="ibox-content">
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}" id="purchase_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5"><b>Razon Social:</b></div>
                                    <div class="col-md-7">{{ $purchase->social_reason->razon_social }}</div>
                                    <input type="hidden" id="social_reason_id" value="{{ $purchase->social_reason_id }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Moneda:</b></div>
                                    <div class="col-md-7">{{ $purchase->currency->name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Tipo:</b></div>
                                    <div class="col-md-7"><span class="label label-{{ config('constants.type_purchases_label.' . $purchase->type) }}">{{ config('constants.type_purchases.'. $purchase->type) }}</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Número:</b></div>
                                    <div class="col-md-7">{{ $purchase->number }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Fecha Compra:</b></div>
                                    <div class="col-md-7">{{ $purchase->date->format('d/m/Y') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Id Proveedor:</b></div>
                                    <div class="col-md-7">{{ number_format($purchase->purchases_provider_id, 0, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Proveedor:</b></div>
                                    <div class="col-md-7">{{ $purchase->purchases_provider->name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Días de gracia:</b></div>
                                    <div class="col-md-7">{{ $purchase->purchases_provider->days_of_grace ?? ' - ' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Ruc:</b></div>
                                    <div class="col-md-7">{{ $purchase->ruc }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Monto:</b></div>
                                    <div class="col-md-7">{{ number_format($purchase->amount, 0, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>OP:</b></div>
                                    <div class="col-md-7">{{ $purchase->payment_order }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Usuario Creación:</b></div>
                                    <div class="col-md-7">{{ $purchase->user->fullname }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Fecha Creación:</b></div>
                                    <div class="col-md-7">{{ $purchase->created_at->format('d/m/Y h:i:s') }}</div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-5"><b>Factura:</b></div>
                                    @if(auth()->user()->can('invoice_copy.edit'))
                                        <div class="col-md-7">{{ Form::select('invoice_copy', $invoice_copy, old('invoice_copy',$purchase->invoice_copy), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => 'Seleccione']) }}</div>
                                    @else
                                        <div class="col-md-7">{{ config('constants.invoice_copy.'. $purchase->invoice_copy) }}</div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Sucursal:</b></div>
                                    <div class="col-md-7">{{ Form::select('branch_id', $branches,old('branch_id', $purchase->branch_id), ['data-live-search'=>'true', 'class' => 'form-control selectpicker']) }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Condición:</b></div>
                                    <div class="col-md-7">{{ Form::select('condition',config('constants.invoice_condition'), old('condition',$purchase->condition), ['data-live-search'=>'true', 'class' => 'form-control selectpicker']) }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Nro. Timbrado:</b></div>
                                    <div class="col-md-7"><input class= "form-control" type="text" name="stamped" value="{{ old('stamped', $purchase->stamped) }}"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Vigencia Timbrado:</b></div>
                                    <div class="col-md-7"><input class= "form-control" name="stamped_validity" type="text" date-mask placeholder="dd/mm/yyy" value="{{ $purchase->stamped_validity ? $purchase->stamped_validity->format('d/m/Y') : '' }}"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><b>Observación:</b></div>
                                    <div class="col-md-7"><textarea name="observation" rows=1 cols=1 class="form-control">{{ old('observation', $purchase->observation) }}</textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="line">
                            <h2 class="title"><span>Centro de Costos</span></h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-condensed table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th width="10%" class="text-center">Cód.</th>
                                                <th width="60%" class="text-center">Centro de Costos</th>
                                                <th width="25%" class="text-center">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchase->purchases_cost_centers as $cost_center)
                                                <tr>
                                                    <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="text-right">{{ $cost_center->cost_center_id }}</td>
                                                    <td>{{ Form::select('detail_cost_center[]', $cost_centers, $cost_center->cost_center_id, ['class' => 'form-control', 'select2']) }}</td>
                                                    <td class="text-right">{{ number_format($cost_center->amount, 0, ',', '.') }}</td>
                                                    <input type="hidden" name="detail_cost_center_id[]" value="{{ $cost_center->id }}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="line">
                            <h2 class="title"><span>Detalle a Pagar</span></h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Caja Chica</label>
                                    {{ Form::select('cash_box_id', $cash_boxes, old('cash_box_id',$purchase->cash_box_id), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => 'Seleccione Caja Chica', 'id' => 'cash_box_id']) }}
                                </div>
                                <!-- SI ES PROVEEDOR Y POSEE CALENDARIO DE PAGO GENERADO -->
                                @if($purchase->purchases_collects)
                                    <br>
                                    <div class="form-group col-md-6 text-center">
                                        <label>Fecha Pago</label>
                                    </div>
                                    <div class="form-group col-md-6 text-center">
                                        <label>Monto a Pagar en Tesoreria</label>
                                    </div>
                                    @foreach($purchase->purchases_collects as $purchases_collect)
                                        @if($purchases_collect->residue == $purchases_collect->amount)
                                            <div class="form-group col-md-6 text-center">
                                                <input type="hidden" name="purchases_collect_id[]" value="{{ $purchases_collect->id }}">
                                                <input class="form-control text-center date" type="text" name="expiration[]" value="{{ $purchases_collect->expiration->format('d/m/Y') }}" date-mask>
                                            </div>
                                            <div class="form-group col-md-6 text-center">
                                                @if($loop->iteration == 1)
                                                    <input class="form-control  text-right" type="text" name="amount_treasury[]" value="{{ number_format($purchases_collect->amount,0,',','.') }}" period-data-mask>
                                                @else
                                                    <input class="form-control  text-right" type="text" name="amount_treasury[]" value="{{ number_format($purchases_collect->amount,0,',','.') }}" period-data-mask>
                                                @endif
                                            </div>
                                        @else
                                            <div class="form-group col-md-6 text-center">
                                                <input type="hidden" name="purchases_collect_id[]"  value="{{ $purchases_collect->id }}">
                                                <input class="form-control text-center" type="text" name="expiration[]" value="{{ $purchases_collect->expiration->format('d/m/Y') }}" date-mask readonly>
                                            </div>
                                            <div class="form-group col-md-6 text-center">
                                                <input class="form-control  text-right" type="text" name="amount_treasury[]" value="{{ number_format($purchases_collect->amount,0,',','.') }}" readonly>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <input type="hidden" name="accounting_account_provider_id" id="accounting_account_provider_id" value="{{ $provider_accounting_plan_id }}">
                            </div>
                        </div>
                        <br>
                        <div class="line">
                            <h2 class="title"><span>Detalle de Anticipo</span></h2>
                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label>Cuenta Contable</label>
                                    <select class="form-control" name="other_accounting_account_id" id="other_accounting_account_id" select2></select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Monto</label>
                                    <input class="form-control" type="text" name="other_accounting_account_amount" id="other_accounting_account_amount" period-data-mask>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Agregar</label>
                                    <button type="button" class="btn btn-success" id="button_other_accounting_account"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12" id="div_detail_other_accounting_account">                    
                                    <table class="table table-condensed table-hover table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Cuenta Contable</th>                                    
                                                <th class="text-center">Monto</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody_detail_other_accounting_account"></tbody>
                                        <tfoot class="bold" id="div_total_no_pago">
                                            <tr>                                    
                                                <td class="text-right">Totales</td>
                                                <td class="text-right"><b id="div_total_not_payment"></b></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                        <input type="hidden" name="total_not_payment" id="total_not_payment" value="0">                          
                                    </table>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Cód</th>
                            <th class="text-center">Producto</th>
                            <th class="text-center">OC</th>
                            <th class="text-center">Cuenta Contable</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Exenta</th>
                            <th class="text-center">IVA 5%</th>
                            <th class="text-center">IVA 10%</th>
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
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right"><b><h3>Total de Compra</h3></b></td>
                            <td class="text-right" bgcolor= "#E0F8F7"><b><h3 id="total_purchases"></h3></b></td>
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
            @foreach($purchase->accounting_entry as $accounting)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="ibox-title">
                            <h5>Asientos Contables # {{ $accounting->number }} </h5>
                        </div>
                        <div class="ibox-content table-responsive no-padding">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th colspan="2" class="text-center">Cuenta Contable</th>
                                        <th class="text-center">Concepto</th>
                                        <th class="text-center">Débito</th>
                                        <th class="text-center">Crédito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accounting->EntryDetail() as $accounting_detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @if($accounting_detail->debit>0)
                                                <td>{{ $accounting_detail->accounting_plan->fullname }}</td>
                                                <td></td>
                                            @else
                                                <td></td>
                                                <td>{{ $accounting_detail->accounting_plan->fullname }}</td>
                                            @endif
                                            <td>{{ $accounting_detail->concept }}</td>
                                            <td class="text-right">{{ number_format($accounting_detail->debit, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($accounting_detail->credit, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right"><b>Total:</b></th>
                                        <th class="text-right">{{ number_format($accounting->details->sum('debit'), 0, ',', '.') }}</th>
                                        <th class="text-right">{{ number_format($accounting->details->sum('credit'), 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="ibox-footer">
                    <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                    <a href="{{ url('purchases') }}" class="btn btn-sm btn-danger">Cancelar</a>
                </div>
            </div>
        <!-- </div> -->
    {{ Form::close() }}
</div>
@endsection
@section('layout_css')
    <style type="text/css">
        .title{
            margin-top: -12px !important;
            font-size: 20px;
        }
        .line{
            border: 1px solid #1ab394!important;
            border-radius: 16px;
            padding-left: 10px;
            padding-right: 10px;
            padding-bottom: 10px;
        }
        .title span{
            background-color: white;
        }
    </style>
@endsection

@section('layout_js')
    <script>
        var counter                        = 0;
        var invoice_items_array            = [];
        var other_accounting_account_array = [];
        var accounting_plans               = {!! json_encode($array_accounting_plans) !!}

        $(document).ready(function ()
        {
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('purchases.update', $purchase->id) }}',
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect ("{{ url('purchases') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#button_other_accounting_account").click(function() {
                addOtherAccountingAccount();
            });

            $("#other_accounting_account_id").append('<option>Seleccione Cuenta Contable..</option>')
            $.each(accounting_plans, function(index, value){
                $("#other_accounting_account_id").append('<option value="'+ index + '">'+ value +'</option>')
            });
        });    
        //CARGAR DETALLE DE FACTURA
        @foreach($purchase->purchases_details as $detail)
            addToTable('{{ $detail->id }}',
                    '{{ $detail->purchases_product_id }}',
                    '{{ $detail->purchases_product->name }}',
                        {{ $detail->amount }},
                        {{ $detail->quantity }},
                        {{ $detail->purchases_product->type_iva }},
                        0, 
                        0,
                        {{ $detail->accounting_plan_id }},
                    '{{ $detail->emergency_mobile_id ? $detail->emergency_mobile->name : '' }}',
                    '{{ $detail->emergency_mobile_id }}' );
        @endforeach
        //CARGAR DETALLE DE ANTICIPO
        @foreach($purchases_accounting_plans as $detail)
            addToTableOtherAccountingAccount( {{ $detail->accounting_plan_id }}, '{{ $detail->accounting_plan->fullname }}', {{ $detail->amount }});
        @endforeach

        function addToTable(purchase_detail_id, id, name, amount, quantity, type_iva, number_orders, id_orders, accounting_plan, emergency_mobile, emergency_mobile_id, emergency_mobile_name)
        {
            counter++;

            var subtotal      = 0;
            var total_excenta = 0;
            var total_iva5    = 0;
            var total_iva10   = 0;

            subtotal = quantity * amount;
            subtotal = $.number(subtotal,2,'.','');

            invoice_items_array.push(id);
                        
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

            $('#tbody_detail').append('<tr>' +
                '<td>' + counter + '</td>' +
                '<td class="text-right">' + id + '<input type="hidden" name="detail_product_id[]" value="' + id + '"></td>' +
                '<td>' + name + ( emergency_mobile==1 ? '<br><i><b><span class="red">'+ emergency_mobile_name +'</span></b></i>' : '' ) + ' <input type="hidden" name="detail_product_name[]" value="' + name + '"></td>' + 
                '<td class="text-center">' + number_orders + ' <input type="hidden" name="detail_product_orders_id[]" value="' + id_orders + '"></td>' +
                '<td width="25%"><select class="form-control" name="detail_accounting_plan[]" id="select_'+counter+'" select2></select></td>' +
                '<td width="8%" class="text-center">' + $.number(quantity, 0, ',', '.') + '<input type="hidden" name="detail_product_quantity[]" onkeyup="changeReCalculo();" value="' + quantity + '"></td>' +
                '<td class="text-right" id="td_detail_amount">' + $.number(amount, 0, ',', '.') + '</td>' +
                '<td class="text-right" id="td_detail_total_excenta">' + $.number(total_excenta, 0, ',', '.') + '</td>' +
                '<td class="text-right" id="td_detail_total_iva5">' + $.number(total_iva5, 0, ',', '.')  + '</td>' +
                '<td class="text-right" id="td_detail_total_iva10">' + $.number(total_iva10, 0, ',', '.') + '</td>' +                
                '<input type="hidden" name="detail_total_excenta[]" value="' + total_excenta + '">' +
                '<input type="hidden" name="detail_total_iva5[]" value="' + total_iva5 + '">' +
                '<input type="hidden" name="detail_total_iva10[]" value="' + total_iva10 + '">' +
                '<input type="hidden" name="detail_type_iva[]" value="' + type_iva + '">' +
                '<input type="hidden" name="detail_emergency_mobile[]" value="' + emergency_mobile_id + '">' +
                '<input type="hidden" name="detail_purchase_detail_id[]" value="' + purchase_detail_id + '">' +                
            '</tr>');
            change_load_accounting_account(counter, accounting_plan);
            calculateGrandTotal();
            $("[select2]").select2();
        }
        //CARGAR CUENTAS CONTABLES DE PRODUCTOS
        function change_load_accounting_account(id, accounting_plan)
        {           
            $("#select_"+id).html('');                
            $.each(accounting_plans, function(index, value)
            {    
                if( typeof value !== 'undefined')
                {
                    if(accounting_plan == index)
                    {
                        $("#select_"+id).append('<option value="'+ index +'" selected>'+ value +'</option>')
                    }else
                    {
                        $("#select_"+id).append('<option value="'+ index +'">'+ value +'</option>')
                    }
                }
            });            
        }
        //CALCULAR TOTALES
        function calculateGrandTotal()
        {
            var grandTotal_Excenta = 0;
            var grandTotal_Iva5    = 0;
            var grandTotal_Iva10   = 0;
            var grandTotal_Factura = 0;
            var iva_5              = 0;
            var iva_10             = 0;
            var total_iva          = 0;

            $('input[name^="detail_total_excenta[]"]').each(function ()
            {
                grandTotal_Excenta += +$(this).val().replace('.00', '');
            });
            $("#total_excenta").html('<b>' + $.number(grandTotal_Excenta, 0, ',', '.') + '</b>');

            $('input[name^="detail_total_iva5[]"]').each(function ()
            {
                grandTotal_Iva5 += +$(this).val().replace('.00', '');
            });
            $("#total_iva5").html('<b>' + $.number(grandTotal_Iva5, 0, ',', '.') + '</b>');

            $('input[name^="detail_total_iva10[]"]').each(function ()
            {
                grandTotal_Iva10 += +$(this).val().replace('.00', '');
            });
            $("#total_iva10").html('<b>' + $.number(grandTotal_Iva10, 0, ',', '.') + '</b>');

            grandTotal_Factura = grandTotal_Excenta + grandTotal_Iva5 + grandTotal_Iva10;
            iva_5              = Math.round(grandTotal_Iva5 / 21);
            iva_10             = Math.round(grandTotal_Iva10 / 11);
            total_iva          = iva_5 + iva_10;

            $("#total_purchases").html($.number(grandTotal_Factura, 0, ',', '.'));
            $("#totales_iva").html('IVA 5 % ' + $.number(iva_5, 0, ',', '.') + ' -  IVA 10 % ' + $.number(iva_10, 0, ',', '.'));

            $("#total_excenta_final").val(grandTotal_Excenta);
            $("#total_iva5_final").val(grandTotal_Iva5);
            $("#total_iva10_final").val(grandTotal_Iva10);
            $("#total_product").val(grandTotal_Factura);
            $("#amount_iva5").val(iva_5);
            $("#amount_iva10").val(iva_10);
        }
        //AGREGAR DETALLE DE ANTICIPO
        function addOtherAccountingAccount()
        {
            var other_accounting_account_name = $("select[name='other_accounting_account_id'] option:selected").text();
            var other_accounting_account_id   = $("select[name='other_accounting_account_id']").val();            
            var other_accounting_amount       = $("input[name='other_accounting_account_amount']").val().replace(/\./g, '');

            if(other_accounting_account_id != '' && other_accounting_amount != '')
            {
                if($.inArray(other_accounting_account_id, other_accounting_account_array) != '-1')
                {
                    alert('Ya existe la Cuenta Contable');
                    return false;
                }
                else
                { 
                    addToTableOtherAccountingAccount(other_accounting_account_id, other_accounting_account_name, other_accounting_amount);
                    other_accounting_account_array.push(other_accounting_account_id);
                    $("#other_accounting_account_amount").val('');                    
                }
            }else
            {
                alert('Hay campos vacíos');
                return false;     
            }
        }
        //AGREGAR A TABLA DE ANTICIPO
        function addToTableOtherAccountingAccount(id, name, amount)
        {
            $('#tbody_detail_other_accounting_account').append('<tr>' +                
                '<td>' + name + '<input type="hidden" name="detail_other_accounting_account_id[]" value="' + id + '"></td>' +                
                '<td class="text-right">' + $.number(amount, 0, ',', '.') +' <input type="hidden" name="detail_other_accounting_account_amount[]" value="' + amount + '"></td>' +                
                '<td class="text-center"><a href="javascript:;" onClick="removeRowOtherAccountingAccount(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
            '</tr>');
            calculateOtherAccountingAccount();
        }
        //REMOVER DETALLE DE ANTICIPO
        function removeRowOtherAccountingAccount(t, other_accounting_account_id)
        {
            $(t).parent().parent().remove();
            other_accounting_account_array.splice($.inArray(other_accounting_account_id, other_accounting_account_array), 1 );
            calculateOtherAccountingAccount();
        }
        //CALCULAR TOTAL ANTICIPO
        calculateOtherAccountingAccount();
        function calculateOtherAccountingAccount()
        {
            var grandTotalNoPago  = 0;
            var grandTotalProduct = parseInt($('#total_product').val().replace(/\./g, '')); 

            $('input[name^="detail_other_accounting_account_amount[]"]').each(function ()
            {
                grandTotalNoPago += +$(this).val().replace('.', '');
            });
            $("#div_total_not_payment").html($.number(grandTotalNoPago, 0, ',', '.'));
            $("#total_not_payment").val(grandTotalNoPago);

            // if(grandTotalNoPago > 0 && grandTotalProduct>0)
            // {   
                $("#amount_treasury").val($.number(grandTotalProduct - grandTotalNoPago, 0, ',', '.'));
            // }           
        }
    </script>
@endsection
