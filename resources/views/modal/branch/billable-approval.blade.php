<div id="service-inModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">BILLABLE ITEM REQUEST</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="indate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="inengr" readonly>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="inclient" readonly>
                            <input type="text" id="inclient-id" value="" hidden>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="incustomer" readonly>
                            <input type="text" id="incustomer-id" value="" hidden>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-right">Item Description:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="outitem" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-right">Serial:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="outserial" readonly>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-right">Status:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="outstatus" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table" id="itemdiv" style="display:none">
                <table class="table-hover table billitemTable" id="billitemTable" style="font-size:80%;width:99%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                ITEM DESCRIPTION
                            </th>
                            <th>
                                SERIAL
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer justify-content-center">
                @if(auth()->user()->hasAnyRole('Head', 'Tech'))
                    <div class="printBtn mr-auto" role="tab" id="printBtn"></div>
                    <input type="button" class="btn btn-primary doneBtn" id="doneBtn" class="button" value="DONE">
                    <input type="button" class="btn btn-primary delBtn" id="delBtn" class="button" value="DELETE REQUEST">
                    <div class="prcBtn" role="tab" id="prcBtn"></div>
                @endif
                @if(auth()->user()->hasAnyRole('Warehouse Manager'))
                    <input type="button" class="btn btn-primary approveBtn" id="approveBtn" class="button" value="Approve">
                @endif
            </div>
        </div>
    </div>
</div>