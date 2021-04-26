<nav class="nav nav-tabs navbar-expand-md">
    <div class="navbar-collapse collapse justify-content-between align-items-center w-100">
        @auth
        <ul class="nav mr-auto">
            @if(!auth()->user()->hasrole('Viewer'))
            <li class="nav-item">
                <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
            </li>
            @endif
            @if(auth()->user()->hasrole('Returns Manager'))
                <li class="nav-item" style="margin-left:0px;margin-right:0px;">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('disposed') ? 'active' : '' }}" href="{{ url('/disposed') }}">Disposed</a>
                </li>
            @endif
            @if(auth()->user()->hasrole('Repair'))
                <li class="nav-item" style="margin-left:0px;margin-right:0px;">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('unrepair') ? 'active' : '' }}" href="{{ url('/unrepair') }}">Unrepairable</a>
                </li>
            @endif
            @if(!auth()->user()->hasanyrole('Repair', 'Returns Manager', 'Viewer'))
                <li class="nav-item" style="margin-left:0px;margin-right:0px;">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('branch') ? 'active' : '' }}" href="{{ route('branch.index') }}">Service Center</a>
                </li>
                @if(auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-Office')
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('customer') ? 'active' : '' }} {{ Request::is('customer/*') ? 'active' : '' }}" href="{{ url('customer') }}">Customer</a>
                    </li>
                @endif
                @if(auth()->user()->hasanyrole('Manager', 'Editor'))
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('customer') ? 'active' : '' }} {{ Request::is('customer/*') ? 'active' : '' }}" href="{{ url('customer') }}">Customer</a>
                    </li>
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('stocks') ? 'active' : '' }}" href="{{ route('stocks.index') }}">Warehouse Stock</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('request') ? 'active' : '' }}" href="{{ route('stock.index') }}">Stock Request</a>
                </li>
                @if(auth()->user()->hasanyrole('Warehouse Manager', 'Manager'))
                <li class="nav-item">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('resolved') ? 'active' : '' }}" href="{{ route('resolved.index') }}">Resolved</a>
                </li>
                @endif
                @if(auth()->user()->hasanyrole('Warehouse Manager', 'Head', 'Tech', 'Encoder'))
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('stocks') ? 'active' : '' }}" href="{{ route('stocks.index') }}">Stocks</a>
                    </li>
                @endif
                
                @if(auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-Office')
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('service-unit') ? 'active' : '' }}" href="{{ route('index.service-unit') }}">Service IN / OUT</a>
                    </li>
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('preventive') ? 'active' : '' }}" href="{{ route('index.preventive') }}">Preventive Maintenance</a>
                    </li>
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('loans') ? 'active' : '' }}" href="{{ route('loans') }}">Loans</a>
                    </li>
                @endif
                @if(!auth()->user()->hasanyrole('Tech', 'Warehouse Manager', 'Encoder'))
                <li class="nav-item">
                    <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('return') ? 'active' : '' }}" href="{{ route('return.index') }}">Returns</a>
                </li>
                @endif
                @if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder'))
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('return') ? 'active' : '' }}" href="{{ route('return.index') }}">Repaired</a>
                    </li>
                @endif
                @if(auth()->user()->hasanyrole('Manager', 'Editor', 'Head', 'Warehouse Manager'))
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('user') ? 'active' : '' }}" href="{{ url('user') }}">Users</a>
                    </li>
                @endif
                @if(auth()->user()->hasanyrole('Manager', 'Editor'))
                    <li class="nav-item">
                        <a style="padding-right:8px; padding-left:8px" class="nav-link {{ Request::is('item') ? 'active' : '' }}" href="{{ url('item') }}">Items</a>
                    </li>
                @endif
            @endif
        </ul>
        <ul class="nav">
            <li class="nav-item" style="padding: 10px 0;">
                <a class="nav-link {{ Request::is('report-a-problem') ? 'active' : '' }}" href="{{ url('report-a-problem') }}" style="background-color: white; color:#0d1a80; font-size: 10px;border-radius: 5px;padding: 2px 0;">&nbsp;&nbsp;REPORT A PROBLEM&nbsp;&nbsp;</a>
            </li>
             <li class="nav-item">
                <a href="{{route('logout')}}" class="nav-link"><b>Logout</b>&nbsp;&nbsp;<i class="fa fa-sign-out" aria-hidden="true"></i></a>
            </li>
        </ul>
        @endauth
    </div>
</nav>