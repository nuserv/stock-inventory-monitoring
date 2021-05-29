@extends('layouts.app')

@section('content')
    @if(auth()->user()->hasanyrole('Repair'))
        <div class="table" id="returndiv">
            <table class="table returnTable" id="returnTable" style="font-size:80%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            DATE
                        </th>
                        <th>
                            BRANCH
                        </th>
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