@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display: flex; justify-content: flex-end" class="pt-3">
        <a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <input type="text" hidden id="name" value="{{ $customer }}">
</div>
<div class="table-responsive">
    <table class="table customerbranchTable" id="customerbranchTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
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
            </tr>
            <tr>
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
@role('Viewer')
<input type="button" id="branchBtn" class="btn btn-primary" value="Add Customer Branch">
@endrole
@endsection