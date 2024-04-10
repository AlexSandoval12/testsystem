@extends('layouts.sistema')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Editar Horarios del Doctor</h5>
                    </div>
                    {{ Form::open(['route' => ['doctors-schedule.update', $doctor_schedule->id], 'method' => 'PUT']) }}
                        <div class="ibox-content">
                            @include('partials.messages')
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Doctor:</label>
                                    <input type="text" value="{{ $doctor_schedule->doctor->fullname }}" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Oficina:</label>
                                    {{ Form::select('office_id', $dental_offices, $doctor_schedule->office_id, ['class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Inicio de Jornada:</label>
                                    <input type="time" value="{{ $doctor_schedule->work_start->format('H:i') }}" name="work_start" class="form-control">
                                </div> 
                                <div class="form-group col-md-2">
                                    <label>Fin de Jornada:</label>
                                    <input type="time" value="{{ $doctor_schedule->work_end->format('H:i') }}" name="work_end" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Inicio de Receso:</label>
                                    <input type="time" value="{{ $doctor_schedule->break_start ? $doctor_schedule->break_start->format('H:i') : '' }}" name="break_start" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Fin de Receso:</label>
                                    <input type="time" value="{{ $doctor_schedule->break_end ? $doctor_schedule->break_end->format('H:i') : '' }}" name="break_end" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Dia:</label>
                                    {{ Form::select('day', config('constants.day-week'), $doctor_schedule->days, ['class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Estado:</label>
                                    {{ Form::select('status', config('constants.status'), old('status', $doctor_schedule->status), ['class' => 'form-control', 'select2'] )}}
                                </div>
                            </div>
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
                                    <label>Agregar</label> <br>
                                    <a class="btn btn-success" onclick="addSpecilityToTable();"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <div class="row p-1 {{count($doctor_schedule->details) > 0 ? '' : 'hide'}}" id="div_day">
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
                                        <tbody id="tbody_specialities">
                                            @foreach($doctor_schedule->details as $detail)
                                                <tr>
                                                    <td>{{$detail->speciality->name}}</td>
                                                    <td>{{Carbon\Carbon::parse($detail->from_time)->format('H:i') }}</td>
                                                    <td>{{Carbon\Carbon::parse($detail->until_time)->format('H:i') }}</td>
                                                    <td class="text-right"><a href="javascript:;" onClick="removeRow(this);"><i style="font-size:17px;" class="fa fa-times"></i></a></td>
                                                    <input type="hidden" name="specialities_ids[]" value="{{ $detail->speciality_id }}">
                                                    <input type="hidden" name="start_with_specialities[{{ $detail->speciality_id }}]" value="{{ $detail->from_time }}">
                                                    <input type="hidden" name="end_with_specialities[{{ $detail->speciality_id }}]" value="{{ $detail->until_time }}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Editar">
                            <a href="{{ url('doctors-schedule') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <script>
        var day_index = 0;
        var doctor_specialities = @json($doctor_specialities);
        var specialities =@json($specialities);

        function addSpecilityToTable()
        {
            var speciality_name = $('#speciality_id option:selected').text();
            var speciality_id = $('#speciality_id').val();
            var start_with_speciality = $('#start_with_speciality').val();
            var end_with_speciality = $('#end_with_speciality').val();

            var exists = false;
            $('input[name^="specialities_ids[]"]').each(function(){
                if(speciality_id == $(this).val()) 
                {
                    exists = true;
                }
            });

            if(speciality_id != '' && start_with_speciality != '' && end_with_speciality != '' && !exists) 
            {
                $('#tbody_specialities').append(
                    '<tr>' +  
                        '<td>'+speciality_name+'</td>' +
                        '<td>'+start_with_speciality+'</td>' +
                        '<td>'+end_with_speciality+'</td>' +
                        '<td class="text-right"><a href="javascript:;" onClick="removeRow(this);"><i style="font-size:17px;" class="fa fa-times"></i></a></td>' +
                        '<input type="hidden" name="specialities_ids[]" value="'+speciality_id+'">'+
                        '<input type="hidden" name="start_with_specialities['+speciality_id+']" value="'+start_with_speciality+':00">'+
                        '<input type="hidden" name="end_with_specialities['+speciality_id+']" value="'+end_with_speciality+':00">'+
                    '</tr>');

                $('#div_day').removeClass('hide');

                // toastr.success('Agregado exitosamente', '', {
                //             progressBar: true,
                //             timeOut: 4000,
                //         });

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

            var doctor_id = {{ $doctor_schedule->doctor_id }};

            $.each(doctor_specialities, function(index, element){
                if(element.doctor_id == doctor_id) 
                {
                    $('#speciality_id').append('<option value="'+element.especiality_id+'">'+specialities[element.especiality_id]+'</option>');
                }
            })
        }

    </script>
@endsection
