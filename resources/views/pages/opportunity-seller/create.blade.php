@extends('layouts.AdminLTE.index')
@section('title', 'Oportunidad ')
@section('content')
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Agregar Oportunidad de Venta</h5>
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
                                        {{ Form::select('client_type', config('constants.client-type'), null, ['class' => 'form-control', 'select2', 'id' => 'client_type']) }}
                                    </div>
                                @endpermission
                                <div class="form-group col-md-4">
                                    <label>Nombre Completo</label>
                                    <input class="form-control" type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" autofocus>
                                </div>
                                @if(auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="form-group col-md-4">
                                        <label>Consultorio</label>
                                        <input class="form-control" type="text" id="dental_office" name="dental_office" value="{{ old('dental_office') }}">
                                    </div>
                                    <div class="col-sm-3 address"> 
                                        <label>Ubicación</label>
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="addresses_locations" placeholder="Ubicación" id="addresses_locations" onChange="onchangeLinkLocation( \'#addresses_locations'\', this.value);">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" onclick="show_modal();"><i class="fa fa-map"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="physical_client">
                                    <div class="form-group col-md-3">
                                        <label>Número de Documento</label>
                                        <input class="form-control" type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" period-data-mask>
                                    </div>
                                </div>
                                <div class="legal_client">
                                    <div class="form-group col-md-3">
                                        <label>Número de Ruc</label>
                                        <input class="form-control" type="text" id="ruc" name="ruc" value="{{ old('ruc') }}">
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="form-group col-md-2"> 
                                    <label>Tel. Prefijo</label> 
                                    {{ Form::select('prefix_id', config('constants.phone-prefixes'), old('prefix_id'), ['placeholder' => 'Seleccione Prefijo', 'class' => 'form-control', 'select2','id' => 'prefix_id']) }}
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Tel. Número</label> 
                                    <input class="form-control" type="text" name="phone" placeholder="Número ej: 123123 (sin prefijo)" numeric-data-mask>
                                </div>
                                <div class="form-group col-md-2"> 
                                    <label>Tipo Número</label> 
                                    {{ Form::select('type_number', [], old('type_number'), ['class' => 'form-control', 'select2', 'id' => 'type_number']) }}
                                </div>
                                @if(!auth()->user()->can('denpro-seller-opportunities.create'))
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input class="form-control" type="text" name="email" value="{{ old('email') }}">
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
                                        {{ Form::select('enterprise_id', $enterprises, null, ['class' => 'form-control', 'select2', 'id' => 'enterprise_id']) }}
                                    </div>
                                @endpermission
                                <div class="form-group col-md-3">
                                    <label>Medio Contacto</label>
                                    {{ Form::select('half_contact_id', $half_contacts, old('half_contact_id'), ['placeholder' => 'Seleccione Medio Contacto', 'class' => 'form-control', 'select2']) }}
                                </div>
                                @if(!auth()->user()->can('physical-legal-opportunities.create'))
                                    <div class="physical_client">
                                        <div class="form-group col-md-3" id="div_halfcontact">
                                            <label>Thinkchat ID Chat</label>
                                            <input class="form-control" type="text" name="message_id" id="message_id" value="{{ old('message_id') }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Lead</label>
                                            <input class="form-control" type="text" name="lead" value="{{ old('lead') }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Seguro</label>
                                            {{ Form::select('insurance_id', $insurances, old('insurance_id'), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endif
                                @if(!auth()->user()->can('physical-legal-opportunities.create') && !auth()->user()->can('denpro-seller-opportunities.create'))
                                    <div class="row">
                                        <div class="physical_client form-group col-md-3">
                                            <label>Tipo de Plan</label>
                                            {{ Form::select('type_plan', config('constants.type_plan'), old('type_plan'), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                        </div>
                                        <div class="physical_client form-group col-md-3">
                                            <label>Tipo de Contrato</label>
                                            {{ Form::select('contract_type', config('constants.contract_type'), old('contract_type'), ['placeholder' => ' - ', 'class' => 'form-control', 'select2']) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <span class="red" id="client_fullname_exist"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <span class="red" id="text_client_exist"></span>
                                    </div>
                                </div>
                                @if(auth()->user()->can('physical-legal-opportunities.create')  && !auth()->user()->can('denpro-seller-opportunities.create'))
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
                                            <div id="div_contacts"></div>
                                        </div>
                                    </div>
                                @endif
                            <div class="row">
                                <div class="col-md-12">
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
                                        <textarea class="form-control" name="observation">{{ old('observation') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-footer">
                            <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                            <a href="{{ url('sales-opportunity-seller') }}" class="btn btn-sm btn-danger">Cancelar</a>
                        </div>
                    {{ Form::close() }}

                    <!-- Modal -->
                    <div class="modal fade" id="maps_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Marcar ubicación</h4>

                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12 modal_body_map">
                                            <div class="location-map" id="location-map">
                                                <div style="width: 565px; height: 400px;" id="map_canvas"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                </div>
            </div>
        </div>
@endsection

@section('layout_css')
    <style>
        .address {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    </style>
@endsection

@section('layout_js')
    <script>
        var types_phone_prefixes = {!! json_encode( $types_phone_prefixes) !!};
        var type_numbers = {!! json_encode( $type_numbers) !!};
        var additional_services = {!! json_encode( $additional_services) !!};
        var services_items_array = [];

        var marker;

        $(document).ready(function() {

            initMap(-25.2921766, -57.5931077);

            $('#form').submit(function(e)
            {
                $('input[type="submit"]').prop('disabled', true);
                e.preventDefault();
                $.ajax({
                    url: '{{ route('sales-opportunity-seller.store') }}',
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        redirect("{{ url('sales-opportunity-seller') }}");
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                        $('input[type="submit"]').prop('disabled', false);
                    }
                });

            });

            @if(!auth()->user()->can('physical-legal-opportunities.create') && !auth()->user()->can('denpro-seller-opportunities.create'))

                $("#fullname").donetyping(function(callback){
                    checkAccountHolderClientFullname();
                });
                $("#document_number").donetyping(function(callback){
                    checkAccountHolder();
                });
                $("#ruc").donetyping(function(callback){
                    checkAccountHolder();
                });
            @endif

            $("#prefix_id").on("change", function() {
                loadTypeNumbers();
            });

            $("#client_type").on("change", function() {
                clientTypeChange();
            });
            $(".legal_client").hide();

            $("#button_add_contact").click(function() {
                add_contact();
            });

        });

        function checkAccountHolder() {
            $('#text_client_exist').html('');
            var document_number = $('#document_number').val().replace(/\./g, '');
            var client_type = 1;
            if ($("#client_type").length > 0)
            {
                client_type = $("#client_type").val();
            }
            if (client_type == 1)
            {
                var document_number = $('#document_number').val().replace(/\./g, '');
            }
            else
            {
                var document_number = $('#ruc').val().replace(/\./g, '');
            }
            if(document_number)
            {
                $.ajax({
                    url: '{{ url('ajax/contract-clients') }}',
                    type: "GET",
                    data: { document_number:document_number },
                    success: function(data) {
                        if(data.items)
                        {
                            $('#text_client_exist').html('<b>ATENCIÓN Ya existe un contrato con este documento:</b>');
                            $(data.items).each(function(index, element) {
                                $('#text_client_exist').append('<br><a href="{{ url('account-status') }}/' + element.contract_id + '" target="_blank"><b>' + element.enterprise_name + ':</b> ' + element.contract_number + ' [' + element.contract_client_type_name + ']</a>');
                            });
                        }
                        else
                        {
                            $('#text_client_exist').html('');
                        }
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
        }

        function checkAccountHolderClientFullname() {
            $('#client_fullname_exist').html('');
            var fullname = $('#fullname').val().replace(/\./g, '');
            if(fullname)
            {
                $.ajax({
                    url: '{{ url('ajax/contract-clients') }}',
                    type: "GET",
                    data: { fullname:fullname },
                    success: function(data) {
                        if(data.items)
                        {
                            $('#client_fullname_exist').html('<b>ATENCIÓN Ya existe un cliente con este nombre:</b>');
                            $(data.items).each(function(index, element) {
                                if(element.contract_id)
                                {
                                    $('#client_fullname_exist').append('<br><a href="{{ url('account-status') }}/' + element.contract_id + '" target="_blank"><b>' + element.enterprise_name + ':</b> ' + element.contract_number + ' [' + element.contract_client_type_name + ']</a>');
                                }
                                else
                                {
                                    $('#client_fullname_exist').append('<br>' + '<b>' + element.opportunity_fullname + '</b>');
                                }
                            });
                        }
                        else
                        {
                            $('#client_fullname_exist').html('');
                        }
                    },
                    error: function(data){
                        laravelErrorMessages(data);
                    }
                });
            }
        }

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

        function removeRow(t, additional_service_id) {
            $(t).parent().parent().remove();
            // services_items_array.splice($.inArray(additional_service_id, services_items_array), 1 );
        }


    </script>
    <script>
        var marker;
        var actual_address_index;
        $(document).ready(function () {
            initMap(-25.2921766, -57.5931077);
        }); 

        function initMap(lat, lng) {
            myLatlng = new google.maps.LatLng(lat, lng);

            var myOptions = {
              zoom: 12,
              zoomControl: true,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            google.maps.event.addListener(map, 'click', function(event) {
                placeMarker(event.latLng);
            });
        }
   
        function show_modal()
        {
            $("#maps_modal").modal('show');
        }     
        function placeMarker(location)
        {
            if (marker == undefined)
            {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                });
            }
            else
            {
                marker.setPosition(location);
            }

            google.maps.event.addListener(marker, 'dragend', function(event) {
                placeMarker(event.latLng);
            });

            $('#addresses_locations').val(location.lat()+','+location.lng());
        }
    </script>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1EOe3Hanle757v9kQSyxt8Or0Z6jx9iE&libraries=places&callback=initMap">
    </script>
@endsection
