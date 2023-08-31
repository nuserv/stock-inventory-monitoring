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
                    Service By
                </th>
            </tr>
        </thead>
    </table>
</div>
<div class="d-flex">
    @if(auth()->user()->hasAnyRole('Head', 'Tech'))
    <input type="button" id="out_Btn" class="btn btn-xs btn-primary" value="SERVICE OUT">&nbsp;
    @endif
</div>
@endsection