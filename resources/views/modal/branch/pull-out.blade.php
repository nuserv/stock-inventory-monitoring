<div id="pulloutModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">PULL-OUT FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pdate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="pengr" value="{{ strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" list="pclient-name" style="color: black" class="form-control form-control-sm " id="pclient" placeholder="client name" autocomplete="off">
                            <datalist id="pclient-name">
                            </datalist>
                            <input type="text" id="pclient-id" value="" hidden>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" list="pcustomer-name" style="color: black" class="form-control form-control-sm " id="pcustomer" placeholder="client branch name" autocomplete="off">
                            <datalist id="pcustomer-name">
                            </datalist>
                            <input type="text" id="pcustomer-id" value="" hidden>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ITEM DETAILS</h6>
            </div>
            <div class="modal-body" id="poutfield">
                <table class="table pullDetails">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Description</th>
                        <th>Serial&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </thead>
                </table>
                <div class="row no-margin" id="poutrow1">
                    <div class="col-md-2 form-group">
                        <select id="poutcategory1" class="form-control poutcategory" row_count="1" style="color: black;">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="poutdesc1" class="form-control poutdesc" row_count="1" style="color: black;">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="text" id="poutserial1" class="form-control poutserial" row_count="1" style="color: black;" placeholder="serial">
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="pout_add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary pout_sub_Btn" id="pout_sub_Btn" reqno="0" class="button" value="Submit">
            </div>
        </div>
    </div>
</div>