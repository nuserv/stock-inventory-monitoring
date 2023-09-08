<div id="returnModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">Return Details</h6>
                <button class="close cancel" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if(auth()->user()->branch->branch == 'Conversion')
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Pull out Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pulloutdate" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Pull out by:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pulloutby" readonly>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Branch:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="client" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">DR reference no.:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="refno" readonly>
                        </div>
                    </div>
                </div>
                @endif
                <table class="table-hover table returnitems" id="returnitems" style="width:100%">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Item Description</th>
                        <th>Serial</th>
                        @if (auth()->user()->branch->branch != 'Conversion')
                            <th>Service by</th>
                            <th>Remarks</th>
                        @endif
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <div class="printBtn pt-3" id="printBtn"></div>
            </div>
        </div>
    </div>
</div>