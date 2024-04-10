@extends('layouts.AdminLTE.index')
@section('title', 'Horario Doctores ')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Agregar Horario de Doctores</h5>
                    </div>
                    {{ Form::open(['id' => 'form']) }}
                        <div class="ibox-content">
                            @include('partials.messages')
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::select('doctor_id', $doctors, request()->doctor_id, ['class' => 'form-control', 'select2', 'id' => 'doctor_id']) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::select('office_id', $dental_offices, request()->office_id, [ 'placeholder' => 'Seleccione Clinica', 'class' => 'form-control', 'select2']) }}
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-md-2">
                                    <label>Dias</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Inicio de Jornada</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Fin de Jornada</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Inicio de Receso</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Fin de Receso</label>
                                </div>
                            </div>
                            @foreach(config('constants.day-week') as $key => $value)
                                @if($key != 7)
                                    <div class="row p-1">
                                        <div class="col-md-2">
                                            <input type="text" name="days[{{$key}}]" class="form-control" value="{{$value}}" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-control" type="time" name="work_start[{{$key}}]">
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-control" type="time" name="work_end[{{$key}}]">
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-control" type="time" name="break_start[{{$key}}]">
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-control" type="time" name="break_end[{{$key}}]">
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-success btn-xs" onclick="load_specialities({{$key}});"><i class="fa fa-plus"></i> Agregar Especialidades</a>
                                        </div>
                                    </div>
                                    <div class="row p-1 hide" id="div_day{{$key}}">
                                        <div class="col-md-12">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Especialidad</th>
                                                        <th>Desde</th>
                                                        <th>Hasta</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_specialities_{{$key}}"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div id="div_hours"></div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                            <a href="{{ url('doctors-schedule') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    <div class="modal fade" id="modal_specialities" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Especialidad</label>
                            {{ Form::select('speciality_id', [], null, ['class' => 'form-control', 'select2', 'id' => 'speciality_id', 'style' => 'width:100%']) }}
                        </div>
                        <div class="form-group col-md-3">
                            <label>Desde</label>
                            <input type="time" name="start_with_speciality" id="start_with_speciality" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Hasta</label>
                            <input type="time" name="end_with_speciality" id="end_with_speciality" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Agregar</label>
                            <a class="btn btn-success" onclick="addSpecilityToTable();"><i class="fa fa-plus"></i></a>
                        </div>
                        <input type="hidden" name="modal_day" id="modal_day">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Cerrar</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('layout_css')
<style>
    .select2-container--default.select2-container--open {
        border-color: #3c8dbc;
        z-index: 9999 !important;
    }
</style>
@endsection
@section('layout_js')
    <script>
        var day_index = 0;
        var doctor_specialities = @json($doctor_specialities);
        var specialities =@json($specialities);
        $(document).ready(function () {
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('doctors-schedule.store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                         redirect ("{{ url('doctors-schedule') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });
            });

            $("#doctor_id").on("change", function() {
                change_doctor();
            });
        });

        function load_specialities(day)
        {
            $('#modal_day').val(day);
            $('#modal_specialities').modal('show');

            $('#speciality_id').val('').trigger('change');
            $('#start_with_speciality').val('');
            $('#end_with_speciality').val('');

            $('#speciality_id').each(function(){
                var $p = $(this).parent();
                $(this).select2({  
                    dropdownParent: $p  
                });  
            });
        }

        function addSpecilityToTable()
        {
            var speciality_name = $('#speciality_id option:selected').text();
            var speciality_id = $('#speciality_id').val();
            var start_with_speciality = $('#start_with_speciality').val();
            var end_with_speciality = $('#end_with_speciality').val();
            var modal_day = $('#modal_day').val();

            var exists = false;
            $('input[name^="specialities_ids[]"]').each(function(){
                if(speciality_id == $(this).val()) 
                {
                    exists = true;
                }
            });

            if(speciality_id != '' && start_with_speciality != '' && end_with_speciality != '' && !exists) 
            {
                $('#tbody_specialities_'+modal_day).append(
                    '<tr>' +  
                        '<td>'+speciality_name+'</td>' +
                        '<td>'+start_with_speciality+'</td>' +
                        '<td>'+end_with_speciality+'</td>' +
                        '<td class="text-right"><a href="javascript:;" onClick="removeRow(this);"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                        '<input type="hidden" name="specialities_ids'+modal_day+'[]" value="'+speciality_id+'">'+
                        '<input type="hidden" name="start_with_specialities'+modal_day+'['+speciality_id+']" value="'+start_with_speciality+'">'+
                        '<input type="hidden" name="end_with_specialities'+modal_day+'['+speciality_id+']" value="'+end_with_speciality+'">'+
                    '</tr>');

                $('#div_day'+modal_day).removeClass('hide');

                toastr.success('Agregado exitosamente', '', {
                            progressBar: true,
                            timeOut: 4000,
                        });

                $('#speciality_id').val('').trigger('change');
                $('#start_with_speciality').val('');
                $('#end_with_speciality').val('');
            }

            if(exists) 
            {
                alert('La especialidad ya ha sido cargada.');
            }

            if(speciality_id == '' && start_with_speciality == '' && end_with_speciality == '' && !exists)
            {
                alert('Hay campos vacios.');
            }
        }

        function removeRow(t)
        {
            $(t).parent().parent().remove();
        }

        change_doctor();
        function change_doctor()
        {
            $('#speciality_id').html('');
            $('#speciality_id').html(new Option('Seleccione Especialidad', ''));

            var doctor_id = $("#doctor_id").val();

            $.each(doctor_specialities, function(index, element){
                if(element.doctor_id == doctor_id) 
                {
                    $('#speciality_id').append('<option value="'+element.especiality_id+'">'+specialities[element.especiality_id]+'</option>');
                }
            })
        }

    </script>
   
@endsection