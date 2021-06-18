<div id="sendModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">STOCK REQUEST FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mod" style="max-height:400px;overflow-y: auto;">
                <form id="sendForm">
                    {{ csrf_field() }}
                    <div class="row no-margin">
                        <div class="col-md-6 form-group row">
                            <label for="bname" style="color: black" class="col-md-5 col-form-label text-md-right">Date requested:</label>
                            <div class="col-md-7">
                                <input type="text" style="color: black" class="form-control form-control-sm " name="date" id="sdate" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 form-group row">
                            <label for="reqno" class="col-md-4 col-form-label text-md-right">Request no.:</label>
                            <div class="col-md-8">
                                <input type="text" style="color: black" class="form-control form-control-sm " name="reqno" id="sreqno" placeholder="1-001" disabled>
                            </div>
                        </div>
                    </div>
                </form>
                <div>
                    <h5 class="modal-title w-100 text-center">REQUEST DETAILS</h5>
                </div>
                <table class="table sendDetails" style="height: 150px;width:100%;font-size:80%">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Description</th>
                        <th>Quantity</th>
                    </thead>
                </table>
            </div>
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ENTER ITEM HERE</h6>
            </div>
            <div class="modal-body" id="reqfield">
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary sub_Btn" id="sub_Btn" class="button" value="Submit">
            </div>
        </div>
    </div>
</div>