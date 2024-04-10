@extends('layouts.AdminLTE.index')
@section('title', 'Aprobación de Presupuestos Cargados')
@section('layout_css')
    <style>
        .button-container {
            text-align: center;
            border: 2px solid #126f16;
        }
        .button-container a {
            border: 1px solid #126f16;
        }
        .image-box {
            margin-bottom: 30px;
            border: 3px solid #126f16;
            display: inline-block;
        }
        .image-box img {
            width: 400px;
            height: 500px;
        }
        p {
            font-size: 14px;
        }
    </style>
@endsection
@section('content')
        <div class="ibox-content">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Detalles de Solicitud N# {{$wish_purchase->number}}</h3>
                        <div class="row">
                            <div class="col-md-2">
                                <p>Fecha:</p>
                                <p>Solicitante: </p>
                                <p>Sucursal: </p>
                            </div>
                            <div class="col-md-10">
                                <p>{{$wish_purchase->date}}</p>
                                <p> {{$wish_purchase->requested_by}}</p>
                                <p> {{$wish_purchase->branch->name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>Productos Solicitados Según Solicitud Nro #{{$wish_purchase->number}}</h3>
                        <div class="table">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="80%">Producto</th>
                                        <th width="10%">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wish_purchase->wish_purchase_details as $wish_purchase_detail)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$wish_purchase_detail->description}}</td>
                                            <td>{{$wish_purchase_detail->quantity}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br><br><br>
                <h3><b>Presupuestos Cargados:</b></h3>
                <div class="row">
                    @foreach($wish_purchase->purchase_budgets()->where(function($query){$query->where('status',2)->orwhere('status',1);})->get() as $index => $purchase_budget)
                        @if($index % 2 == 0)
                            <div class="clearfix"></div>
                        @endif
                        <div class="col-md-3">
                            <div class="image-box">
                                <a href="{{ $purchase_budget->filePath() }}" title="{{ $purchase_budget->original_name }}" data-gallery=""><img alt="image" class="img-responsive" src="{{ $purchase_budget->filePath() }}"></a>
                                <div class="button-container">
                                    <a href="javascript:;" class="btn btn-success" onclick="approved_restocking({{$purchase_budget->id}},1)" id="approve-link{{$purchase_budget->id}}" title="Autorizar Presupuesto">Autorizar</a>
                                    <a href="javascript:;" class="btn btn-danger" onclick="approved_restocking({{$purchase_budget->id}},2)" id="approve-link{{$purchase_budget->id}}" title="Rechazar Solicitud">Rechazar</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endsection
@section('layout_js')
    <script>
        function approved_restocking(id,type) {
            $(document).on('click', '#approve-link'+id, function(event) {
                text = type == 1 ?  'Aprobar' : 'Rechazar';
                var url     = 'wish-purchase';

                event.preventDefault(); // Prevenir que el enlace se siga en su ruta
                swal({
                    title: "¿Desea "+text+" la solicitud de compra?",
                    icon: "info",
                    buttons: ["Cancelar", "Sí,"+text],
                    dangerMode: type == 1 ? false : true,
                })
                .then((willApprove) => {
                    if (willApprove) {
                        window.location.href = `{{ url('wish-purchase-budgets/${id}/confirm-purchase-budgets?type=${type}&url=${url}')}}`;
                    }
                });
            });
        }
    </script>
@endsection
