@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <table class="table-hover table sUnitTable w-100" id="sUnitTable" style="font-size:80%; width:100%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Client & Branch Name
                </th>
                <th>
                    Ticket Number
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
        <input type="button" id="in_Btn" class="btn btn-xs btn-primary mr-auto" value="SERVICE IN - Pullout Only">;
    @endif
</div>
@endsection
