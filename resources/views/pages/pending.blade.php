@extends('layouts.app')

@section('content')
<div class="table" id="requestdiv">
    <table class="table-hover table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" placeholder="Search Date" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" data-column="1" placeholder="Search F.E" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-2" data-column="2" placeholder="Search Branch" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-3" data-column="3" placeholder="Search Customer" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-4" data-column="4" placeholder="Search Ticker #" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-5" data-column="5" placeholder="Search Status" />
                </td>
            </tr>
            <tr>
                <th>
                    DATE
                </th>
                <th>
                    REQUESTED BY
                </th>
                <th>
                    BRANCH NAME
                </th>
                <th>
                    Customer
                </th>
                <th>
                    TICKET NO.
                </th>
                <th>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
</div>
@endsection