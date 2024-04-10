@extends('layouts.AdminLTE.index')
@section('title', 'Proveedor')
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <div class="ibox-tools">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle pull-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Exportar <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="{{ route('provider.export-xls', request()->query()) }}"><i class="fa fa-file-excel"></i> XLS</a></li>
                    </ul>
                </div>
                <a href="{{ url('provider/create') }}" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus"></i> Agregar</a>
            </div>
        </div>
        <div class="ibox-content pb-0">
            <div class="row">
                <form method="GET">
                    <div class="form-group col-sm-3">
                        <input type="text" class="form-control" name="s" placeholder="Buscar" value="{{ request()->s }}">
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        @if(request()->s)
                            <a href="{{ url('provider') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-striped table-condensed table-hover">
          <thead>
              <tr>
                <th>Nombre</th>
                <th class="text-center">Ruc</th>
                <th class="text-center">Direccion</th>
                <th class="text-center">Telefono</th>
                <th class="text-center">Acciones</th>
              </tr>
          </thead>
          <tbody>
            @foreach($providers as $provider)
                <tr>
                    <td>{{ $provider->name }}</td>
                    <td class="text-center">{{ $provider->ruc}}</td>
                    <td class="text-center">{{ $provider->address}}</td>
                    <td class="text-center">{{ $provider->phone}}</td>
                    <td class="text-center">
                      <a href="{{ url('provider/' . $provider->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        </table>
      </div>
  </div>
</div>
@endsection
