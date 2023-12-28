@extends('layouts.app')
@section('content')
<button class="btn btn-primary bp btnNewAssembly float-end mb-2" type="button">
    <i class="fa-solid fa-file-circle-plus fa-lg mr-2"></i>
    NEW ASSEMBLY REQUEST
</button>
<br><br>
<table id="assemblyTable" class="table assemblyTable table-hover display" style="width: 100%; zoom: 80%; cursor: pointer;">
    <thead style="background-color: #0d1a80; color: white;">
        <tr>
            <td colspan="9">
                <a href="/assembly" title="Reload">
                    <div class="text-center" style="background-color: #0d1a80; color: white; font-size: 25px; font-weight: bold; height: 43px; line-height: 43px;">
                        ASSEMBLY JOB ORDERS
                    </div>
                </a>
            </td>
        </tr>
        <tr class="tbsearch">
            <td class="d-none">
                <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
            </td>
            <td class="d-none">
                <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
            </td>
            <td>
                <input type="search" class="form-control filter-input" data-column="8" style="border:1px solid #808080"/>
            </td>
        </tr>
        <tr>
            <th class="d-none">DATE CREATED</th>
            <th class="d-none">DATE NEEDED</th>
            <th style="width: 14%;">DATE CREATED</th>
            <th style="width: 14%;">DATE NEEDED</th>
            <th style="width: 12%;">REQUEST NUMBER</th>
            <th style="width: 10%;">REQUEST TYPE</th>
            <th>PRODUCT DESCRIPTION</th>
            <th style="width: 8%;">QUANTITY</th>
            <th style="width: 18%;">STATUS</th>
        </tr>
    </thead>
</table>
@include('pages.assembly.newAssembly')
@include('pages.assembly.detailsAssembly')
@endsection
@section('script')
<script src="{{asset('min/?f=js/assembly.js')}}&version={{ \Illuminate\Support\Str::random(30) }}"></script>
@endsection
