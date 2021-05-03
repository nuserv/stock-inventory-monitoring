<div id="requestModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">STOCK REQUEST</h6>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date requested:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="date" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Status:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="status" placeholder="1-001" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Branch name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="branch" value="{{ auth()->user()->branch->branch }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Requested by:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="name" value="{{ auth()->user()->name }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Area:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="area" value="{{ auth()->user()->area->area }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row ticketno">
                        <label class="col-md-5 col-form-label text-md-right">Ticket no.:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="tickets" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin" id="clientrows" style="display:none">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="clients" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Customer Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="customers" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Request type:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="requesttypes" value="" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row sched">
                        <label class="col-md-5 col-form-label text-md-right">Schedule on:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="sched" value="" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row" id="intransitrow" style="display:none">
                        <label class="col-md-5 col-form-label text-md-right">In transit on:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="intransitsched" value="" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h5 class="modal-title w-100 text-center" id="reqlabel">REQUEST DETAILS</h5>
            </div>
            <div class="modal-body">
                <table class="table requestDetails" style="font-size:80%">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Description</th>
                        <th>Qty</th>
                    </thead>
                </table>
                <br>
                <h5 class="modal-title w-100 text-center" id='schedslabel'>SCHEDULED ITEM DETAILS</h5>
                <table class="table schedDetails" id="schedDetails" style="font-size:80%">
                    <thead class="thead-dark">
                        <th>Delivery Schedule</th>
                        <th>Description</th>
                        <th>Qty - UOM</th>
                        <th>Serial</th>
                    </thead>
                </table>
                <br>
                <h5 class="modal-title w-100 text-center" id='intransitlabel'>INTRANSIT ITEM DETAILS</h5>
                <table class="table intransitDetails" id="intransitDetails" style="font-size:80%">
                    <thead class="thead-dark">
                        <th>Delivery Schedule</th>
                        <th>Description</th>
                        <th>Qty - UOM</th>
                        <th>Serial</th>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary mr-auto" id="not_rec_Btn" class="button" value="Not received">
                <span id="msg">Please select an item to receive.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="button" class="btn btn-primary del_Btn" id="del_Btn" reqno="0" class="button" value="Delete">
                <input type="button" class="btn btn-primary rec_Btn" id="rec_Btn" class="button" value="Received">
            </div>
        </div>
    </div>
</div>