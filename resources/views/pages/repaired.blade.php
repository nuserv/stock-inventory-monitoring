@extends('layouts.app')

@section('content')
    @if(auth()->user()->hasanyrole('Repair'))
        <div class="table" id="pulloutdiv">
            <table class="table repairedTable" id="repairedTable" style="font-size:80%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            DATE
                        </th>
                        <th>
                            CATEGORY
                        </th>
                        <th>
                            ITEM DESCRIPTION
                        </th>
                        <th>
                            SERIAL NUMBER
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="test" style="display:none">
            <div class="printBtn pt-3" id="printBtn"></div>
        </div>
    @endif
    @if(auth()->user()->hasanyrole('Encoder', 'Warehouse Manager'))
        <div class="table" id="repaireddiv">
            <table class="table repairedTable" id="repairedTable" style="font-size:80%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            DATE
                        </th>
                        <th>
                            REFERENCE NUMBER
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    @endif
@endsection