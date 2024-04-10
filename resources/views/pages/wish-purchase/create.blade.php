@extends('layouts.AdminLTE.index')
@section('title', 'Pedido de Compra')
@section('content')
{{ Form::open(['id' => 'form', 'files' => true]) }}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Pedido de Compra</h5>
                </div>
                <div class="ibox-content pb-0">
                    <div class="row">
                        <div id="grafico"></div>
                        <div class="form-group col-md-4">
                            <label>Solicitado por</label>
                            <input class="form-control" type="text" name="requested_by" value="{{auth()->user()->name}}" disabled>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sucursal</label>
                            {{ Form::select('branch_id', $branches, old('branch_id'), ['class' => 'form-control', 'select2', 'id' => 'branch_id']) }}
                        </div>
                        <div class="form-group col-md-2">
                            <label>Fecha</label>
                            <input class="form-control" type="text" name="date" value="{{ old('date', date('d/m/Y')) }}" date-mask readonly>
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
                    {{ Form::select('raw_material_id', $raw_materials, old('raw_material_id'), ['id' => 'raw_material_id', 'placeholder' => 'Seleccione Materia Prima', 'class' => 'form-control', 'select2']) }}
                    <span class="red" id="text_last_purchases"></span>
                </div>
                <div class="form-group col-md-2">
                    <label>Presentación</label>
                    {{ Form::select('product_presentations_id', config('constants.presentation'), old('product_presentations_id'), ['id' => 'product_presentations_id', 'placeholder' => 'Presentación', 'class' => 'form-control', 'select2']) }}
                    <span class="red" id="text_last_purchases"></span>
                </div>
                <div class="form-group col-md-4">
                    <label>Descripción</label>
                    <input class="form-control" type="text" id="products_description" value="{{ old('products_description') }}" placeholder="Concepto Diferenciado">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2">
                    <label>Cantidad</label>
                    <input class="form-control" type="text" name="products_quantity" value="{{ old('products_quantity') }}" placeholder="Cantidad">
                </div>
                <div class="form-group col-md-1">
                    <label>Agregar</label>
                    <button type="button" class="btn btn-success" id="button_add_product"><i class="fa fa-plus"></i></button>
                </div>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tbody_detail"></tbody>
            </table>
        </div>
    </div>
    <div class="ibox-content pb-0">
        <div class="row">
            <div class="form-group col-md-7">
                <label>Observación</label>
                <textarea class="form-control" name="observation" rows="4">{{ old('observation') }}</textarea>
            </div>
        </div>
    </div>
    <div class="ibox-footer">
        <input type="submit" class="btn btn-sm btn-success" value="Guardar">
        <a href="{{ url('purchases-orders') }}" class="btn btn-sm btn-danger">Cancelar</a>
    </div>
{{ Form::close() }}
@endsection

@section('layout_js')
    <script>
        var counter = 0;
        var invoice_items_array = [];

        $(document).ready(function ()
        {
            $('#form').submit(function(e) {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('wish-purchase.store') }}',
                    type: "POST",
                    data: new FormData(this),
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        redirect ("{{ url('wish-purchase') }}");
                    },
                    error: function(data){

                        laravelErrorMessages(data);

                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#button_add_product").click(function() {
                addProduct();
            });
        });

        function addProduct()
        {
            var product_name              = $("select[name='raw_material_id'] option:selected").text();
            var product_id                = $("select[name='raw_material_id']").val();
            var product_description       = $("#products_description").val();
            var product_presentation_id   = $("#product_presentations_id").val();
            var product_presentation_name = $("select[name='product_presentations_id'] option:selected").text();
            var product_quantity          = $("input[name='products_quantity']").val().replace(/\./g, '');
            product_quantity              = (product_quantity > 0 ? product_quantity : 1);

            if(product_id!='' && product_quantity!='' && product_presentation_id!='')
            {
                if($.inArray(product_id, invoice_items_array) != '-1')
                {
                    if(confirm('Ya existe el producto, desea continuar?'))
                    {
                        var description = product_description ? product_description : product_name;

                        addToTable(product_id, description, product_quantity, product_presentation_id, product_presentation_name, product_description);
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    var description = product_description ? product_description : product_name;

                    addToTable(product_id, description, product_quantity, product_presentation_id, product_presentation_name,product_description,product_name);
                }

                $('#purchases_product_id, #product_presentations_id').val(null).trigger('change');
                $("#products_description").val('');
                $("input[name='products_quantity']").val('');

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

        function addToTable(id, name, quantity, presentation_id, presentation_name, description,product_name)
        {
            counter++;
            invoice_items_array.push(id);

            $('#tbody_detail').append('<tr>' +
                    '<td>' + counter + '</td>' +
                    '<td class="text-right">' + id +' <input type="hidden" name="detail_product_id[]" value="' + id + '"></td>' +
                    '<td>' + product_name + '<input type="hidden" name="detail_product_name[]" value="' + product_name + '"></td>' +
                    '<td>' + presentation_name + ' <input type="hidden" name="detail_presentation_id[]" value="' + presentation_id + '"></td>' +
                    '<td class="text-right"><input type="text" class="form-control" name="detail_product_quantity[]" value="' + quantity + '"></td>' +
                    '<input type="hidden" class="form-control" name="detail_product_description[]" value="' + description + '">'+
                    '<td class="text-right"><a href="javascript:;" onClick="removeRow(this, '+ id +');"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                '</tr>');

        }

        function removeRow(t, product_id)
        {
            $(t).parent().parent().remove();
            invoice_items_array.splice($.inArray(product_id, invoice_items_array), 1 );
            // calculateGrandTotal();
            counter--;
        }

    </script>
@endsection
