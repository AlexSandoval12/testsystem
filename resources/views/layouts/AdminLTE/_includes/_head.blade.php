<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>
        {!! \App\Models\Config::find(1)->app_name_abv !!} | @yield('title')
</title>
<link rel="shortcut icon" href="{{ asset(\App\Models\Config::find(1)->favicon) }}" type="image/x-icon"/>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/font-awesome/css/font-awesome.min.css') }}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/select2/dist/css/select2.min.css') }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/dist/css/AdminLTE.min.css') }}">
<!-- adminlte Skins. -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/dist/css/skins/_all-skins.min.css') }}">
<!-- Morris chart -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/jvectormap/jquery-jvectormap.css') }}">
<!-- Date Picker -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/plugins/iCheck/square/blue.css') }}">
<link rel="stylesheet" href="{{ asset('assets/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
<link rel="stylesheet" href="{{ cached_asset('css/bootstrap-3.3.7.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ cached_asset('css/font-awesome-5.6.3.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ cached_asset('css/toastr.2.1.4.min.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/iCheck/custom.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/table_scroll.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/magnific-popup.css') }}">
    {{-- <link rel="stylesheet" href="{{ cached_asset('css/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"> --}}
    <link rel="stylesheet" href="{{ cached_asset('css/helpers.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/select2-4.0.5.min.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/fullcalendar-3.0.9.min.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/bootstrap-datetimepicker-2.4.4.min.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/bootstrap-datepicker-1.8.0.min.css') }}">
    <link rel="stylesheet" href="{{ cached_asset('css/blueimp/css/blueimp-gallery.min.css') }}">

<!-- Google Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<!-- CSS Custom -->
<link rel="stylesheet" href="{{ asset('assets/adminlte/documentation/style.css') }}">
<!-- jQuery 3 -->
<script src="{{ asset('assets/adminlte/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- MAskMoney -->
<style>
        .link_menu_page{ color:#222d32; }
        .caixa-alta { text-transform:uppercase; }
        .caixa-baixa { text-transform:lowercase; }
        .input-text-center{ text-align:center; }
</style>

<script>
        $(function(){
                $.fn.datepicker.dates['pt-br'] = {
                        days: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"],
                        daysShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
                        daysMin: ["Do", "Se", "Te", "Qu", "Qu", "Se", "Sa"],
                        months: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                        monthsShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                        today: "Hoje",
                        monthsTitle: "Meses",
                        clear: "Limpar",
                        format: "dd/mm/yyyy"
                };
        });
</script>

@yield('layout_css')
