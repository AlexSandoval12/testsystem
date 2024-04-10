@extends('layouts.AdminLTE.index')
@section('title', 'Crear Presupuestos de Compras')
@section('page-styles')
    <style>
        .hide {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="ibox-content">
        <div class="table">
            <h3>Listado de Productos Solicitados</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Proveedor Sugerido</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wish_purchase->wish_purchase_details as $detail)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$detail->description}}</td>
                            <td>{{$detail->provider_name ?? '-'}}</td>
                            <td>{{$detail->quantity}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ Form::open(['id' => 'form']) }}
            <div class="row">
                <div class="col-md-2">
                    <button class="btn btn-xs btn btn-success" type="button" onclick="load_file();"><i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold">Adjuntar Archivos</span></button>
                    <input type="file" name="files[]" id="files" class="hidden" multiple>
                </div>
            </div>
            <div class="row" id="div_files"></div>
            <br><br>
            <dir id="text_list" class="hide">Listado de Archivos</dir>
            <div class="ibox-footer">
                <input type="submit" class="btn btn-success" value="Guardar">
                <a href="{{ url('wish-purchase') }}" class="btn btn-danger">Cancelar</a>
            </div>
        {{ Form::close() }}
    </div>
@endsection
@section('layout_js')
    <script>
        $(document).ready(function () {
            $('#form').submit(function(e)
            {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('wish-purchases.charge_purchase_budgets_store',$wish_purchase->id) }}',
                    type: "POST",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        redirect('{{ url('wish-purchase') }}');
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $('#files').on('change', function () {
                var files = $("#files")[0].files;
                var fileName = '';
                $('#div_files').html('');
                $('#text_list').show();

                for(var i = 0; i < files.length; i++)
                {
                    fileName += files[i].name +', ';

                    $('#div_files').append(
                    '<div class="col-md-3 mt-3">' +
                        '<span class="fileinput-filename label label-default p-xs b-r-sm"><b>' + files[i].name + '</b></span>' +
                    '</div>');
                }
                $('#file-text').val(fileName);
            });
        });

        function load_file()
        {
            $('#files').trigger('click');
        }
    </script>
@endsection
