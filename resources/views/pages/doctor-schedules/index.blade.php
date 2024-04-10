@extends('layouts.AdminLTE.index')
@section('title', 'Horario Doctores')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Horario de Doctores</h5>
                        <div class="ibox-tools">
                                <a class="btn btn-primary btn-xs" href="{{ url('doctors-schedule/create') }}"><i class="fa fa-plus"></i> Agregar</a>
                            @if(request()->query())
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                            Exportar <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('doctors-schedule.xlsx', request()->query()) }}"><i class="fa fa-file-excel"></i> XLSX</a></li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content pb-0">
                        <div class="row">
                            <form method="GET">
                                <div class="form-group col-sm-4">
                                    {{ Form::select('doctor_id', $doctors, request()->doctor_id, ['class' => 'form-control', 'select2', 'placeholder' => 'Seleccione doctor']) }}
                                </div>
                                <div class="form-group col-sm-3">
                                    {{ Form::select('office_id', $dental_offices, request()->office_id, ['placeholder' => 'Seleccione clínica', 'class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-sm-3">
                                    {{ Form::select('day', config('constants.day-week'), request()->day, ['placeholder' => 'Seleccione Dia', 'class' => 'form-control', 'select2']) }}
                                </div>
                                <div class="form-group col-sm-2">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                    @if(request()->query())
                                        <a href="{{ url('doctors-schedule') }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    @include('partials.messages')
                    <div class="ibox-content table-responsive no-padding">
                        <table  class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Dias</th>
                                    <th>Doctor</th>
                                    <th>Especialidad</th>
                                    <th>Clinica</th>
                                    <th>Inicio de Jornada</th>
                                    <th>Fin de Jornada</th>
                                    <th>Inicio de receso</th>
                                    <th>Fin de receso</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctor_schedules as $doctor_schedule)
                                    <tr>
                                        <td>{{ config('constants.day-week.' . $doctor_schedule->days) }}</td>
                                        <td>{{ $doctor_schedule->doctor->fullname }}</td>
                                        <td>{{ $doctor_schedule->details->first()->speciality->name }}</td>
                                        <td>{{ $doctor_schedule->office->name }}</td>
                                        <td>{{ Carbon\Carbon::parse($doctor_schedule->work_start)->format('H:i') }}</td>
                                        <td>{{ Carbon\Carbon::parse($doctor_schedule->work_end)->format('H:i') }}</td>
                                        <td>{{ $doctor_schedule->break_start ? Carbon\Carbon::parse($doctor_schedule->break_start)->format('H:i') : NUll }}</td>
                                        <td>{{ $doctor_schedule->break_end ? Carbon\Carbon::parse($doctor_schedule->break_end)->format('H:i') : NULL }}</td>
                                        <td><span class="label label-{{ config('constants.status-label.' . $doctor_schedule->doctor->status) }}">{{ config('constants.status.' . $doctor_schedule->doctor->status) }}<span></td>
                                            <td class="text-right">
                                                <a href="{{ url('doctors-schedule/' . $doctor_schedule->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $doctor_schedules->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
@endsection