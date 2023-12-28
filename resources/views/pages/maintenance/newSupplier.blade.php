<div class="modal fade in" id="newSupplier">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">ADD NEW SUPPLIER</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div id="alertNewSupplier" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
            <div id="emailNewSupplier" class="alert alert-warning" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>CANNOT PROCEED:</b> Email Address has an invalid format!
            </div>
            <div id="marginNewSupplier" style="height: 10px;"></div>
            <form>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control spChar requiredField required_field" type="search" id="supplier_code" name="supplier_code" placeholder=" ">
                        <label for="supplier_code" class="formlabel form-label">Supplier Code</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredField required_field" type="search" id="supplier_name" name="supplier_name" placeholder=" ">
                        <label for="supplier_name" class="formlabel form-label">Supplier Name</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredField required_field" type="search" id="address" name="address" placeholder=" ">
                        <label for="address" class="formlabel form-label">Supplier Address</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredField required_field" type="search" id="contact_person" name="contact_person" placeholder=" ">
                        <label for="contact_person" class="formlabel form-label">Contact Person</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField required_field" type="search" id="contact_number" name="contact_number" placeholder=" ">
                        <label for="contact_number" class="formlabel form-label">Contact Number</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-lowercase requiredField required_field" type="search" id="email" name="email" placeholder=" ">
                        <label for="email" class="formlabel form-label">Email Address</label>
                    </div>
                </div>
                <div style="zoom: 85%;">
                    <button type="reset" id="btnClearSupplier" class="btn btn-primary bp" onclick="$('#supplier_code').focus();">CLEAR</button>
                    <button type="button" id="btnSaveSupplier" class="btn btn-primary float-end bp">SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>