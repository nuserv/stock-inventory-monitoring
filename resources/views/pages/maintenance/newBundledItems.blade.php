<div class="modal fade in" id="newBundledItems">
    <div class="modal-dialog modal-xl">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">CREATE NEW BUNDLED ITEMS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="form-inline">
                <label class="form-control form-control-sm" style="width: 100px;">Product Code</label>
                <input class="form-control form-control-sm spChar required_field" style="width: 305px; margin-right: 10px;" name="bundle_code" id="bundle_code" maxlength="100" placeholder="Please enter product code" type="search" required tabindex="1">
                <label class="form-control form-control-sm" style="width: 140px;">Product Description</label>
                <input class="form-control form-control-sm text-capitalize required_field" style="width: 542px; margin-right: 10px;" name="bundle" id="bundle" maxlength="255" placeholder="Please enter bundled item description to proceed" type="search" required tabindex="2">
            </div>
            <div class="create_label alert alert-primary mt-4" role="alert">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
        </div>
        <div id="bundleDetails" style="display: none;">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">BUNDLE INCLUSIVES</h6>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form class="mt-2 mb-2">
                    <div class="form-inline">
                        <div class="f-outline">
                            <select class="forminput form-control form-select required_field" id="categoryBundled" name="categoryBundled" style="width: 300px;" placeholder=" " required tabindex="4">
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                                @endforeach
                            </select>
                            <label for="categoryBundled" class="formlabel form-label">Category</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <select class="forminput form-control form-select required_field" id="itemBundled" name="itemBundled" style="width: 540px;" placeholder=" " tabindex="5">
                                <option value="" selected disabled>Select Item</option>
                            </select>
                            <label id="itemBundled_label" for="itemBundled" class="formlabel form-label">Product Description</label>
                        </div>
                        <div class="f-outline" style="margin-left: 8px;">
                            <input class="forminput form-control" id="uomBundled" name="uomBundled" style="background-color: white; width: 70px;" type="text" placeholder=" " disabled>
                            <label for="uomBundled" class="formlabel form-label">UOM</label>
                        </div>
                        <div class="f-outline" style="margin-left: 10px;">
                            <input class="forminput form-control numbersOnly required_field" id="qtyBundled" name="qtyBundled" min="1" style="width: 70px;" type="number" placeholder=" " tabindex="6">
                            <label for="qtyBundled" class="formlabel form-label">Qty</label>
                        </div>
                        <input type="button" id="add-row" class="btn btn-primary bp btnRequired" value="ADD ITEM" style="zoom: 90%; margin-left: 10px; margin-top: -1px;" tabindex="7">
                    </div>
                </form>
                <div class="container-fluid" id="#divBundle">
                    <table id='tblBundle' class="table tblBundle" style="cursor: pointer; font-size: 12px; display: none;">
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
                <div class="submit_label alert alert-primary mt-4" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> Please fill up all required fields to proceed.
                </div>
                <div class="ordering_label alert alert-warning" role="alert">
                    <i class='fa fa-exclamation-triangle'></i>
                    <b>NOTE:</b> The current list above will be their ordering when displayed as a bundle. You can <b>DRAG</b> each row to change order.
                </div>
                <div id="submit_footer" style="display: none;">
                    <button type="button" class="btn btn-primary bp" data-bs-dismiss="modal" data-dismiss="modal">CANCEL</button>
                    <button type="button" id="btnSaveBundle" class="btn btn-primary float-end bp">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>