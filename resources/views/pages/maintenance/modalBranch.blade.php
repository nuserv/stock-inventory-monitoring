<div class="modal fade in" id="modalBranch">
    <div class="modal-dialog modal-m modal-dialog-centered">
        <div class="modal-content card">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">BRANCH MAINTENANCE</h6><!--BACKLOG-->
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <div type="hidden" id="branch_id"></div>
                <form>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize required_field" type="search" id="branch_name" name="branch_name" orig_value="" placeholder=" ">
                            <label for="branch_name" class="formlabel form-label">Branch Name</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize required_field" type="search" id="address" name="address" placeholder=" ">
                            <label for="address" class="formlabel form-label">Branch Address</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control formatTIN" type="search" id="tin_number" name="tin_number" placeholder=" ">
                            <label for="tin_number" class="formlabel form-label">T.I.N.</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize required_field" type="search" id="contact_person" name="contact_person" placeholder=" ">
                            <label for="contact_person" class="formlabel form-label">Contact Person</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control text-capitalize" type="search" id="position" name="position" placeholder=" ">
                            <label for="position" class="formlabel form-label">Position</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control required_field" type="search" id="contact_number" name="contact_number" placeholder=" ">
                            <label for="contact_number" class="formlabel form-label">Contact Number</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="f-outline">
                            <input class="forminput form-control text-lowercase" type="search" id="email_address" name="email_address" placeholder=" " onkeyup="emailCheck(this.id)" onchange="emailCheck(this.id)">
                            <label for="email_address" class="formlabel form-label">Email Address</label>
                        </div>
                    </div>
                    <div style="zoom: 85%;">
                        <button type="reset" id="btnClearBranch" class="btn btn-primary bp" onclick="$('#branch_name').focus();">CLEAR</button>
                        <button type="button" id="btnSaveBranch" class="btn btn-primary float-end bp">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>