<nav class="nav nav-tabs navbar-expand-md">
    <div class="navbar-collapse collapse justify-content-between align-items-center w-100">
        @auth
        <ul class="nav mr-auto">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
            </li>
            @if(auth()->user()->hasrole('Repair'))
                
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('unrepair') ? 'active' : '' }}" href="{{ url('/unrepair') }}">Unrepairable</a>
                </li>
            @endif
            @if(!auth()->user()->hasrole('Repair'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('branch') ? 'active' : '' }}" href="{{ route('branch.index') }}">Service Center</a>
                </li>
                @if(auth()->user()->branch->branch != 'Warehouse')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('customer') ? 'active' : '' }} {{ Request::is('customer/*') ? 'active' : '' }}" href="{{ url('customer') }}">Customer</a>
                    </li>
                @endif
                @if(auth()->user()->hasanyrole('Manager', 'Editor'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('customer') ? 'active' : '' }} {{ Request::is('customer/*') ? 'active' : '' }}" href="{{ url('customer') }}">Customer</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('request') ? 'active' : '' }}" href="{{ route('stock.index') }}">Stock Request</a>
                </li>
                @if(auth()->user()->hasanyrole('Administrator', 'Encoder', 'Head', 'Tech'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('stocks') ? 'active' : '' }}" href="{{ route('stocks.index') }}">Stocks</a>
                    </li>
                @endif
                
                @role('Manager')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('unrepair') ? 'active' : '' }}" href="{{ url('/unrepair') }}">Unrepairable</a>
                    </li>
                @endrole
                @if(auth()->user()->branch->branch != 'Warehouse')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('service-unit') ? 'active' : '' }}" href="{{ route('index.service-unit') }}">Service IN / OUT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('preventive') ? 'active' : '' }}" href="{{ route('index.preventive') }}">Preventive Maintenance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('loans') ? 'active' : '' }}" href="{{ route('loans') }}">Loans</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('return') ? 'active' : '' }}" href="{{ route('return.index') }}">Returns</a>
                </li>
                @if(auth()->user()->hasanyrole('Manager', 'Editor', 'Head', 'Administrator'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('user') ? 'active' : '' }}" href="{{ url('user') }}">Users</a>
                    </li>
                @endif
                
            @endif
        </ul>
        <ul class="nav">
             <li class="nav-item">
                <a href="{{route('logout')}}" class="nav-link"><b>Logout</b>&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true"></i></a>
            </li>
        </ul>
        @endauth
    </div>
</nav>