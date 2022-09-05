@extends('layouts.app')

@section('content')
<center><span style="font-size:130%;color:#00127f;font-family:arial"><b>ITEMS REQUESTS BY CATEGORY</b></span></center>
<div class="table" id="requestdiv">
    <table class="table-hover table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Total Qty
                </th>
                <th>
                    Stock Available
                </th>
            </tr>
            <tr>
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Total Qty
                </th>
                <th>
                    Stock Available
                </th>
            </tr>
        </thead>
    </table>
</div>
@endsection