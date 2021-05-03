@extends('layouts.app')

@section('content')
<div style="float: right;" class="pt-3">
    <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div class="table-responsive">
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
@role('Editor')
<input type="button" id="customerBtn" class="btn btn-primary" value="New Customer">
<input type="button" id="editBtn" class="btn btn-primary" value="Edit Customer Details">
@endrole
@endsection