@extends('layouts.app')

@section('content')
<div class="panel-heading pt-3" role="tab" id="heading-all-data-table"></div><br><br>
<div class="pt-3">
    <table class="table-hover table retTable" id="retTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>Date</th>
                <th>
                    Return Reference Nnumber
                </th>
            </tr>
        </thead>
    </table>
</div>
<!--input type="button" id="printBtn" class="btn btn-primary" value="PRINT" disabled-->
@endsection