@extends('layouts.app')

@section('content')
<style>
    .stock-request-summary {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 28px 52px;
        margin: 12px 0 18px;
    }

    .stock-request-summary__item {
        min-width: 130px;
        padding: 4px 8px 8px;
        border-radius: 8px;
        text-align: center;
        color: #0b0d75;
        font-weight: 700;
        cursor: pointer;
        transition: background-color .15s ease, box-shadow .15s ease;
    }

    .stock-request-summary__item:hover,
    .stock-request-summary__item:focus {
        background: rgba(8, 9, 131, .08);
        outline: none;
    }

    .stock-request-summary__item.is-active {
        background: rgba(8, 9, 131, .12);
        box-shadow: inset 0 0 0 2px #080983;
    }

    body.stock-request-filtering #loading {
        position: fixed;
        z-index: 9999;
    }

    .stock-request-summary__icon {
        display: block;
        width: 92px;
        height: 92px;
        object-fit: contain;
        margin: 0 auto 6px;
    }

    .stock-request-summary__count {
        display: inline-block;
        min-width: 86px;
        padding: 2px 12px;
        border-radius: 6px;
        background: #080983;
        color: #fff;
        font-size: 18px;
        line-height: 1.35;
    }

    .stock-request-summary__label {
        display: block;
        margin-top: 5px;
        font-size: 16px;
        line-height: 1.15;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .stock-request-summary {
            justify-content: flex-start;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 6px;
        }

        .stock-request-summary__item {
            min-width: 112px;
        }

        .stock-request-summary__icon {
            width: 76px;
            height: 76px;
        }

        .stock-request-summary__label {
            font-size: 13px;
        }
    }
</style>
{{-- @if(auth()->user()->hasanyrole('Warehouse Manager','Encoder'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
    <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
    </form>
@endif --}}
<div class="table" id="requestdiv">
    @if(auth()->user()->hasAnyrole('Editor', 'Warehouse Manager'))
    <a href="/stock-request/export" class="btn btn-success">
        EXPORT
    </a>
    @endif

    <div class="stock-request-summary" id="stockRequestSummary" data-summary-url="{{ url('request-summary') }}">
        @foreach([
            ['status' => 'PENDING', 'label' => 'PENDING', 'image' => 'Pending-Icon.png'],
            ['status' => 'SCHEDULED', 'label' => 'SCHEDULED', 'image' => 'Scheduled.png'],
            ['status' => 'IN TRANSIT', 'label' => 'IN-TRANSIT', 'image' => 'In-Transit.png'],
            ['status' => 'PARTIAL IN TRANSIT', 'label' => 'PARTIAL IN-TRANSIT', 'image' => 'Partial-In-Transit.png'],
            ['status' => 'PARTIAL PENDING', 'label' => 'PARTIAL PENDING', 'image' => 'Partial-Delivery.png'],
            ['status' => 'UNRESOLVED', 'label' => 'UNRESOLVED', 'image' => 'Unresolved.png'],
        ] as $summary)
            <div class="stock-request-summary__item" data-status="{{ $summary['status'] }}" role="button" tabindex="0" title="Filter {{ $summary['label'] }}">
                <img class="stock-request-summary__icon" src="{{ asset('images/'.$summary['image']) }}" alt="{{ $summary['label'] }}">
                <span class="stock-request-summary__count">0</span>
                <span class="stock-request-summary__label">{{ $summary['label'] }}</span>
            </div>
        @endforeach
    </div>

    <table class="table-hover table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager', 'Warehouse Administrator'))
                <tr>
                    @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager', 'Warehouse Administrator'))
                    <th>
                        ID
                    </th>
                    @endif
                    <th>
                        DATE
                    </th>
                    <th>
                        REQUESTED BY
                    </th>
                    <th>
                        REQUESTED NO.
                    </th>
                    @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager', 'Warehouse Administrator'))
                    <th>
                        BRANCH NAME
                    </th>
                    @endif
                    <th>
                        REQUEST TYPE
                    </th>
                    <th>
                        STATUS
                    </th>
                    <th>
                        TICKET NO.
                    </th>
                    @if (auth()->user()->hasAnyrole('Warehouse Manager'))
                        <th></th>
                    @endif
                </tr>
            @endif
            <tr>
                @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager', 'Warehouse Administrator'))
                <th>
                    ID
                </th>
                @endif
                <th>
                    DATE
                </th>
                <th>
                    REQUESTED BY
                </th>
                <th>
                    REQUESTED NO.
                </th>
                @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder', 'Editor', 'Manager', 'Warehouse Administrator'))
                <th>
                    BRANCH NAME
                </th>
                @endif
                <th>
                    REQUEST TYPE
                </th>
                <th>
                    STATUS
                </th>
                <th>
                    TICKET NO.
                </th>
                @if (auth()->user()->hasAnyrole('Warehouse Manager'))
                    <th></th>
                @endif
            </tr>
        </thead>
    </table>
</div>
@if(auth()->user()->hasrole('Warehouse Manager'))
    <div id="salltable" style="display: none">
        <table class="table-hover table searchtable" id="searchtable" style="display: none;font-size:80%;width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Date
                    </th>
                    <th>
                        Item Description
                    </th>
                    <th>
                        Serial
                    </th>
                    <th>
                        Branch
                    </th>
                    <th>
                        Prepared By
                    </th>
                </tr>
            </thead>
        </table>
    </div>
@endif
@if(auth()->user()->hasAnyRole('Head', 'Tech'))
<input type="button" id="reqBtn" class="btn btn-primary" value="REQUEST STOCKS">
<br><br><br>
@endif
@if(auth()->user()->branch->branch == "Warehouse" || auth()->user()->branch->branch == "Main-Office")
<div>
    <ul class="legend">
        <li><span class="BLUE"></span> Urgent Service Stock Request (PENDING)</li><br>
        <li><span class="GREEN"></span> Stock Request (PENDING)</li><br>
        <li><span class="MAGENTA"></span> Delivery Delays (SCHEDULED & PARTIAL SCHEDULED)</li><br>
        <li><span class="GRAYROW"></span> (GRAY ROW) 24 Hours Delay (Urgent Service - PENDING)</li><br>
        <li><span class="RED"></span> Unresolved and Incomplete issues</li>
    </ul>
</div>
@endif
@endsection
