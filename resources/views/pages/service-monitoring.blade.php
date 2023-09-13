@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <table class="table-hover table sUnitTable w-100" id="sUnitTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th style="width:150px">
                    Date
                </th>
                <th>
                    Client & Branch Name
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
                    Branch
                </th>
                <th>
                    Service By
                </th>
            </tr>
        </thead>
    </table>
</div><br>
@endsection

@section('script')
    <script src="{{asset('min/?f=js/service-unit.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
    <script src="{{asset('min/?f=js/service-out.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
@endsection