<div id="replace-return" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">REPAIRED FORM</h6>
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
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replaceselectengr" value="{{ mb_strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Item Description:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="replace-item" disabled>
                        </div>
                    </div>
                </div>
                <div id="table">
                    <table class="table replace-return" style="font-size:80%;width: 100%">
                        <thead class="thead-dark">
                            <th></th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>item Description</th>
                            <th>Serial</th>
                            <th>Status</th>
                        </thead>
                    </table>
                </div>
                <br>
                <hr><br>
                <div class="container">
                    <div class="modal-footer">
                        <input type="button" class="repret_sub_Btn btn btn-xs btn-primary" btn_id="1" value="Submit">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>