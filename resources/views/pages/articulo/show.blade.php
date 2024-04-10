@extends('layouts.AdminLTE.index')
@section('title', 'Articulo')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('articulo') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro° Articulo:</b></div>
                                <div class="col-md-9">{{ $articulo->id }}</div>
                            </div> 
                            <div class="row">
                                <div class="col-md-3"><b>Articulo:</b></div>
                                <div class="col-md-9">{{ $articulo->name }}</div>
                            </div>  
                            <div class="row">
                                <div class="col-md-3"><b>Precio:</b></div>
                                <div class="col-md-9">{{ number_format($articulo->price, 0  , ',', '.') }}</div>
                            </div>
                             <div class="row">
                                <div class="col-md-3"><b>Fecha Creación:</b></div>
                                <div class="col-md-9">{{ $articulo->created_at->format('d/m/Y H:m:s') }}</div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <center><h2>Configuraciones</h2></center>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                </div>
                <div class="ibox-content table-responsive no-padding">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Materia Prima</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($articulo->setting_product))
                                @foreach($articulo->setting_product->whereNotNull('raw_materials_id') as $res)
                                    <tr>
                                        <td>{{ $res->raw_material->description }}</td>
                                        <td>{{ $res->quantity }}</td>
                                    </tr>
                                @endforeach 
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                </div>
                <div class="ibox-content table-responsive no-padding">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Control Produccion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($articulo->setting_product))
                                @foreach($articulo->setting_product->whereNotNull('stage_id') as $res)
                                    <tr>
                                        <td class="text-center">{{ $res->stage->name }}</td>
                                    </tr>
                                @endforeach 
                             @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                </div>
                <div class="ibox-content table-responsive no-padding">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Control Calidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($articulo->setting_product))
                                @foreach($articulo->setting_product->whereNotNull('production_qualities_id') as $res)
                                    <tr>
                                    <td>{{ $res->qualities->name }}</td>
                                    </tr>
                                @endforeach  
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>        
</div>
@endsection
