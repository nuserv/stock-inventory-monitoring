<div id="service-unitModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">SERVICE OUT FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="date" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="engr" value="{{ strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" list="client-name" style="color: black" class="form-control form-control-sm " id="client" placeholder="client name" autocomplete="off">
                            <datalist id="client-name">
                            </datalist>
                            <input type="text" id="client-id" value="" hidden>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-6">
                            <input type="text" list="customer-name" style="color: black" class="form-control form-control-sm " id="customer" placeholder="client branch name" autocomplete="off">
                            <datalist id="customer-name">
                            </datalist>
                            <input type="text" id="customer-id" value="" hidden>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ITEM DETAILS</h6>
            </div>
            <div class="modal-body" id="outfield">
                <table class="table requestDetails">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Description</th>
                        <th>Serial&nbsp;&nbsp;&nbsp;</th>
                        <th>Stock</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </thead>
                </table>
                <div class="row no-margin" id="outrow1">
                    <div class="col-md-2 form-group">
                        <select id="outcategory1" class="form-control outcategory" row_count="1" style="color: black;">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="outdesc1" class="form-control outdesc" row_count="1" style="color: black;">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="outserial1" class="form-control outserial" row_count="1" style="color: black;">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="number" class="form-control" min="0" name="outstock1" id="outstock1" placeholder="0" style="color:black; width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="out_add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary out_sub_Btn" id="out_sub_Btn" reqno="0" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>