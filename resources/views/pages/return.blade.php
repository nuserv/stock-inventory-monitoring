@extends('layouts.app')

@section('content')
    @if(auth()->user()->hasanyrole('Repair','Head') || auth()->user()->id == 326)
        <div class="table" id="returndiv">
            <table class="table-hover table returnTable" id="returnTable" style="font-size:80%">
                <thead class="thead-dark">
                    <tr class="tbsearch">
                        <td>
                            <input type="text" class="form-control filter-input fl-0" data-column="0" />
                        </td>
                        @if (auth()->user()->hasanyrole('Repair') || auth()->user()->id == 326)
                            <td>
                                <input type="text" class="form-control filter-input fl-1" data-column="1" />
                            </td>
                            <td>
                                <input type="text" class="form-control filter-input fl-2" data-column="2" />
                            </td>
                            <td>
                                <input type="text" class="form-control filter-input fl-3" data-column="3" />
                            </td>
                        @else
                            <td>
                                <input type="text" class="form-control filter-input fl-1" data-column="1" />
                            </td>
                            <td>
                                <input type="text" class="form-control filter-input fl-2" data-column="2" />
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <th>
                            DATE
                        </th>
                        @if (auth()->user()->hasanyrole('Repair') || auth()->user()->id == 326)
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
