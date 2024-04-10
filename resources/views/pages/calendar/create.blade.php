@extends('layouts.AdminLTE.index')
@section('title', 'Reserva de turnos ')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Agregar Reserva de Turno</h5>
                @if(auth()->user()->can('clients.create-fast'))
                    <div class="ibox-tools">
                        <a href="javascript:;" onclick="modalClientCreate();" id="button_add_event" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Crear Cliente</a>
                    </div>
                @endif
            </div>
            {{ Form::open(['id' => 'form']) }}
                <div class="ibox-content">
                    @include('partials.messages')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Paciente</label>
                                        <select id="client_id" class="form-control" name="client_id">
                                            <option value="">Seleccione Cliente</option>
                                        </select>
                                        <span class="red" id="text_client_exist"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>Doctor</label>
                                    {{ Form::select('doctor_id', $doctors, request()->doctor_id, ['class' => 'form-control', 'select2', 'id' => 'doctor_id']) }}
                                </div>
                                {{-- <div class="form-group col-md-4">
                                    <label>Contrato</label>
                                    {{ Form::select('contract_id', [], old('contract_id', request()->contract_id), ['class' => 'form-control', 'select2', 'id' => 'contract_id']) }}
                                </div> --}}
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Consultorio</label>
                                        {{ Form::select('office_id', [], NULL, ['class' => 'form-control', 'select2', 'id' => 'office_id']) }}
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Paciente de</label>
                                        {{ Form::select('type_scheduling', config('constants.type-scheduling'), NULL, ['class' => 'form-control', 'select2', 'id' => 'type_scheduling']) }}
                                    </div>
                                </div> --}}
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="start" class="control-label">Comienzo</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" id="startCalendarButton">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" name="start" id="start" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end" class="control-label">Final</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" id="endCalendarButton">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" name="end" id="end" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Observación ATC <i>(lo ve solo ATC)</i></label>
                                    <textarea class="form-control" name="observation"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Observación Doctores <i>(lo ve ATC y Doctores)</i></label>
                                    <textarea class="form-control" name="observation_doctor"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                {{-- @permission('schedule-path.view')
                                    <div class="form-group col-md-4">
                                        {{ Form::select('scheduling_path', config('constants.scheduling-path'), request()->scheduling_path, ['placeholder' => 'Seleccione Via', 'class' => 'form-control', 'select2']) }}
                                    </div>
                                @endpermission --}}
                                <div class="form-group col-md-4">
                                    <label for="first_consultation"><input type="checkbox" value="1" id="first_consultation" name="first_consultation"> Primera Consulta</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="out_of_turn"><input type="checkbox" value="1" id="out_of_turn" name="out_of_turn"> Entre Paciente</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="urgency"><input type="checkbox" value="1" id="urgency" name="urgency"> Urgencia</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="prospect"><input type="checkbox" value="1" id="prospect" name="prospect"> Prospecto</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="div_client_detail">
                                <div id="div_new_patient"></div>
                                <div class="row mb-2">
                                    <div class="col-md-3 bold">Contratos:</div>
                                    <div class="col-md-9" id="div_client_contracts"></div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-3 bold">Ficha - Saldo:</div>
                                    <div class="col-md-9" id="div_saldo_ficha"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 bold">Ficha - A Favor:</div>
                                    <div class="col-md-9" id="div_saldo_favor"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Telefonos del Cliente</label>
                                        <table class="table table-condensed table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Teléfono</th>
                                                    <th>Obs</th>
                                                    <th class="text-center"><i class="fab fa-whatsapp"></i></th>
                                                    <th class="text-center">T</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_detail_phones"></tbody>
                                        </table>
                                        <br>
                                        <button type="button" class="btn btn-warning btn-xs white" onClick="client_add_phone();"><i class="fa fa-plus"></i> Agregar Telefono</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Observación del Odontólogo para la próxima cita.</label>
                                        <table class="table table-condensed table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Turno</th>
                                                    <th>Próxima Visita</th>
                                                    <th>Odontólogo</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_next_visits_list"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-footer">
                    <button type="button" onclick="openModal();" class="btn btn-sm btn-success" value="Guardar">Guardar</button>
                    <a href="{{ url('calendar-events?doctor_id=' . request()->doctor_id) }}" class="btn btn-sm btn-danger">Cancelar</a>
                </div>
                <div class="modal fade" id="myModal" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="modal_title">Confirmar Reserva de Turno</h4>
                            </div>
                            <div class="modal-body">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <th class="text-right" width="20%">Paciene:</th>
                                            <td width="10%"></td>
                                            <td class="text-left" width="70%" id="modal_client_full_name"></td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" width="20%">Odontólogo:</th>
                                            <td width="10%"></td>
                                            <td class="text-left" width="70%" id="modal_doctor_name"></td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" width="20%">Consultorio:</th>
                                            <td width="10%"></td>
                                            <td class="text-left" width="70%" id="modal_dental_office"></td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" width="20%">Fecha:</th>
                                            <td width="10%"></td>
                                            <td class="text-left" width="70%" id="modal_start_fecha" ></td>
                                        </tr>
                                        <tr>
                                            <th class="text-right" width="20%">Hora:</th>
                                            <td width="10%"></td>
                                            <td class="text-left" width="70%" id="modal_end_hour"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-sm btn-success" value="Confirmar">
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@section('layout_js')
<script type="text/javascript">
    $(document).ready(function(){
        $("#client_id").select2({
            language: 'es',
            minimumInputLength: 2,
            ajax: {
                url: '{{ url('ajax/clients') }}',
                dataType: 'json',
                // cache: true,
                method: 'GET',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.items
                    };
                }
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: function (repo) {
                if (repo.loading) return repo.text;

                var markup = repo.fullname + "<br>" +
                        "<i class='fa fa-id-card'></i> " + $.number(repo.document_number, 0, ',', '.');

                return markup;
            },
            templateSelection: function (repo) {
                return repo.text;
            }
        }).on("select2:select", function (e) {
        });

        $("#start, #end").datetimepicker({
            format: 'dd/mm/yyyy hh:ii',
            language: 'es',
            // minuteStep: 20,
            autoclose: true,
            todayBtn: true,
            // startDate: "{{ date('Y-m-d H') }}:00",
        }).on('changeDate', function(e){
            var minutes = moment(e.date).add(20, 'minutes').format('DD/MM/YYYY HH:mm');
            $('#end').val(minutes);
        });

        $("#startCalendarButton").click(function() {
            $("#start").datetimepicker("show");
        });

        $("#endCalendarButton").click(function() {
            $("#end").datetimepicker("show");
        });

        $('#form').submit(function(e)
        {
            $('input[type="submit"]').prop('disabled', true);
            e.preventDefault();
            $.ajax({
                url: '{{ url('calendar-store') }}',
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                   redirect('{{ url('calendar') }}?doctor_id=' + $('#doctor_id').val() + '&goToDate=' + $('#start').val());
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

        $("#client_id").on("change", function() {
            change_phone_client();
            getNextVisits($(this).val());
        });
    });

    var doctors_dental_office_js = {!! json_encode( $doctors_dental_office) !!};

    function client_add_phone()
    {
        var client_id = $('#client_id').val();
        modalAddClientPhone_open(client_id, 1);
    }

    change_phone_client();
    function change_phone_client()
    {
        $('#div_client_detail').hide();
        $('#contract_id').html('');
        var client_id = $("#client_id").val();

        if(client_id > 0)
        {
            $.ajax({
                url: '{{ route('ajax.clients') }}',
                type: "GET",
                data: { client_id : client_id, odontology_calendar_events_count: true },
                success: function(data) {
                    $('#tbody_detail_phones, #div_client_contracts, #div_saldo_favor, #div_saldo_ficha').html('');

                    $('#div_new_patient').html('');
                    if(data.odontology_calendar_events_count==0)
                    {
                        $('#div_new_patient').html('<h4 style="color:red">Es paciente de primera consulta</h4>');
                    }

                    var ficha_url = '<a target="_blank" href="{{ url('client-service-treatments') }}/' + client_id + '"><i class="fa fa-arrow-right"></i></a>';
                    $('#div_saldo_favor').html($.number(data.client_services_advances, 0, ',', '.') + ' ' + ficha_url);
                    $('#div_saldo_ficha').html($.number(data.client_services_debts, 0, ',', '.') + ' ' + ficha_url);

                    $(data.contracts).each(function(index, element) {
                        $('#div_client_contracts').append('<a target="_blank" href="{{ url('account-status') }}/' + element.contract_id + '"><span class="label label-' + element.enterprise_label + '">' + element.enterprise_abbreviation + ' &nbsp; ' + $.number(element.number, 0, ',', '.') + ' &nbsp; (' + element.insurance_name + ') &nbsp; ' + (element.is_active ? 'Activo' : 'Inactivo') + ' &nbsp; [' + element.contract_client_type_name + '] &nbsp; CV: ' + element.due_fees + '</span></a><br>');
                        if (element.enterprise_id == 1 || element.enterprise_id == 2)
                        {
                            $('#contract_id').append('<option data-enterprise-id="'+element.enterprise_id+'" data-usufruct="'+element.usufruct+'" value="'+ element.contract_id +'">'+ element.number +' ('+ element.insurance_name +')</option>')
                        }
                        if (element.enterprise_id == 10)
                        {
                            $('#contract_id').append('<option data-enterprise-id="'+element.enterprise_id+'" data-usufruct="'+element.usufruct+'" value="'+ element.contract_id +'">'+ element.number +' (ALIVIO)</option>');
                        }
                    });

                    $(data.phones).each(function(index, element) {
                        $('#tbody_detail_phones').append('<tr>' +
                            '<td>' + element.type_name +'</td>' +
                            '<td>' + element.number +'</td>' +
                            '<td>' + (element.observation ? element.observation : '') +'</td>' +
                            '<td class="text-center">' + (element.has_whatsapp ? '<i class="fa fa-check"></i>' : '') +'</td>' +
                            '<td class="text-center">' + (element.turns ? '<i class="fa fa-check"></i>' : '') +'</td>' +
                        '</tr>');
                    });

                    $('#div_client_detail').show();
                    changeContract();
                },
                error: function(data) {
                    laravelErrorMessages(data);
                }
            });
        }
    }

    change_doctor();
    function change_doctor()
    {
        $('#office_id').html('');
        $('#office_id').html(new Option('Seleccione consultorio', ''));

        var doctor_id = $("#doctor_id").val();

        for (var i = doctors_dental_office_js.length - 1; i >= 0; i--)
        {
            console.log(doctors_dental_office_js[i]);
            if (doctors_dental_office_js[i].id == doctor_id)
            {
                var dental_offices = doctors_dental_office_js[i].offices;
                for (var i = dental_offices.length - 1; i >= 0; i--) {
                    var option = new Option(dental_offices[i].name, dental_offices[i].id);
                    $('#office_id').append($(option));
                }
            }
        }
        $('#office_id').select2();
    }
    
    changeContract();
    function changeContract()
    {
        $('#type_scheduling').html(''); 
        let type_scheduling = @json(config('constants.type-scheduling'));
        let enterprise_id   = $('#contract_id option:selected').data('enterprise-id');
        let usufruct        = $('#contract_id option:selected').data('usufruct');
        $.each(type_scheduling,function(index,element){
            if (enterprise_id != undefined)
            {
                if ($.inArray(parseInt(index), [1,2,3]) != -1)
                {
                    if(usufruct == 0)
                    {
                        $('#type_scheduling').append('<option value="'+index+'">'+element+'</option>');
                    }
                    //SI ES ALIVIO
                    // else if (enterprise_id == 10 || enterprise_id == 17)
                    // {
                    //     if (index == 2)
                    //     {
                    //         $('#type_scheduling').append('<option value="'+index+'">'+element+'</option>');
                    //     }
                    // }
                    else if ($.inArray(enterprise_id),[2,5,10,17] != -1)
                    {
                        $('#type_scheduling').append('<option value="'+index+'">'+element+'</option>');
                    }
                    //SI NO TIENE PERMISO PARA VER ALIVIO
                    else
                    {
                        //si es distinto a alivio
                        if (index != 2)
                        {
                            $('#type_scheduling').append('<option value="'+index+'">'+element+'</option>');
                        }
                    }
                }
            }
            else
            {
                $('#type_scheduling').append('<option value="'+index+'">'+element+'</option>');
            }
        });
        $('#type_scheduling').trigger('change');
    }

    function openModal()
    {
        $('#myModal').modal('show');
        $('#modal_client_full_name').html($("select[name='client_id'] option:selected").text());
        $('#modal_doctor_name').html($("select[name='doctor_id'] option:selected").text());
        $('#modal_dental_office').html($("select[name='office_id'] option:selected").text());
        $('#modal_start_fecha').html($("input[name='start']").val().split(" ")[0] ? $("input[name='start']").val().split(" ")[0] : '-');
        $('#modal_end_hour').html(($("input[name='start']").val().split(" ")[1] ? $("input[name='start']").val().split(" ")[1]+' a '+$("input[name='end']").val().split(" ")[1] + ' hs.' :'-') );
    }

    function getNextVisits(client_id)
    {
        $('#tbody_next_visits_list').html('');
        $.ajax({
            url: '{{ route('ajax.calendar-events') }}',
            type: "GET",
            data: {
                status: 20,
                next_visit_not_null: true,
                client_id: client_id,
                get_last_event:1
            },
            success: function(data) {
                if (data.events)
                {
                    $(data.events).each(function( index, element ) {
                        $('#tbody_next_visits_list').append('<tr>' +
                                '<td class="text-center">'+element.dayOfWeek+'</td>'+
                                '<td>' + moment(element.start, 'YYYY-MM-DD HH:mm').format('DD/MM/YYYY HH:mm') + '</td>' +
                                '<td>' + element.next_visit + '</td>' +
                                '<td>' + element.doctor_fullname + '</td>' +
                            '</tr>');
                    });
                }
                else
                {
                    $('#tbody_next_visits_list').append('<tr><td colspan="4" class="text-center">NO HAY REGISTROS</td></tr>');
                }
            },
            error: function(data){
                laravelErrorMessages(data);
            }
        });
    }

</script>
@endsection