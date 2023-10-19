<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        @if(Auth::guest())
        <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }};url={{ url('/login') }}">
        @endif
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @auth
        @if (Session::has('success') && Request::is('report-a-problem'))
            <meta http-equiv="refresh" content="5;url={{ url('/') }}">
        @else
            @if(auth()->user()->hasanyrole('Warehouse Administrator', 'Manager', 'Editor'))
                <meta http-equiv="refresh" content="1800">
            @else
                @if(auth()->user()->branch->branch != "Warehouse")
                    <meta http-equiv="refresh" content="1800;url={{ url('/logout') }}">
                @endif
            @endif
            <meta name="ctok" content="{{ csrf_token() }}">
        @endif
        @endauth
        @auth
            <script src="https://unpkg.com/jquery@2.2.4/dist/jquery.js"></script>
        @endauth
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="{{ url('/css/style.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ url('/css/styles.css') }}" />
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
        {{-- <link href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css" /> --}}
        <link href="{{ url('/css/jquery-ui.css') }}" rel="Stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/af-2.3.7/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/cr-1.5.4/date-1.1.1/fc-3.3.3/fh-3.1.9/kt-2.6.4/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.5/sb-1.2.1/sp-1.4.0/datatables.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css'>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.all.min.js"></script>
        @auth
        @else
            <title>Login</title>
        @endauth
        <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <style>
            .f-outline{
            position:relative;
            }
            .forminput{
                background-color:white;
                color:black !important;
            }
            .formlabel{
                position:absolute;
                left:0.2rem;
                top:0.5rem;
                padding:0 0.5rem;
                color:black;
                cursor:text;
                transition: top 200ms ease-in,
                    left 200ms ease-in,
                    font-size 200ms ease-in;
            }
            .forminput:focus{
                background-color:white;
                border-color:#0d1a80;
                box-shadow:0 0 10px #0d1a80;
            }
            .forminput:hover{
                background-color:white;
                border-color:#0d1a80;
                box-shadow:0 0 10px #0d1a80;
                color:black;
            }
            .forminput:focus ~ .form-label,
            .forminput:not(:placeholder-shown).forminput:not(:focus)
            ~.formlabel{
                top:-0.8rem;
                font-size:0.8rem;
                left:1rem;
                color:#0d1a80;
                background-color:white;
            }
            input:-webkit-autofill,
            input:-webkit-autofill:focus {
                transition: background-color 600000s 0s, color 600000s 0s;
            }
            input[data-autocompleted] {
                background-color: transparent !important;
            }
            #loading {
                display: none;
                position: absolute;
                top: 0;
                left: 0;
                z-index: 100;
                width: 100vw;
                height: 100vh;
                background-color: rgba(192, 192, 192, 0.5);
                background-image: url("{{asset('loading.gif')}}");
                background-repeat: no-repeat;
                background-position: center;
                }
            input, select, textarea{
                color: black;
            }
            .legend { list-style: none; }
            .legend li { float: left; margin-right: 10px; }
            .legend span { border: 1px solid #ccc; float: left; width: 12px; height: 12px; margin: 2px; }
            /* your colors */
            .legend .BLUE { background-color: blue; }
            .legend .GREEN { background-color: green; }
            .legend .MAGENTA { background-color: darkmagenta; }
            .legend .GRAYROW { background-color: gray; }
            .legend .RED { background-color: #F1423A; }
            li:hover {
                background-color: #4285f4;
                color: white !important; 
                border-radius: 4px !important;
            }
            .nohover:hover {
                background-color: transparent !important;
            }
           .nav-link{
                border: 0px solid #88f2fa !important;
                border-radius: 4px !important;
                padding-right:8px !important;
                padding-left:8px !important;
                padding: 4px
           }
           .active{
                padding: 4px 0;
           }
            .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
                background-color: #DCDCDC;
            }
        </style>
    </head>
    <body>
    <div id="loading"></div>
        @include('inc.header')
        @include('inc.navbar')
        @if(!Auth::guest())
            <input type="text" hidden id="level" value="{{ auth()->user()->roles->first()->name }}">
        @endif
        <div class="py-2">
        @yield('content')
        </div>

        @if(Request::is('branch'))
            @include('modal.warehouse.branch')
            @if(auth()->user()->hasrole('Warehouse Manager'))
                @include('modal.warehouse.initial')
            @endif
        @endif

        @if(Request::is('billable'))
             @include('modal.branch.billable')
             @include('modal.branch.billable-approval')
        @endif

        @if(Request::is('schedule'))
             @include('modal.pmsched')
        @endif

        @if(Request::is('pmlist'))
             @include('modal.branch.export')
        @endif

        @if(Request::is('request'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                @include('modal.warehouse.request')
                @include('modal.warehouse.send')
                @include('modal.warehouse.resched')
                @include('modal.warehouse.serial')
                @include('modal.remarks')
            @endif
            @if(auth()->user()->hasAnyrole('Manager', 'Editor', 'Warehouse Administrator'))
                @include('modal.warehouse.request')
                @include('modal.remarks')
            @endif
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                @include('modal.branch.request')
                @include('modal.branch.send')
            @endif
        @endif
        @if(Request::is('resolved'))
            @include('modal.warehouse.resolved')
        @endif

        @if(Request::is('customer'))
            @if(auth()->user()->hasrole('Editor'))
                @include('modal.customer')
            @endif
        @endif

        @if(Request::is('customer/*'))
            @if(auth()->user()->hasrole('Editor'))
                @include('modal.customerbranch')
            @endif
        @endif

        @if(Request::is('service-unit'))
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                @include('modal.branch.in')
                @include('modal.branch.out')
                @include('modal.branch.pull-in')
                @include('modal.branch.service-in')
            @endif
        @endif
        @if(Request::is('pullview'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                @include('modal.warehouse.pullout')
            @endif
        @endif
        @if(Request::is('pullviewlist'))
            @if(auth()->user()->hasAnyrole('Head'))
                @include('modal.warehouse.pullout')
            @endif
        @endif
        @if(Request::is('bufferviewlist'))
            @if(auth()->user()->hasAnyrole('Main Warehouse Manager','Warehouse Manager', 'Warehouse Administrator') || auth()->user()->id == 283 || auth()->user()->id == 110)
                @include('modal.warehouse.buffer')
                @include('modal.warehouse.sendbuffer')
            @endif
        @endif

        @if(Request::is('repaired-ware'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                @include('modal.warehouse.repaired')
            @endif
        @endif
        @if(Request::is('repaired-list'))
            @include('modal.warehouse.repaired')
        @endif
        @if(Request::is('returnview'))
            @if(auth()->user()->hasAnyrole('Repair') || auth()->user()->id == 326)
                @include('modal.warehouse.return')
                @include('modal.warehouse.serial')
            @endif
            @if(auth()->user()->hasAnyrole('Head'))
                @include('modal.branch.return')
            @endif
        @endif

        @if(Request::is('preventive'))
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                @include('modal.out')
                @include('modal.pm-service-in')
            @endif
        @endif

        @if(Request::is('stocks'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                @include('modal.warehouse.add')
                @include('modal.warehouse.category')
                @include('modal.warehouse.item')
                @include('modal.warehouse.import')
                @include('modal.branch.qty')
            @endif
            @if(auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-office')
                @include('modal.branch.import')
                @include('modal.branch.add')
                @include('modal.branch.pull-out')
                @include('modal.branch.good')
                @include('modal.branch.replacement')
                @include('modal.branch.replace-return')
                @include('modal.branch.stock')
                @include('modal.branch.password')
                @include('modal.branch.qty')
            @endif
        @endif

        @if(Request::is('user'))
            @include('modal.warehouse.user')
        @endif
        @if(Request::is('disposed'))
            @include('modal.dreports')
        @endif
        @if(Request::is('itemrequest'))
            @include('modal.itemrequest')
        @endif
        @if(Request::is('schedule'))
            @include('modal.addPM')
        @endif
        @if(Request::is('loans'))
            @include('modal.branch.loans')
            @include('modal.branch.loan')
        @endif

        @if(Request::is('item'))
            @include('modal.warehouse.items')
        @endif

        @if(Request::is('return'))
            @include('modal.branch.conversion')
            @include('modal.branch.conversiondetails')
        @endif

        @if (Request::is('/') && auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI'))
            @if (auth()->user()->id == 153)
                @include('modal.warehouse.request')
            @endif
        @endif

        @if (Request::is('pending') && auth()->user()->hasanyrole('Viewer', 'Viewer PLSI', 'Viewer IDSI'))
            @include('modal.warehouse.request')
        @endif
        
        @auth
            {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
            <script src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
            {{-- <script type="application/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
            <script type="application/javascript" src="{{asset('js/jquery-ui.js')}}"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
            <!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script-->
            <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
            {{-- <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.js"></script> --}}
            {{-- <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script> --}}
            <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
            {{-- <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script> --}}
            <script src="{{asset('js/jquery-ui.min.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js" charset="utf-8"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
            {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> --}}
            <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.2/af-2.3.7/b-2.0.0/b-colvis-2.0.0/b-html5-2.0.0/b-print-2.0.0/cr-1.5.4/date-1.1.1/fc-3.3.3/fh-3.1.9/kt-2.6.4/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.5/sb-1.2.1/sp-1.4.0/sl-1.3.3/datatables.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
            <script src="//cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js" type="text/javascript"></script>
            <script src="/js/datetime.js"></script>
        @endauth
        @yield('script')
        @if(Request::is('user'))
            <script src="{{asset('min/?f=js/warehouse/user.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('schedule'))
            @if (auth()->user()->hasanyrole('Manager', 'Editor'))
                <script src="{{asset('min/?f=js/pmschedmanager.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @else
                <script src="{{asset('min/?f=js/pmsched.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif

        @if(Request::is('pmlist'))
            <script src="{{asset('min/?f=js/pmdone.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('defective/print'))
            <script src="{{asset('min/?f=js/branch/printdef.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('POS'))
            <script src="{{asset('min/?f=js/branch/pos.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('defective/retno'))
            <script src="{{asset('min/?f=js/branch/retno.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('branch'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/branch.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/branch.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasanyrole('Editor', 'Manager', 'Viewer', 'Viewer PLSI', 'Viewer IDSI'))
                <script src="{{asset('min/?f=js/branch.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('billable'))
            <script src="{{asset('min/?f=js/branch/billable.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('request/*'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request1.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request3.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if(auth()->user()->hasanyrole('Editor', 'Manager', 'Warehouse Administrator'))
                <script src="{{asset('min/?f=js/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/branch/request2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('request'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request1.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/request3.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if(auth()->user()->hasanyrole('Editor', 'Manager', 'Warehouse Administrator'))
                <script src="{{asset('min/?f=js/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/request.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/branch/request2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('resolved'))
            <script src="{{asset('min/?f=js/resolved.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('itemrequest'))
            <script src="{{asset('min/?f=js/itemrequest.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('buffer'))
            <script src="{{asset('min/?f=js/warehouse/buffer.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('pullview'))
            @if (auth()->user()->hasanyrole('Head'))
                <script src="{{asset('min/?f=js/branch/pullout.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/pullout.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('pullviewlist'))
            @if (auth()->user()->hasanyrole('Head'))
                <script src="{{asset('min/?f=js/branch/pulloutlist.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('bufferviewlist'))
            @if (auth()->user()->hasanyrole('Main Warehouse Manager','Warehouse Manager', 'Warehouse Administrator') || auth()->user()->id == 283 || auth()->user()->id == 110)
                <script src="{{asset('min/?f=js/warehouse/bufferlist.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/bufferlist2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('returnview'))
            @if (auth()->user()->hasanyrole('Repair') || auth()->user()->id == 326)
                <script src="{{asset('min/?f=js/warehouse/return.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasanyrole('Head'))
                <script src="{{asset('min/?f=js/branch/return.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('repaired-ware'))
            @if (auth()->user()->hasanyrole('Repair') || auth()->user()->id == 326)
                <script src="{{asset('min/?f=js/warehouse/repaired.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/addtostock.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('repaired-list'))
            @if (auth()->user()->hasanyrole('Repair') || auth()->user()->id == 326)
                <script src="{{asset('min/?f=js/warehouse/repairedlist.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('stocks'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Editor', 'Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/stock.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/warehouse/stock2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @else
                <script src="{{asset('min/?f=js/branch/stocks.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                <script src="{{asset('min/?f=js/branch/service-in.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                @if(auth()->user()->hasrole('Head'))
                    <script src="{{asset('min/?f=js/branch/addstock.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
                @endif
            @endif
            

        @endif

        @if(Request::is('service-unit'))
            <script src="{{asset('min/?f=js/branch/service-unit.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            <script src="{{asset('min/?f=js/branch/service-out.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('preventive'))
            <script src="{{asset('min/?f=js/branch/pm-service-unit.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            <script src="{{asset('min/?f=js/branch/pm.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('print/*'))
            <script src="{{asset('min/?f=js/warehouse/print.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('loans'))
            <script src="{{asset('min/?f=js/branch/loans.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if(Request::is('return'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/defective.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if(auth()->user()->hasanyrole('Editor', 'Manager'))
                <script src="{{asset('min/?f=js/defective.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            @if (auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/defective.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
             @if (auth()->user()->branch->branch == 'Conversion')
                <script src="{{asset('min/?f=js/branch/conversion.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        
        @if(Request::is('customer'))
            <script src="{{asset('min/?f=js/customer.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @if (auth()->user()->hasAnyrole('Head','Tech'))
                <script src="{{asset('min/?f=js/branch/customer.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
        @endif
        @if(Request::is('customer/*'))
            <script src="{{asset('min/?f=js/customerbranch.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        
        @if (Request::is('pending') && auth()->user()->hasanyrole('Viewer','Viewer PLSI', 'Viewer IDSI'))
            <script src="{{asset('min/?f=js/pending.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif

        @if (Request::is('/') && auth()->user()->hasanyrole('Viewer','Viewer PLSI', 'Viewer IDSI'))
            @if (auth()->user()->id == 153)
                <script src="{{asset('min/?f=js/pending.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @else
                <!--script src="{{asset('min/?f=js/pending.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script-->
                <script src="{{asset('min/?f=js/viewer.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
            @endif
            
        @endif
        @if (Request::is('reprec') && auth()->user()->id == 326)
            <script src="{{asset('min/?f=js/warehouse/repair.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('/') && !auth()->user()->hasanyrole('Repair', 'Warehouse Administrator', 'Viewer', 'Viewer PLSI', 'Viewer IDSI'))
            <script src="{{asset('min/?f=js/home.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if (Request::is('/') && auth()->user()->id == 326)
            <script src="{{asset('min/?f=js/warehouse/repair.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if (Request::is('/') && auth()->user()->hasrole('Repair'))
            <script src="{{asset('min/?f=js/warehouse/repair.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if (Request::is('/') && auth()->user()->hasrole('Warehouse Administrator'))
            <script src="{{asset('min/?f=js/unrepair.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('log') && auth()->user()->hasanyrole('Repair', 'Warehouse Administrator'))
            <script src="{{asset('min/?f=js/home.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('item') && auth()->user()->hasanyrole('Manager', 'Editor'))
            <script src="{{asset('min/?f=js/item.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('item') && auth()->user()->hasanyrole('Warehouse Manager'))
            <script src="{{asset('min/?f=js/warehouse/item.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('disposed') && auth()->user()->hasrole('Warehouse Administrator'))
            <script src="{{asset('min/?f=js/disposed.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('unrepair') && auth()->user()->hasanyrole('Repair', 'Editor', 'Manager'))
            <script src="{{asset('min/?f=js/unrepair.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @if(Request::is('email/verify'))
            <script src="{{asset('min/?f=js/verify.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
        @endif
        @auth
            @if(Request::is('reports'))
            <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
                {!! $chart->script() !!}
            @endif
        @endauth
    </body>
</html>