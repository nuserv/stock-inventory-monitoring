<div id="disposedModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">Generate Reports</h6>
                <button class="close cancel" aria-label="Close" hidden>
                </button>
            </div>
            <div class="modal-body" id="reqfield">
                <div class="text-center">
                <input type="radio" id="byDate" name="gender" value="Date" checked>
                <label for="byDate">Date range</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" id="byItem" name="gender" value="Item">
                <label for="byItem">By Item</label><br><br></div><hr>
                <div class="text-center" id="datediv">
                    <div class="input-group input-daterange">
                        <input type="text" id="min-date" readonly="readonly" class="form-control date-range-filter" placeholder="From:" autocomplete="off">
                        <div class="input-group-addon">
                            to
                        </div>
                        <input type="text" id="max-date" readonly="readonly" class="form-control date-range-filter" placeholder="To:" autocomplete="off">
                        &nbsp;&nbsp;<input type="button" class="btn btn-primary goBtn" id="dateBtn" value="GENERATE REPORT">
                    </div>
                </div>
                <div class="text-center" id="itemdiv" style="display: none">
                    <div class="input-group input-daterange">
                        <select id="item" class="form-control item" style="color:black">
                            <option selected disabled>select item</option>
                        </select>
                        &nbsp;&nbsp;<input type="button" class="btn btn-primary goBtn" id="itemBtn" value="GENERATE REPORT">
                    </div>
                </div>
                <div class="dateBtn"></div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
            </div>
        </div>
    </div>
</div>