<div id="sendrequestModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">STOCK REQUEST FORM</h6>
                <button class="close cancel" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label for="bname" class="col-md-5 col-form-label text-md-right">Date requested:</label>
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
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label for="name" class="col-md-5 col-form-label text-md-right">Requested by:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " name="name" id="sname" value="{{ auth()->user()->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label for="reqno" class="col-md-4 col-form-label text-md-right">Request type:</label>
                        <div class="col-md-8">
                            <select id="requesttype" class="form-control requesttype" style="color: black;">
                                <option selected disabled>select type</option>
                                <option value="Stock">Stock</option>
                                <option value="Service">Service</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row no-margin" id="clientrow" style="display:none">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" list="client-name" style="color: black" class="form-control form-control-sm " id="client" placeholder="client name" autocomplete="off">
                            <datalist id="client-name">
                            </datalist>
                            <input type="text" id="client-id" value="" hidden>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Client Branch Name:</label>
                        <div class="col-md-8">
                            <input type="text" list="customer-name" style="color: black" class="form-control form-control-sm " id="customer" placeholder="client branch name" autocomplete="off">
                            <datalist id="customer-name">
                            </datalist>
                            <input type="text" id="customer-id" value="" hidden>
                        </div>
                    </div>
                </div>
                <div class="row no-margin" id="ticketrow" style="display:none">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Ticket no.:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="ticket" placeholder="Enter ticket number" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ENTER ITEM HERE</h6>
            </div>
            <div class="modal-body" id="reqfield">
                <div class="row no-margin" id="row1">
                    <div class="col-md-2 form-group">
                        <select id="category1" class="form-control category" row_count="1" style="color: black;">
                            <option selected disabled>select category</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group" id="itemdiv1" style="display:none">
                        <select id="item1" class="form-control item" row_count="1" style="color: black;">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc1" class="form-control desc" row_count="1" style="color: black;">
                            <option selected disabled>select item description</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group text-center">
                        <input type="number" class="form-control" min="0" name="qty1" id="qty1" placeholder="0" style="color:black; width: 6em" disabled>
                    </div>
                    <div class="col-md-2 form-group text-center">
                        <input type="text" class="form-control" name="uom1" id="uom1" style="color:black;"readonly>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
                <input type="button" class="btn btn-primary send_sub_Btn" id="send_sub_Btn" class="button" value="Submit" disabled>
            </div>
        </div>
    </div>
</div>