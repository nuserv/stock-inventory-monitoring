@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <table class="table-hover table sUnitTable" id="sUnitTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Client & Branch Name
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
                <th>
                    Service By
                </th>
            </tr>
        </thead>
    </table>
</div><br>
<div class="d-flex">
    @if(auth()->user()->hasAnyRole('Head', 'Tech'))
        <input type="button" id="out_Btn" class="btn btn-xs btn-primary" value="SERVICE OUT">&nbsp;
        <input type="button" id="in_Btn" class="btn btn-xs btn-primary mr-auto" value="SERVICE IN - Warranty">;
    @endif
</div>
@endsection