@extends('layouts.AdminLTE.index')
@section('title', 'Presupuestos Cargados')
@section('layout_css')
    <style>
        .button-container {
            text-align: center;
        }
        .button-container a {
        }
        .image-box {
            margin-bottom: 30px;
            display: inline-block;
            border: 3px solid #126f16;
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
                                <p> {{$wish_purchase->user->name}}</p>
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
                <h3><b>Presupuestos Aprobados:</b></h3>
                <div class="row">
                    @foreach($purchase_budgets as $index => $purchase_budget)
                        @if($index % 2 == 0)
                            <div class="clearfix"></div>
                        @endif
                        <div class="col-md-3">
                            <div class="image-box">
                                <a href="{{ $purchase_budget->filePath() }}" title="{{ $purchase_budget->original_name }}" data-gallery=""><img alt="image" class="img-responsive" src="{{ $purchase_budget->filePath() }}"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endsection
