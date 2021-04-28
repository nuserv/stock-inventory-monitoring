<div id="loanModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">LOAN FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="loandate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Service Engineer:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="loanengr" value="{{ mb_strtoupper(auth()->user()->name) }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Branch Name:</label>
                        <div class="col-md-7">
                            <select style="color: black" class="form-control form-control-sm " id="loanbranch">
                                <option selected disabled>select branch</option>
                                @foreach ($branches as $branch)
                                @if ($branch->branch != "Warehouse")
                                <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                                @endif
                                @endforeach
                            </select>
                            <input type="text" id="branch-id" value="" hidden>
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
                        <th>Description&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </thead>
                </table>
                <div class="row no-margin" id="outrow1">
                    <div class="col-md-2 form-group">
                        <select id="loanreqcategory1" class="form-control loancategory" row_count="1" style="color: black;">
                            <option selected disabled>select category</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="loanreqdesc1" class="form-control loandesc" row_count="1" style="color: black;">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="button" class="btn btn-primary add_item" id="add_item1" btn_id="1"class="button" value="Add Item">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary serial_sub_Btn" id="loan_sub_Btn" reqno="0" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>