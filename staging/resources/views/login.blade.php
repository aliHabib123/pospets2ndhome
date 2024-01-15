<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-73135470-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'UA-73135470-1');
    </script>
      
    <link href=" {{ URL::asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href=" {{ URL::asset('assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css')}}" rel="stylesheet" type="text/css"/>
    <link href=" {{ URL::asset('assets/dist/css/stylecrm.css')}}" rel="stylesheet" type="text/css"/>
    <link href=" {{ URL::asset('assets/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css"/>


</head>
  <body class="hold-transition sidebar-mini">
 
    @yield('content')<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Home --> 
    <script src=" {{ URL::asset('assets/plugins/jQuery/jquery-1.12.4.min.js')}}" type="text/javascript"></script>
    <script src=" {{ URL::asset('assets/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>

  </body>
</html>