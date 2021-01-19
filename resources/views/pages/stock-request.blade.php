@extends('layouts.app')

@section('content')

<div class="table">
    <table class="table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                @if(auth()->user()->hasAnyrole('Administrator', 'Encoder', 'Viewer'))
                <th>
                    ID
                </th>
                @endif
                <th>
                    DATE
                </th>
                <th>
                    REQUEST NO.
                </th>
                <th>
                    REQUESTED BY
                </th>
                @if(auth()->user()->hasAnyrole('Administrator', 'Encoder', 'Viewer'))
                <th>
                    BRANCH NAME
                </th>
                <th>
                    AREA
                </th>
                @endif
                <th>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
</div>
@if(auth()->user()->hasAnyRole('Head', 'Tech'))
<input type="button" id="reqBtn" class="btn btn-primary" value="REQUEST STOCKS">
@endif
@endsection