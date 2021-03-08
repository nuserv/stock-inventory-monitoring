<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        @if(Auth::guest())
        <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }};url={{ url('/login') }}">
        @endif
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @auth
            @if(auth()->user()->branch->branch != "Warehouse")
                <meta http-equiv="refresh" content="601;url={{ url('/logout') }}">
            @else
                <meta http-equiv="refresh" content="895">
            @endif
        <meta name="ctok" content="{{ csrf_token() }}">
        @endauth
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="{{ url('/css/style.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ url('/css/styles.css') }}" />
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.css"/>
        @auth
            <title>{{$title}}</title>
        @else
            <title>Login</title>
        @endauth
        <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <style>
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

        @if(Request::is('request'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                @include('modal.warehouse.request')
                @include('modal.warehouse.send')
                @include('modal.warehouse.resched')
            @endif
            @if(auth()->user()->hasAnyrole('Manager', 'Editor'))
                @include('modal.warehouse.request')
                @include('modal.remarks')
            @endif
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                @include('modal.branch.request')
                @include('modal.branch.send')
            @endif
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
                @include('modal.branch.in-option')
                @include('modal.branch.service-in')
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
            @else
                @include('modal.branch.import')
                @include('modal.branch.add')
                @include('modal.branch.pull-out')
                @include('modal.branch.in-option')
                @include('modal.branch.good')
                @include('modal.branch.replacement')
                @include('modal.branch.replace-return')
                @include('modal.branch.stock')
                @include('modal.branch.password')
            @endif
        @endif

        @if(Request::is('user'))
            @include('modal.warehouse.user')
        @endif

        @if(Request::is('loans'))
            @include('modal.branch.loans')
            @include('modal.branch.loan')
        @endif

        @if(Request::is('return'))
            @include('modal.branch.return')
        @endif

        @if (Request::is('/') && auth()->user()->hasrole('Repair'))
            @include('modal.branch.return')
        @endif
        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <!--script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script-->
        <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
        
        @if(Request::is('user'))
            <script src="{{asset('min/?f=js/warehouse/user.js')}}"></script>
        @endif

        @if(Request::is('defective/print'))
            <script src="{{asset('min/?f=js/branch/printdef.js')}}"></script>
        @endif

        @if(Request::is('branch'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/branch.js')}}"></script>
            @endif
            @if (auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/branch.js')}}"></script>
            @endif
            @if (auth()->user()->hasanyrole('Editor', 'Manager'))
                <script src="{{asset('min/?f=js/branch.js')}}"></script>
            @endif
        @endif

        @if(Request::is('request'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/request.js')}}"></script>
                <script src="{{asset('min/?f=js/warehouse/request1.js')}}"></script>
                <script src="{{asset('min/?f=js/warehouse/request2.js')}}"></script>
                <script src="{{asset('min/?f=js/warehouse/request3.js')}}"></script>
            @endif
            @if(auth()->user()->hasanyrole('Editor', 'Manager'))
                <script src="{{asset('min/?f=js/request.js')}}"></script>
            @endif
            @if(auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('js/branch/request.js')}}"></script>
                <script src="{{asset('js/branch/request2.js')}}"></script>
            @endif
        @endif

        @if(Request::is('stocks'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/stock.js')}}"></script>
            @else
                <script src="{{asset('min/?f=js/branch/stocks.js')}}"></script>
                <script src="{{asset('min/?f=js/branch/service-in.js')}}"></script>
            @endif
            @if(auth()->user()->hasrole('Head'))
                <script src="{{asset('min/?f=js/branch/addstock.js')}}"></script>
            @endif

        @endif

        @if(Request::is('service-unit'))
            <script src="{{asset('min/?f=js/branch/service-unit.js')}}"></script>
            <script src="{{asset('min/?f=js/branch/service-out.js')}}"></script>
        @endif

        @if(Request::is('preventive'))
            <script src="{{asset('min/?f=js/branch/pm-service-unit.js')}}"></script>
            <script src="{{asset('min/?f=js/branch/pm.js')}}"></script>
        @endif

        @if(Request::is('print/*'))
            <script src="{{asset('min/?f=js/warehouse/print.js')}}"></script>
        @endif

        @if(Request::is('loans'))
            <script src="{{asset('min/?f=js/branch/loans.js')}}"></script>
        @endif

        @if(Request::is('return'))
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
                <script src="{{asset('min/?f=js/warehouse/defective.js')}}"></script>
            @endif
            @if(auth()->user()->hasanyrole('Editor', 'Manager'))
                <script src="{{asset('min/?f=js/defective.js')}}"></script>
            @endif
            @if (auth()->user()->hasAnyrole('Head', 'Tech'))
                <script src="{{asset('min/?f=js/branch/defective.js')}}"></script>
            @endif
        @endif
        
        @if(Request::is('customer'))
            <script src="{{asset('min/?f=js/customer.js')}}"></script>
        @endif
        @if(Request::is('customer/*'))
            <script src="{{asset('min/?f=js/customerbranch.js')}}"></script>
        @endif

        @if(Request::is('/') && !auth()->user()->hasrole('Repair'))
            <script src="{{asset('min/?f=js/home.js')}}"></script>
        @endif
        @if (Request::is('/') && auth()->user()->hasrole('Repair'))
            <script src="{{asset('min/?f=js/warehouse/defective.js')}}"></script>
        @endif
        @if(Request::is('log') && auth()->user()->hasrole('Repair'))
            <script src="{{asset('min/?f=js/home.js')}}"></script>
        @endif
        @if(Request::is('unrepair') && auth()->user()->hasanyrole('Repair', 'Editor', 'Manager'))
            <script src="{{asset('min/?f=js/unrepair.js')}}"></script>
        @endif
    </body>
</html>