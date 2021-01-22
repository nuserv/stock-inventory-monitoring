@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <table class="table defectiveTable" id="defectiveTable">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
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
            </tr>
            <tr>
                <th></th>
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
                <th>
                    Status
                </th>
            </tr>
        </thead>
    </table>
</div>
<br>
<div class="d-flex">
    <div class="printBtn pt-3" id="#printBtn"></div>
    <input type="button" id="returnBtn" class="btn btn-primary" value="CREATE LIST" disabled>
    <input type="button" id="printBtn" class="btn btn-primary ml-auto" value="VIEW FOR RECEIVING">
</div>
@endsection