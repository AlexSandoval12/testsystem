@extends('layouts.AdminLTE.index')
@section('title', 'Oportunidad ')
@section('content')
<div class="row">
  <div class="col-lg-12">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <h5>Oportunidades de Venta</h5>
              <div class="ibox-tools">
                      <a href="javascript:;" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal_change_days_call_again_leads">Dias Llamador</a>
                      <a href="{{ url('sales-opportunity/import') }}" class="btn btn-warning btn-xs white"><i class="fa fa-upload"></i> Importar</a>
                      <a href="{{ url('opportunity/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
              </div>
          </div>
          <div class="ibox-content pb-0">
              <form method="GET">
                  <div class="row">
                      <div class="form-group col-md-3">
                          {{ Form::select('half_contact_id[]', $half_contacts, request()->half_contact_id, ['class' => 'form-control',  'placeholder'=>'Seleccione Medio de Contacto']) }}
                      </div>
                      <div class="form-group col-md-3">
                          {{ Form::select('status[]', $status, request()->status, ['class' => 'form-control','placeholder'=>'Selecccione Estado']) }}
                      </div>
                      <div class="form-group col-md-3">
                          {{ Form::select('seller_id', $sellers, old('seller_id', request()->seller_id), ['placeholder' => 'Seleccione Vendedor', 'class' => 'form-control']) }}
                      </div>
                  </div>
                  <div class="row">
                      <div class="form-group col-md-3">
                          <input type="text" class="form-control" name="s" placeholder="Buscar" value="{{ request()->s }}">
                      </div>
                      <div class="form-group col-md-2">
                          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                          @if(request()->s || request()->enterprise_id || request()->half_contact_id || request()->seller_id || request()->status)
                              <a href="{{ request()->url() }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                          @endif
                      </div>
                  </div>
              </form>
          </div>
          <div class="ibox-content table-responsive no-padding">
              <table class="table table-hover table-condensed table-striped mb-0">
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Suc</th>
                          <th>Fecha</th>
                          <th>Medio</th>
                          <th>Nombre Completo</th>
                          <th>Teléfono</th>
                          <th>Email</th>
                          <th>Vendedor</th>
                          <th>Estado</th>
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($opportunities as $opportunity)
                          <tr>
                              <td>{{ $opportunity->id }}</td>
                              <td>{{ $opportunity->branch ? $opportunity->branch->name : '-' }}</td>
                              <td>{{ $opportunity->created_at->format('d/m/Y H:i:s') }}</td>
                              <td>{{ $opportunity->contact_medium->name }}</td>
                              <td>{{ $opportunity->fullname }}</td>
                              <td>{{ $opportunity->phone ?? '-' }}</td>
                              <td>{{ $opportunity->email ?? '-' }}</td>
                              <td>{{ $opportunity->seller->name }}</td>
                              <td><span class="label label-{{ config('constants.sales-opportunity-status-label.' . $opportunity->status) }}">{{ config('constants.sales-opportunity-status.' . $opportunity->status) }}</span></td>
                              <td class="text-right">
                                  <a href="{{ url('sales-opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                  {{-- @permission('sales-opportunity.edit') --}}
                                      <a href="{{ url('sales-opportunity/' . $opportunity->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                  {{-- @endpermission --}}
                                  {{-- @permission('sales-opportunity.delete') --}}
                                      <a href="{{ url('sales-opportunity/' . $opportunity->id . '/delete') }}"><i class="fa fa-trash"></i></a>
                                  {{-- @endpermission --}}
                              </td>
                          </tr>
                      @endforeach
                  </tbody>
              </table>
              {{ $opportunities->appends(request()->query())->links() }}
          </div>
      </div>
  </div>
</div>                   
@endsection
