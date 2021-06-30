@extends('layouts.app')

@section('content')
    @if(auth()->user()->hasanyrole('Repair','Head'))
        <div class="table" id="returndiv">
            <table class="table-hover table returnTable" id="returnTable" style="font-size:80%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            DATE
                        </th>
                        @if (auth()->user()->hasanyrole('Repair'))
                            <th>
                            BRANCH
                            </th>
                        @endif
                        <th>
                            RETURN NUMBER
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