@extends('layouts.app')

@section('content')

<br><div><input type="button" class="btn btn-primary" id="generate" value="Reports"><input type="text" id="searchall" style="float: right;margin-right: 20px;" placeholder="Search.." autocomplete="off"></div><br>
<div class="table-responsive">
    <table class="table disposedTable" id="disposedTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Date
                </th>
                @if(!auth()->user()->hasrole('Warehouse Administrator'))
                <th>
                    Branch
                </th>
                @endif
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Serial
                </th>
            </tr>
        </thead>
    </table>
    <div class="dtables" id="tables" style="display:none">
    <table class="table disposedsTable" id="disposedsTable" style="font-size:80%" >
        <thead class="thead-dark">
            <tr>
                <th></th>
                <th>
                    Date
                </th>
                @if(!auth()->user()->hasrole('Warehouse Administrator'))
                <th>
                    Branch
                </th>
                @endif
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th>
                    Serial
                </th>
            </tr>
        </thead>
    </table>
    </div>
</div>
@endsection