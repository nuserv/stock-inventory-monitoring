<div id="addModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ADD STOCK FORM</h6>
                <button class="close cancel" aria-label="Close" hidden>
                </button>
            </div>
            <div class="modal-body" id="reqfield">
                <div class="row no-margin" id="row1">
                    <div class="col-md-2 form-group">
                        <select id="category1" class="form-control category" row_count="1" style="color: black">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ strtoupper($category->category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group" id="itemdiv1" style="display: none">
                        <select id="item1" class="form-control item" row_count="1" style="color: black">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc1" class="form-control desc" row_count="1" style="color: black">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="text" id="serial1" class="form-control serial" row_count="1" value="N/A" style="color: black" onkeypress="return event.charCode >= 47">
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
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