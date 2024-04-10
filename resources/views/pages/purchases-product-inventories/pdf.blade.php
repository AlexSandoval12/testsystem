<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            size: A4;
            margin: 0px;
            padding: 0px;
        }
        * {
            margin: 0px;
            padding: 0px;
            font-size: 10px;
            font-family: 'dejavu sans';
        }
        html {
            margin: 15px;
        }
        body {
            margin: 0px 5px;
        }
        table {
            border-collapse: collapse;
        }
        .with_border td {
            border: 1px black solid;
            padding: 3px;
        }
        .with_outside_border {
            border: 1px black solid;
        }
        .with_side_border td {
            border-right: 1px black solid;
            border-left: 1px black solid;
            padding: 3px;
        }
        .border_with_padding {
            padding: 3px;
            border: 1px black solid;
        }
    </style>
</head>
<body>
    <div class="border_with_padding">
        <table width="100%">
            <tr>
                <td width="30%" valign="top" style="font-size:7px;">
                    
                </td>
                <td width="40%" valign="top" align="center" style="font-size:20px;">
                    Inventario de Stock
                </td>
                <td width="30%" valign="top" align="right" style="font-size: 10px;">
                    Nro. :
                    {{ $purchases_product_inventory->id }}
                </td>
            </tr>
        </table>
    </div>    
    <br>
    <br>
    <div class="border_with_padding">
        <table width="100%">
            <tr>
                <td width="50%"><b>Deposito Origen:</b> {{ $purchases_product_inventory->deposit->name }}</td>
                <td width="50%"><b>Fecha:</b> {{ $purchases_product_inventory->date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td width="100%"><b>Observacion:</b> {{ $purchases_product_inventory->observation }}</td>                
            </tr>
        </table>
    </div>
    <br>    
    <table width="100%" style="font-size:10px;">
        <tr class="with_border">
            <td width="15%" align="center"><b>Código</b></td>
            <td width="50%" align="center"><b>Nombre Producto</b></td>
            <td width="15%" align="center"><b>Cantidad</b></td>
            <td width="10%" align="center"><b>Existencia</b></td>
            <td width="10%" align="center"><b>Tipo</b></td>
        </tr>
        @foreach($purchases_product_inventory->purchases_product_inventory_details as $detail)
            <tr class="with_border">
                <td valign="top" align="right">{{ $detail->product_id }}</td>
                <td valign="top">{{ $detail->purchases_product->name }}</td>
                <td valign="top" align="right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                <td valign="top" align="right">{{ number_format($detail->existence, 0, ',', '.') }}</td>
                <td valign="top" align="right">
                    {{ $detail->quantity > $detail->existence ? 'Entrada' : ($detail->quantity == $detail->existence ? 'Sin Movimiento' : 'Salida') }}
                </td>
            </tr>
        @endforeach            
    </table>
</body>
</html>
