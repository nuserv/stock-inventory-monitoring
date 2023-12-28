<div class="modal fade in" id="detailsCategory">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
            <h6 class="modal-title w-100">UPDATE CATEGORY DETAILS</h6>
            <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <input type="hidden" name="category_id" id="category_id">
            <input type="hidden" name="category_original" id="category_original">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" style="width: 125px;">Category Name</label>
                </div>
                <input class="text-uppercase" id="category_details" style="width: 73%; border-width: thin;" type="search" maxlength="150" placeholder="Please enter category name">
            </div>
            <button type="button" id="btnUpdateCategory" class="btn btn-primary float-end bp">UPDATE</button>
        </div>
    </div>
    </div>
</div>