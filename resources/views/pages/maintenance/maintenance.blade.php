@extends('layouts.app')
@section('content')
<div style="float: right;">
    <button class="btn btn-primary bp btnNewAssembled" type="button" style="display: none;"><i class="fa-solid fa-circle-plus fa-lg mr-2"></i>CREATE ASSEMBLED ITEM</button>
</div>
<table class="table-hover table assembleditemsTable" id="assembleditemsTable" style="width: 100%; display: none; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <th style="width: 25%;">PRODUCT CODE</th>
            <th>PRODUCT DESCRIPTION</th>
            <th style="width: 10%;">MIN. STOCK</th>
        </tr>
    </thead>
</table>
<style>
    .active-link{
        background-color: #0d1a80 !important;
        color: white !important;
        border-radius: 6px 6px 0px 0px !important;
    }
    .nav-item-link>a:hover {
        background-color: #0d6efd !important;
        color:white !important;
        border-radius: 6px 6px 0px 0px !important;
    }
    .nav-pills a{
        color: #0d1a80;
        padding: 8px;
    }
    .checkbox_span{
        font-weight: bolder;
        zoom: 125%;
    }
</style>
@if(app('request')->input('tbl') == 'assembleditems')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.newAssembledItem')
    @include('pages.maintenance.detailsAssembledItem')
@elseif(app('request')->input('tbl') == 'bundleditems')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.newBundledItems')
    @include('pages.maintenance.detailsBundledItems')
@elseif(app('request')->input('tbl') == 'categories')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.newCategory')
    @include('pages.maintenance.detailsCategory')
@elseif(app('request')->input('tbl') == 'locations')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.newLocation')
    @include('pages.maintenance.detailsLocation')
@elseif(app('request')->input('tbl') == 'suppliers')
    <style>
        div.dataTables_processing{
            margin-top: 10px !important;
        }
    </style>
    @include('pages.maintenance.newSupplier')
    @include('pages.maintenance.detailsSupplier')
@elseif(app('request')->input('tbl') == 'warranty')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.warranty')
@elseif(app('request')->input('tbl') == 'customers')
    <style>
        div.dataTables_processing{
            margin-top: -10px !important;
        }
    </style>
    @include('pages.maintenance.modalCustomer')
    @include('pages.maintenance.modalBranch')
@else
    <style>
        div.dataTables_processing{
            margin-top: 7px !important;
        }
    </style>
    @include('pages.maintenance.importItem')
    @include('pages.maintenance.newItem')
    @include('pages.maintenance.detailsItem')
@endif
@endsection
@section('script')
<script src="/js/maintenance.js"></script>
@endsection