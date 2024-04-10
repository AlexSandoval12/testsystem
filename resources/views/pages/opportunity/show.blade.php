@extends('layouts.AdminLTE.index')
@section('title', 'Oportunidad ')
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Oportunidad de Venta</h5>
                <div class="ibox-tools">
                    <a href="{{ url('opportunity') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>

                            <a href="{{ url('sales-opportunity-seller/' . $opportunity->id . '/edit') }}"  class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i> Editar</a>

                        @if($call_center_login)
                            <button type="button" onclick="call();" class="btn btn-info btn-xs"><i class="fas fa-phone" title="Volver a llamar"></i> REMARCAR</button>
                            <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal_Pausa"><i class="fas fa-pause-circle" title="Pausar Llamada"></i> PAUSA</button>
                        @endif
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-4"><b>ID</b></div>
                    <div class="col-md-8">{{ $opportunity->id }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><b>Medio</b></div>
                    <div class="col-md-8">{{ $opportunity->contact_medium->name }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><b>Nombre</b></div>
                    <div class="col-md-8">{{ $opportunity->fullname }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4"><b>Teléfono</b></div>
                    <div class="col-md-8">{{ $opportunity->phone or '-' }}</div>
                </div>
                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                    <div class="row">
                        <div class="col-md-4"><b>Email</b></div>
                        <div class="col-md-8">{{ $opportunity->email or '-' }}</div>
                    </div>
                @endif
                @if($opportunity->enterprise_id != 3 && $opportunity->enterprise_id != 6 && (!auth()->user()->can('denpro-seller-opportunities.create')))
                    <div class="row">
                        <div class="col-md-4"><b>Nro.Documento</b></div>
                        <div class="col-md-8">{{ $opportunity->document_number ? number_format($opportunity->document_number, 0, ',', '.') : '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Tipo de Plan</b></div>
                        <div class="col-md-8">{{ $opportunity->type_plan ? config('constants.type_plan.' . $opportunity->type_plan) : '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Monto Seguro</b></div>
                        <div class="col-md-8">{{ $opportunity->amount ? number_format($opportunity->amount, 0, ',', '.') : '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Tipo de Contrato</b></div>
                        <div class="col-md-8">{{ $opportunity->contract_type ? config('constants.contract_type.' . $opportunity->contract_type) : '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><b>Lead</b></div>
                        <div class="col-md-8">{{ $opportunity->lead or '-' }}</div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-4"><b>Gestiones</b></div>
                    <div class="col-md-8">{{ $opportunity->trackings()->where('status', 1)->count() }}</div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-4"><b>Estado</b></div>
                    @if($opportunity->closed_in == 2 && $opportunity->status == 10)
                        <div class="col-md-8"><span class="label label-{{ config('constants.sale-closing-type-label.' . $opportunity->closed_in) }}">{{ config('constants.sale-closing-type.' . $opportunity->closed_in) }}</span></div>
                    @else
                        <div class="col-md-8"><span class="label label-{{ config('constants.sales-opportunity-status-label.' . $opportunity->status) }}">{{ config('constants.sales-opportunity-status.' . $opportunity->status) }}</span></div>
                    @endif
                </div>
                @if($opportunity->status == 20)
                    <div class="row">
                        <div class="col-md-4"><b>Motivo de Rechazo</b></div>
                        <div class="col-md-8">{{ $opportunity->rejected_motive ? $opportunity->rejected_motive->name : '' }}</div>
                    </div>
                @endif
                <div class="row mt-1">
                    <div class="col-md-4"><b>Creado Por</b></div>
                    <div class="col-md-8">{{ $opportunity->user->fullname }} <i>({{ $opportunity->created_at->format('d/m/Y H:i:s') }})</i></div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-4"><b>Vendedor</b></div>
                    <div class="col-md-8">{{ $opportunity->seller->fullname }}</div>
                </div>

               

                @if($opportunity->closer)
                    <div class="row">
                        <div class="col-md-4"><b>Cerrador</b></div>
                        <div class="col-md-8">{{ $opportunity->closer->fullname }}</div>
                    </div>
                @endif
                <div class="row mt-1">
                    <div class="col-md-4"><b>Observación</b></div>
                    <div class="col-md-8">{{ $opportunity->observation }}</div>
                </div>
            </div>
        </div>
        {{-- @if($opportunity->dental_budget)
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Informacion del Presupuesto</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover table-striped mb-0 table-condensed">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Doctor</th>
                                        <th>Consultorio</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $opportunity->dental_budget->date->format('d/m/Y') }}</td>
                                        <td>{{ $opportunity->dental_budget->doctor ? $opportunity->dental_budget->doctor->fullname : '' }}</td>
                                        <td>{{ $opportunity->dental_budget->dental_office ? $opportunity->dental_budget->dental_office->name : '' }}</td>
                                        <td>Gs.{{ number_format($opportunity->dental_budget->total, 0, ',', '.') }}</td>
                                        <td><a target="_blank" href="{{ url('dental-budgets/' . $opportunity->dental_budget_id) }}"><i class="fa fa-info-circle"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4"><b>Telefonos:</b></div>
                        @if($opportunity->dental_budget->client->phones()->count() > 0)
                            @foreach($opportunity->dental_budget->client->phones->sortBy('created_at') as $phone)
                                <div class="col-md-8">-{{ $phone->number }}</div><br>
                                <div class="col-md-4"></div>
                            @endforeach
                        @else
                            <div class="col-md-8 text-left">-</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif --}}
        {{-- @if($opportunity->crm_products->count() > 0)
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Productos aceptados</h5>
                    @if(auth()->user()->can('sales-opportunity-seller.add-product'))
                        <div class="text-right">
                            <button class="btn btn-primary btn-xs" id="button_add_product" onclick="show_modal();"> Agregar Producto</button>
                        </div>
                    @endif
                </div>
                <div class="ibox-content">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opportunity_products as $product)
                                @if($product->sold == 1)
                                    <tr>
                                        <td>{{ $product->additional_service->service }}
                                            @if($product->paramedic)
                                               &nbsp;&nbsp;<a href="#" data-toggle="tooltip" data-placement="top" title="Cobertura de Paramédico"><i class="fa fa-user-md" style="font-size: 20px;"></i></a>
                                            @endif
                                            @if($product->ambulance)
                                               &nbsp;&nbsp;<a href="#" data-toggle="tooltip" data-placement="top" title="Ambulancia"><i class="fa fa-ambulance" style="font-size: 20px;"></i></a>
                                            @endif
                                        </td>
                                        <td>{{ number_format($product->amount,0,',','.') }}</td>
                                        <td>{{ $product->observation }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}
        @php
            $confirm = true;
                //SI ES VENDEDOR CALL ODONTOLOGIA
               
        @endphp
        @if($confirm)
            <!-- VISTA CALL CENTER -->
                {{ Form::open(['id' => 'form']) }}
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Contacto</label>
                                    {{ Form::select('attended', config('constants.attended'), NULL, ['placeholder' => 'Seleccionar...', 'select2', 'class' => 'col-md-12', 'id' => 'attended']) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Medio</label>
                                    {{ Form::select('contact_form', config('constants.opportunity-contact-form'), NULL, ['placeholder' => 'Seleccionar...', 'select2', 'class' => 'col-md-12', 'id' => 'contact_form']) }}
                                </div>
                            </div>
                            <div class="row" id="div_motive">
                                <div class="col-md-12 form-group">
                                    <label>Motivo de Contacto Negativo</label>
                                    {{ form::select('not_attended', config('constants.not_attended'), NULL, ['placeholder' => 'Seleccionar...', 'select2',  'class' => 'col-md-12', 'id' => 'not_attended']) }}
                                </div>
                            </div>
                            <div class="row" id="div_dates">
                                <div class="col-md-12 form-group">
                                    <label>Volver a Llamar</label>
                                    <input type="text" name="call_again" id="call_again" class="form-control" date-mask>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-12 form-group">
                                    <label>Cliente Agendado</label>
                                    {{ Form::select('scheduled', config('constants.yes-no'), request()->scheduled, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) }}
                                </div>
                            </div>
                            <div class="row" id="div_radio" style="display:none;">
                                @if($opportunity->status < 10)
                                    {{-- <div class="col-md-6 form-group">
                                        <label><input type="radio" name="radio" value="1"> Asignar a Cerrador de Venta</label>
                                    </div> --}}
                                    <div class="col-md-6 form-group">
                                        <label><input type="radio" name="radio" value="4"> Cierre en Sucursal</label>
                                    </div>
                                @elseif($opportunity->status == 10 || $opportunity->status == 13)
                                    <div class="col-md-6 form-group">
                                        <label><input type="radio" name="radio" value="2"> Vendido</label>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label><input type="radio" name="radio" value="3"> Rechazar</label>
                                    </div>
                                @endif
                                @if($opportunity->status < 10)
                                    <div class="col-md-6 form-group">
                                        <label><input type="radio" name="radio" value="3"> Rechazar</label>
                                    </div>
                                @endif
                            </div>
                            <div class="row" id="div_checkbox" style="display:none;">
                                <div class="col-md-6 form-group">
                                    <label><input type="checkbox" name="reject" value="1" id="reject_checkbox"> Rechazar</label>
                                </div>
                            </div>
                            <div class="row" id="div_rejected_motives" style="display:none;">
                                <div class="col-md-12 form-group">
                                    <label>Motivo de Rechazo</label>
                                    {{ form::select('rejected_motive_id', config('constants.rejected_motive'), NULL, ['placeholder' => 'Seleccionar...', 'select2',  'class' => 'col-md-12', 'id' => 'rejected_motive_id']) }}
                                </div>
                            </div>
                            <div class="row" id="div_cities" style="display:none;">
                                <div class="col-md-12 form-group">
                                    <label>Ciudad de cierre</label>
                                    {{ form::select('city_id', $cities, NULL, ['select2',  'class' => 'col-md-12', 'id' => 'city_id']) }}
                                </div>
                            </div>
                            <div class="row" id="div_address_zone" style="display:none;">
                                <div class="col-md-12 form-group">
                                    <label>Dirección/Zona</label>
                                    <input type="text" name="address" class="form-control">
                                </div>
                            </div>
                            <div class="row" id="div_deadline" style="display:none;">
                                <div class="col-md-12 form-group">
                                    <label>Fecha de cierre</label>
                                    <input type="text" name="deadline" id="deadline" class="form-control" date-mask>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Observación</label>
                                    <textarea class="form-control" name="observation" id="observation"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                        </div>
                    </div>
                {{ Form::close() }}
           

                {{ Form::open(['id' => 'form_files', 'files' => true]) }}
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Agregar Archivo</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Archivos</label>
                                    <input type="file" class="form-control" name="files[]" multiple>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" name="description">
                                </div>
                            </div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Subir">
                        </div>
                    </div>
                {{ Form::close() }}
        @endif
    </div>
    <div class="col-md-7">
        <div class="ibox float-e-margins">
            @foreach($opportunity->trackings->sortByDesc('created_at') as $tracking)
                <div class="panel panel-{{ $tracking->attended==1 ? 'primary' : ($tracking->attended==2 ? 'danger' : 'info') }}">
                    <div class="panel-heading">
                        <h4 class="my-0">
                            @if($tracking->reassigned == 1)
                                REASIGNADO
                            @endif
                            <i class="fa fa-calendar"></i> {{ $tracking->created_at->format('d/m/Y H:i:s') }} &nbsp;
                            <i class="fa fa-user"></i> {{ $tracking->user->fullname }} &nbsp;
                            @if($tracking->contact_form==1)
                                <i class="fab fa-whatsapp"></i>
                            @elseif($tracking->contact_form==2)
                                <i class="fa fa-mobile-alt"></i>
                            @elseif($tracking->contact_form==3)
                                <i class="fa fa-phone"></i>
                            @elseif($tracking->contact_form==4)
                                <i class="fa fa-user"></i>
                            @endif
                            {{-- @permission('recovery-tracking.delete')
                                <a href="{{ route('recovery-tracking.delete', $tracking->id) }}" class="white"><i class="fa fa-trash pull-right"></i></a>
                            @endpermission
                            @permission('recovery-tracking.edit')
                                <a href="{{ route('recovery-tracking.edit', $tracking->id) }}" class="white"><i class="fa fa-pencil pull-right"></i></a>
                            @endpermission --}}
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                {!! $tracking->attended==2 ? '<b>Motivo de Contacto Negativo:</b> ' . config('constants.not_attended.' . $tracking->not_attended) . '<br>' : '' !!}
                                {!! $tracking->action ? '<b>Acción:</b> ' . config('constants.sales-opportunity-actions.' .$tracking->action)  . '<br>' : '' !!}
                                {!! $tracking->call_again ? '<b>Volver a Llamar:</b> ' . $tracking->call_again->format('d/m/Y')  . '<br>' : '' !!}
                                {!! $tracking->closer ? '<b><i class="fa fa-lock"></i> Asignado a Cerrador</b><br>' : '' !!}
                                {!! $tracking->sold ? '<b><i class="fa fa-handshake"></i> Vendido</b><br>' : '' !!}
                                {!! $tracking->reject ? '<b><i class="fa fa-times"></i> Rechazado</b><br>' : '' !!}
                                {!! $tracking->scheduled ? '<b><i class="fa fa-calendar"></i> Agendado</b><br>' : '' !!}
                                {{ $tracking->observation }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
{{-- {{ Form::open(['route' => 'call-center-pause']) }}
    <div class="modal fade" id="myModal_Pausa" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modal_title">REGISTRO DE PAUSAS</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Motivo</label>
                            {{ Form::select('pause_motive_id', $pause_motives, NULL, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <span id="span_buttons_treatment">
                        <button type="submit" class="btn btn-success disable_button" onclick="submitModalSoloConsulta();">Grabar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }} --}}

@endsection
@section('layout_css')
    <style>
        table {
            line-height: 40px;                   
        }

        td {
            padding-right: 10px;
        }
        @if(in_array($opportunity->enterprise_id,[3,6,7]))
            .select2-container {
                z-index:100000;
            }
        @endif
    </style>
@endsection
@section('layout_js')
@if($confirm)
        <script>
            var call_center_call_id = 0;
            var services_items_array = [];

            $(document).ready(function () {
                autosize($('textarea'));
                $('#form').submit(function(e)
                {
                    $('input[type="submit"]').prop('disabled', true);
                    e.preventDefault();
                    $.ajax({
                        url: '{{ route('opportunity-trackings.store', $opportunity->id) }}',
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(data) {
                            redirect("{{ url('opportunity-seller') }}");
                        },
                        error: function(data){
                            laravelErrorMessages(data);
                            $('input[type="submit"]').prop('disabled', false);
                        },
                    });
                });

                $('#reject_checkbox').change(function(){
                    onChangeDivRejectedMotives();
                });

                $("input[name='radio']").change(function(){
                    onChangeDivRejectedMotives();
                });

                $("#attended").change(function(){
                    onChangeDivMotive();
                    onChangeDivDates();
                    onChangeDivRadioCheckbox();
                    resetContactForm();
                    onChangeDivRejectedMotives();
                });

                $("#action_id").change(function(){
                    if($(this).val()==13)
                    {
                        $('#div_motive').show();
                    }
                    else
                    {
                        $('#div_motive').hide();
                    }
                    $("[select2]").select2({
                        language: 'es'
                    });
                });

                @if(auth()->user()->can('sales-opportunity-trackings.corporative-view'))
                    $("#div_dates").hide();
                    $('#div_radio').show();

                    $("#action_id").change(function(){
                        if ($("#action_id").val() == 7)
                        {
                            $("#div_dates").show();
                        }
                        else
                        {
                            $("#div_dates").hide();
                        }
                    });
                @endif

                $("#div_products").hide();

                $('#enterprise_id').change(function(){
                    onEnterpriseChange();
                });

                $('input[name^="product_selected[]').click(function() {
                    clear();
                });
            });


           


            function resetContactForm() {
                $('#contact_form').val('').trigger('change');
                $('input[name="radio"], input[name="reject"]').prop('checked', false);
            }

            onChangeDivDates();
            function onChangeDivDates() {
                var attended = $("#attended").val();
                if(attended==1)
                {
                    $('#div_deadline').show();
                }
                else
                {
                    $('#div_deadline').hide();
                }
            }

            @if(!auth()->user()->can('sales-opportunity-trackings.corporative-view'))
                onChangeDivRadioCheckbox();
            @endif
            //console.log('ingresa');
            function onChangeDivRadioCheckbox() {
                var attended = $("#attended").val();
                if(attended==1)
                {
                    $('#div_radio').show();
                    $('#div_checkbox').hide();
                }
                else if(attended==2)
                {
                    $('#div_radio').hide();
                    $('#div_checkbox').show();
                }
                else
                {
                    $('#div_radio, #div_checkbox').hide();
                }
            }

            onChangeDivMotive(1);
            function onChangeDivMotive() {
                var attended = $("#attended").val();
                if(attended==2)
                {
                    $('#div_motive').show();
                }
                else
                {
                    $('#div_motive').hide();
                    $('#not_attended').val('');
                }
                $("[select2]").select2({
                    language: 'es'
                });
            }

            onChangeDivRejectedMotives();
            function onChangeDivRejectedMotives() {
                var radio = $("input[name='radio']:checked").val();
                var reject_checkbox = $("#reject_checkbox").prop('checked');
                if(radio==3 || reject_checkbox)
                {
                    $('#div_rejected_motives').show();
                    $('#div_cities').hide();
                    $('#div_address_zone').hide();
                    $("#div_deadline").hide()
                    $('#div_products').hide();
                }
                else
                {
                    if (radio == 1)
                    {
                        $('#div_cities').show();
                        $('#div_address_zone').show();
                        $('#div_deadline').show();
                        $('#div_products').hide();
                    }
                    else
                    {
                        $('#div_address_zone').hide();
                        if (radio == 4) {
                            $('#div_cities').show();
                            $('#div_deadline').show();
                            $('#div_products').hide();
                        }
                        else
                        {
                            $('#div_deadline').hide();
                            $('#div_products').show();
                        }
                    }
                    $('#div_rejected_motives').hide();
                    $('#rejected_motive_id').val('');
                }
                $("[select2]").select2({
                    language: 'es'
                });
            }

            function add_product()
            {
                var additional_service_id = $("#additional_service_id").val();
                var additional_service_name = $("#additional_service_id option:selected").text();
                var additional_service_amount = $("#product_amount").val();
                if ($.inArray(additional_service_id, services_items_array) != '-1')
                {
                    alert('El producto seleccionado ya fue insertado.')
                }
                else
                {
                    services_items_array.push(additional_service_id);
                    $('#products').append('<div class="row">' +
                            '<input type="hidden" name="product[]" value="'+ additional_service_id +'">'+
                            '<div class="col-md-6">' +
                               additional_service_name +
                            '</div>' +
                            '<div class="col-md-4">' +
                                '<input class="form-control" type="text" name="product_amount[]" value="'+additional_service_amount +'" placeholder="Monto">'+
                            '</div>' +
                            '<div class="col-md-2">' +
                                '<button type="button" class="btn btn-warning" onclick="removeRow(this, '+ additional_service_id +');"><i class="fa fa-times"></i></button>' +
                            '</div></div>');
                }

                $('[select2]').select2({
                    language: 'es'
                });
            }

            function removeRow(t, additional_service_id) {
                $(t).parent().parent().remove();
                services_items_array.splice($.inArray(additional_service_id, services_items_array), 1 );
            }

            onEnterpriseChange();
            function onEnterpriseChange()
            {
                $(".tr").hide()
                var enterprise_id = $("#enterprise_id").val();
                $(".enterprise_"+enterprise_id).show();
                clear();
            }

            function clear()
            {
                $('input[name^="product_selected[]"]').each(function ()
                {
                    if (this.checked && $(this).val() == 0 && $("#enterprise_id").val() == 3)
                    {
                        $('.tr_paramedic').show();
                        return false;
                    }
                    else
                    {
                        $('.tr_paramedic').hide();
                    }
                    if (this.checked == false && $(this).val() == 1)
                    {
                        $('#paramedic').prop('checked', false);
                        $('#ambulance').prop('checked', false);
                    }
                });
            }
        </script>
    @endif
    <script>
        $(document).ready(function () {
            
        });

        function show_modal()
        {
            $('#myModal_product').modal('show');
            /* $('.prueba').next('.select2-container').css('z-index',100000); */
        }
    </script>
@endsection
