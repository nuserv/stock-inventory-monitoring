@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <table class="table-hover table defectiveTable" id="defectiveTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
                @if (auth()->user()->branch->branch != 'Conversion')
                    <td>
                        <input type="text" class="form-control filter-input fl-0" data-column="0" hidden/>
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-1" data-column="1" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-2" data-column="2" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-3" data-column="3" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-4" data-column="4" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-4" data-column="5" />
                    </td>
                @else
                    <td>
                        <input type="text" class="form-control filter-input fl-0" data-column="0">
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-1" data-column="1" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-2" data-column="2" />
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-3" data-column="3" />
                    </td>
                @endif
            </tr>
            <tr>
                @if (auth()->user()->branch->branch != 'Conversion')
                    <th></th>
                @endif
                <th>
                    Date
                </th>
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Serial
                </th>
                @if (auth()->user()->branch->branch != 'Conversion')
                <th>
                    Status
                </th>
                @endif
            </tr>
        </thead>
    </table>
</div>
<br>
@if(auth()->user()->hasrole('Head'))
<div class="printBtn pt-3" id="printBtn"></div>
<div class="d-flex">
    <input type="button" id="returnBtn" class="btn btn-primary" value="CREATE LIST" disabled>
    @if (auth()->user()->branch->branch == 'Conversion')
        <input type="button" id="addBtn" class="btn btn-primary ml-auto" value="ADD STOCK" >
    @endif
    <input type="button" id="printrecBtn" class="btn btn-primary ml-auto" value="VIEW RETURN LIST">
</div>
@endif

@endsection