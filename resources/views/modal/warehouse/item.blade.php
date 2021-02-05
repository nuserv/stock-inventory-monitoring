<div id="itemModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ADD ITEM FORM</h6>
                <button class="close" data-dismiss="modal" aria-label="Close" hidden>

                </button>
            </div>
            <div class="modal-body" id="itemfield">
                <div class="row no-margin" id="itemrow1">
                    <div class="col-md-4 form-group">
                        <select id="itemcat1" class="form-control item-category" row_count="1" style="color: black">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="item-desc1" style="color: black" class="form-control" row_count="1" placeholder="Item Description" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <select id="itemuom1" class="form-control item-uom" row_count="1" style="color: black">
                            <option selected disabled>select uom</option>
                            <option value="Meter">Meter</option>
                            <option value="Unit">Unit</option>
                            <option value="Pc">Pc</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="1" value="Add">
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
                <input type="button" class="btn btn-primary sub_item_Btn" id="sub_item_Btn" class="button" value="Submit">
            </div>
        </div>
    </div>
</div>