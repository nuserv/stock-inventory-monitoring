<div id="addModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">CONVERSION ITEM ENTRY</h6>
                <button class="close cancel" aria-label="Close" hidden>
                </button>
            </div>
            <div class="modal-body" id="reqfield">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="indate" value="{{ Carbon\Carbon::now()->toDayDateTimeString() }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Prepared by:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="inengr" value="{{ mb_strtoupper(auth()->user()->name) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Pull out Date:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm datepicker" name="datepullout" id="datepullout" readonly="readonlyy" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">DR Reference no.:</label>
                        <div class="col-md-6">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="drno">
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="incustomer" autocomplete="off">
                            <div id="branchlist" style="position:absolute;z-index: 10000;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-6 col-form-label text-md-right">Branch Name:</label>
                        <div class="col-md-6">
                            <select style="color: black" class="form-control form-control-sm " id="branch">
                                <option selected disabled>select branch</option>
                                @foreach ($branches as $branch)
                                @if ($branch->branch != "Warehouse")
                                    @if ($branch->branch != "Conversion")
                                        <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                                    @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div hidden>
                    <div class="modal-header" style="z-index: 100;">
                        <h6 class="modal-title w-100 text-center">POS DETAILS</h6>
                    </div><br>
                    <div class="modal-body" id="posreqfield">
                        <div class="row no-margin" id="posrow1">
                            <div class="col-md-3 form-group">
                                <select style="color: black" class="form-control form-control-sm " id="possel1">
                                    <option selected disabled>Select POS Model</option>
                                    <option value="4800-722">4800-722</option>
                                    <option value="4800-723">4800-723</option>
                                    <option value="4800-743">4800-743</option>
                                    <option value="4900-745">4900-745</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" class="form-control form-control-sm serial" id="posserial1" placeholder="Serial number" style="color: black" >
                            </div>
                            <div class="col-md-1 form-group">
                                <input type="button" class="btn btn-xs btn-primary form-control-sm addpos" pos_count="1" value="Add POS">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-header" style="z-index: 100;">
                    <h6 class="modal-title w-100 text-center">PARTS DETAILS</h6>
                </div><br>
                <div class="modal-body" id="reqfield">
                    <div class="row no-margin" id="row1">
                        <div class="col-md-3 form-group">
                            <select id="category1" class="form-control category" row_count="1" style="color: black">
                                <option selected disabled>select category</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ mb_strtoupper($category->category) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <select id="desc1" class="form-control desc" row_count="1" style="color: black">
                                <option selected disabled>select item description</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <input type="text" id="serial1" class="form-control serial" row_count="1" placeholder="serial" style="color: black" disabled>
                        </div>
                        <div class="col-md-1 form-group">
                            <input type="button" class="add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
                <input type="button" class="btn btn-primary sub_Btn" id="sub_Btn" class="button" value="Submit">
            </div>
        </div>
    </div>
</div>