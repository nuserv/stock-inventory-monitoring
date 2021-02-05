<div id="replacementSelectModal" class="modal fade">
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
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replaceselectdate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replaceselectengr" value="{{ strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replaceselectclient" placeholder="client name" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replaceselectcustomer" placeholder="client branch name" disabled>
                        </div>
                    </div>
                </div>
                <div id="table">
                    <table class="table replacement1Details">
                        <thead class="thead-dark">
                            <th>Date</th>
                            <th>Category</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Serial</th>
                        </thead>
                    </table>
                </div>
                <br>
                <hr><br>
                <div class="container">
                    <div class="row no-margin" id="goodrow1">
                        <div class="col-md-5 form-group">
                            <select id="repdesc1" class="form-control repdesc" row_count="1">
                                <option selected disabled>select item description</option>
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <select id="repserial1" class="form-control repserial" row_count="1">
                                <option selected disabled>select serial</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="rep_sub_Btn btn btn-xs btn-primary" btn_id="1" value="Submit">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>