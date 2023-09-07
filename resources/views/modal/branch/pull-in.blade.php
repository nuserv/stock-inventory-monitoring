<div id="pull_unitModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">SERVICE IN FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pull_date" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pull_engr" value="{{ mb_strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7" id="pull_clientdiv">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pull_client" placeholder="" autocomplete="off" disabled>
                            <div id="pull_clientlist" style="position:absolute;z-index: 10000;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pull_customer" placeholder="client branch name" autocomplete="off">
                            <div id="pull_branchlist" style="position:absolute;z-index: 10000;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-header" style="z-index: 100;">
                <h6 class="modal-title w-100 text-center">ITEM DETAILS</h6>
            </div>
            <div class="modal-body" id="pull_outfield">
                <table class="table-hover table pull_requestDetails">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Description</th>
                        <th>Serial&nbsp;&nbsp;&nbsp;</th>
                        <th>Stock</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </thead>
                </table>
                <div class="row no-margin" id="pull_outrow1">
                    <div class="col-md-2 form-group">
                        <select id="pull_outcategory1" class="form-control pull_outcategory" row_count="1" style="color: black;">
                            <option selected disabled>select category</option>
                            @foreach ($pull_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="pull_outdesc1" class="form-control pull_outdesc" row_count="1" style="color: black;">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="text" style="color: black" class="form-control pull_outserial text-uppercase" row_count="1" id="pull_outserial1" autocomplete="off">
                    </div>
                    {{-- <div class="col-md-1 form-group">
                        <input type="number" class="form-control" min="0" name="outstock1" id="outstock1" placeholder="0" style="color:black; width: 6em" disabled>
                    </div> --}}
                    <div class="col-md-1 form-group">
                        <input type="button" class="pull_out_add_item btn btn-xs btn-primary" btn_id="1" value="Add Item" disabled>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary pull_out_sub_Btn" id="pull_out_sub_Btn" reqno="0" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>