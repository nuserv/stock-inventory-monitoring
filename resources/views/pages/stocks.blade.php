@extends('layouts.app')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
            {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
            @foreach ( $errors->all() as $error )
                - {{$error}} not found. Import data failed<br>
            @endforeach
    </div>
@endif

@if(auth()->user()->hasanyrole('Head', 'Tech'))
<form class="search-form" action="#" style="margin:auto;max-width:300px">
  <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
</form>
@endif
<div id="itemsearch">
    <input type="hidden" id="check" value="{{ $customers }}" />
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>
<div class="table-responsive">
    <div id="ctable">
        <table class="table catTable" id="catTable" style="font-size:80%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Category
                    </th>
                    @if(auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != "Main-Office")
                    <th>
                        Stock Available
                    </th>
                    <th>
                        Stock Out
                    </th>
                    <th>
                        Defectives
                    </th>
                    @endif
                    <th>
                        @if(auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != "Main-Office")Total @else Quantity @endif
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div id="stable">
        <center><h5 id="catname"></h5></center>
        <table class="table stockTable" id="stockTable" style="display: none;font-size:80%">
            <thead class="thead-dark">
                <tr class="tbsearch" style="display:none">
                    <td>
                        <input type="text" class="form-control filter-input fl-0" data-column="0" placeholder="Search description" style="border: 1px solid black;"/>
                    </td>
                    <td>
                        <input type="text" class="form-control filter-input fl-1" data-column="1" placeholder="Search quantity" style="border: 1px solid black;"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item Description
                    </th>
                    <th>
                        @if(auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != "Main-Office")Stock Available @else Stock In @endif
                    </th>
                    <th>
                        Stock Out
                    </th>
                    @if(auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != "Main-Office")
                    <th>
                        Defectives
                    </th>
                    @endif
                    <th>
                        @if(auth()->user()->branch->branch != "Warehouse" && auth()->user()->branch->branch != "Main-Office")Total @else Stock Available @endif
                    </th>
                    <th>
                        UOM
                    </th>
                    @if(auth()->user()->hasanyrole('Head', 'Tech'))
                        <th>
                        </th>
                    @endif
                </tr>
            </thead>
        </table>
    </div>
    @if(auth()->user()->hasanyrole('Head', 'Tech'))
    <div id="salltable" style="display: none">
        <table class="table searchtable" id="searchtable" style="display: none;font-size:80%;width: 100%">
            <thead class="thead-dark">
                <tr>
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
    @endif
</div>
<div class="d-flex">
    @if(auth()->user()->hasrole('Warehouse Manager') || auth()->user()->id == 70)
        <input type="button" id="addCatBtn" class="btn btn-xs btn-primary" value="Add Category">&nbsp;&nbsp;
        <input type="button" id="addCodeBtn" class="btn btn-xs btn-primary" value="Add Item">
    @endif
    @if(auth()->user()->hasAnyrole('Warehouse Manager|Head|Encoder'))
        <input type="button" id="importBtn" class="btn btn-xs btn-primary ml-auto" value="IMPORT">&nbsp;&nbsp;
        <input type="button" id="addStockBtn" class="btn btn-xs btn-primary" value="ADD STOCK">
    @endif
</div>
@endsection