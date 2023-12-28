<div class="modal fade in" id="newAssembledItem">
    <div class="modal-dialog modal-xl">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CREATE NEW ASSEMBLED ITEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 140px;">Product Description</label>
                <select class="forminput form-control form-select required_field form-control-sm" id="aic_item_description" name="aic_item_description" style="width: auto;" placeholder=" " required>
                    <option value="" selected disabled>Select Product Description</option>
                    @foreach($toAssy as $item)
                        <option value="{{$item->id}}">{{strtoupper($item->item)}}</option>
                    @endforeach
                </select></div>
            <div class="create_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="partsDetails" style="display: none;">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">PARTS DETAILS</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline">
                        <div class="f-outline">
                            <select class="forminput form-control form-select required_field" id="categoryAssembly" name="categoryAssembly" style="width: 300px;" placeholder=" " required tabindex="4">
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                            </select>
                            <label for="categoryAssembly" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select required_field" id="itemAssembly" name="itemAssembly" style="width: 540px;" placeholder=" " tabindex="5">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label id="itemAssembly_label" for="itemAssembly" class="formlabel form-label">Product Description</label>
                        </div>
                        <div class="f-outline" style="margin-left: 8px;">
                            <input class="forminput form-control" id="uomAssembly" name="uomAssembly" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uomAssembly" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control numbersOnly required_field" id="qtyAssembly" name="qtyAssembly" min="1" style="width: 70px;" type="number" placeholder=" " tabindex="6">
                            <label for="qtyAssembly" class="formlabel form-label">Qty</label>
                        </div>
                        <input type="button" class="add-row btn btn-primary bp btnRequired" value="ADD ITEM" style="zoom: 90%; margin-left: 10px; margin-top: -1px;" tabindex="7">
                    </div>
                </form>
                <div class="container-fluid" id="#divCreateItem">
                    <table id='tblSaveParts' class="table tblSaveParts" style="cursor: pointer; font-size: 12px; display: none;">
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
                        <tbody style="cursor: grab;" title="DRAG ROW">
                        </tbody>
                    </table>
                </div>
                <div class="requiredSaveParts alert alert-primary mt-4" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> Please fill up all required fields to proceed.
                </div>
                <div class="labelSaveParts alert alert-warning" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> The current list above will be their ordering when displayed as parts of the assembled item. You can <b>DRAG</b> each row to change order.
                </div>
                <button type="button" id="btnClose" class="btn btn-primary bp" style="display: none;" data-bs-dismiss="modal" data-dismiss="modal">CANCEL</button>
                <button type="button" id="btnSave" class="btn btn-primary float-end bp" style="display: none;">SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>