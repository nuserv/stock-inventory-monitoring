<div class="modal fade in" id="detailsAssembledItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE ASSEMBLED ITEM DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="aim_item_id" id="aim_item_id">
            <input type="hidden" name="aim_item_name_details_original" id="aim_item_name_details_original">
            <input type="hidden" name="aim_item_code_details_original" id="aim_item_code_details_original">
            <input type="hidden" name="aim_minimum_details_original" id="aim_minimum_details_original">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 100px;">Product Code</label>
                <input class="form-control form-control-sm spChar required_field" style="width: 215px; margin-right: 10px;" name="aim_item_code_details" id="aim_item_code_details" maxlength="100" placeholder="Please enter product code" type="search" tabindex="1" required>
                <label class="form-control form-control-sm" style="width: 140px;">Product Description</label>
                <input class="form-control form-control-sm text-capitalize required_field" style="width: 442px; margin-right: 10px;" name="aim_item_name_details" id="aim_item_name_details" maxlength="255" placeholder="Please enter assembled item description to proceed" tabindex="2" type="search" required>
                <label class="form-control form-control-sm" style="width: 110px;">Minimum Stock</label>
                <input class="form-control form-control-sm numbersOnly required_field" style="width: 80px;" name="aim_minimum_details" id="aim_minimum_details" type="number" min="1" placeholder="Qty" tabindex="3" required>
            </div>
        </div>
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">PARTS DETAILS</h6>
        </div>
        <div class="container-fluid mt-2" id="#divItemDetails">
            <div id="editParts" style="display: none;">
                <form class="mx-1 mt-2 mb-2">
                    <div class="form-inline">
                        <div class="f-outline">
                            <select class="forminput form-control form-select required_field" id="categoryAssemblyDetails" name="categoryAssemblyDetails" style="width: 300px;" placeholder=" " required tabindex="4">
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                            </select>
                            <label for="categoryAssemblyDetails" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select required_field" id="itemAssemblyDetails" name="itemAssemblyDetails" style="width: 540px;" placeholder=" " tabindex="5">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label id="itemAssembly_label" for="itemAssemblyDetails" class="formlabel form-label">Product Description</label>
                        </div>
                        <div class="f-outline" style="margin-left: 8px;">
                            <input class="forminput form-control" id="uomAssemblyDetails" name="uomAssemblyDetails" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uomAssemblyDetails" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control numbersOnly required_field" id="qtyAssemblyDetails" name="qtyAssemblyDetails" min="1" style="width: 70px;" type="number" placeholder=" " tabindex="6">
                            <label for="qtyAssemblyDetails" class="formlabel form-label">Qty</label>
                        </div>
                        <input type="button" class="add-row-details btn btn-primary bp btnRequired" value="ADD ITEM" style="zoom: 90%; margin-left: 10px; margin-top: -1px;" tabindex="7">
                    </div>
                </form>
                <table id='tblEditParts' class="table tblEditParts" style="cursor: pointer; font-size: 12px;">
                    <thead>
                        <tr>
                            <th class="d-none">ITEM ID</th>
                            <th>PRODUCT DESCRIPTION</th>
                            <th>QTY</th>
                            <th>UOM</th>
                            <th></th>
                            <th class="d-none"></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyEditParts" style="cursor: grab;" title="DRAG ROW">
                    </tbody>
                </table>
                <div class="requiredEditParts alert alert-primary mt-4" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> Please fill up all required fields to proceed.
                </div>
                <div class="labelEditParts alert alert-warning" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> The current list above will be their ordering when displayed as parts of the assembled item. You can <b>DRAG</b> each row to change order.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary bp mr-auto" data-bs-dismiss="modal" data-dismiss="modal">CANCEL</button>
            <span class="mr-auto" id="show_barcode"></span>
            <button type="button" id="btnUpdate" class="btn btn-primary float-end bp">UPDATE</button>
        </div>
    </div>
    </div>
</div>
<script>
setInterval(() => {
    if(!$('#aim_item_code_details_original').val() || $('#aim_item_code_details_original').val() == '[BLANK]'){
        $('#show_barcode').hide();
    }
    else{
        $('#show_barcode').show();
    }
}, 0);
</script>