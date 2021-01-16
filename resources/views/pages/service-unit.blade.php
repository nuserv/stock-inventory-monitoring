@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <table class="table sUnitTable" id="sUnitTable">
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
    <input type="button" id="out_Btn" class="btn btn-xs btn-primary" value="CREATE SERVICE">&nbsp;
    @endif
</div>
@endsection