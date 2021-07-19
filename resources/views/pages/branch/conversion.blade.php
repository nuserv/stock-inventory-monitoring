@extends('layouts.app')

@section('content')
    <div class="table" id="returndiv">
        <table class="table-hover table drTable" id="drTable" style="font-size:80%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        DATE
                    </th>
                    <th>
                        DR REFERENCE NO.
                    </th>
                    <th>
                        PULL OUT DATE 
                    </th>
                    <th>
                        PULL OUT BY
                    </th>
                    <th>
                        BRANCH NAME
                    </th>
                </tr>
            </thead>
        </table>
    </div>
@if(auth()->user()->hasrole('Head'))
    <div class="d-flex">
        <input type="button" id="addBtn" class="btn btn-primary mr-auto" value="NEW RETURN" >
        <input type="button" id="printrecBtn" class="btn btn-primary ml-auto" value="VIEW RETURN LIST">
    </div>
@endif
@endsection