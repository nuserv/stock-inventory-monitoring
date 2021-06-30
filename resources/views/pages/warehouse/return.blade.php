@extends('layouts.app')

@section('content')
<div class="table-responsive">
    
    <table class="table-hover table defectiveTable" id="defectiveTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch">
                <td>
                    <input type="text" class="form-control filter-input fl-0" data-column="0" />
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
                <th>
                    Date
                </th>
                <th>
                    Branch
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
                @if(auth()->user()->hasAnyRole('Repair'))
                <th>
                </th>
                @endif
            </tr>
        </thead>
    </table>
</div>
@endsection