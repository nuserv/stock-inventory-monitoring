<div id="schedModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">PM FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date Encoded:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="indate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">PM Date:</label>
                        <div class="col-md-6">
                            <input type="text" title="This field is required." style="color: black" class="form-control form-control-sm datepicker datesched" placeholder="Select PM Date" name="datesched" id="datesched" readonly="readonly" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="customer" placeholder="client branch name" readonly="readonly" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">FSR No.:</label>
                        <div class="col-md-6">
                            <input type="text" title="PM Date is required." style="color: black" class="form-control form-control-sm " id="fsrno" placeholder="Fsr No." autocomplete="off" readonly="readonly">
                            <div id="fsrlist" style="position:absolute;z-index: 10000;">
                            </div>
                        </div>
                    </div>
                </div><hr>
                <div class="row no-margin">
                    <p style="color: red"><B>NOTE:</B></p><br><p style="color: black">To be able to post the PM you made, you need first to upload the FSR in the FSR System. Once you upload it, you can choose the branch and the date of the PM and the FSR number will appear automatically.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <input type="button" class="btn btn-primary cancel" id="cancelBtn" class="button" value="CANCEL">
                <input type="button" class="btn btn-primary saveBtn" id="saveBtn" class="button" value="SAVE">
            </div>
        </div>
    </div>
</div>