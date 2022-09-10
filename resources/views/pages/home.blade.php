@extends('layouts.app')

@section('content')
    @if(!auth()->user()->hasanyrole('Repair', 'Warehouse Administrator', 'Viewer', 'Viewer PLSI', 'Viewer IDSI'))
        @if(!auth()->user()->hasanyrole('Main Warehouse Manager'))
            @if (auth()->user()->branch->branch != "Conversion")
                <div class="container pt-5">
                    <div class="container-fluid">
                        <div class="row" style="zoom:60%">
                            @if (!auth()->user()->hasrole('Manager', 'Editor'))
                                <div class="col-sm-2">
                                    <a href="{{ route('stock.index')}}" style="text-decoration: none;">
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('Stock-Request.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($stockreq)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">STOCK REQUEST</strong>
                                        </center>
                                    </a>
                                </div>
                                @if (!auth()->user()->hasanyrole('Manager', 'Editor'))
                                <div class="col-sm-2">
                                    <a href="{{ route('stocks.index')}}"  style="text-decoration: none;">
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('Stocks.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($units)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">STOCKS</strong>
                                        </center>
                                    </a>
                                </div>
                                @endif
                                @if (!auth()->user()->hasanyrole('Manager', 'Editor'))
                                <div class="col-sm-2">
                                    <a style="text-decoration: none;" href=@if (auth()->user()->hasanyrole("Warehouse Manager", "Encoder")) {{ route('repaired.list')}} @else {{ route('return.index')}} @endif>
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('Returns.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($returns)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">@if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder')) REPAIRED @else RETURNS @endif</strong>
                                        </center>
                                    </a>
                                </div>
                                @endif
                                @if(auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-Office')
                                <div class="col-sm-2">
                                    <a href="{{ route('index.service-unit')}}"  style="text-decoration: none;">
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('Service-Out.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($sunits)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">SERVICE OUT</strong>
                                        </center>
                                    </a>
                                </div>
                                <div class="col-sm-2">
                                    <a href="{{ url('loans') }}"  style="text-decoration: none;">
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('Loans.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($loans)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">LOAN REQUEST</strong>
                                        </center>
                                    </a>
                                </div>
                                @endif
                            @endif
                            @if(auth()->user()->hasanyrole('Warehouse Manager', 'Manager', 'Editor'))
                            <div class="col-sm-2">
                                <a href="{{ url('resolved') }}"  style="text-decoration: none;">
                                    <center>
                                        <div class="text-center">
                                            <img style="height: 100px;" src="{{ asset('Resolved.png') }}">
                                        </div>
                                        <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                            {{number_format($resolved)}}
                                        </div>
                                        <strong style="color: #0d1a80; font-size: 16px;">RESOLVED</strong>
                                    </center>
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ url('request') }}"  style="text-decoration: none;">
                                    <center>
                                        <div class="text-center">
                                            <img style="height: 100px;" src="{{ asset('Unresolve.png') }}">
                                        </div>
                                        <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                            {{number_format($unresolved)}}
                                        </div>
                                        <strong style="color: #0d1a80; font-size: 16px;">UNRESOLVED</strong>
                                    </center>
                                </a>
                            </div>
                            @if(auth()->user()->hasanyrole('Warehouse Manager'))
                                <div class="col-sm-2">
                                    <a href="{{ url('pullview') }}"  style="text-decoration: none;">
                                        <center>
                                            <div class="text-center">
                                                <img style="height: 100px;" src="{{ asset('pull-out.png') }}">
                                            </div>
                                            <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                                                {{number_format($pullout)}}
                                            </div>
                                            <strong style="color: #0d1a80; font-size: 16px;">PULLOUT</strong>
                                        </center>
                                    </a>
                                </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <br>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <p>USER ACTIVITIES</p>
            </li>
        </ul>
    @endif

<div class="table-responsive">
    <div style="float: right;" class="pt-3" hidden>
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <table class="table-hover table activityTable" id="activityTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
                <td>
                    <input type="search" class="form-control filter-input fl-0" placeholder="id" data-column="0" style="border: 1px solid black;"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input fl-1" placeholder="Date" data-column="1" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="search" class="form-control filter-input fl-2" placeholder="Fullname" data-column="2" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="search" class="form-control filter-input fl-3" placeholder="Branch" data-column="3" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="search" class="form-control filter-input fl-3" placeholder="Activity" data-column="4" style="border: 1px solid black;" />
                </td>
            </tr>
            <tr>
                <th>
                    id
                </th>
                <th>
                    DATE & TIME
                </th>
                <th>
                    FULLNAME
                </th>
                <th>
                    BRANCH
                </th>
                <th>
                    ACTIVITY
                </th>
            </tr>
        </thead>
    </table>
</div>
@endsection