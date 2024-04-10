@extends('layouts.AdminLTE.index')
@section('title', 'Oportunidad ')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agregar Oportunidad de Venta</h5>
                </div>
                {{ Form::open(['route' => 'opportunity.store']) }}
                    <div class="ibox-content">
                        @include('partials.messages')
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Vendedor</label>
                                {{ Form::select('seller_id', $sellers, old('seller_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => 'Seleccione Vendedor']) }}
                            </div>
                            <div class="form-group col-md-3">
                                <label>Sucursal</label>
                                {{ Form::select('branch_id', $branches, old('branch_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => 'Seleccione Sucursal']) }}
                            </div>
                            <div class="form-group col-md-3">
                                <label>Medio Contacto</label>
                                {{ Form::select('contact_medium_id', $half_contacts, old('contact_medium_id'), ['class'=>'form-control','select2','placeholder'=>'Seleccione Medio de Contacto']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Nombre Completo</label>
                                <input class="form-control" type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" autofocus>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Email</label>
                                <input class="form-control" type="text" name="email" value="{{ old('email') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Teléfono</label>
                                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Número de Documento</label>
                                <input class="form-control" type="text" id="document_number" name="document_number" value="{{ old('document_number') }}" period-data-mask>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <span class="red" id="text_client_exist"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <span class="red" id="client_fullname_exist"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Tipo de Contrato</label>
                                {{ Form::select('contract_type', config('constants.contract_type'), old('contract_type'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => ' - ']) }}
                            </div>
                            {{-- <div class="form-group col-md-3">
                                <label>Seguro</label>
                                {{ Form::select('insurance_id', $insurances, old('insurance_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => ' - ']) }}
                            </div> --}}
                            {{-- <div class="form-group col-md-3">
                                <label>Promoción</label>
                                {{ Form::select('contract_promotion_id', $contract_promotions, old('contract_promotion_id'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => ' - ']) }}
                            </div> --}}
                            <div class="form-group col-md-3">
                                <label>Tipo de Plan</label>
                                {{ Form::select('type_plan', config('constants.type_plan'), old('type_plan'), ['data-live-search'=>'true', 'class' => 'form-control selectpicker', 'placeholder' => ' - ']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Lead</label>
                                <input class="form-control" type="text" name="lead" value="{{ old('lead') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Monto seguro</label>
                                <input class="form-control" type="text" name="amount" value="{{ old('amount') }}" period-data-mask>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Observación</label>
                                <textarea class="form-control" name="observation">{{ old('observation') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold">Equipos a asignar leads:</h4>
                            </div>
                            @foreach($seller_teams as $key => $team)
                                <div class="col-md-4">
                                    <input type="checkbox" class="ml-2" name="teams[]" value="{{ $key }}" {{ old('teams') ? in_array($key, old('teams', [])) ? 'checked' : '' : '' }}>&nbsp; &nbsp;<label class="nw" for="team_{{ $key }}"> {{ $team }}</label><br>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <input type="submit" class="btn btn-sm btn-success" value="Guardar">
                        <a href="{{ url('sales-opportunity') }}" class="btn btn-sm btn-danger">Cancelar</a>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
@section('layout_js')
    <script>
        $(document).ready(function() {
            $("#fullname").donetyping(function(callback){
                checkAccountHolderClientFullname();
            });
            $("#document_number").donetyping(function(callback){
                checkAccountHolder();
            });
        });

        // function checkAccountHolder() {
        //     $('#text_client_exist').html('');
        //     var document_number = $('#document_number').val().replace(/\./g, '');
        //     if(document_number)
        //     {
        //         $.ajax({
        //             url: '{{ url('ajax/contract-clients') }}',
        //             type: "GET",
        //             data: { document_number:document_number },
        //             success: function(data) {
        //                 console.log(data);
        //                 if(data.items)
        //                 {
        //                     $('#text_client_exist').html('<b>ATENCIÓN Ya existe un contrato con este documento:</b>');
        //                     $(data.items).each(function(index, element) {
        //                         $('#text_client_exist').append('<br><a href="{{ url('account-status') }}/' + element.contract_id + '" target="_blank"><b>' + element.enterprise_name + ':</b> ' + element.contract_number + ' [' + element.contract_client_type_name + ']</a>');
        //                     });
        //                 }
        //                 else
        //                 {
        //                     $('#text_client_exist').html('');
        //                 }
        //             },
        //             error: function(data){
        //                 laravelErrorMessages(data);
        //             }
        //         });
        //     }
        // }

        // function checkAccountHolderClientFullname() {
        //     $('#client_fullname_exist').html('');
        //     var fullname = $('#fullname').val().replace(/\./g, '');
        //     if(fullname)
        //     {
        //         $.ajax({
        //             url: '{{ url('ajax/contract-clients') }}',
        //             type: "GET",
        //             data: { fullname:fullname },
        //             success: function(data) {
        //                 console.log(data);
        //                 if(data.items)
        //                 {
        //                     $('#client_fullname_exist').html('<b>ATENCIÓN Ya existe un cliente con este nombre:</b>');
        //                     $(data.items).each(function(index, element) {
        //                         if(element.contract_id)
        //                         {
        //                             $('#client_fullname_exist').append('<br><a href="{{ url('account-status') }}/' + element.contract_id + '" target="_blank"><b>' + element.enterprise_name + ':</b> ' + element.contract_number + ' [' + element.contract_client_type_name + ']</a>');
        //                         }
        //                         else
        //                         {
        //                             $('#client_fullname_exist').append('<br>' + '<b>' + element.opportunity_fullname + '</b>');
        //                         }
        //                     });
        //                 }
        //                 else
        //                 {
        //                     $('#client_fullname_exist').html('');
        //                 }
        //             },
        //             error: function(data){
        //                 laravelErrorMessages(data);
        //             }
        //         });
        //     }
        // }
    </script>
@endsection
