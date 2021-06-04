@extends('layouts.app')

@section('content')

@if(auth()->user()->hasanyrole('Head', 'Tech'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
        <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
    </form>
@else
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
@endif
<div class="table-responsive" id="customerdiv">
    <table class="table customerTable" id="customerTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" style="border: 1px solid black;"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" style="border: 1px solid black;"/>
                </td>
            </tr>
            <tr>
                <th>
                    CUSTOMER CODE
                </th>
                <th>
                    CUSTOMER NAME
                </th>
            </tr>
        </thead>
    </table>
</div>
<div id="searchtable" style="display:none">
    <table class="table customerbranchTable" id="customerbranchTable" style="font-size:80%;width: 100%">
        <thead class="thead-dark">
            <tr>
                <th>
                    CLIENT NAME
                </th>
                <th>
                    BRANCH CODE
                </th>
                <th>
                    BRANCH NAME
                </th>
                <th>
                    PHONE
                </th>
                <th>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
</div>
@role('Editor')
<input type="button" id="customerBtn" class="btn btn-primary" value="New Customer">
<input type="button" id="editBtn" class="btn btn-primary" value="Edit Customer Details">
@endrole
@endsection