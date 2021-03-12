@extends('layouts.app')

@section('content')
<!--div style="float: right;" class="pt-3" hidden>
    <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</div><br><br><br-->
<div class="container pt-3">
    <div class="col-md-4 pull-right">
        <div class="input-group input-daterange">
            <input type="text" id="min-date" class="form-control date-range-filter" placeholder="From:" autocomplete="off">
        <div class="input-group-addon">to</div>
            <input type="text" id="max-date" class="form-control date-range-filter" placeholder="To:" autocomplete="off"><button id="goBtn" class="btn btn-primary">Search</button>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table disposedTable" id="disposedTable" style="font-size:80%">
        <thead class="thead-dark">
            <!--tr class="tbsearch" style="display:none">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" />
                </td>
                @if(!auth()->user()->hasrole('Returns Manager'))
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" />
                </td>
                @endif
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
            </tr-->
            <tr>
                <th></th>
                <th>
                    Date
                </th>
                @if(!auth()->user()->hasrole('Returns Manager'))
                <th>
                    Branch
                </th>
                @endif
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
@endsection