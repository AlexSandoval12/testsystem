@extends('layouts.AdminLTE.index')
@section('title', 'Pedido Compras')
@section('layout_css')
<style>
    input[type=checkbox]{
        transform: scale(1.5);
    }
</style>
@endsection
@section('content')
<div class="ibox float-e-margins">
    <div class="ibox-content">
        <form id="form" method="GET" action="{{route('wish-purchases.transfer_create')}}">
            <input type="text" hidden name="wish_purchase_ids[]" id="wish_purchase_ids" value="">
            <input type="text" hidden name="wish_purchase_details_ids[]" id="wish_purchase_details_ids" value="">
            <div class="table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th> Descripción </th>
                            <th>Producto</th>
                            <th> Presentación </th>
                            {{-- <th> Proveedor </th> --}}
                            <th> Cant. Solicitada</th>
                            <th> Saldo</th>
                            <th> Cant. Aprobada</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wish_purchases as $wish_purchase)
                            <tr class="alert alert-default">
                                <th class="text-left" colspan="11"><h4><b>Solicitud Nro. {{number_format($wish_purchase->number,0,'.',',')}} - Fecha: {{$wish_purchase->date}} </b></h4></th>
                            </tr>
                            @foreach ($wish_purchase->wish_purchase_details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td width="20%">{{ $detail->description ?? '-' }}</td>
                                    <td width="30%">
                                        {{ Form::select('material_id', $raw_materials, old('material_id',$detail->material_id), ['id' => 'material_id'.$detail->id.'' ,'placeholder' => 'Seleccione Producto', 'width'=> '100%' ,'class' => 'form-control select_'.$detail->id.'', 'select2']) }}
                                    </td>
                                    <td width="10%">
                                        {{ Form::select('presentation', config('constants.presentation') , old('presentation',$detail->presentation), ['id' => 'presentation_'.$detail->id.'', 'placeholder' => 'Presentacion', 'class' => 'form-control select_'.$detail->id.'', 'select2']) }}
                                    </td>
                                    {{-- <td width="10%">{{ $detail->provider_name ?? '-' }}</td> --}}
                                    <td width="5%">{{ $detail->quantity}}</td>
                                    <td width="5%"><input class="form-control text-right input_{{$detail->id}}" type="hidden" name="quantity_{{$detail->id}}" id="quantity_{{$detail->id}}" max="{{$detail->residue}}" value="{{$detail->residue}}" >{{$detail->residue}}</td>
                                    <td width="5%"><input class="form-control readOnly disabled  @if($detail->residue == 0) readOnly @endif text-right input_{{$detail->id}}" type="number" name="aproved_quantity_{{$detail->id}}" id="aproved_quantity_{{$detail->id}}" max="{{$detail->residue}}" value="{{$detail->residue}}" ></td>
                                    <td>
                                        <a href="javascript:;" class="buttons_icons"  onclick="show_field({{ $detail->id }});"><i style="font-size:17px;" class="fa fa-retweet" id="icon_{{ $detail->id }}"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td>
                                <button type="submit" class="btn btn-md btn-primary" data-action="{{ route('purchase-order.create') }}">
                                    <i class="fa fa-upload" data-toggle="tooltip" title="Generar">&nbsp;Generar OC</i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
    <div class="ibox-footer">
    </div>
</div>
@endsection
@section('layout_js')
    <script>
        var conteo = 0;
        $(document).ready(function () {
            $('select[name="material_id"]').select2();

            $('#form button[type="submit"]').click(function() {
            var action = $(this).data('action');
            $('#form').attr('action', action);
            });
        });

        function show_field(id)
        {
            if ($("a #icon_"+id).attr('class') == 'fa fa-edit')
            {
                $('.select_'+id).prop('disabled', false).select2();   
                $('.input_'+id).prop('readOnly', false);   
                $('#icon_'+ id).attr('class', 'fa fa-retweet');      
            }
            else
            {
                let product_id      = $('#material_id'+id).val();
                let presentation    = $('#presentation_'+id).val();

                if(product_id && product_id != 'Seleccione Producto' && product_id != null && presentation)
                {
                    if (parseInt($('#aproved_quantity_'+id).val()) > parseInt($('#quantity_'+id).val()))
                    {
                        console.log('validacion 1');
                        swal({
                            title: "Sistema",
                            text: "Cantidad Aprobada no puede superar a la Solicitada!!",
                            icon: "info",
                            button: "OK",
                        });

                        return false;
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{route('wish-purchases.show_multiple_submit')}}",
                        dataType: "json",
                        data: { _token: "{{ csrf_token() }}",detail_id: id ,aproved_quantity : $('#aproved_quantity_'+id).val(),product_id: product_id,presentation:presentation },
                        success: function (response) {
                            if(response.message)
                            {
                                swal({
                                    title: "Sistema",
                                    text: response.message,
                                    icon: "info",
                                    button: "OK",
                                });
                            }
                            if(response.success)
                            {
                                $('.select_'+id).prop('disabled', true).select2("destroy");
                                $('.input_'+id).prop('readOnly', true);
                                $('#icon_'+ id).attr('class', 'fa fa-edit');
                                $('#wish_purchase_ids').val(response.restocking_id);
                                $('#wish_purchase_details_ids').val(id)
                                console.log($('#wish_purchase_details_ids').val(),$('#wish_purchase_ids').val(), conteo);
                                conteo++;
                            }
                        }
                    });
                }
                else 
                {
                    swal({
                        title: "Sistema",
                        text: "Hay campos vacios.!!",
                        icon: "info",
                        button: "OK",
                    });
                }
            }
        }
    </script>
@endsection