@extends('layouts.app')

@section('content')
@if(auth()->user()->hasrole('Warehouse Manager'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
    <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
    </form>
@endif
<div class="table" id="requestdiv">
    <table class="table-hover table billableTable" id="billableTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    DATE REQUESTED
                </th>
                <th>
                    CLIENT NAME
                </th>
                <th>
                    ITEM DESCRIPTION
                </th>
                <th>
                    SERIAL
                </th>
                <th>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
</div>
@if(auth()->user()->hasAnyRole('Head', 'Tech'))
    <input type="button" id="reqBtn" class="btn btn-primary" value="CREATE NEW">
    <br><br><br>
@endif
@endsection