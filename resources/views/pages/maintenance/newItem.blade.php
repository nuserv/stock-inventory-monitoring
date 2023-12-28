<div class="modal fade in" id="newItem">
    <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW ITEM</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="alert alert-primary requiredNote" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Category Name<i class="xitem_category fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <select id="item_category" style="width: 563px;">
                    <option value="" selected disabled>Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{strtoupper($category->category)}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Product Code
                    </label>
                </div>
                <input id="prodcode" class="spChar" style="width: 563px; border-width: thin;" type="search" maxlength="255" placeholder="Please enter product code">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Product Description<i class="xitem_name fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <input id="item_name" class="text-capitalize" style="width: 563px; border-width: thin;" type="search" maxlength="255" placeholder="Please enter product description">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Product Specifications
                    </label>
                </div>
                <textarea id="specs" class="text-capitalize" style="width: 563px; border-width: thin;" rows="5" placeholder="Please enter product specifications (if available) separated by ENTER"></textarea>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Minimum Stock<i class="xminimum fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <input class="numbersOnly" id="minimum" style="width: 563px; border-width: thin;" type="number" min="1" placeholder="Please enter minimum stock">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Unit of Measure (UOM)<i class="xitem_uom fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <select id="item_uom" style="width: 563px;">
                    <option value="" selected disabled>Select UOM</option>
                    <option value="Unit">Unit</option>
                    <option value="Pc">Pc</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>
            <div class="input-group mb-3 divSerial" style="display: none;">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px; height: 35px;">Has Serial? (YES/NO)</label>
                </div>
                <label class="switch" style="zoom: 110%; height: 30px; width: 100px; margin-left: -10px;">
                    <input type="checkbox" id="serialize" class="togBtn" value="ACTIVE" checked>
                    <div class="slider round" style="zoom: 90%;">
                        <span class="on" style="zoom: 175%;">YES</span>
                        <span class="off" style="zoom: 175%;">NO</span>
                    </div>
                </label>
            </div>
            <button type="button" id="btnSaveItem" class="btn btn-primary float-end bp btnRequired">SUBMIT</button>
        </div>
    </div>
    </div>
</div>