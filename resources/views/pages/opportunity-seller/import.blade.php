@extends('layouts.sistema')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Importar Oportunidades de Venta</h5>
                    </div>
                    {{ Form::open(['route' => 'sales-opportunity-seller.import', 'files' => true]) }}
                        <div class="ibox-content">
                            @include('partials.messages')
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Archivo</label>
                                    <input class="form-control" type="file" name="file">
                                    <a href="{{ url('sales-opportunity-seller/download_matriz') }}">Descargar matriz</a>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Importar">
                            <a href="{{ url('sales-opportunity-seller') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
