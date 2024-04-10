@extends('layouts.AdminLTE.index')
@section('title', 'Agregar Cliente ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            {{ Form::open(['route' => 'cliente.store']) }}
            <div class="ibox-content">
                    @include('partials.messages')
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Nombre</label><br>
                        <input id="name" class="form-control" name="name" type="text"  value="{{ old('first_name') }}">
                    </div>
                <div class="form-group col-md-4">
                    <label>Apellido</label><br>
                    <input id="apellido" class="form-control" name="apellido" type="text"  value="{{ old('last_name')}}">
                </div>
                    <div class="form-group col-md-4">
                        <label>Direccion</label><br>
                        <input id="address" class="form-control" name="address" type="text"  value="{{ old('address')}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>RUC</label><br>
                        <input id="ruc" class="form-control" name="ruc" type="text"  value="{{ old('ruc')}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Numero de Documento</label><br>
                        <input id="document_number" class="form-control" name="document_number" type="text"  value="{{ old('document_number')}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Departamento</label>
                        {{ Form::select('departamento', $departamentos, null, ['class' => 'form-control','placeholder' => 'Seleccione Departamento' ,'id' => 'departamento','onChange'=> 'changeDepartment();']) }}
                      </div>
                      <div class="form-group col-md-4">
                          <label>Ciudad</label>
                        <select id="ciudades" name="ciudades" class="form-control">
                          <option>Selecciona una ciudad </option>
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label>Barrio</label><br>
                        <input id="neighborhood" class="form-control" name="neighborhood" type="text"  value="{{ old('neighborhood')}}">
                     </div>
                     <div class="form-group col-md-4">
                        <label>Razon Social</label><br>
                        <input id="razon_social" class="form-control" name="razon_social" type="text"  value="{{ old('razon_social')}}">
                     </div>
                     <div class="form-group col-md-4">
                        <label>Estado Civil</label><br>
                        {{ Form::select('civil_status', config('constants.civil_status') ,null, ['class' => 'form-control selectpicker',  'placeholder'  => 'Seleccione una Estado CIvil']) }}
                     </div>
                     <div class="form-group col-md-4">
                        <label>Genero</label><br>
                        {{ Form::select('gender', config('constants.gender') ,null, ['class' => 'form-control selectpicker', 'placeholder'  => 'Seleccione un Genero']) }}
                     </div>
                     <div class="row">
                    <div class="form-group col-md-4">
                        <label>Nacionalidad</label>
                        {{ Form::select('nationalities_id', $nation ,null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'placeholder'  => 'Seleccione una nacionalidad']) }}
                    </div>
                </div>  
                <div class="ibox-content pb-0">
                        <div class="form-group col-md-5">
                            <label>Observación</label>
                            <textarea class="form-control" name="observation" rows="4">{{ old('observation') }}</textarea>
                        </div>
                </div>
            </div>
        </div>
                <div class="ibox-footer">
                    <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                    <a href="{{ url('cliente') }}" class="btn btn-sm btn-danger">Cancelar</a>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@section('layout_js')
<script>
    function changeDepartment()
    {
        var selected_depart = $('#departamento option:selected').val();
        $.ajax({
            url: '{{ route('ajax.get_deparment') }}',
            type: "GET",
            data: {departamento_id:selected_depart},
            success: function(data) {
                $('#ciudades').html('');
                $('#ciudades').append('<option value="">Selecciona una ciudad</option>');
                console.log(data);
                $.each(data,function (index,element)
                {
                    $('#ciudades').append('<option value="' + element.id + '">' + element.name + '</option>');   
                });
            }
        });
    }
</script>
@endsection
@section('layout_js')
