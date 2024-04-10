<style type="text/css">
    * {
        font-family: texta;
        line-height: 12px;
    }
    @font-face {
        font-family: texta;
        src: url("{{ asset('fonts/Texta-Regular.ttf') }}");
        font-weight: normal;
        line-height: 0px;
    }
    @font-face {
        font-family: texta;
        src: url("{{ asset('fonts/Texta-Bold.ttf') }}");
        font-weight: bold;
        line-height: 0px;
    }
    @page {
        margin: 10px;
    }
	img{
        position: absolute;
        top: 5%;
        left: 5%;
        width: 90%;
	}
</style>
<div>
    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG(str_pad($articulo->id, 5, '0', STR_PAD_LEFT), 'C128')}}" alt="barcode" />
    {{-- <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($articulo->name, 'QRCODE', 8, 8)}}" alt="barcode" /> --}}
    <div style="left: 40%;position: absolute; top: 70px;"><br><br><br>
        {{ $articulo->id }} - {{ $articulo->name }}
    </div>
</div>