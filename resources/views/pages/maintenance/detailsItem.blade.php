<div class="modal fade in" id="detailsItem">
    <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE ITEM DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="alert alert-primary requiredNote" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
            <input type="hidden" name="item_id" id="item_id">
            <input type="hidden" name="category_name_details_original" id="category_name_details_original">
            <input type="hidden" name="item_category_details_original" id="item_category_details_original">
            <input type="hidden" name="item_name_details_original" id="item_name_details_original">
            <input type="hidden" name="prodcode_details_original" id="prodcode_details_original">
            <input type="hidden" name="minimum_details_original" id="minimum_details_original">
            <input type="hidden" name="item_uom_details_original" id="item_uom_details_original">
            <input type="hidden" name="serialize_details_original" id="serialize_details_original">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Category Name<i class="xitem_category_details fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <select id="item_category_details" style="width: 563px;">
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
                <input id="prodcode_details" class="spChar" style="width: 563px; border-width: thin;" type="search" maxlength="255" placeholder="Please enter product code">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Product Description<i class="xitem_name_details fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <input id="item_name_details" class="text-capitalize" style="width: 563px; border-width: thin;" type="search" maxlength="255" placeholder="Please enter product description">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Product Specifications
                    </label>
                </div>
                <textarea id="specs_details" class="text-capitalize" style="width: 563px; border-width: thin;" rows="5" placeholder="Please enter product specifications (if available) separated by ENTER"></textarea>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Minimum Stock<i class="xminimum_details fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <input class="numbersOnly" id="minimum_details" style="width: 563px; border-width: thin;" type="number" min="1" placeholder="Please enter minimum stock">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 205px;">
                        Unit of Measure (UOM)<i class="xitem_uom_details fa fa-exclamation-triangle requiredAlert ml-auto" style="zoom: 150%; color: orange;"></i>
                    </label>
                </div>
                <select id="item_uom_details" style="width: 563px;">
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
                    <input type="checkbox" id="serialize_details" class="togBtn" value="ACTIVE">
                    <div class="slider round" style="zoom: 90%;">
                        <span class="on" style="zoom: 175%;">YES</span>
                        <span class="off" style="zoom: 175%;">NO</span>
                    </div>
                </label>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text mr-2" style="width: 205px;">
                        Barcode
                    </label>
                </div>
                <span id="show_barcode"></span>
            </div>
            <button type="button" id="btnUpdateItem" class="btn btn-primary float-end bp btnRequired">UPDATE</button>
        </div>
    </div>
    </div>
</div>
<script>
setInterval(() => {
    if(!$('#prodcode_details_original').val() || $('#prodcode_details_original').val() == '[BLANK]'){
        $('#show_barcode').hide();
    }
    else{
        $('#show_barcode').show();
    }
}, 0);
</script>