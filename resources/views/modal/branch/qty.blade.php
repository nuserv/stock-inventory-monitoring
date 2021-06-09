<div id="qtyModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">Stock Request</h6>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <button class="in-close" data-dismiss="modal" aria-label="Close" hidden>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row justify-content-center">
                         <div class="col-md-12 form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Category:</label>
                            <div class="col-md-8">
                                <input type="text" style="color: black" class="form-control form-control-sm client" id="requestcategory"disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Item Description:</label>
                            <div class="col-md-8">
                                <input type="text" style="color: black" class="form-control form-control-sm client" id="requestitem"disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-12 form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Quantity:</label>
                            <div class="col-md-8">
                                <input type="number" id="qty" placeholder="Input Qty">
                            </div>
                        </div>
                        <p style="color:black">Base on your current stock, the minimum quantity for this item is indicated above. You may increase the quantity if you feel there is a need.</p>
                    </div>
                </div>
            </div>
            
            <div class="moday-body text-center">
                <input type="button" class="btn btn-primary" id="req" value="SUBMIT">
            </div><br>
        </div>
    </div>
</div>