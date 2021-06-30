@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <!--div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div-->
    <table class="table-hover table unrepairTable" id="unrepairTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" />
                </td>
                @if(!auth()->user()->hasrole('Warehouse Administrator'))
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
            </tr>
            <tr>
                <th>
                    Date
                </th>
                @if(!auth()->user()->hasrole('Warehouse Administrator'))
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
                @if(auth()->user()->hasrole('Warehouse Administrator'))
                    <th>
                    </th>
                @endif
            </tr>
        </thead>
    </table>
</div>
@endsection