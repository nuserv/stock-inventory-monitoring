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
@include('modal.warehouse.category')
@include('modal.warehouse.item')
@if(auth()->user()->hasanyrole('Head', 'Tech'))
<form class="search-form" action="#" style="margin:auto;max-width:300px">
  <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
</form>
@endif
<div id="itemsearch">
    {{-- <input type="hidden" id="check" value="{{ $customers }}" /> --}}
    <div style="float: right;" class="pt-3">
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>
<div class="table-responsive">
    <div id="ctable">
        <table class="table-hover table catTable" id="catTable" style="font-size:80%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Category
                    </th>
                    <th>
                        Stock Available
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div id="stable">
        <center><h5 id="catname"></h5></center>
        <table class="table-hover table stockTable" id="stockTable" style="display: none;font-size:80%">
            <thead class="thead-dark">
                <tr class="tbsearch" style="display:none">
                    <td>
                        <input type="text" class="form-control filter-input fl-0" data-column="0" placeholder="Search description" style="border: 1px solid black;"/>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item Description
                    </th>
                    <th>
                        Stock
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<br>
<div class="d-flex">
    <input type="button" id="addCatBtn" class="btn btn-xs btn-primary" value="Add Category">&nbsp;&nbsp;
    <input type="button" id="addCodeBtn" class="btn btn-xs btn-primary" value="Add Item">
    {{-- <a class="ml-auto" href="{{ route('backup-inventory') }}">
        <input type="button" id="backupBtn" class="btn btn-xs btn-primary" value="BACK UP INVENTORY">
    </a> --}}
    @if(auth()->user()->hasAnyrole('Warehouse Manager|Head') || auth()->user()->id == 283)
        @if(auth()->user()->hasAnyrole('Head'))
            <a class="mr-auto" href="{{ route('backup-inventory') }}">
                <input type="button" id="backupBtn" class="btn btn-xs btn-primary" value="BACK UP INVENTORY">
            </a>
        @endif
        <input type="button" id="importBtn" class="btn btn-xs btn-primary ml-auto" value="IMPORT">&nbsp;&nbsp;
        <input type="button" id="addStockBtn" class="btn btn-xs btn-primary" value="ADD STOCK">
    @endif
</div>
@endsection
@section('script')
    <script src="{{asset('min/?f=js/warehouse/repairstock.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
    <script src="{{asset('min/?f=js/warehouse/repairstock2.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
@endsection