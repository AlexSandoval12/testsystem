@extends('layouts.AdminLTE.index')
@section('title', 'Presupuestos Cargado')
@section('page-styles')
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
    <div class="wrapper wrapper-content">
        <div class="ibox-content">
            <div class="ibox-title">
                <h3>Presupuestos Cargados</h3>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Detalles de Solicitud N# {{$restocking->number}}</h3>
                        <div class="row">
                            <div class="col-md-2">
                                <p>Fecha:</p>
                                <p>Solicitante: </p>
                                <p>Sucursal: </p>
                                <p>Departamento: </p>
                            </div>
                            <div class="col-md-10">
                                <p>{{$restocking->date->format('d/m/Y')}}</p>
                                <p> {{$restocking->requested_by}}</p>
                                <p> {{$restocking->branch->name}}</p>
                                <p>
                                    {{$restocking->purchases_requesting_department->name}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>Productos Solicitados Según Solicitud Nro #{{$restocking->number}}</h3>
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
                                    @foreach($restocking->restocking_details as $restocking_detail)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$restocking_detail->description}}</td>
                                            <td>{{$restocking_detail->quantity}}</td>
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
                    @foreach($restocking_budgets as $index => $restocking_budget)
                        @if($index % 2 == 0)
                            <div class="clearfix"></div>
                        @endif
                        <div class="col-md-3">
                            <div class="image-box">
                                <a href="{{ $restocking_budget->filePath() }}" title="{{ $restocking_budget->original_name }}" data-gallery=""><img alt="image" class="img-responsive" src="{{ $restocking_budget->filePath() }}"></a>
                            </div>
                            <div id="blueimp-gallery" class="blueimp-gallery">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev">‹</a>
                                <a class="next">›</a>
                                <a class="close">×</a>
                                <a class="play-pause"></a>
                                <ol class="indicator"></ol>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
