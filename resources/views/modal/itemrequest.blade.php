<div id="itemrequestModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">ITEM REQUESTED PER BRANCH</h6>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span style="font-size:90%;color:#00127f;font-family:arial" id="catname"></span>
                <hr>
                <span style="font-size:90%;color:#00127f;font-family:arial" id="itemname"></span>
                <hr>
                <span style="font-size:90%;color:#00127f;font-family:arial;" id="brname"></span>
                <div id="branchitemsdiv">
                    <table class="table-hover table branchitems" id="branchitems" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>Branch</th>
                                <th>Qty</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div id="branchitems2div" style="display:none">
                    <table class="table-hover table branchitems2" id="branchitems2" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>Request Number</th>
                                <th>Qty</th>
                                <th>Date Requested</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>