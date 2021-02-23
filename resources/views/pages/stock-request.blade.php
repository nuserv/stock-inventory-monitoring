@extends('layouts.app')

@section('content')
@if(auth()->user()->hasrole('Administrator'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
    <input type="text" placeholder="Search serial.." id="searchall" size="50" autocomplete="off">
    </form>
@endif
<div class="table" id="requestdiv">
    <table class="table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                @if(auth()->user()->hasAnyrole('Administrator', 'Encoder', 'Viewer'))
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
                @if(auth()->user()->hasAnyrole('Administrator', 'Encoder', 'Viewer'))
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
@if(auth()->user()->hasrole('Administrator'))
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
@endif
@endsection