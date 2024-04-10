@extends('layouts.AdminLTE.index')
@section('title', 'Recepcion')
@section('content')
<div class="row">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="{{ url('purchase-movement') }}" class="btn btn-primary btn-xs"><i class="fa fa-arrow-left"></i> Volver</a>
                    </div>
                </div>
                <br>
                <div class="row">                        
                    <div class="col-md-12">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3"><b>Nro° Recepcion:</b></div>
                                <div class="col-md-9">{{ $purchase_movement->id }}</div>
                            </div>  
                         <div class="row">
                                <div class="col-md-3"><b>Deposito:</b></div>
                                <div class="col-md-9">{{ $purchase_movement->deposit->name}}</div>
                            </div> 
                            <div class="row">
                                <div class="col-md-3"><b>Solicitado por:</b></div>
                                <div class="col-md-9">{{ $purchase_movement->user->name }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><b>Observación:</b></div>
                                <div class="col-md-9">{{ $purchase_movement->observation ?? '' }}</div>
                            </div>
                            @if ($purchase_movement->status ==2)
                                <div class="row">
                                    <div class="col-md-3"><b>Motivo:</b></div>
                                    <div class="col-md-9">{{ $purchase_movement->reason_deleted}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"><b>Fecha Eliminacion:</b></div>
                                    <div class="col-md-9">{{ $purchase_movement->date_deleted ? $purchase_movement->date_deleted : '-' }}</div>
                                </div>
                            @endif
                             <div class="row">
                                <div class="col-md-3"><b>Fecha Creación:</b></div>
                                <div class="col-md-9">{{ $purchase_movement->created_at->format('d/m/Y H:m:s') }}</div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">                
        <div class="col-md-12">
            <div class="ibox-content table-responsive no-padding">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Materia Prima</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase_movement->purchases_movement_details as $detail)
                            <tr>
                               <td>{{ $detail->quantity }}</td>
                               <td> {{ $detail->raw_material->description }}</td> 
                               <td>{{ $detail->price_cost}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>        
</div>
@endsection
