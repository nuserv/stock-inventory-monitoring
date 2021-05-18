<div id="service-inModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">PM SERVICE IN FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm" id="indate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="inengr" value="{{ mb_strtoupper(auth()->user()->name) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-7">
                            <select id="incustomer" class="form-control intype" style="color: black;">
                                <option selected disabled>select client branch</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ITEM DETAILS</h6>
            </div>
            <div class="modal-body" id="infield">
                <table class="table requestDetails">
                    <thead class="thead-dark">
                        <th>Service type</th>
                        <th>Description</th>
                        <th>Serial</th>
                        <th>status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </thead>
                </table>
                <div class="row no-margin" id="inrow1">
                    <div class="col-md-3 form-group">
                        <select id="intype" class="form-control intype" style="color: black;">
                            <option selected disabled>select service type</option>
                            <option value="service-unit">service unit</option>
                            <option value="replacement">replacement</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="repdesc" class="form-control" style="color: black;" disabled>
                            <option selected disabled>select item description</option>
                        </select>
                        <input type="number" id="indescid" class="form-control" style="color: black; display:none;" readonly>  
                        <input type="text" id="indesc" class="form-control" style="color: black; display:none;" readonly>  
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="text" id="inserial" class="form-control" style="color: black; display:none;" readonly> 
                        <input type="text" id="repserial" class="form-control" placeholder="input serial number" style="color: black; display:none;" onkeypress="return event.charCode >= 47">  
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="instatus" class="form-control intype" style="color: black;" disabled>
                            <option selected disabled>select item status</option>
                            <option value="good">good</option>
                            <option value="defective">defective</option>
                        </select>
                        <input type="text" id="repstatus" class="form-control" value="defective" style="color: black; display:none;" readonly>  

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary in_sub_Btn mr-auto" id="in_sub_Btn" class="button" value="SERVICE IN">
            </div>
        </div>
    </div>
</div>