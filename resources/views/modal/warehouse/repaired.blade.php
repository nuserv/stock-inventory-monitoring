<div id="repairedModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">Repaired Details</h6>
                <button class="close cancel" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table repaireditems" id="repaireditems" style="width:100%">
                    <thead class="thead-dark">
                        <th>Category</th>
                        <th>Item Description</th>
                        <th>Serial</th>
                    </thead>
                </table>
            </div>
            @if(auth()->user()->hasAnyrole('Warehouse Manager', 'Encoder'))
            <div class="modal-footer">
                <input type="button" class="btn btn-primary mr-auto" id="not_rec_Btn" class="button" value="Not received">
                <span id="msg">Please select an item to receive.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="button" class="btn btn-primary" id="rec_Btn" class="button" value="Received" disabled>
            </div>
            @else
                 <div class="modal-footer">
                    <div class="printBtn pt-3" id="printBtn"></div>
                </div>
            @endif
        </div>
    </div>
</div>