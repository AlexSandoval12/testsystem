<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @page { margin: 20px; }
            body {
                background-color: transparent;
                color: black;
                font-family: "verdana", "sans-serif";
                margin: 0px;
                padding-top: 0px;
                font-size: 12px;
            }
            table {
                font-size:9px;
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0px 20px;
            }
            /* thead {
                border-bottom: 1px black solid;
            } */
            /* thead th {
                text-align: left;
            } */
            /* tbody td {
                border-bottom: 0.5px darkgray solid;
            } */
            h1, h2, h3, h4, h5, h6 {
                padding: 0px;
                margin: 0px;
            }
            td {
                padding: 3px;
            }
            .page_break {
                page-break-before: always;
            }
        </style>
    </head>
    <body>
        <table>
           <thead>
                <tr><th colspan="13"> <hr> </tr> </th>
                <tr><th colspan="13"><center><h2>LIBRO COMPRAS</h2></center> </th> </tr>
                <tr><th colspan="11" align="left" ><p><b>Fecha:</b> {{ request()->date_range }}</p></th></tr>
                <tr style="background-color:rgb(41, 40, 40);color:white;">
                    <th align="center" style="width:5%">#</th>
                    <th align="left" style="width:5%">Fecha</th>
                    <th align="left" style="width:10%">Nro.</th>
                    <th align="left" style="width:1%">Tipo</th>
                    <th align="left" style="width:8%">Timbrado</th>
                    <th align="left" style="width:20%">Razon Social</th>
                    <th align="left" style="width:8%">RUC</th>
                    <th align="center">Exenta</th>
                    <th align="center">IVA 5%</th>
                    <th align="center">Gravada 5%</th>
                    <th align="center">IVA 10%</th>
                    <th align="center">Gravada 10%</th>
                    <th align="center">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_exenta   = 0; 
                    $amount_iva5    = 0;
                    $total_iva5     = 0;
                    $amount_iva10   = 0;
                    $total_iva10    = 0;
                    $amount         = 0;

                    $count              = 1;//Contador
                    $count_show         = 0;//Contador para mostrar
                    $actual_quantity    = 0;//contador para mostrar total al final de los registros
                    $purchases_quantity  = count($purchases); //Cantidad de registros
                    $old_quantity       = 0; //cantidad anterior

                    $old_total_exenta   = 0; 
                    $old_amount_iva5    = 0;
                    $old_total_iva5     = 0;
                    $old_amount_iva10   = 0;
                    $old_total_iva10    = 0;
                    $old_amount         = 0;

                @endphp
                @foreach($purchases as $purchase)
                    @php
                        $actual_quantity++;
                        $count_show++;
                    @endphp
                    <tr>
                        <td style="width:5%" align="center">{{ $count_show }}</td>
                        <td style="width:5%">{{ $purchase->date->format('d/m') }}</td>
                        <td style="width:10%">{{ $purchase->number }}</td>
                        <td style="width:5%">{{ config('constants.type_purchases.'. $purchase->type). ' ' .config('constants.invoice_condition.'. $purchase->condition) }}</td>
                        <td style="width:8%">{{ $purchase->stamped }}</td>
                        <td style="width:20%">{{ $purchase->razon_social }}</td>
                        <td style="width: 5%;">{{ $purchase->ruc }}</td>
                        <td align="right">{{ number_format($purchase->total_excenta, 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($purchase->amount_iva5, 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($purchase->total_iva5 - $purchase->amount_iva5 , 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($purchase->amount_iva10, 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($purchase->total_iva10 - $purchase->amount_iva10, 0, ',', '.') }}</td>
                        <td align="right">{{ number_format($purchase->amount, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $total_exenta   += $purchase->total_excenta;
                        $amount_iva5    += $purchase->amount_iva5;
                        $total_iva5     += $purchase->total_iva5 - $purchase->amount_iva5;
                        $amount_iva10   += $purchase->amount_iva10;
                        $total_iva10    += $purchase->total_iva10 - $purchase->amount_iva10;
                        $amount         += $purchase->amount;

                        $count++;
                        $old_quantity = $actual_quantity;
                    @endphp
                    @if ($count > 33 || $actual_quantity == $purchases_quantity)
                        @php
                            $old_total_exenta   += $total_exenta;
                            $old_amount_iva5    += $amount_iva5;
                            $old_total_iva5     += $total_iva5;
                            $old_amount_iva10   += $amount_iva10;
                            $old_total_iva10    += $total_iva10;
                            $old_amount         += $amount;
                        @endphp
                        <tr>
                            <td colspan="7" align="right"><b>Total ({{ number_format($count-1, 0, ',', '.') }}):</b></td>
                            <td align="right"><b>{{ number_format($total_exenta, 0, ',', '.') }}</b></td>
                            <td align="right"><b>{{ number_format($amount_iva5, 0, ',', '.') }}</b></td>
                            <td align="right"><b>{{ number_format($total_iva5, 0, ',', '.') }}</b></td>
                            <td align="right"><b>{{ number_format($amount_iva10, 0, ',', '.') }}</b></td>
                            <td align="right"><b>{{ number_format($total_iva10, 0, ',', '.') }}</b></td>
                            <td align="right"><b>{{ number_format($amount, 0, ',', '.') }}</b></td>
                        </tr>
                        {{-- si es la primera hoja o el ultimo registro --}} 
                        @if(($count == 34 && $actual_quantity > 34) || ($actual_quantity == $purchases_quantity) )
                            <tr style="background-color:rgb(41, 40, 40);color:white;">
                                <td align="right" colspan="7"><b>{{$actual_quantity == $purchases_quantity ? 'Total General ' : 'Traspaso '}}({{ number_format($old_quantity, 0, ',', '.') }}):</b></td>
                                <td align="right"><b>{{ number_format($old_total_exenta, 0, ',', '.') }}</b></td>
                                <td align="right"><b>{{ number_format($old_amount_iva5, 0, ',', '.') }}</b></td>
                                <td align="right"><b>{{ number_format($old_total_iva5, 0, ',', '.') }}</b></td>
                                <td align="right"><b>{{ number_format($old_amount_iva10, 0, ',', '.') }}</b></td>
                                <td align="right"><b>{{ number_format($old_total_iva10, 0, ',', '.') }}</b></td>
                                <td align="right"><b>{{ number_format($old_amount, 0, ',', '.') }}</b></td>
                            </tr>
                        @else
                            <tr><td></td></tr>
                        @endif
                        @php
                            $count = 1;

                            $total_exenta   = 0; 
                            $amount_iva5    = 0;
                            $total_iva5     = 0;
                            $amount_iva10   = 0;
                            $total_iva10    = 0;
                            $amount         = 0;
                        @endphp
                    @endif
                @endforeach
            </tbody>
        </table>
        <table>
            <thead>
                <tr><th colspan="11" align="left">&nbsp;</th> </tr>
                <tr><th colspan="11" align="left" >&nbsp;</th></tr>
                <tr><th colspan="13"> &nbsp; </th></tr> 
                <tr><th colspan="13" align="left"><h2>Resumen</h2></th> </tr>
                <tr><th colspan="11" align="left" >&nbsp;</th></tr>
                <tr style="background-color:rgb(41, 40, 40);color:white;">
                    <th align="center" style="width:5%">&nbsp;&nbsp;</th>
                    <th align="center" style="width:5%">&nbsp;&nbsp;</th>
                    <th align="center" style="width:5%">&nbsp;&nbsp;</th>
                    <th align="center" style="width:5%">&nbsp;&nbsp;</th>
                    <th align="center">Exenta</th>
                    <th align="center">IVA 5%</th>
                    <th align="center">Gravada 5%</th>
                    <th align="center">IVA 10%</th>
                    <th align="center">Gravada 10%</th>
                    <th align="center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( config('constants.purchases_type') as $index => $purchase_type )
                    @if ($index == 1 || $index == 4 )    
                        <tr>
                            <th colspan="2" align="left">{{ $index == 3 ? '' :$purchase_type }}</th>
                            <th colspan="8">
                                @if ($index == 1)
                                    
                                @else
                                    <hr>
                                @endif
                            </th>
                        </tr>
                        @if ($index == 1){{-- Facturas --}}
                            @foreach ( config('constants.invoice_condition') as $key => $condition )
                                <tr>
                                    <td colspan="4"  align="right">{{$condition}}</td>
                                    <td align="right"> {{-- EXENTAS --}}
                                        {{ 
                                            $key == 1 ? 
                                            ($purchase_counted ? number_format($purchase_counted->total_excenta, 0, ',','.') : 0) : 
                                            ($purchase_credit ? number_format($purchase_credit->total_excenta, 0, ',','.') : 0 )
                                        }}
                                    </td>
                                    <td align="right"> {{-- IVA 5% --}}
                                        {{ 
                                            $key == 1 ? 
                                            ($purchase_counted ? number_format($purchase_counted->amount_iva5, 0, ',','.') : 0) : 
                                            ($purchase_credit ? number_format($purchase_credit->amount_iva5, 0, ',','.') : 0 ) 
                                        }}
                                    </td>
                                    <td align="right"> {{-- GRAVADA 5% --}}
                                        {{ 
                                            $key == 1 ? 
                                            ($purchase_counted ? number_format($purchase_counted->total_iva5 - $purchase_counted->amount_iva5, 0, ',','.') : 0) : 
                                            ($purchase_credit ? number_format($purchase_credit->total_iva5 - $purchase_credit->amount_iva5, 0, ',','.') : 0 ) 
                                        }}
                                    </td>
                                    <td align="right"> {{-- IVA 10% --}}
                                        {{ 
                                            $key == 1 ? 
                                            ($purchase_counted ? number_format($purchase_counted->amount_iva10, 0, ',','.') : 0) : 
                                            ($purchase_credit ? number_format($purchase_credit->amount_iva10, 0, ',','.') : 0 ) 
                                        }}
                                    </td>
                                    <td align="right"> {{-- GRAVADA 10% --}}
                                        {{ 
                                            $key == 1 ? 
                                            ($purchase_counted ? number_format($purchase_counted->total_iva10 - $purchase_counted->amount_iva10, 0, ',','.') : 0) : 
                                            ($purchase_credit ? number_format($purchase_credit->total_iva10 - $purchase_credit->amount_iva10, 0, ',','.') : 0 ) 
                                        }}
                                    </td>
                                    <td align="right" id="total_final">{{$key == 1 ? $purchase_counted_total : $purchase_credit_total }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if($index == 4){{-- Notas de Credito --}}
                            <tr>
                                <td colspan="4"  align="right">&nbsp;</td>
                                <td align="right"> {{-- EXENTAS --}}
                                    {{ $credit_notes ? number_format($credit_notes->total_excenta, 0, ',','.') : 0 }}
                                </td>
                                <td align="right"> {{-- IVA 5% --}}
                                    {{ $credit_notes ?  number_format($credit_notes->amount_iva5, 0, ',','.') : 0 }}
                                </td>
                                <td align="right"> {{-- GRAVADA 5% --}}
                                    {{ $credit_notes ? number_format($credit_notes->total_iva5 - $credit_notes->amount_iva5 , 0, ',','.') : 0 }}
                                </td>
                                <td align="right"> {{-- IVA 10% --}}
                                    {{ $credit_notes ? number_format($credit_notes->amount_iva10, 0, ',','.') : 0 }}
                                </td>
                                <td align="right"> {{-- GRAVADA 10% --}}
                                    {{ $credit_notes ? number_format($credit_notes->total_iva10 - $credit_notes->amount_iva10, 0, ',','.') : 0 }}
                                </td>
                                <td align="right" id="total_final">{{$credit_notes ? number_format($credit_notes->amount, 0, ',', '.') : 0 }}</td>
                            </tr>
                        @endif
                    @endif
                @endforeach
                <tr>
                    <th colspan="2"></th>
                    <th colspan="8"><hr></th>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->total_excenta, 0, ',', '.') : 0 }}</td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->amount_iva5, 0, ',', '.') : 0 }}</td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->total_iva5, 0, ',', '.') : 0 }}</td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->amount_iva10, 0, ',', '.') : 0 }}</td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->total_iva10 - $purchases_sum->amount_iva10, 0, ',', '.') : 0 }}</td>
                    <td align="right">{{ $purchases_sum ? number_format($purchases_sum->amount, 0, ',', '.') : 0 }}</td>
                </tr>
            </tbody>
        </table>
        <div class="page_break"></div>
    </body>
    </html>
