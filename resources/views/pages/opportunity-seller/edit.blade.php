@extends('layouts.sistema')
@section('content')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Editar Oportunidad de Venta</h5>
                    </div>
                    {{ Form::open(['id' => 'form']) }}
                        <div class="ibox-content">
                            @include('partials.messages')
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <h2>Datos del cliente</h2>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>         
                                </div>
                                <br>
                                @permission('physical-legal-opportunities.create')
                                    <div class="form-group col-sm-3">
                                        <label>Tipo de cliente</label>
                                        {{ Form::select('client_type', config('constants.client-type'), old('client_type', $opportunity->client_type), ['class' => 'form-control', 'select2', 'id' => 'client_type']) }}
                                    </div>
                                @endpermission
                                <div class="form-group col-md-4">
                                    <label>Nombre Completo</label>
                                    <input class="form-control" type="text" name="fullname" value="{{ old('fullname', $opportunity->fullname) }}">
                                </div>
                                @if(auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="form-group col-md-4">
                                        <label>Consultorio</label>
                                        <input class="form-control" type="text" id="dental_office" name="dental_office" value="{{ old('dental_office', $opportunity->fullname) }}">
                                    </div>
                                @endif
                                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="physical_client">
                                        <div class="form-group col-md-3">
                                            <label>Número de Documento</label>
                                            <input class="form-control" type="text" id="document_number" name="document_number" value="{{ old('document_number', $opportunity->document_number) }}" period-data-mask>
                                        </div>
                                    </div>
                                    <div class="legal_client">
                                        <div class="form-group col-md-3">
                                            <label>Número de Ruc</label>
                                            <input class="form-control" type="text" id="ruc" name="ruc" value="{{ old('ruc', $opportunity->ruc) }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="form-group col-md-2"> 
                                    <label>Tel. Prefijo</label> 
                                    {{ Form::select('prefix_id', config('constants.phone-prefixes'), old('prefix_id', $opportunity->prefix), ['placeholder' => 'Seleccione Prefijo', 'class' => 'form-control', 'select2','id' => 'prefix_id']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Tel. Número</label> 
                                    <input class="form-control" type="text" name="phone" value="{{ $opportunity->number_without_prefix }}" placeholder="Número ej: 123123 (sin prefijo)" numeric-data-mask>
                                </div>
                                <div class="form-group col-md-2"> 
                                    <label>Tipo Número</label> 
                                    {{ Form::select('type_number', [], old('type_number'), ['class' => 'form-control', 'select2', 'id' => 'type_number']) }}
                                </div>
                                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="form-group col-md-3">
                                        <label>Email</label>
                                        <input class="form-control" type="text" name="email" value="{{ old('email', $opportunity->email) }}">
                                    </div>
                                @endif
                                @if(auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="form-group col-md-2">
                                        <label>Ciudad</label>
                                        {{ Form::select('city_id', $cities, old('city_id'), ['class' => 'form-control', 'select2', 'placeholder' => 'Seleccione ciudad']) }}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Dirección</label>
                                        <input class="form-control" type="text" name="address" value="{{ old('address') }}">
                                    </div>
                                @endif
                            </div>
                            @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <h2>Datos de control</h2>
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br>
                                    @permission('physical-legal-opportunities.create')
                                        <div class="form-group col-sm-3">
                                            <label>Unidad de Negocio</label>
                                            <input class="form-control" type="text" disabled="disabled" value="{{ $opportunity->enterprise->name }}">
                                        </div>
                                    @endpermission
                                    <div class="form-group col-md-3">
                                        <label>Medio Contacto</label>
                                        {{ Form::select('half_contact_id', $half_contacts, old('half_contact_id', $opportunity->half_contact_id), ['placeholder' => 'Seleccione Medio Contacto', 'class' => 'form-control', 'select2']) }}
                                    </div>
                                    <div class="physical_client">
                                        @if(!auth()->user()->can('physical-legal-opportunities.create'))
                                            <div class="form-group col-md-3" id="div_halfcontact">
                                                <label>Thinkchat ID Chat</label>
                                                <input class="form-control" type="text" name="message_id" id="message_id" value="{{ old('message_id', $opportunity->message_id) }}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Lead</label>
                                                <input class="form-control" type="text" name="lead" value="{{ old('lead', $opportunity->lead) }}">
                                            </div>
                                            <div class="physical_client">
                                                <div class="form-group col-md-3">
                                                    <label>Seguro</label>
                                                    {{ Form::select('insurance_id', $insurances, old('insurance_id', $opportunity->insurance_id), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(!auth()->user()->can('physical-legal-opportunities.create') && !auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="row">
                                    <div class="physical_client form-group col-md-3">
                                        <label>Tipo de Plan</label>
                                        {{ Form::select('type_plan', config('constants.type_plan'), old('type_plan', $opportunity->type_plan), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                    </div>
                                    <div class="physical_client form-group col-md-3">
                                        <label>Tipo de Contrato</label>
                                        {{ Form::select('contract_type', config('constants.contract_type'), old('contract_type', $opportunity->contract_type), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                    </div>
                                </div>
                            @endif
                            @if(auth()->user()->can('physical-legal-opportunities.create') && !auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">
                                                        <h2>Datos de Contactos</h2>
                                                    </th>
                                                    <th class="text-right"><button type="button" class="btn btn-success" id="button_add_contact"><i class="fa fa-plus"></i></button></th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div id="div_contacts">
                                            @foreach($opportunity->crm_contacts as $contact)
                                                <br>
                                                <div class="row">'
                                                    <div class="col-sm-3">
                                                        <input class="form-control" type="text" name="contact_name[]" placeholder="Nombre" value="{{ $contact->contact_name }}">
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="text" name="contact_charge[]" placeholder="Cargo" value="{{ $contact->contact_charge }}">
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="text" name="contact_email[]" placeholder="Email" value="{{ $contact->email }}">
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <select name="contact_prefix[]" select2 class="form-control">
                                                            @foreach(config('constants.phone-prefixes') as $key => $value)
                                                                <option value="{{ $key }}" {{ $key == $contact->prefix  ? 'selected' : '' }}>{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="text" name="contact_number[]" placeholder="Número ej: 123123 (sin prefijo)" value="{{ $contact->number_without_prefix }}" numeric-data-mask> 
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <button type="button" class="btn btn-warning" onclick="removeRow(this);"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(auth()->user()->can('physical-legal-opportunities.create') or auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <h2>Observaciones</h2>
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <br>
                                        <div class="form-group col-md-12">
                                            <textarea class="form-control" name="observation">{{ old('observation', $opportunity->observation) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                            <a href="{{ url('sales-opportunity-seller') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <script>
        var types_phone_prefixes = {!! json_encode( $types_phone_prefixes) !!};
        var type_numbers = {!! json_encode( $type_numbers) !!};
        var services_items_array = [];

        $(document).ready(function() {
            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('sales-opportunity-seller.update',  $opportunity->id) }}',
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect(data.return_url);
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });

            });

            $("#prefix_id").on("change", function() {
                loadTypeNumbers();
            });
            
            $("#client_type").on("change", function() {
                clientTypeChange();
            });

            $("#button_add_contact").click(function() {
                add_contact();
            });
            // $("#button_add_product").click(function() {
            //     add_product();
            // });
        });

        loadTypeNumbers();
        function loadTypeNumbers()
        {
            var prefix_id = $("#prefix_id").val();
            $("#type_number").html('');
            $(types_phone_prefixes[prefix_id]).each(function(index, element){
                $("#type_number").append('<option value="'+element+'">'+ type_numbers[element] +'</option>');
            });
        }

        clientTypeChange();
        function clientTypeChange()
        {
            $(".legal_client").hide();
            var client_type = 1;
            if ( $("#client_type").length > 0 )
            {
                client_type = $("#client_type").val();
            }
            if (client_type == 2)
            {
                $(".physical_client").hide();
                $(".legal_client").show();
            }
            else
            {
                $(".legal_client").hide();
                $(".physical_client").show();
            }
            $('[select2]').select2({
                language: 'es'
            });
        }

        function add_contact()
        {
            $('#div_contacts').append('<br><div class="row">' +
                    '<div class="col-sm-3">' +
                        '<input class="form-control" type="text" name="contact_name[]" placeholder="Nombre">'+
                    '</div>' +
                    '<div class="col-sm-2">' +
                        '<input class="form-control" type="text" name="contact_charge[]" placeholder="Cargo">' +
                    '</div>' +
                    '<div class="col-sm-2">' +
                        '<input class="form-control" type="text" name="contact_email[]" placeholder="Email">' +
                    '</div>' +
                    '<div class="col-sm-2">' +
                        '{{ Form::select('contact_prefix[]', config('constants.phone-prefixes'), NULL, ['class' => 'form-control', 'select2']) }}' +
                    '</div>' +
                    '<div class="col-sm-2">' +
                        '<input class="form-control" type="text" name="contact_number[]" placeholder="Número ej: 123123 (sin prefijo)" numeric-data-mask>'+
                    '</div>' +
                    '<div class="col-sm-1">' +
                        '<button type="button" class="btn btn-warning" onclick="removeRow(this);"><i class="fa fa-times"></i></button>' +
                    '</div>' +
                '</row>');

            $('[select2]').select2({
                language: 'es'
            });
        }
    
        // function add_product()
        // {
        //     var additional_service_id = $("#additional_service_id").val();
        //     var additional_service_name = $("#additional_service_id option:selected").text();
        //     var additional_service_amount = $("#product_amount").val();
        //     if ($.inArray(additional_service_id, services_items_array) != '-1')
        //     {
        //         alert('El producto seleccionado ya fue insertado.')
        //     }
        //     else
        //     {
        //         services_items_array.push(additional_service_id);
        //         $('#div_products').append('<br><div class="row">' +
        //                 '<input type="hidden" name="product[]" value="'+ additional_service_id +'">'+
        //                 '<div class="col-md-6">' +
        //                    additional_service_name +
        //                 '</div>' +
        //                 '<div class="col-md-4">' +
        //                     '<input class="form-control" type="text" name="product_amount[]" value="'+ additional_service_amount +'" placeholder="Monto">'+
        //                 '</div>' +
        //                 '<div class="col-md-2">' +
        //                     '<button type="button" class="btn btn-warning" onclick="removeRow(this, '+ additional_service_id +');"><i class="fa fa-times"></i></button>' +
        //                 '</div></div>');
        //     }

        //     $('[select2]').select2({
        //         language: 'es'
        //     });
        // }

        function removeRow(t) {
            $(t).parent().parent().remove();
            // services_items_array.splice($.inArray(additional_service_id, services_items_array), 1 );
        }

    </script>
@endsection
