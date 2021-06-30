@extends('layouts.app')

@section('content')
<div>
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b>FILTER&nbsp;&nbsp;</b><a href="#" id="filter" data-placement="bottom" data-toggle="popover" data-content='@include("inc.branchfilter")'><i class="fa fa-lg fa-filter" aria-hidden="true"></i></a>
    </div>
    <table class="table-hover table branchTable" id="branchTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" style="border: 1px solid black;"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-2" data-column="2" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-3" data-column="3" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-4" data-column="4" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-5" data-column="5" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-6" data-column="6" style="border: 1px solid black;" />
                </td>
            </tr>
            <tr>
                <th>BRANCH NAME</th>
                <th>AREA</th>
                <th>CONTACT PERSON</th>
                <th>PHONE</th>
                <th>EMAIL</th>
                <th>STATUS</th>
                <th>BRANCH ADDRESS</th>
            </tr>
        </thead>
    </table>
</div>
@role('Editor')
<input type="button" id="addBtn" class="btn btn-primary" value="New branch">
@endrole
@endsection