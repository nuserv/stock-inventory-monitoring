<div id="importModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">IMPORT STOCK FORM</h6>
                <button class="close" data-dismiss="modal" aria-label="Close" hidden>

                </button>
            </div>
            <div class="modal-body" id="import">
                <div class="row no-margin" id="importrow">
                    
                    <form action="warehouse/import" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-11 form-group">
                            <input type="file" name="upload" class="form-control" />
                        </div>
                        <div class="col-md-1 form-group">
                            <input type="submit" class="btn btn-xs btn-primary" value="Upload" name="submit">
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <input type="button" class="btn btn-primary cancel" value="Cancel">
            </div>
        </div>
    </div>
</div>