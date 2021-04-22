@extends('layouts.app')

@section('content')
<div class="row no-margin">
    <div class="col-md-6 form-group row">
        <label class="col-md-5 col-form-label text-md-right">Date requested:</label>
        <div class="col-md-7">
            <input type="text" class="form-control form-control-sm " id="date" value="{{ $request->created_at->toDayDateTimeString()}}" disabled>
        </div>
    </div>
    <div class="col-md-6 form-group row">
        <label class="col-md-4 col-form-label text-md-right">Request no.:</label>
        <div class="col-md-8">
            <input type="text" class="form-control form-control-sm " id="reqno" value="{{ $request->request_no}}" disabled>
        </div>
    </div>
</div>
<div class="row no-margin">
    <div class="col-md-6 form-group row">
        <label class="col-md-5 col-form-label text-md-right">Branch name:</label>
        <div class="col-md-7">
            <input type="text" class="form-control form-control-sm " id="branch" value="{{ \App\Branch::where('id', $request->branch_id)->first()->branch }}" disabled>
        </div>
    </div>
    <div class="col-md-6 form-group row">
        <label class="col-md-4 col-form-label text-md-right">Requested by:</label>
        <div class="col-md-8">
            <input type="text" class="form-control form-control-sm " id="name" value="{{ \App\User::where('id', $request->user_id)->first()->name }} {{ \App\User::where('id', $request->user_id)->first()->lastname }}" disabled>
        </div>
    </div>
</div>
<div class="row no-margin">
    <div class="col-md-6 form-group row">
        <label class="col-md-5 col-form-label text-md-right">Area:</label>
        <div class="col-md-7">
            <input type="text" class="form-control form-control-sm " id="area" value="{{ \App\Area::where('id', $request->area_id)->first()->area }}" disabled>
        </div>
    </div>
    <div class="col-md-6 form-group row sched">
        <label class="col-md-4 col-form-label text-md-right">Schedule on:</label>
        <div class="col-md-8">
            <input type="text" style="color: black" class="form-control form-control-sm datepicker" value="{{$request->schedule}}" name="datesched" id="datesched" readonly="readonlyy" autocomplete="off">
        </div>
    </div>
</div>
<div>
    <h5 class="modal-title w-100 text-center">ITEM DETAILS</h5>
</div>
<div>
    <table class="table itemDetails" style="font-size:80%">
        <thead class="thead-dark">
            <th>Description</th>
            <th>Qty - UOM</th>
            <th>Serial</th>
        </thead>
    </table>
</div>
<hr>
<div class="panel-heading" role="tab" id="heading-all-data-table"></div>
@endsection