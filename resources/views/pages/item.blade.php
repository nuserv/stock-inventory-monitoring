@extends('layouts.app')

@section('content')

<div class="table-responsive">
    <table class="table itemTable" id="itemTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Category
                </th>
                <th>
                    Item Description
                </th>
                <th title="Indicate if the item is required to have a serial number. Click Yes or No.">
                   Requires serial no.
                </th>
            </tr>
        </thead>
    </table>
</div>
@endsection