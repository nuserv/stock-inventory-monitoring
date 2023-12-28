<div class="modal fade in" id="detailsSupplier">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE SUPPLIER DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div id="alertUpdateSupplier" class="alert alert-primary" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all required fields to proceed.
            </div>
            <div id="emailUpdateSupplier" class="alert alert-warning" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>CANNOT PROCEED:</b> Email Address has an invalid format!
            </div>
            <div id="marginUpdateSupplier" style="height: 10px;"></div>
            <form>
                <input type="hidden" id="supplier_id">
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control spChar requiredFields required_field" type="search" id="supplier_code_new" name="supplier_code_new" placeholder=" ">
                        <label for="supplier_code_new" class="formlabel form-label">Supplier Code</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredFields required_field" type="search" id="supplier_name_new" name="supplier_name_new" placeholder=" ">
                        <label for="supplier_name_new" class="formlabel form-label">Supplier Name</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredFields required_field" type="search" id="address_new" name="address_new" placeholder=" ">
                        <label for="address_new" class="formlabel form-label">Supplier Address</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-capitalize requiredFields required_field" type="search" id="contact_person_new" name="contact_person_new" placeholder=" ">
                        <label for="contact_person_new" class="formlabel form-label">Contact Person</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control requiredFields required_field" type="search" id="contact_number_new" name="contact_number_new" placeholder=" ">
                        <label for="contact_number_new" class="formlabel form-label">Contact Number</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control text-lowercase requiredFields required_field" type="search" id="email_new" name="email_new" placeholder=" ">
                        <label for="email_new" class="formlabel form-label">Email Address</label>
                    </div>
                </div>
                <div style="zoom: 85%;">
                    <button type="button" id="btnResetSupplier" class="btn btn-primary bp" onclick="$('#supplier_code_new').focus();">RESET</button>
                    <button type="button" id="btnUpdateSupplier" class="btn btn-primary float-end bp">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>