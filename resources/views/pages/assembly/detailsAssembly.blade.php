<div class="modal fade in" id="detailsAssembly" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <a id="btnRefresh" class="fa fa-refresh" style="zoom: 150%; cursor: pointer; text-decoration: none; color: white;" aria-hidden="true"></a>
                <h6 class="modal-title w-100">ASSEMBLY REQUEST DETAILS</h6>
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <input type="hidden" id="req_type_id_details">
                <input type="hidden" id="status_id_details">
                <input type="hidden" id="item_id_details">
                <div class="form-inline" style="margin-left: 35px;">
                    <label class="form-control form-control-sm" id="status_label" style="width: 160px;">Status</label>
                    <input class="form-control form-control-sm" id="status_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm repshow" style="width: 160px; display: none;">Original Request No.</label>
                    <input class="form-control form-control-sm repshow" id="asm_request_num_details" style="width: 280px; margin-right: 10px; display: none;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Request Type</label>
                    <input class="form-control form-control-sm" id="request_type_details" style="width: 280px; margin-right: 10px;" type="text" value="ASSEMBLY" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Job Order No.</label>
                    <input class="form-control form-control-sm" id="request_num_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Date Needed</label>
                    <input class="form-control form-control-sm" id="needdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm" style="width: 160px;">Date Created</label>
                    <input class="form-control form-control-sm" id="reqdate_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm rephide" id="qty_label" style="width: 60px;">Qty</label>
                    <input class="form-control form-control-sm rephide numbersOnly" id="qty_details" style="width: 100px;" type="number" readonly>
                </div>
                <div class="form-inline" style="margin-left: 35px; margin-top: 10px;">
                    <label class="form-control form-control-sm" style="width: 160px;">Requested By</label>
                    <input class="form-control form-control-sm" id="requested_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                    <label class="form-control form-control-sm rephide" id="item_desc_label" style="width: 160px;">Assembled Item Name</label>
                    <input class="form-control form-control-sm rephide" id="item_desc_details" style="width: 450px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline cancel_field" style="margin-left: 35px; margin-top: 10px; display: none;">
                    <label class="form-control form-control-sm" style="width: 160px;">Cancelled By</label>
                    <input class="form-control form-control-sm" id="cancelled_by_details" style="width: 280px; margin-right: 10px;" type="text" readonly>
                </div>
                <div class="form-inline cancel_field" style="margin-left: 35px; margin-top: 10px; display: none;">
                    <label class="form-control form-control-sm" style="margin-top: -56px; width: 160px;">Cancellation Reason</label>
                    <textarea class="form-control" id="cancel_reason_details" style="width: 280px; margin-right: 10px; font-size: 12px; resize: none;" rows="4" readonly></textarea>
                </div>
            </div>
            <div id="asmItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 class="modal-title w-100">RECEIVED ASSEMBLED ITEM DETAILS</h6>
                </div>
                <div class="modal-body">
                    <div class="form-inline" style="margin-left: 35px;">
                        <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Received By</label>
                        <input class="form-control form-control-sm" id="recby" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                    </div>
                    <div class="form-inline" style="margin-left: 35px;">
                        <label class="form-control form-control-sm" style="width: 160px;">Date Received</label>
                        <input class="form-control form-control-sm" id="recsched" style="width: 280px;" type="text" readonly>
                    </div>
                    <br>
                    <table id="asmItems" class="table asmItems table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                        <thead>
                            <tr>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT DESCRIPTION</th>
                                <th class="sum">QTY</th>
                                <th>UOM</th>
                                <th>SERIAL NUMBER</th>
                                <th>LOCATION</th>
                            </tr>
                        </thead>
                        <tfoot style="font-size: 14px;">
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                    <br>
                </div>
            </div>
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">NEEDED PARTS DETAILS</h6>
            </div>
            <div class="modal-body">
                <table id="stockDetails" class="table stockDetails table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                    <thead>
                        <tr>
                            <th>PRODUCT CODE</th>
                            <th>PRODUCT DESCRIPTION</th>
                            <th>UOM</th>
                            <th class="sum">REQUESTED</th>
                        </tr>
                    </thead>
                    <tfoot style="font-size: 14px;">
                        <tr>
                            <th colspan="3" style="text-align: right;">TOTAL ITEM COUNT:</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" id="btnDelete" class="btn btn-outline-danger font-weight-bold mt-4" style="display: none;"><i class="fa-solid fa-trash-can fa-lg mr-2"></i>DELETE</button>
            </div>
            <div id="prepItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 id="modalheader" class="modal-title w-100"></h6>
                </div>
                <div class="modal-body">
                    <div id="receive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <b>NOTE:</b> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
                    </div>
                    <div id="defective_label" class="alert alert-warning" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <b>NOTE:</b> Select table rows to confirm <b>defective items</b> then click the <b>DEFECTIVE</b> button below. Otherwise, click the <b>ASSEMBLE</b> button to proceed.
                    </div>
                    <div class="prephide">
                        <div class="form-inline" style="margin-left: 35px;">
                            <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Scheduled By</label>
                            <input class="form-control form-control-sm" id="prep_by1" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                        </div>
                        <div class="form-inline" style="margin-left: 35px;">
                            <label class="form-control form-control-sm" style="width: 160px;">Date Scheduled</label>
                            <input class="form-control form-control-sm" id="sched1" style="width: 280px;" type="text" readonly>
                        </div>
                    </div>
                    <br>
                    <table id="prepItems" class="table prepItems table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                        <thead>
                            <tr>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT DESCRIPTION</th>
                                <th class="sum">QTY</th>
                                <th>UOM</th>
                                <th>SERIAL NUMBER</th>
                                <th>LOCATION</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot style="font-size: 14px;">
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th id="prepItems_total"></th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                    <br>
                    <div class="divPrint" style="display: none;">
                        <hr>
                        <button type="button" class="btnPrint btn btn-primary bp"><i class="fas fa-file-invoice fa-lg mr-2"></i>VIEW DR</button>
                    </div>
                    <div class="prephide">
                        <hr>
                        <button type="button" class="btn btn-primary float-end bp btnReceive" style="display: none;" disabled>RECEIVE</button>
                        <button type="button" id="btnAssemble" class="btn btn-primary float-end bp" style="display: none;">ASSEMBLE</button>
                        <button type="button" id="btnDefective" class="btn btn-primary float-end bp" style="display: none;">DEFECTIVE</button>
                        <button type="button" class="btnPrint btn btn-primary bp"><i class="fas fa-file-invoice fa-lg mr-2"></i>VIEW DR</button>
                        <input type="button" class="btn btn-outline-danger font-weight-bold ml-2" id="btnCancelRequest" style="display: none;" value="CANCEL REQUEST">
                    </div>
                    <div class="pendshow" style="display: none;">
                        <hr>
                        <button type="button" class="btnPrint btn btn-primary bp"><i class="fas fa-file-invoice fa-lg mr-2"></i>VIEW DR</button>
                        <button type="button" id="btnPending" class="btn btn-primary float-end bp">PENDING DETAILS</button>
                    </div>
                </div>
            </div>
            <div id="incItemsModal" style="display: none;">
                <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                    <h6 id="incmodalheader" class="modal-title w-100">INCOMPLETE ITEM DETAILS</h6>
                </div>
                <div class="modal-body">
                    <div id="increceive_label" class="alert alert-primary" role="alert" style="display: none;">
                        <i class='fa fa-exclamation-triangle'></i>
                        <b>NOTE:</b> Please select table rows to confirm <b>received items</b> then click the <b>RECEIVE</b> button below.
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px; margin-bottom: 10px;">Rescheduled By</label>
                        <input class="form-control form-control-sm" id="reprep_by" style="width: 280px; margin-bottom: 10px;" type="text" readonly>
                    </div>
                    <div class="form-inline divResched" style="margin-left: 35px; display: none;">
                        <label class="form-control form-control-sm" style="width: 160px;">Date Rescheduled</label>
                        <input class="form-control form-control-sm" id="resched" style="width: 280px;" type="text" readonly>
                    </div>
                    <br>
                    <table id="incItems" class="table incItems table-hover display" style="cursor: pointer; border: none; font-size: 12px; width: 100%;">
                        <thead>
                            <tr>
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT DESCRIPTION</th>
                                <th class="sum">QTY</th>
                                <th>UOM</th>
                                <th>SERIAL NUMBER</th>
                                <th>LOCATION</th>
                            </tr>
                        </thead>
                        <tfoot style="font-size: 14px;">
                            <tr>
                                <th colspan="2" style="text-align: right;">TOTAL ITEM COUNT:</th>
                                <th id="incItems_total"></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                    <br>
                    <div id="incFooter">
                        <hr>
                        <input type="button" class="btn btn-primary float-end bp btnReceive" style="display: none;" value="RECEIVE" disabled>
                        <div class="mb-3"><br></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>