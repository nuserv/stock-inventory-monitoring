<div id="replacementModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">REPLACEMENT FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <button class="closes" data-dismiss="modal" aria-label="Close" hidden>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replacementdate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replacementengr" value="{{ mb_strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" list="replacementclient-name" style="color: black" class="form-control form-control-sm " id="replacementclient" placeholder="client name" autocomplete="off">
                            <datalist id="replacementclient-name">
                            </datalist>
                            <input type="text" id="replacementclient-id" value="" hidden>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" list="replacementcustomer-name" style="color: black" class="form-control form-control-sm " id="replacementcustomer" placeholder="client branch name" autocomplete="off">
                            <datalist id="replacementcustomer-name">
                            </datalist>
                            <input type="text" id="replacementcustomer-id" value="" hidden>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary replacement_next_Btn" id="replacement_next_Btn" class="button" value="Next">
            </div>
        </div>
    </div>
</div>