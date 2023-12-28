<div class="modal fade in" id="importItem">
    <div class="modal-dialog modal-m modal-dialog-centered">
        <div class="modal-content card">
            <div class="modal-header text-center" style="border-radius: 0px; background-color: #0d1a80; color: white; height: 45px;">
                <h6 class="modal-title w-100">ADD ITEMS VIA IMPORT FILE</h6>
                <button type="button" class="btn-close btn-close-white close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background-color: white; color: black;">
                <form id="formUpload" action="/items/import" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row no-margin">
                        <div class="col-md-12 form-group">
                            <input type="file" id="xlsx" name="xlsx" class="form-control required_field" accept=".xls,.xlsx" onchange="validate_xlsx(this);" required/>
                        </div>
                        <span style="color: Red; font-size: 14px;">Please upload an EXCEL (.xls/.xlsx) file with less than 10MB.</span>
                    </div>
                    <br>
                    <button type="button" id="btnDetach" class="btn btn-primary bp">RESET</button>
                    <button type="button" id="btnUpload" class="btn btn-primary bp float-end">UPLOAD</button>
                    <a href="/templates/import_items.xlsx" class="btn btn-primary bp float-end mr-2">DOWNLOAD TEMPLATE</a>
                    <input type="submit" id="btnSubmitImport" class="btn btn-primary bp float-end d-none"/>
                </form>
            </div>
        </div>
    </div>
</div>