@extends('layouts.AdminLTE.index')
@section('title', 'Oportunidad ')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Oportunidades de Venta</h5>
                        <div class="ibox-tools">
                            {{-- <a href="{{ url('sales-opportunity-seller') }}" class="btn btn-default btn-xs gray"><i class="fa fa-sync"></i> Actualizar</a>
                            <a href="{{ url('sales-opportunity-seller/create') }}" class="btn btn-warning btn-xs white"><i class="fa fa-plus"></i> Agregar</a>
                            <a href="{{ url('sales-opportunity-seller/create-import') }}" class="btn btn-warning btn-xs white"><i class="fa fa-plus"></i> Importar</a> --}}
                        </div>
                    </div>
                    <div class="ibox-content pb-0">
                            <form method="GET">
                                <div class="row">
                                        <div class="form-group col-md-3">
                                        {{ Form::select('half_contact_id[]', $half_contacts, request()->half_contact_id, ['class' => 'form-control selectpicker', 'placeholder'=>'Medio de Contacto']) }}
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <input type="text" class="form-control" name="s" placeholder="Buscar" value="{{ request()->s }}">
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <input type="text" class="form-control" name="date" placeholder="Fecha reasignado" value="{{ request()->date }}" date-mask>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>
                                                <input type="checkbox" name="leads_reassigned" {{ request()->leads_reassigned ? 'checked' : '' }}> Leads reasignados
                                            </label>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-2">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                        @if(request()->query())
                                            <a href="{{ request()->url() }}" class="btn btn-warning"><i class="fa fa-times"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        
                            <li id="tab_1" class="li tab-1 active"><a data-toggle="tab" href="#tab-1">En Proceso ({{ $opportunities_process->count() }})</a></li>
                            <li id="tab_2" class="li tab-2 "><a data-toggle="tab" href="#tab-2">Sin Seguimientos ({{ $opportunities_without_trackings_count }})</a></li>
                            <li id="tab_3" class="li tab-3 "><a data-toggle="tab" href="#tab-3">Nuevos ({{ $opportunities_new->count() }})</a></li>
                            <li id="tab_4" class="li tab-4 "><a data-toggle="tab" href="#tab-4">En Cerrador ({{ $opportunities_closer->count() }})</a></li>
                            <li id="tab_5" class="li tab-5"><a data-toggle="tab" href="#tab-5">Cerradas ({{ $closed_opportunities_qty }})</a></li>
                            {{-- <li id="tab_6" class="li tab-6 "><a data-toggle="tab" href="#tab-6">Contratos en Mora ({{ $contracts->count() }})</a></li> --}}
                            <li id="tab_7" class="li tab-7 "><a data-toggle="tab" href="#tab-7">Ventas Cajón ({{ $opportunities_drawer_sales->count() }})</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body table-responsive no-padding">
                                @if($opportunities_process->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                @if(request()->leads_reassigned)
                                                    <th>Fecha de reasignación</th>
                                                @endif
                                                <th>Fecha V.A.L.</th>
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th>Gestiones</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunities_process as $opportunity)
                                                <tr>
                                                    <td>
                                                        @if(request()->leads_reassigned)
                                                            {{ $opportunity->sales_movements->first()->created_at->format('d/m/Y') }}
                                                        @elseif(auth()->user()->can('physical-legal-opportunities.create') || auth()->user()->can('denpro-seller-opportunities.create'))
                                                            {{ $opportunity->last_day_tracking->format('d/m/Y') }}
                                                        @else
                                                            {{ $opportunity->first_call_again ? $opportunity->first_call_again->format('d/m/Y') : '' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $opportunity->fullname }}
                                                    </td>
                                                    <td>
                                                        {{ $opportunity->phone or '-' }}
                                                    </td>
                                                    <td>
                                                        @unless ('denpro-seller-opportunities.create')
                                                        {{ $opportunity->email or '-' }}
                                                        @endunless
                                                        {{ $opportunity->trackings()->where('status', 1)->count() }}
                                                    </td>
                                                    <td colspan="3" class="text-right">
                                                        <a href="{{ url('opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                            <a href="{{ url('opportunity-seller/' . $opportunity->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                                            <a href="{{ url('opportunity-seller/' . $opportunity->id . '/delete') }}"><i class="fa fa-trash"></i></a></td>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $opportunities_process->appends(request()->query())->links() }}
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($opportunities_without_trackings->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                @if(request()->leads_reassigned)
                                                    <th>Fecha de reasignación</th>
                                                @endif
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th>Medio</th>
                                                @endunless
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th>Gestiones</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunities_without_trackings as $opportunity)
                                                <tr>
                                                    @if(request()->leads_reassigned)
                                                        <td>{{ $opportunity->sales_movements->first()->created_at->format('d/m/Y') }}</td>
                                                    @endif
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->half_contact->name }}</td>
                                                    @endunless
                                                    <td>{{ $opportunity->fullname }}</td>
                                                    <td>{{ $opportunity->phone or '-' }}</td>
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->email or '-' }}</td>
                                                    @endunless
                                                    <td>{{ $opportunity->trackings()->where('status', 1)->count() }}</td>
                                                    <td colspan="3" class="text-right">
                                                        <a href="{{ url('opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                            <a href="{{ url('opportunity-seller/' . $opportunity->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                                        <a href="{{ url('opportunity-seller/' . $opportunity->id . '/delete') }}"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $opportunities_without_trackings->appends(request()->query())->links() }}
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($opportunities_new->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Fecha carga</th>
                                                @if(request()->leads_reassigned)
                                                    <th>Fecha de reasignación</th>
                                                @endif
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th>Medio</th>
                                                @endunless
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th>Gestiones</th>
                                                
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunities_new as $opportunity)
                                                <tr>
                                                    <td>{{ $opportunity->created_at->format('d/m/Y H:i:s') }}</td>
                                                    @if(request()->leads_reassigned)
                                                        <td>{{ $opportunity->sales_movements->first()->created_at->format('d/m/Y') }}</td>
                                                    @endif
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->half_contact->name }}</td>
                                                    @endunless
                                                    <td>{{ $opportunity->fullname }}</td>
                                                    <td>{{ $opportunity->phone or '-' }}</td>
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->email or '-' }}</td>
                                                    @endunless
                                                    <td>{{ $opportunity->trackings()->where('status', 1)->count() }}</td>
                                                    <td colspan="3" class="text-right">
                                                        <a href="{{ url('opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                            <a href="{{ url('sales-opportunity-seller/' . $opportunity->id . '/edit') }}"><i class="fa fa-pencil-alt"></i></a>
                                                        <a href="{{ url('sales-opportunity-seller/' . $opportunity->id . '/delete') }}"><i class="fa fa-trash"></i></a>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $opportunities_new->appends(request()->query())->links() }}
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div>
                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($opportunities_closer->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                @if(request()->leads_reassigned)
                                                    <th>Fecha de reasignación</th>
                                                @endif
                                                <th>Medio</th>
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th>Cerrador</th>
                                                <th>Gestiones</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunities_closer as $opportunity)
                                                <tr>
                                                    @if(request()->leads_reassigned)
                                                        <td>{{ $opportunity->sales_movements->first()->created_at->format('d/m/Y') }}</td>
                                                    @endif
                                                    <td>{{ $opportunity->contact_medium->name }}</td>
                                                    <td>{{ $opportunity->fullname }}</td>
                                                    <td>{{ $opportunity->phone or '-' }}</td>
                                                    <td>{{ $opportunity->email or '-' }}</td>
                                                    @if($opportunity->closed_in == 2)
                                                        <td><span class="label label-{{ config('constants.sale-closing-type-label.' . $opportunity->closed_in) }}">{{ config('constants.sale-closing-type.' .$opportunity->closed_in) }}</span></td>
                                                    @else
                                                        <td>{!! $opportunity->closer ? $opportunity->closer->fullname : '<label class="label label-warning">Pendiente</label>' !!}</td>
                                                    @endif
                                                    <td>{{ $opportunity->trackings()->where('status', 1)->count() }}</td>
                                                    <td class="text-right">
                                                        <a href="{{ url('opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $opportunities_closer->appends(request()->query())->links() }}
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div>
                        <div id="tab-5" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($closed_opportunities->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Fecha cerrada</th>
                                                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                                    <th>Medio</th>
                                                @endif
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($closed_opportunities as $opportunity)
                                                <tr>
                                                    <td>{{ $opportunity->selled_at->format('d/m/Y') }}</td>
                                                    @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                                        <td>{{ $opportunity->contact_medium->name }}</td>
                                                    @endif
                                                    <td>{{ $opportunity->fullname }}</td>
                                                    <td>{{ $opportunity->phone or '-' }}</td>
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->email or '-' }}</td>
                                                    @endunless
                                                    <td class="text-right">
                                                        <a href="{{ url('opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $closed_opportunities->appends(request()->query())->links() }}
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif 
                            </div>
                        </div>
                        {{-- <div id="tab-6" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($contracts->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>UN</th>
                                                <th>Nro.</th>
                                                <th>Fecha</th>
                                                <th>Cédula</th>
                                                <th>Titular</th>
                                                <th>Vía de Cobro</th>
                                                <th>Cuota</th>
                                                <th>Debe</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contracts as $contract)
                                                <tr>
                                                    <td><span class="fs-3 label label-{{ $contract->enterprise_label }}">{{ $contract->enterprise_abbreviation }}</span></td>
                                                    <td>{{ number_format($contract->number, 0, ',', '.') }}</td>
                                                    <td>{{ $contract->date->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($contract->account_holder_document_number, 0, ',', '.') }}</td>
                                                    <td>{{ $contract->account_holder_fullname }}</td>
                                                    <td>{{ $contract->contract_contract_type == 1 ? config('constants.contract_type.'. $contract->contract_contract_type) : $contract->contractingentity_debitentity_name }}</td>
                                                    <td>{{ number_format($contract->amount, 0, ',', '.') }}</td>
                                                    <td>{{ number_format($contract->cuotas, 0, ',', '.') }}</td>
                                                    <td class="text-right">
                                                        <a href="{{ url('account-status/' . $contract->id) }}" target="_blank"><i class="fa fa-info-circle"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div> --}}
                        <div id="tab-7" class="tab-pane">
                            <div class="panel-body table-responsive no-padding">
                                @if($opportunities_drawer_sales->count() > 0)
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Medio</th>
                                                <th>Nombre Completo</th>
                                                <th><i class="fa fa-phone"></i></th>
                                                @unless ('denpro-seller-opportunities.create')
                                                    <th><i class="fa fa-envelope"></i></th>
                                                @endunless
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunities_drawer_sales as $opportunity)
                                                <tr>
                                                    <td>{{ $opportunity->half_contact->name }}</td>
                                                    <td>{{ $opportunity->fullname }}</td>
                                                    <td>{{ $opportunity->phone or '-' }}</td>
                                                    @unless ('denpro-seller-opportunities.create')
                                                        <td>{{ $opportunity->email or '-' }}</td>
                                                    @endunless
                                                    <td class="text-right">
                                                        <a href="{{ url('sales-opportunity/' . $opportunity->id) }}"><i class="fa fa-info-circle"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h3 class="text-center my-3">No se encontraron registros</h3>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('layout_js')
    <script>
        $(document).ready(function() {
            $(".date").datepicker({
                format: "dd/mm/yyyy",
                language: "es",
                daysOfWeekDisabled: "0",
                autoclose: true
            });
            
            $(".pagination a").click(function(){
                var id =  $(this).parent().parent().parent().parent().parent().attr('id');
                document.cookie = "tab_id="+id;
            });
            $(".li a").click(function(){
                document.cookie = "tab_id=; max-age=0";
            });
            var tab_id = readCookie("tab_id");
            if (tab_id)
            {
                $(".tab-pane").removeClass('active');
                $("#"+ tab_id).addClass('active');
                $(".li").removeClass('active');
                $("."+tab_id).addClass('active');
            }

            $('.date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                  cancelLabel: 'Clear'
                }
            });

            $('.date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });
        });

        function changeLead(type, opportunity)
        {
            var text = opportunity != 0 ? "&opportunity_id="+ opportunity : "";
            redirect ("{{ url('call-center-lead/call') }}?type=" + type + text);
        }

        function readCookie(name)
        {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + name.replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        }

    </script>
@endsection
