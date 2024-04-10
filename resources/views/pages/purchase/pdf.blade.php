<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * {
            font-size: 12px;
            font-family: Helvetica;
        }
        @page {
            margin: 15px;
        }
        body {
            margin: 15px;
        }
        table {
            border-collapse: collapse;
        }
        td {
            padding: 3px;
        }
        .with_border td {
            border: 1px black solid;
        }
        ol {
            padding: 0px 0px 0px 20px;
        }
        #watermark {
            position: fixed;
            top: 90%;
            left: 60%;
            width: 100%;
            text-align: center;
            opacity: .4;
            z-index: -1000;
        }
    </style>
</head>
<body style="background-repeat: no-repeat; background-position: bottom right;">
    <table width="100%">
        <tr>
            <td valign="bottom" align="left" style="font-size:20px;">
                {{ config('constants.type_purchases.'. $purchase->type). ' - ' . $purchase->number }}
            </td>
        </tr>
    </table>
    <hr style="height:1px; border:none; color:#C0C0C0; background-color:#C0C0C0;" />
    <table width="100%">
        <tr>
            <td width="15%" style="padding:3px;"><b>Proveedor:</b></td>
            <td width="85%" style="padding:3px;">{{ $purchase->provider->name }}</td>
        </tr>
        <tr>
            <td width="20%" style="padding:3px;"><b>Fecha:</b></td>
            <td width="30%" style="padding:3px;">{{ $purchase->date->format('d/m/Y') }}</td>
            <td width="20%" style="padding:3px;"><b>Ruc:</b></td>
            <td width="30%" style="padding:3px;">{{ $purchase->ruc }}</td>
        </tr>
        <tr>
            <td width="20%" style="padding:3px;"><b>Moneda:</b></td>
            <td width="30%" style="padding:3px;">GUARANI</td>
            <td width="20%" style="padding:3px;"><b>Timbrado:</b></td>
            <td width="30%" style="padding:3px;">{{ $purchase->stamped }}</td>
        </tr>
    </table>
    <div style="font-size:10px; border: 1px solid black; border-radius: 5px;">
        <table width="100%">
            <tr>
                <td width="20%" align="center"><b>Producto</b></td>
                <td width="20%" align="center"><b>Descripción</b></td>
                <td width="15%" align="center"><b>Precio</b></td>
                <td width="15%" align="center"><b>Excenta</b></td>
                <td width="15%" align="center"><b>IVA 5%</b></td>
                <td width="15%" align="center"><b>IVA 10%</b></td>
            </tr>
        </table>
    </div>
    <table width="100%">
        @foreach($purchase->purchase_details as $details)
            <tr>
                <td width="20%" valign="top">{{ $details->material->description }}</td>
                <td width="20%" valign="top">{{ $details->description }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($details->amount, 0, ',', '.') }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($details->excenta, 0, ',', '.') }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($details->iva5, 0, ',', '.') }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($details->iva10, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <div style="font-size:10px; border: 1px solid black; border-radius: 5px;">
        <table width="100%">
            <tr>
                <td width="55%" valign="top" align="right">SUB-TOTAL</td>                
                <td width="15%" valign="top" align="right">{{ number_format($purchase->total_excenta, 0, ',', '.') }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($purchase->total_iva5, 0, ',', '.') }}</td>
                <td width="15%" valign="top" align="right">{{ number_format($purchase->total_iva10, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    <br>
    <table width="100%">
        <tr>
            <td valign="bottom" align="right" style="font-size:16px;">
                {{ 'Total Compra = ' . number_format($purchase->amount, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</body>
</html>
