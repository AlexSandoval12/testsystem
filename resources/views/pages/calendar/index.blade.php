@extends('layouts.AdminLTE.index')
@section('title', 'Reserva de turnos ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Odontología - Reserva de Turnos</h5>
                <div class="ibox-tools">
                    {{-- <a class="btn btn-success btn-xs mr-1 white" href="javascript:;" id="button_refresh" onClick="refreshCalendar();">
                        <i class="fa fa-sync"></i>
                    </a> --}}
                    {{ Form::select('doctor_id', $doctors, request()->doctor_id, ['id' => 'doctor_id']) }}
                        <a href="{{ url('calendar/create') }}" id="button_add_event" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                        {{-- <a href="{{ url('calendar-events/create-scheduling') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agendar</a> --}}
                </div>
            </div>
            <div class="ibox-content table-responsive">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Detalle Reserva</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-3"><b>Cliente:</b></div>
                    <div class="col-xs-9" id="modal_text_client_name">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Cédula:</b></div>
                    <div class="col-xs-9" id="modal_text_client_document_number">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Teléfono/s:</b></div>
                    <div class="col-xs-9">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Teléfono</th>
                                    <th>Obs</th>
                                    <th class="text-center"><i class="fab fa-whatsapp"></i></th>
                                    <th class="text-center">T</th>
                                </tr>
                            </thead>
                            <tbody id="modal_tbody_client_phones"></tbody>
                            @permission('clients.edit')
                                <tfoot>
                                    <tr>
                                        <td colspan="3" id="modal_button_add_phone"></td>
                                        <td colspan="3" id="modal_button_edit_phone"></td>
                                    </tr>
                                </tfoot>
                            @endpermission
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3"><b>Contratos:</b></div>
                    <div class="col-xs-9" id="modal_text_contracts">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Comienzo:</b></div>
                    <div class="col-xs-9" id="modal_text_start">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Final:</b></div>
                    <div class="col-xs-9" id="modal_text_end">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Consultorio:</b></div>
                    <div class="col-xs-9" id="modal_dental_office">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Estado:</b></div>
                    <div class="col-xs-9" id="modal_text_status">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Reservado por:</b></div>
                    <div class="col-xs-9" id="modal_text_user_name">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Observación ATC:</b></div>
                    <div class="col-xs-9" id="modal_text_observation">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Observación Dr:</b></div>
                    <div class="col-xs-9" id="modal_text_observation_doctor">cargando...</div>
                </div>
                <div class="row mt-1">
                    <div class="col-xs-3"><b>Tipo de Cliente</b></div>
                    <div class="col-xs-9" id="modal_text_type_scheduling">cargando...</div>
                </div>
            </div>
            <div class="modal-footer">
                    {{-- <a class="btn btn-default" id="modal_confirm_link" href="javascript:;" onclick="return confirm('¿Desea confirmar la reserva?');"><i class="fa fa-check"></i> Confirmar</a>
                    <a class="btn btn-change" id="modal_change_link" href="javascript:;" onclick="return confirm('¿Desea cancelar la reserva?');"><i class="fa fa-sync"></i> Reagendar</a>
                    <a class="btn btn-danger" id="modal_cancel_link" href="javascript:;" onclick="show_modal();"><i class="fa fa-times"></i> Cancelar</a>
                    <a class="btn btn-warning" id="modal_edit_link" href="javascript:;"><i class="fa fa-pencil-alt"></i> Editar</a> --}}
                    {{-- <a class="btn btn-warning" id="modal_edit_scheduling_link" href="javascript:;"><i class="fa fa-pencil-alt"></i> Editar Agendamiento</a> --}}
                <button data-dismiss="modal" class="btn btn-default" type="button"><i class="fa fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('layout_css')
    <style type="text/css">
        .select2-selection {
            text-align: left;
            font-size: 10px;
        }

        .select2-container {
            vertical-align: top;
        }

        .select2-container .select2-selection--single {
            height: 24px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 22px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 22px;
        }
    </style>
@endsection

@section('layout_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                allDaySlot: false,
                defaultView: 'agendaWeek',
                slotDuration: '00:10:00',
                slotLabelInterval: 10,
                slotLabelFormat: 'H:mm',
                minTime: '07:00:00',
                maxTime: '22:00:00',
                timeFormat: 'H:mm',
                hiddenDays: [ 0 ],
                events: {
                    url: '{{ route('ajax.calendar-events') }}',
                    data: function () {
                        return {
                            doctor_id: $('#doctor_id').val(),
                        };
                    },
                    success: function(response) {
                        console.log(response);
                        return response.events;
                    }
                },
                eventAfterRender: function (event, element, view) {
                    if(event.out_of_turn==1)
                    {
                        element.css('background', 'repeating-linear-gradient(45deg, ' + event.color + ', ' + event.color + ' 30px, #000000 30px, #000000 31px)');
                    }
                },
                eventClick: function(event, jsEvent, view) {
                    $('#modal_text_start').html(event.start.format('DD/MM/Y HH:mm') + (event.first_consultation ? ' <label class="label label-warning fs-1">Primera Consulta</label>' : '') + (event.out_of_turn ? ' <label class="label label-out fs-1">Entre Paciente</label>' : '') + (event.urgency ? ' <label class="label label-danger fs-1">URGENCIA</label>' : '') + (event.prospect ? ' <label class="label label-default fs-1">PROSPECTO</label>' : ''));
                    $('#modal_text_end').html(event.end.format('DD/MM/Y HH:mm'));
                    $('#modal_dental_office').html(event.dental_office);
                    $('#modal_text_status').html('<span class="badge badge-' + event.status_label + '">' + event.status_name + '</span>');
                    $('#modal_text_user_name').html(event.user_name+' ('+ event.created_at +')');
                    $('#modal_text_client_name').html(event.title);
                    $('#modal_text_client_document_number').html($.number(event.client_document_number, 0, ',', '.'));
                    $('#modal_button_add_phone').html('<button type="button" class="btn btn-warning btn-xs white mb-0 fs-2" onclick="modalAddClientPhone_open(' + event.client_id + ', 4);"><i class="fa fa-plus"></i>  Agregar Teléfono</button>');
                    $('#modal_button_edit_phone').html('<a type="button" href="{{ url('clients')}}/'+ event.client_id +'/edit" target="_blank" class="btn btn-warning btn-xs white mb-0 fs-2"> <i class="fa fa-pencil"></i> Editar Teléfono</a>');

                    $('#modal_tbody_client_phones').html('');
                    var phones_counter = 1;
                    $(event.phones).each(function(index, element) {
                        $('#modal_tbody_client_phones').append('<tr>' +
                                '<td>' + phones_counter + '</td>' +
                                '<td>' + element.type_name +'</td>' +
                                '<td>' + element.number +'</td>' +
                                '<td>' + (element.observation ? element.observation : '') +'</td>' +
                                '<td class="text-center">' + (element.has_whatsapp ? '<i class="fa fa-check"></i>' : '') +'</td>' +
                                '<td class="text-center">' + (element.turns ? '<i class="fa fa-check"></i>' : '') +'</td>' +
                            '</tr>');
                        phones_counter++;
                    });

                    var constants_type_scheduling = @json(config('constants.type-scheduling'));

                    $('#modal_text_contracts').html(event.contracts_html);
                    $('#modal_text_observation').html(event.observation);
                    $('#modal_text_observation_doctor').html(event.observation_doctor);
                    $('#modal_text_type_scheduling').html(constants_type_scheduling[event.type_scheduling_id]);
                    $('#modal_edit_link').attr("href", '{{ url('calendar-events') }}/' + event.id + '/edit');
                    $('#modal_edit_scheduling_link').attr("href", '{{ url('calendar-events') }}/' + event.id + '/scheduling-edit');

                    $('#modal_change_link').attr("onclick", "return confirm('¿Desea reagendar la reserva?') ? reschedule(" + event.id + ") : false;");
                    $('#modal_cancel_link').attr("onclick", "return confirm('¿Desea cancelar la reserva?') ? cancel_turn(" + event.id + ") : false;");
                    $('#modal_confirm_link').attr("onclick", "return confirm('¿Desea confirmar la reserva?') ? changeStatus(" + event.id + ", 5) : false;");
                    /* $('#modal_cancel_link').attr("onclick", "return confirm('¿Desea cancelar la reserva?') ? changeStatus(" + event.id + ", 25) : false;"); */
                    /* $('#modal_cancel_link').attr("onclick", "return confirm('¿Desea cancelar la reserva?') ? changeStatus(" + event.id + ", 25) : false;"); */

                    if(event.status==1)
                    {
                        $('#modal_confirm_link').show();
                    }
                    else
                    {
                        $('#modal_confirm_link').hide();
                    }

                    if(event.status == 3 || event.status == 20 || event.status == 23 || event.status == 25)
                    {
                        $('#modal_change_link').hide();
                        $('#modal_cancel_link').hide();
                        $('#modal_edit_link').hide();
                        $('#modal_edit_scheduling_link').hide();
                    }
                    else
                    {
                        $('#modal_change_link').show();
                        $('#modal_cancel_link').show();
                        $('#modal_edit_link').show();
                        $('#modal_edit_scheduling_link').show();
                    }

                    /* if(event.status!=25)
                    {
                        $('#modal_cancel').show();
                    }
                    else
                    {
                        $('#modal_cancel').hide();
                    } */

                    /* if(event.status!=23)
                    {
                        $('#modal_change_link').show();
                    }
                    else
                    {
                        $('#modal_change_link').hide();
                    } */

                    $('#modal_event').modal('show');
                }
            });

            $("#doctor_id").select2({
                language: 'es',
                width: '250px'
            }).on("select2:select", function (e) {
                refreshCalendar();
                changeCreateLink();
            });

            // setInterval(function(){
            //     refreshCalendar();
            // }, 10000);

            @if(request()->goToDate)
                var goToDate = moment("{{ request()->goToDate }}", "DD/MM/YYYY HH:mm");
                $("#calendar").fullCalendar('gotoDate', goToDate);
            @endif
        });

        // changeCreateLink();
        // function changeCreateLink()
        // {
        //     var doctor_id = $('#doctor_id').val();
        //     $('#button_add_event').attr("href", '{{ url('calendar-events') }}/create?doctor_id=' + doctor_id);
        // }


        // function changeStatus(id, status, rollback)
        // {
        //     $.ajax({
        //         url: "{{ url('ajax/calendar-events-change-status') }}/" + id,
        //         type: "POST",
        //         data: { status: status, rollback: rollback },
        //         success: function(data) {
        //             refreshCalendar();
        //         },
        //         error: function(data){
        //             laravelErrorMessages(data);
        //         }
        //     });
        // }
    </script>
@endsection