@extends('layouts.app')

@section('content')
<center><span style="font-size:130%;color:#00127f;font-family:arial"><b>PREVIOUS PM</b></span></center>
<div class="table-responsive">
    <table class="table-hover table pmTable" id="pmTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Date
                </th>
                <th>
                    FSR Number
                </th>
                <th>
                    Client Name
                </th>
                <th>
                    Client Branch Name
                </th>
                <th>
                    Serviced By
                </th>
            </tr>
            <tr>
                <th>
                    Date
                </th>
                <th>
                    FSR Number
                </th>
                <th>
                    Client Name
                </th>
                <th>
                    Client Branch Name
                </th>
                <th>
                    Serviced By
                </th>
            </tr>
        </thead>
    </table>
</div><br>
<div class="d-flex" id="exportBtn">
    @if(auth()->user()->hasAnyRole('Head', 'Tech', 'Manager', 'Editor'))
    <input type="button" id="listBtn" class="btn btn-xs btn-primary ml-auto" value="GENERATE REPORTS">
    @endif
</div>
@endsection