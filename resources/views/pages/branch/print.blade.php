@extends('layouts.app')

@section('content')
<div class="panel-heading pt-3" role="tab" id="heading-all-data-table"></div><br><br>
<div class="pt-3">
    <table class="table defectiveTable" id="defectiveTable">
        <thead class="thead-dark">
            <tr>
                <th>&nbsp;</th>
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
<!--input type="button" id="printBtn" class="btn btn-primary" value="PRINT" disabled-->
@endsection