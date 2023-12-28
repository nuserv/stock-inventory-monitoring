<div id="selectBranch" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center">PLEASE SELECT CUSTOMER BRANCH</h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <hr>
                <h4 style="font-weight: bold; color: #0d1a80;">CUSTOMER PROFILE</h4>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize" type="search" id="show_customer_name" name="show_customer_name" disabled>
                            <label for="show_customer_name" class="formlabel form-label">CUSTOMER NAME</label>
                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-2">
                        <button class="btn btn-primary bp w-100 btnNewBranch"><i class="fa-solid fa-circle-plus fa-lg mr-2"></i>NEW BRANCH</button>
                    </div>
                </div>
                <hr>
                <h4 style="font-weight: bold; color: #0d1a80;">BRANCH PROFILES (SELECT A BRANCH BELOW)</h4>
                <hr>
                <div id="divSelectBranchTable">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary bp float-end" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> CLOSE</button>
            </div>
        </div>
    </div>
</div>