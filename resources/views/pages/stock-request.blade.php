@extends('layouts.app')

@section('content')
@if(auth()->user()->hasrole('Warehouse Manager'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
    <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
    </form>
@endif
<div class="table" id="requestdiv">
    <table class="table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager'))
                <th>
                    ID
                </th>
                @endif
                <th>
                    DATE
                </th>
                <th>
                    REQUESTED BY
                </th>
                @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager'))
                <th>
                    BRANCH NAME
                </th>
                @endif
                <th>
                    REQUEST TYPE
                </th>
                <th>
                    STATUS
                </th>
                <th>
                    TICKET NO.
                </th>
            </tr>
        </thead>
    </table>
</div>
@if(auth()->user()->hasrole('Warehouse Manager'))
    <div id="salltable" style="display: none">
        <table class="table searchtable" id="searchtable" style="display: none;font-size:80%;width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Date
                    </th>
                    <th>
                        Item Description
                    </th>
                    <th>
                        Serial
                    </th>
                    <th>
                        Branch
                    </th>
                    <th>
                        Prepared By
                    </th>
                </tr>
            </thead>
        </table>
    </div>
@endif
@if(auth()->user()->hasAnyRole('Head', 'Tech'))
<input type="button" id="reqBtn" class="btn btn-primary" value="REQUEST STOCKS">
<br><br><br>
@endif
@if(auth()->user()->branch->branch == "Warehouse" || auth()->user()->branch->branch == "Main-Office")
<div>
    <ul class="legend">
        <li><span class="BLUE"></span> Urgent Service Stock Request (PENDING)</li><br>
        <li><span class="GREEN"></span> Stock Request (PENDING)</li><br>
        <li><span class="MAGENTA"></span> Delivery Delays (SCHEDULED & PARTIAL SCHEDULED)</li><br>
        <li><span class="GRAYROW"></span> (GRAY ROW) 24 Hours Delay (Urgent Service - PENDING)</li><br>
        <li><span class="RED"></span> Unresolved and Incomplete issues</li>
    </ul>
</div>
@endif
@endsection