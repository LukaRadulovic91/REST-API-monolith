<head>
    <base href=""/>
    <title>The Marshall Group</title>
    <meta charset="utf-8"/>
    <meta name="description"
          content="The Marshall Group is an exclusive staffing, recruitment and employment agency that has gained a reputation for consistently providing qualified candidates to employers in the Greater Toronto area."/>
    <meta name="keywords"
          content="employment, dentists, employment agency, dentist, agency, recruitment, staffing, medicine, Marshall group, Marshall, The Marshall group, The Marshall"/>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title"
          content="The Marshall Group - is an exclusive staffing, recruitment and employment agency"/>

    <meta property="og:url" content="https://marshall.thinkit.rs"/>
    <meta property="og:site_name" content="Marshall Group"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
{{--    <link rel="canonical" href="https://preview.keenthemes.com/metronic8"/>--}}
    <link rel="shortcut icon" href="{{asset('images/logos/logo.svg')}}"/>
    @stack('css')
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{asset('plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('plugins/custom/daterangepicker/css/daterangepicker.css')}}"/>
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}"  type="text/css"/>


    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/style.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/custom/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <!--end::Global Stylesheets Bundle-->
    <script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
