<div id="bufferModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">buffer Details</h6>
                <button class="close cancel" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <button hidden class="closes" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="pending">
                <div class="modal-header">
                    <h6 class="modal-title w-100 text-center" id="head">Pending Requested items</h6>
                </div>
                <div class="modal-body">
                    <table class="table bufferitems" id="bufferitems" style="width:100%">
                        <thead class="thead-dark">
                            <th>Category</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                        </thead>
                    </table>
                    <br>
                </div>
            </div>
            
            <div id="receiving">
                <div class="modal-header">
                    <h6 class="modal-title w-100 text-center">For receiving items</h6>
                </div>
                <div class="modal-body">
                    <table class="table buffersend" id="buffersend" style="width:100%">
                        <thead class="thead-dark">
                            <th>Category</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                            @if (auth()->user()->hasanyrole('Warehouse Manager') || auth()->user()->id == 228 || auth()->user()->id == 110)
                            <th></th>
                            @endif
                        </thead>
                    </table>
                </div>
            </div>

            @if (auth()->user()->hasanyrole('Main Warehouse Manager'))
                <div class="modal-footer">
                    <div class="d-flex"> 
                        <input type="button" id="prcBtn" class="btn btn-primary" value="PROCEED" >
                    </div>
                </div>
            @endif
            @if (auth()->user()->hasanyrole('Warehouse Administrator'))
                <div class="modal-footer">
                    <div class="d-flex"> 
                        <input type="button" id="approvedBtn" class="btn btn-primary" value="APPROVE" >
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>