@extends('layouts.app')

@section('content')
<div class="table-responsive">
    <span style="font-size:130%;color:#00127f"><b>
    @if (Carbon\Carbon::now() <= Carbon\Carbon::now()->firstOfQuarter()->add(7, 'day'))
        @if ($pmbranches == 0)
            @if (Carbon\Carbon::now()->quarter == 1)
                1ST
            @endif
            @if (Carbon\Carbon::now()->quarter == 2)
                2ND
            @endif
            @if (Carbon\Carbon::now()->quarter == 3)
                3RD
            @endif
            @if (Carbon\Carbon::now()->quarter == 4)
                4TH
            @endif
        @else
            @if (Carbon\Carbon::now()->quarter == 1)
                4TH
            @endif
            @if (Carbon\Carbon::now()->quarter == 2)
                1ST
            @endif
            @if (Carbon\Carbon::now()->quarter == 3)
                2ND
            @endif
            @if (Carbon\Carbon::now()->quarter == 4)
                3RD
            @endif
        @endif
    @else
        @if (Carbon\Carbon::now()->quarter == 1)
            1ST
        @endif
        @if (Carbon\Carbon::now()->quarter == 2)
            2ND
        @endif
        @if (Carbon\Carbon::now()->quarter == 3)
            3RD
        @endif
        @if (Carbon\Carbon::now()->quarter == 4)
            4TH
        @endif
    @endif
    QTR REMAINING BRANCHES FOR PM</b></span>
    <table class="table-hover table pmTable" id="pmTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                @if (auth()->user()->hasanyrole('Manager', 'Editor'))
                    <th>
                        Area
                    </th>
                    <th>
                        Branch
                    </th>
                @endif
                <th>
                    Client name
                </th>
                <th>
                    Last PM
                </th>
            </tr>
            <tr>
                @if (auth()->user()->hasanyrole('Manager', 'Editor'))
                    <th>
                        Area
                    </th>
                    <th>
                        Branch
                    </th>
                @endif
                <th>
                    Client name
                </th>
                <th>
                    Last PM
                </th>
            </tr>
        </thead>
    </table>
</div><br>
<div class="d-flex">
    @if(auth()->user()->hasAnyRole('Head', 'Tech', 'Editor', 'Manager'))
        @if(auth()->user()->hasAnyRole('Editor', 'Manager'))
            <input type="button" id="addPMBtn" class="btn btn-xs btn-primary" value="ADD PM BRANCH">
        @endif
        <input type="button" id="prevBtn" class="btn btn-xs btn-primary ml-auto" value="VIEW PREVIOUS PM">
    @endif
</div>
@endsection