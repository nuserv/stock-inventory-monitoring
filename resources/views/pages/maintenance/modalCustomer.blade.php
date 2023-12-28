<div id="modalCustomer" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center">CUSTOMER MAINTENANCE</h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <hr>
                <h4 style="font-weight: bold; color: #0d1a80;">CUSTOMER PROFILE</h4>
                <hr>
                <div type="hidden" id="customer_id"></div>
                <div class="row">
                    <div class="col-2">
                        <div class="f-outline">
                            <input class="forminput form-control text-uppercase spChar requiredField required_field" type="search" id="customer_code" name="customer_code" orig_value="" placeholder=" " onkeyup="duplicateCheck('customer_code', 'code', 'customers')" onchange="duplicateCheck('customer_code', 'code', 'customers')">
                            <label for="customer_code" class="formlabel form-label">CUSTOMER CODE</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize requiredField required_field" type="search" id="customer_name" name="customer_name" orig_value="" placeholder=" " onkeyup="duplicateCheck('customer_name', 'customer', 'customers')" onchange="duplicateCheck('customer_name', 'customer', 'customers')">
                            <label for="customer_name" class="formlabel form-label">CUSTOMER NAME</label>
                        </div>
                    </div>
                    <div class="col-2">
                        <button id="btnSaveCustomer" class="btn btn-primary bp w-100"><i class="fas fa-save mr-2"></i>SAVE CUSTOMER</button>
                    </div>
                    <div class="col-2">
                        <button id="btnNewBranch" class="btn btn-primary bp w-100"><i class="fa-solid fa-circle-plus fa-lg mr-2"></i>NEW BRANCH</button>
                    </div>
                </div>
                <hr>
                <h4 style="font-weight: bold; color: #0d1a80;">BRANCH PROFILES</h4>
                <hr>
                <div id="divBranchTable">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary bp float-end" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> CLOSE</button>
            </div>
        </div>
    </div>
</div>