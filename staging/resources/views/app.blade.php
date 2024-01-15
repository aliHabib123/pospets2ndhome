<?php ini_set('display_errors', 'on');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" ng-app="tagpos">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Start Global Style
       =====================================================================-->
    <!-- jquery-ui css -->
    <link href="{{ URL::asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap -->
    <link href=" {{ URL::asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap rtl -->
    <!--<link href=" {{ URL::asset('assets/bootstrap-rtl/bootstrap-rtl.min.css')}}" rel="stylesheet" type="text/css"/>-->
    <!-- Lobipanel css -->
    <link href=" {{ URL::asset('assets/plugins/lobipanel/lobipanel.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Pace css -->
    <link href=" {{ URL::asset('assets/plugins/pace/flash.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link href=" {{ URL::asset('assets/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Pe-icon -->
    <link href=" {{ URL::asset('assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css')}}" rel="stylesheet" type="text/css" />
    <!-- Themify icons -->
    <link href=" {{ URL::asset('assets/themify-icons/themify-icons.css')}}" rel="stylesheet" type="text/css" />
    <!-- End Global Mandatory Style
       =====================================================================-->
    <!-- Start Theme Layout Style
       =====================================================================-->
    <!-- Theme style -->
    <link href=" {{ URL::asset('assets/dist/css/stylecrm.css')}}" rel="stylesheet" type="text/css" />
    <!-- Theme style rtl -->
    <!--<link href=" {{ URL::asset('assets/dist/css/stylecrm-rtl.css')}}" rel="stylesheet" type="text/css"/>-->
    <!-- End Theme Layout Style
       =====================================================================-->

    <?php /*<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:996945,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>

  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script>
     (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-1386993382328535",
          enable_page_level_ads: true
     });
</script>
  */ ?>
    <script type="text/javascript">
        const mainUrl = '<?php echo $site_url; ?>';
    </script>

    @yield('style')
</head>

<body class="hold-transition sidebar-mini">

    <!-- Site wrapper -->
    <div class="wrapper">
        <header class="main-header">
            <span class="logo">
                <!-- Logo -->
                <span class="logo-mini">
                    <img src="{{ URL::asset('assets/dist/img/mini-logo.png')}}" alt="">
                </span>
                <span class="logo-lg">
                    <img src="{{ URL::asset('assets/dist/img/logo.png')}}" alt="">
                </span>
            </span>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <!-- Sidebar toggle button-->
                    <span class="sr-only">Toggle navigation</span>
                    <span class="pe-7s-menu"></span>

                    <span style="float: right;margin-left: 14px;line-height: 26px;color: #d2e7fa">{{Auth::user()->name}}</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <span style="float: left;padding: 15px 14px;line-height: 26px;color: #d2e7fa">
                                @if(!empty(Session::get('selectedLocation')))
                                {!! Session::get('selectedLocation') !!}
                                @endif
                            </span>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" style="color: white" data-toggle="dropdown">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>

                            </a>

                            @if ($globalLocations->count() > 0)
                            <ul class="dropdown-menu">
                                @foreach($globalLocations as $value)
                                <li>
                                    <a href="{{ URL::to('/locations/saveLocation/'.$value->id) }}" class="border-gray">{{$value->name}}</a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        <!-- Notifications -->
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="pe-7s-bell"></i>
                                @if ($global_transfers->count() > 0)
                                <span class="label label-danger">
                                    {{ $global_transfers->count() }}
                                </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">

                                @foreach($global_transfers as $value)
                                <li>
                                    <a href="{{ URL::to('/transfer/'.$value->id) }}" class="border-gray">New Transfer
                                        from {{$value->fromLocation->name}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        <!--
                     -->
                        <li class="dropdown dropdown-user">

                            <a href="{{ route('logout') }}" style="color: white" onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- =============================================== -->
        <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar -->
            <div class="sidebar">
                <!-- sidebar menu -->
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ url('/') }}"><i class="fa fa-tachometer"></i><span>{{trans('menu.dashboard')}}</span>
                            <span class="pull-right-container">
                            </span>
                        </a>
                    </li>

                    @if (Auth::check())
                    @if (Auth::user()->hasPermissionTo('sales'))
                    <li>
                        <a href="{{ url('/sales') }}"><i class="fa fa-shopping-cart"></i><span>{{trans('menu.sales')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('wholesales'))
                    <li>
                        <a href="{{ url('/wholesales') }}"><i class="fa fa-shopping-cart"></i><span>{{trans('menu.wholesales')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->roles[0]->name !="Admin" && Auth::user()->roles[0]->name !="Custom Role")
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-paperclip"></i><span>{{trans('menu.reports')}}</span>
                        </a>
                        <ul class="treeview-menu active">
                            @if(Auth::user()->hasPermissionTo('sale_report'))
                            <li><a href="{{ url('/generalReports/sales') }}">{{trans('menu.sales_report')}}</a></li>
                            @endif
                            @if(Auth::user()->hasPermissionTo('reports'))

                            @endif
                            @if(Auth::user()->hasPermissionTo('closeout'))
                            <li><a href="{{ url('/generalReports/closeout') }}">{{trans('menu.closeout')}}</a></li>
                            @endif
                            <li><a href="{{ url('/generalReports/transfer') }}">{{trans('menu.transfer_report')}}</a></li>
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('receiving'))
                    <li>
                        <a href="{{ url('/receivings') }}"><i class="fa fa-inbox"></i><span>{{trans('menu.receivings')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('send_transfer'))
                    <li>
                        <a href="{{ url('/transfer') }}"><i class="fa fa-exchange"></i><span>{{trans('menu.transfer')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>

                    @endif

                    @if (Auth::user()->hasPermissionTo('expenses_view'))
                    <li>
                        <a href="{{ url('/expenses') }}"><i class="fa fa-dollar"></i><span>{{trans('menu.expenses')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @if (!Auth::user()->hasRole('User'))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-gears"></i><span>{{trans('menu.setup')}}</span>
                        </a>
                        <ul class="treeview-menu">
                            @if (Auth::user()->hasPermissionTo('items_view'))
                            <li><a href="{{ url('/items') }}">{{trans('menu.items')}}</a></li>
                            @endif
                            @if (Auth::user()->hasPermissionTo('categories_view'))
                            <li><a href="{{ url('/categories') }}">{{trans('menu.categories')}}</a></li>
                            @endif
                            @if (Auth::user()->hasPermissionTo('location_view'))
                            <li><a href="{{ url('/locations') }}">{{trans('menu.locations')}}</a></li>
                            @endif
                            @if (Auth::user()->hasPermissionTo('print_barcodes'))
                            <li><a href="{{ url('/barcodes') }}">{{trans('menu.barcodes')}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->hasRole('User'))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-gears"></i><span>{{trans('menu.setup')}}</span>
                        </a>
                        <ul class="treeview-menu">
                            @if (Auth::user()->hasPermissionTo('items_view_user'))
                            <li><a href="{{ url('/items') }}">{{trans('menu.items')}}</a></li>
                            @endif
                            @if (Auth::user()->hasPermissionTo('print_barcodes'))
                            <li><a href="{{ url('/barcodes') }}">{{trans('menu.barcodes')}}</a></li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if ((Auth::user()->hasPermissionTo('customers_view')) || (Auth::user()->hasPermissionTo('suppliers_view')))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-users"></i><span>{{trans('menu.people')}}</span>
                        </a>
                        <ul class="treeview-menu">
                            @if (Auth::user()->hasPermissionTo('suppliers_view'))
                            <li><a href="{{ url('/customers') }}">{{trans('menu.customers')}}</a></li>
                            @endif
                            @if (Auth::user()->hasPermissionTo('suppliers_view'))
                            <li><a href="{{ url('/suppliers') }}">{{trans('menu.suppliers')}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('refund'))
                    <li>
                        <a href="{{ url('/refund') }}"><i class="fa fa-undo"></i><span>{{trans('menu.refund')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('reports'))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-paperclip"></i><span>{{trans('menu.reports')}}</span>
                        </a>
                        <ul class="treeview-menu active">
                            <li><a href="{{ url('/generalReports/receivings') }}">{{trans('menu.receivings_report')}}</a>
                            </li>
                            <li><a href="{{ url('/generalReports/sales') }}">{{trans('menu.sales_report')}}</a></li>
                            <li><a href="{{ url('/generalReports/wholesales') }}">{{trans('menu.wholesales_report')}}</a></li>
                            <li><a href="{{ url('/generalReports/transfer') }}">{{trans('menu.transfer_report')}}</a></li>
                        </ul>
                        <ul class="treeview-menu">
                            <li><a href="{{ url('/generalReports/closeout') }}">{{trans('menu.closeout')}}</a></li>
                            <li><a href="{{ url('/generalReports/itemReport') }}">{{trans('menu.item_report')}}</a></li>
                            <li><a href="{{ url('/generalReports/categoriesProfit') }}">{{trans('menu.categories')}}</a></li>
                        </ul>
                        @if (Auth::user()->hasPermissionTo('reports'))
                        <ul class="treeview-menu">
                            <li><a href="{{ url('/generalReports/inventoryLocations') }}">Inventory</a></li>
                        </ul>
                        @endif
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('custom_reports'))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-paperclip"></i><span>{{trans('menu.reports')}}</span>
                        </a>
                        <ul class="treeview-menu active">
                            <li><a href="{{ url('/generalReports/wholesales') }}">{{trans('menu.wholesales_report')}}</a></li>
                            <li><a href="{{ url('/generalReports/closeout2') }}">{{trans('menu.closeout')}}</a></li>
                        </ul>
                        <!-- @if (Auth::user()->hasPermissionTo('reports'))
                        <ul class="treeview-menu">
                            <li><a href="{{ url('/generalReports/inventoryLocations') }}">Inventory</a></li>
                        </ul>
                        @endif -->
                    </li>
                    @endif
                    @if (Auth::user()->hasPermissionTo('users'))
                    <li class="treeview active">
                        <a href="#">
                            <i class="fa fa-user"></i><span>{{trans('menu.users')}}</span>
                        </a>
                        <ul class="treeview-menu">

                            @if (Auth::user()->hasPermissionTo('admin'))
                            <li>
                                <a href="{{ route('admin.permissions.index') }}">@lang('global.permissions.title')</a>
                            </li>
                            <li><a href="{{ route('admin.roles.index') }}"> @lang('global.roles.title')</a></li>
                            @endif
                            <li><a href="{{ route('admin.users.index') }}"> @lang('global.users.title')</a></li>
                        </ul>
                    </li>
                    @endif

                    @if (Auth::user()->hasPermissionTo('settings'))
                    <li>
                        <a href="{{ url('/settings') }}"><i class="fa fa-gear"></i><span>{{trans('menu.settings')}}</span>
                            <span class="pull-right-container"></span>
                        </a>
                    </li>
                    @endif
                    @endif
                </ul>
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- =============================================== -->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <?php /*        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Login -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1386993382328535"
     data-ad-slot="2924870764"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>*/ ?>
            @yield('content')

        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs"><b>Version</b> 1.0</div>
            <strong>Copyright &copy; <?= date('Y'); ?> </strong>
        </footer>
    </div>
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src=" {{ URL::asset('assets/plugins/jQuery/jquery-1.12.4.min.js')}}" type="text/javascript"></script>
    <!-- jquery-ui -->
    <script src=" {{ URL::asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js')}}" type="text/javascript"></script>
    <!-- Bootstrap -->
    <script src=" {{ URL::asset('assets/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <!-- lobipanel -->
    <script src=" {{ URL::asset('assets/plugins/lobipanel/lobipanel.min.js')}}" type="text/javascript"></script>
    <!-- Pace js -->
    <script src=" {{ URL::asset('assets/plugins/pace/pace.min.js')}}" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src=" {{ URL::asset('assets/plugins/slimScroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
    <!-- FastClick -->
    <script src=" {{ URL::asset('assets/plugins/fastclick/fastclick.min.js')}}" type="text/javascript"></script>
    <!-- CRMadmin frame -->
    <script src=" {{ URL::asset('assets/dist/js/custom.js')}}" type="text/javascript"></script>
    <!-- End Core Plugins
   =====================================================================-->
    <!-- Dashboard js -->
    <script src=" {{ URL::asset('assets/dist/js/dashboard.js')}}" type="text/javascript"></script>
    <!-- End Theme label Script
   =====================================================================-->
    @yield('script')
</body>

</html>