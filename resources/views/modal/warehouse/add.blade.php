<div id="addModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ADD STOCK FORM</h6>
                <button class="close" data-dismiss="modal" aria-label="Close" hidden>
                </button>
            </div>
            <div class="modal-body" id="reqfield">
                <div class="row no-margin" id="row1">
                    <div class="col-md-2 form-group">
                        <select id="category1" style="color: black" class="form-control category" row_count="1">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ strtoupper($category->category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item1" style="color: black" class="form-control item" row_count="1">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc1" class="form-control desc" row_count="1" style="color: black">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group text-center">
                        <input type="number" class="form-control" min="0" name="qty1" id="qty1" placeholder="0" style="color:black; width: 6em">
                    </div>
                    <div class="col-md-2 form-group text-center">
                        <input type="text" class="form-control" name="uom1" id="uom1" style="color:black;"readonly>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                
                <input type="button" class="btn btn-primary cancel" value="Cancel">
                <input type="button" class="btn btn-primary sub_Btn" id="sub_Btn" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>