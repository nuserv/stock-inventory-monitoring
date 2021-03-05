@extends('layouts.app')

@section('content')
<br><br>
<div class="table-responsive">
    <table class="table loanTable" id="loanTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Date
                </th>
                <th>
                    Branch
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Status
                </th>
            </tr>
        </thead>
    </table>
</div>
@if(!auth()->user()->hasrole('Warehouse Manager'))
<input type="button" id="loan_Btn" class="btn btn-xs btn-primary" value="NEW LOAN">
@endif
@endsection