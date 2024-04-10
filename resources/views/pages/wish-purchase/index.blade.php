@extends('layouts.AdminLTE.index')
@section('title', 'Pedido Compras')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="btn-group pull-right">
                    <a href="{{ url('wish-purchase/create') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="ibox-content pb-0">
                <div class="row">
                    <form method="GET">
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="p" placeholder="Buscar" value="{{ request()->p }}">
                        </div>
                    </form>
                </div>
            </div>
            <form method="get" action="{{ route('wish-purchases.show_multiple') }}" id="form">
                <div class="ibox-content table-responsive no-padding">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nro°</th>
                                <th>SU</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->number }}</td>
                                    <td>{{ $purchase->branch->name }}</td>
                                    <td>{{ $purchase->date }}</td>
                                    <td>
                                        <span class="label label-{{ config('constants.wish-purchase-status-label.' . $purchase->status) }}">{{ config('constants.wish-purchase-status.'. $purchase->status) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown" style="display: inline-block;">
                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Acción
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu pull-right">
                                                @if($purchase->status == 2)
                                                    <li>
                                                            <a href="{{ url('wish-purchase-budgets/' . $purchase->id . '/wish-purchase-budgets-approved') }}"><i class="fa fa-user"></i> Ver Presupuestos Aprobados</a>
                                                    </li>
                                                @endif
                                                    <li>
                                                        <a href="{{ url('wish-purchase/' . $purchase->id) }}"><i class="fa fa-info-circle"></i>Ver Pedido </a>
                                                    </li>
                                                @if($purchase->status == 1)
                                                    <li>
                                                        <a href="{{ url('wish-purchase/' . $purchase->id . '/charge-purchase-budgets') }}"><i title="Anclar Presupuestos" class="fa fa-upload"></i>Anclar Presupuestos</a>
                                                    </li>
                                                @endif
                                                @if($purchase->status == 1)
                                                    <li>
                                                        <a href="{{ url('wish-purchase/' . $purchase->id . '/confirm-purchase-budgets') }}"><i class="fa fa-user"></i> Ir a Confirmar Presupuestos</a>                                            
                                                    </li>
                                                @endif
                                                    <li>
                                                        <a href="{{ url('wish-purchase/' . $purchase->id . '/pdf') }}" target="_blank"><i class="fa fa-file-pdf-o"></i>PDF</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ url('wish-purchase/' . $purchase->id . '/edit') }}"target="_blank" data-toggle="tooltip"><i class="fa fa-pencil"></i>Editar Pedido</a>
                                                    </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        @if (request()->query() && $purchase->status == 2)
                                            &nbsp;&nbsp;<input type="checkbox" name="wish_purchase_ids[]"
                                                id="wish_purchase_ids[]" value="{{ $purchase->id }}" onclick="showVerifyButton()">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="display: none" id="verify_button">
                            <tr>
                                <td colspan="5" class="text-center">
                                </td>
                                <td colspan="1" class="text-center">
                                    <button type="submit" style="color: white;" data-toggle="tooltip"
                                        data-placement="top" title="Verificar Seleccionados"
                                        class="btn btn-success btn-xs"><i class="fa fa-print"></i>VERIFICAR</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
            {{ $purchases->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
@section('layout_js')
    <script>

        function showVerifyButton() {
            let wish_purchase_ids = $('input[name="wish_purchase_ids[]"]:checked');
            if (wish_purchase_ids.length > 0) 
            {
                $('#verify_button').show();
            } else{
                $('#verify_button').hide();
            }
        }
    </script>
@endsection
