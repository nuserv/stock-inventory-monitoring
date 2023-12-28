<div class="modal fade in" id="detailsLocation">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE LOCATION DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="location_id" id="location_id">
            <input type="hidden" name="location_original" id="location_original">
            <input type="hidden" name="status_original" id="status_original">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="height: 35px; width: 125px;">Location Name</label>
                </div>
                <input class="text-uppercase" id="location_details" style="height: 35px; width: 49.5%; border-width: thin; margin-right: 2px;" type="search" maxlength="100" placeholder="Please enter location name">
                <label class="switch">
                    <input type="checkbox" id="status" class="togBtn" value="ACTIVE">
                    <div class="slider round">
                        <span class="on">ACTIVE</span>
                        <span class="off">INACTIVE</span>
                    </div>
                </label>
            </div>
            <button type="button" id="btnUpdateLocation" class="btn btn-primary float-end bp">UPDATE</button>
        </div>
    </div>
    </div>
</div>