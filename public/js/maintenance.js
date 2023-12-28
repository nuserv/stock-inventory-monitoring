var tblItem, tblAssembled, tblBundled, tblCategory,
tblLocation, tblSupplier, tblWarranty,
wrdata, tblEditParts_isEdited = 'false';
var current_location = $(location).attr('pathname')+window.location.search;
if(
    current_location == '/maintenance' ||
    current_location == '/maintenance?import=success_without_errors' ||
    current_location == '/maintenance?import=success_with_errors' ||
    current_location == '/maintenance?import=failed'
    ){
    $('#nav1').addClass("active-link");
    $('.btnExport').show();
    $('.btnImport').show();
    $('.btnNewItem').show();
    $('#itemTable').show();
    tblItem = $('table.itemTable').DataTable({
        dom: 'Blftrip',
        buttons:[{
            extend: 'excelHtml5',
            title: 'MWSMS Export - Items',
            exportOptions:{
                modifier:{
                    order: 'index',
                    page: 'all',
                    search: 'none'
                },
            },
        }],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Items",
            lengthMenu: "Show _MENU_ Items",
            emptyTable: "No Items Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_items',
        },
        columns: [
            { data: 'category' },
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'minimum' },
            { data: 'uom' },
            { data: 'serialize' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_items/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblItem.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);

    $('.filter-select').on('change', function(){
        tblItem.column($(this).data('column')).search(!$(this).val()?'':'^'+$(this).val()+'$',true,false,true).draw();
    });

    $('.filter-dropdown').on('change', function(){
        $('#data-column-0').val($(this).val());
        $('#data-column-0').keyup();
    });

    $('.filter-input').on('keyup search', function(){
        tblItem.column($(this).data('column')).search($(this).val()).draw();
    });
}
else if(current_location == '/maintenance?tbl=assembleditems'){
    $('#nav2').addClass("active-link");
    $('.btnExport').show();
    $('.btnNewAssembled').show();
    $('#assembleditemsTable').show();
    tblAssembled = $('table.assembleditemsTable').DataTable({
        dom: 'Blftrip',
        buttons:[{
            extend: 'excelHtml5',
            title: 'MWSMS Export - Assembled Items',
            exportOptions:{
                modifier:{
                    order: 'index',
                    page: 'all',
                    search: 'none'
                },
            },
        }],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Assembled Items",
            lengthMenu: "Show _MENU_ Assembled Items",
            emptyTable: "No Assembled Items Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_assembled',
        },
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'minimum' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });
}
else if(current_location == '/maintenance?tbl=bundleditems'){
    $('#nav3').addClass("active-link");
    $('.btnNewBundled').show();
    $('#bundleditemsTable').show();
    tblBundled = $('table.bundleditemsTable').DataTable({
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Bundled Items",
            lengthMenu: "Show _MENU_ Bundled Items",
            emptyTable: "No Bundled Items Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_bundled',
        },
        columns: [
            { data: 'item' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_bundled/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblBundled.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);
}
else if(current_location == '/maintenance?tbl=categories'){
    $('#nav4').addClass("active-link");
    $('.btnExport').show();
    $('.btnNewCategory').show();
    $('#categoryTable').show();
    tblCategory = $('table.categoryTable').DataTable({
        dom: 'Blftrip',
        buttons:[{
            extend: 'excelHtml5',
            title: 'MWSMS Export - Categories',
            exportOptions:{
                modifier:{
                    order: 'index',
                    page: 'all',
                    search: 'none'
                },
            },
        }],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Categories",
            lengthMenu: "Show _MENU_ Categories",
            emptyTable: "No Categories Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_categories',
        },
        columns: [
            { data: 'category' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_categories/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblCategory.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);
}
else if(current_location == '/maintenance?tbl=locations'){
    $('#nav5').addClass("active-link");
    $('.btnNewLocation').show();
    $('#locationTable').show();
    tblLocation = $('table.locationTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Locations",
            lengthMenu: "Show _MENU_ Locations",
            emptyTable: "No Locations Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_locations',
        },
        columns: [
            { data: 'location' },
            {
                data: 'status',
                "render": function(data, type, row){
                    if(row.status == 'ACTIVE'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Green; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status == 'INACTIVE'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Red; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status == 'PENDING'){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Blue; font-weight: bold;'>"+row.status+"</span>";
                    }
                    if(row.status.includes('CHANGE REQUESTED')){
                        return "<span class='d-none'>"+row.status+"</span><span style='color: Indigo; font-weight: bold;'>"+row.status+"</span>";
                    }
                }
            }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_locations/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblLocation.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);
}
else if(current_location == '/maintenance?tbl=suppliers'){
    $('#nav6').addClass("active-link");
    $('.btnExport').show();
    $('.btnNewSupplier').show();
    $('#supplierTable').show();
    tblSupplier = $('table.supplierTable').DataTable({
        dom: 'Blftrip',
        buttons:[{
            extend: 'excelHtml5',
            title: 'MWSMS Export - Suppliers',
            exportOptions:{
                modifier:{
                    order: 'index',
                    page: 'all',
                    search: 'none'
                },
            },
        }],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Suppliers",
            lengthMenu: "Show _MENU_ Suppliers",
            emptyTable: "No Suppliers Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_suppliers',
        },
        columns: [
            { data: 'supplier_code' },
            { data: 'supplier_name' },
            { data: 'address' },
            { data: 'contact_person' },
            { data: 'contact_number' },
            { data: 'email' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_suppliers/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblSupplier.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);

    $('.filter-input').on('keyup search', function(){
        tblSupplier.column($(this).data('column')).search($(this).val()).draw();
    });
}
else if(current_location == '/maintenance?tbl=warranty'){
    $('#nav7').addClass("active-link");
    $('.btnNewWarranty').show();
    $('#warrantyTable').show();
    tblWarranty = $('table.warrantyTable').DataTable({
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Warranty",
            lengthMenu: "Show _MENU_ Warranty",
            emptyTable: "No Warranty Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/GetWarranty'
        },
        async: false,
        initComplete: function(){
            $('#loading').hide();
        },
        columns: [
            { data: 'Warranty_Name', name:'Warranty_Name'},
            { data: 'Duration',
                render: function(data, type){
                    return data+' Months';
                }
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Phone Support') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Onsite Visit') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Software') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Hardware') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Parts Replacement') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            },
            { data: null,
                render: function(data, type, row) {
                    if (row.Inclusive) {
                        if (row.Inclusive.indexOf('Service Unit') > -1) {
                            return '<center><span class="checkbox_span" style="color:green">✓</span></center>';
                        }
                        else{
                            return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                        }
                    }
                    else{
                        return '<center><span class="checkbox_span" style="color:red">X</span></center>';
                    }
                },
                orderable: false,
                className: "dt-head-center"
            }

        ]
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_warranty/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblWarranty.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);
}
else if(current_location == '/maintenance?tbl=customers'){
    $('#nav8').addClass("active-link");
    $('.btnExport').show();
    $('.btnNewCustomer').show();
    $('#customerTable').show();
    tblCustomer = $('table.customerTable').DataTable({
        dom: 'Blftrip',
        buttons:[{
            extend: 'excelHtml5',
            title: 'MWSMS Export - Customers',
            exportOptions:{
                modifier:{
                    order: 'index',
                    page: 'all',
                    search: 'none'
                },
            },
        }],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Customers",
            lengthMenu: "Show _MENU_ Customers",
            emptyTable: "No Customers Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/fm_customers',
        },
        columns: [
            { data: 'customer_code' },
            { data: 'customer_name' }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/fm_customers/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        tblCustomer.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);
}
else{
    window.location.href = '/maintenance';
}

$('.btnImport').on('click', function(){
    $('#importItem').modal('show');
});

$('#btnDetach').on('click', function(){
    $('#xlsx').val('');
});

function validate_xlsx(xlsx){
    var files_length = $("#xlsx").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    if(files_length > 1){
        Swal.fire('EXCEEDED allowed number of file upload!', 'Please upload only ONE (1) valid EXCEL file.', 'error');
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    for(var i = 0; i < files_length; ++i) {
        var file1=$("#xlsx").get(0).files[i].name;
        var file_size = $("#xlsx").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['xls','xlsx'])===-1){
            error_ext++;
        }
        if(file_size > (5242880 * 2)){
            error_mb++;
        }
    }
    if(error_ext > 0 && error_mb > 0){
        Swal.fire('INVALID file type AND EXCEEDED maximum file size (10MB)!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx; AND with file size not greater than 10MB.', 'error');
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload an EXCEL file with valid file type like the following: xls or xlsx.', 'error');
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum file size (10MB)!', 'Please upload a valid EXCEL file with file size not greater than 10MB.', 'error');
        $('#xlsx').val('');
        $('#xlsx').focus();
        return false;
    }
    return true;
}

$('#btnUpload').on('click', function(){
    if($('#xlsx')[0].files.length === 0){
        $('#btnSubmitImport').click();
    }
    else{
        Swal.fire({
            title: "UPLOAD FILE IMPORT?",
            html: "Click <b style='color: #d33;'>CONFIRM</b> button to ADD ITEMS via uploading import file; otherwise, click <b style='color: #3085d6;'>CANCEL</b> button to select a different file.",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $('#btnSubmitImport').click();
            }
        });
    }
});

$(document).on('click', '.btnNewWarranty', function(){
    $('#WarrantyForm').trigger('reset');
    $('.cb').prop('checked', false );
    $('#txtWarranty').text('ADD NEW WARRANTY');
    $('#btnSubmit').val('SUBMIT');
    $('#AddWarranty').modal('show');
});

$(document).on("click", ".warrantyTable tbody tr", function(){
    if(!tblWarranty.data().any()){ return false; }
    wrdata = tblWarranty.row(this).data();
    $('#WarrantyForm').trigger('reset');
    $('#txtWarranty').text('UPDATE WARRANTY DETAILS');
    $('#btnSubmit').val('UPDATE');
    $('#warranty').val(decodeHtml(wrdata.Warranty_Name));
    $('#duration').val(wrdata.Duration);
    if(wrdata.Inclusive != null){
        $('#software').attr("checked", wrdata.Inclusive.indexOf('Software') > -1);
        $('#onsite').attr("checked", wrdata.Inclusive.indexOf('Onsite Visit') > -1);
        $('#phone').attr("checked", wrdata.Inclusive.indexOf('Phone Support') > -1);
        $('#hardware').attr("checked", wrdata.Inclusive.indexOf('Hardware') > -1);
        $('#replacement').attr("checked", wrdata.Inclusive.indexOf('Parts Replacement') > -1);
        $('#su').attr("checked", wrdata.Inclusive.indexOf('Service Unit') > -1);
    }
    else{
        $('#software').attr("checked", false);
        $('#onsite').attr("checked", false);
        $('#phone').attr("checked", false);
        $('#hardware').attr("checked", false);
        $('#replacement').attr("checked", false);
        $('#su').attr("checked", false);
    }
    $('#AddWarranty').modal('show');
});

$(document).on('click', '#btnSubmit', function(){
    if(!$('#warranty').val() || !$('#duration').val()){
        $('#WarrantyForm')[0].reportValidity();
        return false;
    }
    var inclusive = new Array();
    $('.cb').each(function(){
        if(this.checked)
            inclusive.push($(this).val());
    });
    if($('#btnSubmit').val() == 'SUBMIT'){
        Swal.fire({
            title: "ADD NEW WARRANTY?",
            html: "You are about to ADD this new warranty!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/AddWarranty",
                    type: "POST",
                    dataType: 'json',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        warranty: $.trim($('#warranty').val()).toUpperCase(),
                        duration: $('#duration').val(),
                        inclusive: inclusive
                    },
                    success: function(result){
                        $('#AddWarranty').modal('hide');
                        if(result == true){
                            Swal.fire('SAVE SUCCESS', 'New Warranty has been saved successfully!', 'success');
                            tblWarranty.ajax.reload(null, false);
                        }
                        else{
                            Swal.fire('SAVE FAILED', 'New Warranty save failed!', 'error');
                            tblWarranty.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
    else{
        Swal.fire({
            title: "UPDATE WARRANTY?",
            html: "You are about to UPDATE this warranty!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/UpdateWarranty",
                    type: "PUT",
                    dataType: 'json',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id: wrdata.id,
                        warranty: $.trim($('#warranty').val()).toUpperCase(),
                        duration: $('#duration').val(),
                        inclusive: inclusive
                    },
                    success: function(result){
                        $('#AddWarranty').modal('hide');
                        if(result == true){
                            Swal.fire('UPDATE SUCCESS', 'Warranty details has been updated successfully!', 'success');
                            tblWarranty.ajax.reload(null, false);
                        }
                        else{
                            Swal.fire('UPDATE FAILED', 'Warranty details update failed!', 'error');
                            tblWarranty.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
});

$('.btnNewItem').on('click', function(){
    $('#item_category').val('');
    $('#prodcode').val('');
    $('#item_name').val('');
    $('#specs').val('');
    $('#minimum').val('');
    $('#item_uom').val('');
    $('.divSerial').hide();
    $('#serialize').prop('checked', true);

    $('#newItem').modal('show');
});

$('#item_uom').on('change', function(){
    if($('#item_uom').val() == 'Unit'){
        $('.divSerial').show();
        $('#serialize').prop('checked', true);
    }
    else{
        $('.divSerial').hide();
        $('#serialize').prop('checked', true);
    }
});

setInterval(validation, 0);
function validation(){
    if($('#newItem').is(':visible')){
        !$('#item_category').val() ? $('.xitem_category').css({"display": "inline"}) : $('.xitem_category').css({"display": "none"});
        !$('#item_name').val() ? $('.xitem_name').css({"display": "inline"}) : $('.xitem_name').css({"display": "none"});
        !$('#minimum').val() ? $('.xminimum').css({"display": "inline"}) : $('.xminimum').css({"display": "none"});
        !$('#item_uom').val() ? $('.xitem_uom').css({"display": "inline"}) : $('.xitem_uom').css({"display": "none"});
    }
    if($('#detailsItem').is(':visible')){
        !$('#item_category_details').val() ? $('.xitem_category_details').css({"display": "inline"}) : $('.xitem_category_details').css({"display": "none"});
        !$('#item_name_details').val() ? $('.xitem_name_details').css({"display": "inline"}) : $('.xitem_name_details').css({"display": "none"});
        !$('#minimum_details').val() ? $('.xminimum_details').css({"display": "inline"}) : $('.xminimum_details').css({"display": "none"});
        !$('#item_uom_details').val() ? $('.xitem_uom_details').css({"display": "inline"}) : $('.xitem_uom_details').css({"display": "none"});
    }
}

$('#btnSaveItem').on('click', function(){
    var category_name = $('#item_category').find('option:selected').text();
    var item_category = $('#item_category').val();
    var item_name = $.trim($('#item_name').val());
    var prodcode = $.trim($('#prodcode').val()).toUpperCase();
    var specs = ($.trim($('#specs').val()).split("\n")).join(', ');
    var minimum = $('#minimum').val();
    var item_uom = $('#item_uom').val();
    if($('#serialize').is(":checked")){
        var serialize = 'YES';
    }
    else{
        var serialize = 'NO';
    }
    if(item_uom != 'Unit'){
        serialize = 'NO';
    }
    if(!prodcode){
        prodcode = '[BLANK]';
    }
    if(!specs){
        specs = 'N/A';
    }
    if(!item_category || !item_name || !minimum || !item_uom ){
        Swal.fire('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    Swal.fire({
        title: "ADD NEW ITEM?",
        html: "You are about to ADD this new item!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                url: "/saveItem",
                type: "POST",
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    category_name: category_name,
                    item_category: item_category,
                    item_name: item_name,
                    prodcode: prodcode,
                    specs: specs,
                    minimum: minimum,
                    item_uom: item_uom,
                    serialize: serialize
                },
                success: function(data){
                    if(data.result == 'true'){
                        $('#newItem').modal('hide');
                        Swal.fire("SAVE SUCCESS", "New Item has been saved successfully!", "success");
                        tblItem.ajax.reload(null, false);
                    }
                    else if(data.result == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Product Description already exists!", "error");
                        return false;
                    }
                    else if(data.result == 'duplicatecode'){
                        Swal.fire("DUPLICATE PRODUCT CODE", "Product Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#newItem').modal('hide');
                        Swal.fire("SAVE FAILED", "New Item save failed!", "error");
                        tblItem.ajax.reload(null, false);
                    }
                }
            });
        }
    });
});

$('#itemTable tbody').on('click', 'tr', function(){
    var table = $('table.itemTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    var item_id = data.id;
        $('#item_id').val(item_id);
    var category_name = decodeHtml(data.category);
        $('#category_name_details_original').val(category_name);
    var item_category = data.category_id;
        $('#item_category_details').val(item_category);
        $('#item_category_details_original').val(item_category);
    var item_name = decodeHtml(data.item);
        $('#item_name_details').val(item_name);
        $('#item_name_details_original').val(item_name);
    var prodcode = data.prodcode;
        $('#prodcode_details').val(prodcode);
        $('#prodcode_details_original').val(prodcode);
    var specs =  data.specs.replaceAll(', ', '\n');
        $('#specs_details').val(specs);
        $('#specs_details_original').val(specs);
    var minimum = data.minimum;
        $('#minimum_details').val(minimum);
        $('#minimum_details_original').val(minimum);
    var item_uom = data.uom;
        $('#item_uom_details').val(item_uom);
        $('#item_uom_details_original').val(item_uom);
    var serialize = data.serialize;
        $('#serialize_details_original').val(serialize);
        if(serialize == 'YES'){
            $('#serialize_details').prop('checked', true);
        }
        else{
            $('#serialize_details').prop('checked', false);
        }
        if(item_uom == 'Unit'){
            $('.divSerial').show();
        }
        else{
            $('.divSerial').hide();
            $('#serialize_details').prop('checked', true);
        }
    $('#barcode').val(prodcode);
    show_barcode(prodcode, '#show_barcode');
    $('#detailsItem').modal('show');
});

$('#item_uom_details').on('change', function(){
    if($('#item_uom_details').val() == 'Unit'){
        $('.divSerial').show();
        $('#serialize_details').prop('checked', true);
    }
    else{
        $('.divSerial').hide();
        $('#serialize_details').prop('checked', true);
    }
});

$('#btnUpdateItem').on('click', function(){
    var item_id = $('#item_id').val();
    var category_name_original = $('#category_name_details_original').val();
    var item_category_original = $('#item_category_details_original').val();
    var item_name_original = $('#item_name_details_original').val();
    var prodcode_original = $('#prodcode_details_original').val();
    var specs_original = $.trim($('#specs_details_original').val());
    var minimum_original = $('#minimum_details_original').val();
    var item_uom_original = $('#item_uom_details_original').val();
    var serialize_original = $('#serialize_details_original').val();
    var category_name = decodeHtml($('#item_category_details').find('option:selected').text());
    var item_category = $('#item_category_details').val();
    var item_name = $.trim($('#item_name_details').val());
    var prodcode = $.trim($('#prodcode_details').val()).toUpperCase();
    var specs = ($.trim($('#specs_details').val()).split("\n")).join(', ');
    var minimum = $('#minimum_details').val();
    var item_uom = $('#item_uom_details').val();
    if($('#serialize_details').is(":checked")){
        var serialize = 'YES';
    }
    else{
        var serialize = 'NO';
    }
    if(item_uom != 'Unit'){
        serialize = 'NO';
    }
    if(!prodcode){
        prodcode = '[BLANK]';
    }
    if(!specs){
        specs = 'N/A';
    }
    if(!item_category || !item_name || !minimum || !item_uom ){
        Swal.fire('REQUIRED','Please fill up all required fields!','error');
        return false;
    }
    if(item_name_original.toUpperCase() == item_name.toUpperCase() && prodcode_original == prodcode && specs_original && specs && item_category_original == item_category && minimum_original == minimum && item_uom_original == item_uom && serialize_original == serialize){
        Swal.fire("NO CHANGES FOUND", "Item Details are still all the same!", "error");
        return false;
    }
    Swal.fire({
        title: "UPDATE ITEM?",
        html: "You are about to UPDATE this item!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $.ajax({
                url: "/updateItem",
                type: "PUT",
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    item_id: item_id,
                    category_name_original: category_name_original,
                    item_category_original: item_category_original,
                    item_name_original: item_name_original,
                    prodcode_original: prodcode_original,
                    specs_original: specs_original,
                    minimum_original: minimum_original,
                    item_uom_original: item_uom_original,
                    serialize_original: serialize_original,
                    category_name: category_name,
                    item_category: item_category,
                    item_name: item_name,
                    prodcode: prodcode,
                    specs: specs,
                    minimum: minimum,
                    item_uom: item_uom,
                    serialize: serialize
                },
                success: function(data){
                    if(data.result == 'true'){
                        $('#detailsItem').modal('hide');
                        Swal.fire("UPDATE SUCCESS", "Item details has been updated successfully!", "success");
                        tblItem.ajax.reload(null, false);
                    }
                    else if(data.result == 'duplicate'){
                        Swal.fire("DUPLICATE ITEM", "Product Description already exists!", "error");
                        return false;
                    }
                    else if(data.result == 'duplicatecode'){
                        Swal.fire("DUPLICATE PRODUCT CODE", "Product Code already exists!", "error");
                        return false;
                    }
                    else{
                        $('#detailsItem').modal('hide');
                        Swal.fire("UPDATE FAILED", "Item details update failed!", "error");
                        tblItem.ajax.reload(null, false);
                    }
                }
            });
        }
    });
});

$('.btnNewCategory').on('click', function(){
    $('#category').val('');

    $('#newCategory').modal('show');
});

$('#btnSaveCategory').on('click', function(){
    var category = $.trim($('#category').val()).toUpperCase();
    if(category != ""){
        Swal.fire({
            title: "ADD NEW CATEGORY?",
            html: "You are about to ADD this new category!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/saveCategory",
                    type: "POST",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        category: category
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newCategory').modal('hide');
                            Swal.fire("SAVE SUCCESS", "New Category has been saved successfully!", "success");
                            tblCategory.ajax.reload(null, false);
                            $.ajax({
                                url: "/logNewCategory",
                                type: "POST",
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    category: data.category
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        return true;
                                    }
                                    else{
                                        return false;
                                    }
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newCategory').modal('hide');
                            Swal.fire("SAVE FAILED", "New Category save failed!", "error");
                            tblCategory.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
    else{
        Swal.fire('REQUIRED','Category Name field is required!','error');
        return false;
    }
});

$('#categoryTable tbody').on('click', 'tr', function(){
    var table = $('table.categoryTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    var category_id = data.id;
        $('#category_id').val(category_id);
    var category = decodeHtml(data.category);
        $('#category_details').val(category);
        $('#category_original').val(category);
    if(category_id == '0'){
        $('#category_details').prop('disabled', true);
        $('#btnUpdateCategory').hide();
    }
    else{
        $('#category_details').prop('disabled', false);
        $('#btnUpdateCategory').show();
    }

    $('#detailsCategory').modal('show');
});

$('#btnUpdateCategory').on('click', function(){
    var category_id = $('#category_id').val();
    var category_original = $('#category_original').val();
    var category_details = $.trim($('#category_details').val().toUpperCase());

    if(category_details == ""){
        Swal.fire('REQUIRED','Category Name field is required!','error');
        return false;
    }
    else if(category_original == category_details){
        Swal.fire("NO CHANGES FOUND", "Category Name is still the same!", "error");
        return false;
    }
    else{
        Swal.fire({
            title: "UPDATE CATEGORY?",
            html: "You are about to UPDATE this category!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/updateCategory",
                    type: "PUT",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        category_id: category_id,
                        category_original: category_original,
                        category_details: category_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsCategory').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Category Name has been updated successfully!", "success");
                            tblCategory.ajax.reload(null, false);
                            $.ajax({
                                url: "/logUpdateCategory",
                                type: "POST",
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    category_id: data.category_id,
                                    category_original: data.category_original,
                                    category_details: data.category_details
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        return true;
                                    }
                                    else{
                                        return false;
                                    }
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE CATEGORY", "Category Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsCategory').modal('hide');
                            Swal.fire("UPDATE FAILED", "Category Name update failed!", "error");
                            tblCategory.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
});

$(".btnNewLocation").on('click', function(){
    $('#location').val('');

    $('#newLocation').modal('show');
});

$('#btnSaveLocation').on('click', function(){
    var location_name = $.trim($('#location').val()).toUpperCase();
    if(location_name != ""){
        Swal.fire({
            title: "REQUEST NEW LOCATION?",
            html: "You are about to REQUEST a new location!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/saveLocation",
                    type: "POST",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        location: location_name
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newLocation').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                url: "/logNewLocation",
                                type: "POST",
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    location: data.location
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("REQUEST SUCCESS", "New Location has been requested successfully!", "success");
                                        tblLocation.ajax.reload(null, false);
                                    }
                                    else{
                                        return false;
                                    }
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newLocation').modal('hide');
                            Swal.fire("REQUEST FAILED", "New Location request failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
    else{
        Swal.fire('REQUIRED','Location Name field is required!','error');
        return false;
    }
});

$('#locationTable tbody').on('click', 'tr', function(){
    var table = $('table.locationTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    if(data.status.includes('CHANGE REQUESTED') || data.status.includes('PENDING')){
        return false;
    }
    else{
        var location_id = data.location_id;
            $('#location_id').val(location_id);
        var location_name = decodeHtml(data.location);
            $('#location_details').val(location_name);
            $('#location_original').val(location_name);
        var status = data.status;
            $('#status_original').val(status);
            if(status == 'ACTIVE'){
                $('#status').prop('checked', true);
            }
            else{
                $('#status').prop('checked', false);
            }

        $('#detailsLocation').modal('show');
    }
});

$('#btnUpdateLocation').on('click', function(){
    if($('#status').is(":checked")){
        var status = 'ACTIVE';
    }
    else{
        var status = 'INACTIVE';
    }
    var location_id = $('#location_id').val();
    var location_original = $('#location_original').val();
    var location_details = $.trim($('#location_details').val().toUpperCase());
    var status_original = $('#status_original').val();

    if(location_details == ""){
        Swal.fire('REQUIRED','Location Name field is required!','error');
        return false;
    }
    if(location_original == location_details && status_original == status){
        Swal.fire("NO CHANGES FOUND", "Location Details are all still the same!", "error");
        return false;
    }
    if(location_original != location_details && status_original != status){
        Swal.fire("UPDATE FAILED", "STATUS CHANGE REQUEST is NOT allowed if the current Location Name has been changed!", "error");
        return false;
    }
    if(location_original == location_details && status != status_original){
        Swal.fire({
            title: "REQUEST STATUS CHANGE?",
            html: "You are about to request a STATUS CHANGE to this location!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/updateLocation",
                    type: "PUT",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        location_id: location_id,
                        location_details: location_details,
                        status_original: status_original,
                        status: status
                    },
                    success: function(data){
                        if(data.result == 'request'){
                            $('#detailsLocation').modal('hide');
                            $('#loading').show();
                            $.ajax({
                                url: "/requestStatusChange",
                                type: "POST",
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    id: data.id,
                                    location: data.location,
                                    status_original: data.status_original,
                                    status: data.status
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("REQUEST SUCCESS", "Location Status Change has been requested successfully!", "success");
                                        tblLocation.ajax.reload(null, false);
                                    }
                                    else{
                                        return false;
                                    }
                                }
                            });
                        }
                        else{
                            $('#detailsLocation').modal('hide');
                            Swal.fire("REQUEST FAILED", "Location Status Change request failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    }
                });
            }
        });

    }
    else{
        Swal.fire({
            title: "UPDATE LOCATION NAME?",
            html: "You are about to UPDATE this location!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/updateLocation",
                    type: "PUT",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        location_id: location_id,
                        location_original: location_original,
                        location_details: location_details
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsLocation').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Location Name has been updated successfully!", "success");
                            tblLocation.ajax.reload(null, false);
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE LOCATION", "Location Name already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsLocation').modal('hide');
                            Swal.fire("UPDATE FAILED", "Location Name update failed!", "error");
                            tblLocation.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    }
});

$('.btnNewSupplier').on('click', function(){
    $('#btnClearSupplier').click();

    $('#newSupplier').modal('show');
});

setInterval(checkRequired, 0);
function checkRequired(){
    if($('#newCategory').is(':visible')){
        if(!$('#category').val()){
            $('#btnSaveCategory').prop('disabled', true);
        }
        else{
            $('#btnSaveCategory').prop('disabled', false);
        }
    }
    if($('#detailsCategory').is(':visible')){
        if(!$('#category_details').val()){
            $('#btnUpdateCategory').prop('disabled', true);
        }
        else{
            $('#btnUpdateCategory').prop('disabled', false);
        }
    }
    if($('#newLocation').is(':visible')){
        if(!$('#location').val()){
            $('#btnSaveLocation').prop('disabled', true);
        }
        else{
            $('#btnSaveLocation').prop('disabled', false);
        }
    }
    if($('#detailsLocation').is(':visible')){
        if(!$('#location_details').val()){
            $('#btnUpdateLocation').prop('disabled', true);
        }
        else{
            $('#btnUpdateLocation').prop('disabled', false);
        }
    }
    if($('#newSupplier').is(':visible')){
        var checkA = true;
        var checkB = true;
        if($('.requiredField').filter(function(){ return !!$.trim(this.value); }).length != 6){
            $('#alertNewSupplier').show();
            checkA = false;
        }
        else{
            $('#alertNewSupplier').hide();
            checkA = true;
        }
        if($('#email').val() && !validateEmail($('#email').val())){
            $('#emailNewSupplier').show();
            checkB = false;
        }
        else{
            $('#emailNewSupplier').hide();
            checkB = true;
        }
        if(checkA && checkB){
            $('#btnSaveSupplier').prop('disabled', false);
        }
        else{
            $('#btnSaveSupplier').prop('disabled', true);
        }
        if($('#alertNewSupplier').is(':visible') || $('#emailNewSupplier').is(':visible')){
            $('#marginNewSupplier').show();
        }
        else{
            $('#marginNewSupplier').hide();
        }
    }
    if($('#detailsSupplier').is(':visible')){
        var check1 = true;
        var check2 = true;
        var check3 = true;
        if($('.requiredFields').filter(function(){ return !!$.trim(this.value); }).length != 6){
            $('#alertUpdateSupplier').show();
            check1 = false;
        }
        else{
            $('#alertUpdateSupplier').hide();
            check1 = true;
        }
        if(
            $('#supplier_code_new').val() == $('#supplier_code').val() &&
            $('#supplier_name_new').val() == $('#supplier_name').val() &&
            $('#address_new').val() == $('#address').val() &&
            $('#contact_person_new').val() == $('#contact_person').val() &&
            $('#contact_number_new').val() == $('#contact_number').val() &&
            $('#email_new').val() == $('#email').val()
        ){
            check2 = false;
        }
        else{
            check2 = true;
        }
        if($('#email_new').val() && !validateEmail($('#email_new').val())){
            $('#emailUpdateSupplier').show();
            check3 = false;
        }
        else{
            $('#emailUpdateSupplier').hide();
            check3 = true;
        }
        if(check1 && check2 & check3){
            $('#btnUpdateSupplier').prop('disabled', false);
        }
        else{
            $('#btnUpdateSupplier').prop('disabled', true);
        }
        if($('#alertUpdateSupplier').is(':visible') || $('#emailUpdateSupplier').is(':visible')){
            $('#marginUpdateSupplier').show();
        }
        else{
            $('#marginUpdateSupplier').hide();
        }
    }
}

$('#btnSaveSupplier').on('click', function(){
    var warntext = '';
    var checkemail = 'true';
    var supplier_code = $.trim($('#supplier_code').val()).toUpperCase();
    var supplier_name = $.trim($('#supplier_name').val());
    var address = $.trim($('#address').val());
    var contact_person = $.trim($('#contact_person').val());
    var contact_number = $.trim($('#contact_number').val());
    var email = $.trim($('#email').val());
    // $('#loading').show();
    // setTimeout(function(){
        // if(emailProvider(email)){
        //     $.ajax({
        //         headers:{
        //             Authorization: "Bearer " + current_key
        //         },
        //         async: false,
        //         type: 'GET',
        //         url: 'https://isitarealemail.com/api/email/validate?email='+email,
        //         success: function(data){
        //             if(data.status == 'invalid'){
        //                 checkemail = 'false';
        //             }
        //             else{
        //                 checkemail = 'true';
        //             }
        //         }
        //     });
        // }
        // else{
        //     checkemail = 'unknown';
        // }
        // $('#loading').hide();
        if(checkemail == 'false'){
            Swal.fire('NON-EXISTENT EMAIL','Email Address does not exist!','error');
            return false;
        }
        if(checkemail == 'unknown'){
            warntext = ' <br><b style="color: red;">WARNING: Email Address cannot be verified by the system! CONTINUE?</b>';
        }
        Swal.fire({
            title: "ADD NEW SUPPLIER?",
            html: "You are about to ADD this new supplier!"+warntext,
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/saveSupplier",
                    type: "POST",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        supplier_code: supplier_code,
                        supplier_name: supplier_name,
                        address: address,
                        contact_person: contact_person,
                        contact_number: contact_number,
                        email: email
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#newSupplier').modal('hide');
                            Swal.fire("SAVE SUCCESS", "New Supplier has been saved successfully!", "success");
                            tblSupplier.ajax.reload(null, false);
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE SUPPLIER", "Supplier Name already exists!", "error");
                            return false;
                        }
                        else if(data.result == 'duplicatecode'){
                            Swal.fire("DUPLICATE SUPPLIER CODE", "Supplier Code already exists!", "error");
                            return false;
                        }
                        else{
                            $('#newSupplier').modal('hide');
                            Swal.fire("SAVE FAILED", "New Supplier save failed!", "error");
                            tblSupplier.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    // }, 1000);
});

$('#supplierTable tbody').on('click', 'tr', function(){
    var table = $('table.supplierTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    $('#supplier_code').val(data.supplier_code);
    $('#supplier_name').val(decodeHtml(data.supplier_name));
    $('#address').val(decodeHtml(data.address));
    $('#contact_person').val(decodeHtml(data.contact_person));
    $('#contact_number').val(decodeHtml(data.contact_number));
    $('#email').val(data.email);

    $('#supplier_id').val(data.id);
    $('#supplier_code_new').val(data.supplier_code);
    $('#supplier_name_new').val(decodeHtml(data.supplier_name));
    $('#address_new').val(decodeHtml(data.address));
    $('#contact_person_new').val(decodeHtml(data.contact_person));
    $('#contact_number_new').val(decodeHtml(data.contact_number));
    $('#email_new').val(data.email);

    $('#detailsSupplier').modal('show');
});

$('#btnResetSupplier').on('click', function(){
    $('#supplier_code_new').val($('#supplier_code').val());
    $('#supplier_name_new').val($('#supplier_name').val());
    $('#address_new').val($('#address').val());
    $('#contact_person_new').val($('#contact_person').val());
    $('#contact_number_new').val($('#contact_number').val());
    $('#email_new').val($('#email').val());
});

$('#btnUpdateSupplier').on('click', function(){
    var warntext = '';
    var checkemail = 'true';
    var supplier_code = $.trim($('#supplier_code_new').val()).toUpperCase();
    var supplier_name = $.trim($('#supplier_name_new').val());
    var address = $.trim($('#address_new').val());
    var contact_person = $.trim($('#contact_person_new').val());
    var contact_number = $.trim($('#contact_number_new').val());
    var email = $.trim($('#email_new').val());
    var supplier_code_orig = $('#supplier_code').val();
    var supplier_name_orig = $('#supplier_name').val();
    var address_orig = $('#address').val();
    var contact_person_orig = $('#contact_person').val();
    var contact_number_orig = $('#contact_number').val();
    var email_orig = $('#email').val();
    // $('#loading').show();
    // setTimeout(function(){
        // if(emailProvider(email)){
        //     $.ajax({
        //         headers:{
        //             Authorization: "Bearer " + current_key
        //         },
        //         async: false,
        //         type: 'GET',
        //         url: 'https://isitarealemail.com/api/email/validate?email='+email,
        //         success: function(data){
        //             if(data.status == 'invalid'){
        //                 checkemail = 'false';
        //             }
        //             else{
        //                 checkemail = 'true';
        //             }
        //         }
        //     });
        // }
        // else{
        //     checkemail = 'unknown';
        // }
        // $('#loading').hide();
        if(checkemail == 'false'){
            Swal.fire('NON-EXISTENT EMAIL','Email Address does not exist!','error');
            return false;
        }
        if(checkemail == 'unknown'){
            warntext = ' <br><b style="color: red;">WARNING: Email Address cannot be verified by the system! CONTINUE?</b>';
        }
        Swal.fire({
            title: "UPDATE SUPPLIER?",
            html: "You are about to UPDATE this supplier!"+warntext,
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "/updateSupplier",
                    type: "POST",
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id: $('#supplier_id').val(),
                        supplier_code: supplier_code,
                        supplier_name: supplier_name,
                        address: address,
                        contact_person: contact_person,
                        contact_number: contact_number,
                        email: email,
                        supplier_code_orig: supplier_code_orig,
                        supplier_name_orig: supplier_name_orig,
                        address_orig: address_orig,
                        contact_person_orig: contact_person_orig,
                        contact_number_orig: contact_number_orig,
                        email_orig: email_orig
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('#detailsSupplier').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Supplier details has been updated successfully!", "success");
                            tblSupplier.ajax.reload(null, false);
                        }
                        else if(data.result == 'duplicate'){
                            Swal.fire("DUPLICATE SUPPLIER", "Supplier Name already exists!", "error");
                            return false;
                        }
                        else if(data.result == 'duplicatecode'){
                            Swal.fire("DUPLICATE SUPPLIER CODE", "Supplier Code already exists!", "error");
                            return false;
                        }
                        else{
                            $('#detailsSupplier').modal('hide');
                            Swal.fire("UPDATE FAILED", "Supplier update failed!", "error");
                            tblSupplier.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    // }, 1000);
});

$(document).on('change', '#aic_item_description', function () {
    console.log('test');
    $('#partsDetails').show();
});

// setInterval(checkCreateItem, 0);
// function checkCreateItem(){
//     if($('#newAssembledItem').is(':visible')){
//         var item_description = $.trim($('#aic_item_description').val());
//         var item_code = $('#aic_item_code').val();
//         var minimum = $('#aic_minimum').val();
//         if(item_description && minimum && item_code){
//             $('.create_label').hide();
//             $('#partsDetails').show();
//         }
//         else{
//             $('.create_label').show();
//             $('#partsDetails').hide();
//         }
//     }
// }

$(".btnNewAssembled").on('click', function(){
    // $("#aic_item_description").find('option').remove().end().append('<option value="" selected disabled>Select Product Description</option>').val('');
    $('#tblSaveParts tbody').empty();
    if($('#tblSaveParts tbody').children().length == 0){
        $('#tblSaveParts').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
    }
    $('#newAssembledItem').modal('show');
});

$('#categoryAssembly').on('change', function(){
    var id = $('#categoryAssembly').val();
    var descOp = " ";
    if(!$('#categoryAssembly').val()){
        $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>');
        $("#itemAssembly").chosen();
        $("#itemAssembly").trigger("chosen:updated");
        $("#itemAssembly_chosen").css({"margin-left": "-1px"});
        $('label[for="itemAssembly"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
    }
    else{
        $.ajax({
            type: 'GET',
            url: '/itemsAssembly',
            data:{'category_id':id},
            success: function(data)
            {
                var itemcode = $.map(data, function(value, index){
                    return [value];
                });
                descOp+='<option value="" selected disabled>Select Item</option>';
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
                });
                $("#itemAssembly").find('option').remove().end().append(descOp);
                $("#itemAssembly").chosen();
                $("#itemAssembly").trigger("chosen:updated");
                $("#itemAssembly_chosen").css({"margin-left": "-1px"});
                $('label[for="itemAssembly"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
            }
        });
    }
});

$('#itemAssembly').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type: 'GET',
        url: '/uomAssembly',
        data:{
            'item_id': item_id,
        },
        success: function(data){
            $('#uomAssembly').val(data[0].uom);
        }
    });
});

$(".add-row").on('click', function(){
    var category = $("#categoryAssembly option:selected").text();
    var item = $("#itemAssembly option:selected").text();
    let item_id = $("#itemAssembly").val();
    var uom = $("#uomAssembly").val();
    let qty = $("#qtyAssembly").val();
    var markup = "<tr class='tblSaveParts_data'><td class='d-none td_1'>" + item_id + "</td><td><i class='fa-solid fa-grip fa-lg mr-2'></i>" + item + "</td><td class='td_2'>" + qty + "</td><td>" + uom + "</td><td><button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'><i class='fa-solid fa-trash-can fa-lg mr-2'></i>REMOVE</button></td><td class='d-none td_3'></td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        Swal.fire('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblSaveParts');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item_id == objCells.item(0).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr = 'true';
                category = $("#categoryAssembly").val('');
                item = $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
                uom = $('#uomAssembly').val('');
                qty = $("#qtyAssembly").val('');
                setTimeout(function(){$('#categoryAssembly').change()}, current_timeout);
                $("#categoryAssembly").focus();
                return false;
            }
            else{
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#tblSaveParts tbody").append(markup); }
        category = $("#categoryAssembly").val('');
        item = $("#itemAssembly").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
        uom = $('#uomAssembly').val('');
        qty = $("#qtyAssembly").val('');
        setTimeout(function(){$('#categoryAssembly').change()}, 200);
        $('#tblSaveParts').show();
        $('#divCreateItem').toggle();
        $('#btnClose').show();
        $('#btnSave').show();
        $("#categoryAssembly").focus();
    }
});

$("#tblSaveParts").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if($('#tblSaveParts tbody').children().length == 0){
        $('#tblSaveParts').hide();
        $('#divCreateItem').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
    }
});

$('#categoryAssemblyDetails').on('change', function(){
    var id = $('#categoryAssemblyDetails').val();
    var descOp = " ";
    if(!$('#categoryAssemblyDetails').val()){
        $("#itemAssemblyDetails").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>');
        $("#itemAssemblyDetails").chosen();
        $("#itemAssemblyDetails").trigger("chosen:updated");
        $("#itemAssemblyDetails_chosen").css({"margin-left": "-1px"});
        $('label[for="itemAssemblyDetails"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
    }
    else{
        $.ajax({
            type: 'GET',
            url: '/itemsAssembly',
            data:{'category_id':id},
            success: function(data)
            {
                var itemcode = $.map(data, function(value, index){
                    return [value];
                });
                descOp+='<option value="" selected disabled>Select Item</option>';
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
                });
                $("#itemAssemblyDetails").find('option').remove().end().append(descOp);
                $("#itemAssemblyDetails").chosen();
                $("#itemAssemblyDetails").trigger("chosen:updated");
                $("#itemAssemblyDetails_chosen").css({"margin-left": "-1px"});
                $('label[for="itemAssemblyDetails"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
            }
        });
    }
});

$('#itemAssemblyDetails').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type: 'GET',
        url: '/uomAssembly',
        data:{
            'item_id': item_id,
        },
        success: function(data){
            $('#uomAssemblyDetails').val(data[0].uom);
        }
    });
});

$(".add-row-details").on('click', function(){
    var category = $("#categoryAssemblyDetails option:selected").text();
    var item = $("#itemAssemblyDetails option:selected").text();
    let item_id = $("#itemAssemblyDetails").val();
    var uom = $("#uomAssemblyDetails").val();
    let qty = $("#qtyAssemblyDetails").val();
    var markup = `<tr class='tblEditParts_data'><td class='d-none td_1'>${item_id}</td><td><i class='fa-solid fa-grip fa-lg mr-2'></i>${item}</td><td class='td_2'>${qty}</td><td>${uom}</td><td><button style="zoom: 80%;" class="btn btn-danger bp delete-row-details"><i class="fa-solid fa-trash-can fa-lg mr-2"></i>REMOVE</button></td><td class='d-none td_3'></td></tr>`;
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        Swal.fire('REQUIRED','Please select an item!','error');
        return false;
    }
    else{
        var table = document.getElementById('tblEditParts');
        var count = table.rows.length;
        for(i = 1; i < count; i++){
            var objCells = table.rows.item(i).cells;
            if(item_id == objCells.item(0).innerHTML){
                objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
                ctr = 'true';
                category = $("#categoryAssemblyDetails").val('');
                item = $("#itemAssemblyDetails").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
                uom = $('#uomAssemblyDetails').val('');
                qty = $("#qtyAssemblyDetails").val('');
                setTimeout(function(){$('#categoryAssemblyDetails').change()}, current_timeout);
                $("#categoryAssemblyDetails").focus();
                tblEditParts_isEdited = 'true';
                return false;
            }
            else{
                ctr = 'false';
            }
        }
        if(ctr == 'false')
        { $("#tblEditParts tbody").append(markup); }
        category = $("#categoryAssemblyDetails").val('');
        item = $("#itemAssemblyDetails").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
        uom = $('#uomAssemblyDetails').val('');
        qty = $("#qtyAssemblyDetails").val('');
        setTimeout(function(){$('#categoryAssemblyDetails').change()}, current_timeout);
        $('#tblEditParts').show();
        $("#categoryAssemblyDetails").focus();
        tblEditParts_isEdited = 'true';
    }
});

$("#tblEditParts").on('click', '.delete-row-details', function(){
    tblEditParts_isEdited = 'true';
    $(this).closest("tr").remove();
    if($('#tblEditParts tbody').children().length == 0){
        $('#tblEditParts').hide();
    }
});

$('#btnSave').on('click', function(){
    var item_description = $.trim($('#aic_item_description option:selected').text());
    var item_code = $('#aic_item_description').val().toUpperCase();
    Swal.fire({
        title: "CREATE NEW ASSEMBLED ITEM?",
        html: "You are about to CREATE a new Assembled Item!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#loading').show();
            setTimeout(() => {
                var table = document.getElementById('tblSaveParts');
                var count = table.rows.length;
                for(i = 1; i < count; i++){
                    var objCells = table.rows.item(i).cells;
                    objCells.item(5).innerHTML = i;
                }
                $.ajax({
                    type: 'POST',
                    url: '/saveAssemblyItem',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        item: item_description,
                        prodcode: item_code
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('.tblSaveParts_data').each(function(){
                                $.ajax({
                                    type: 'POST',
                                    url: '/saveParts',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        item_id: data.id,
                                        part_id: $(this).children('.td_1').html(),
                                        quantity: $(this).children('.td_2').html(),
                                        priority: $(this).children('.td_3').html()
                                    }
                                });
                            });
                            $.ajax({
                                type: 'POST',
                                url: '/logItem',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    item_id: data.id,
                                    item: item_description
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        $('#newAssembledItem').modal('hide');
                                        Swal.fire("CREATE SUCCESS", "New Assembled Item has been created successfully!", "success");
                                        tblAssembled.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#loading').hide();
                                        return false;
                                    }
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE ITEM", "Product Description already exists!", "error");
                            return false;
                        }
                        else if(data.result == 'dupecode'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE CODE", "Product Code already exists!", "error");
                            return false;
                        }
                        else{
                            $('#loading').hide();
                            $('#newAssembledItem').modal('hide');
                            Swal.fire("CREATE FAILED", "New Assembled Item create failed!", "error");
                            tblAssembled.ajax.reload(null, false);
                        }
                    }
                });
            }, 200);
        }
    });
});

setInterval(() => {
    if($('#detailsAssembledItem').is(':visible')){
        var item_current = $.trim($('#aim_item_name_details').val());
        var item_original = $('#aim_item_name_details_original').val();
        var code_current = $('#aim_item_code_details').val().toUpperCase();
        var code_original = $('#aim_item_code_details_original').val();
        var minimum_current = $('#aim_minimum_details').val();
        var minimum_original = $('#aim_minimum_details_original').val();
        if((!code_current || !item_current || !minimum_current) || (item_current.toUpperCase() == item_original.toUpperCase() && code_current == code_original && minimum_current == minimum_original && tblEditParts_isEdited == 'false') || $('#tblEditParts tbody').children().length == 0){
            $('#btnUpdate').prop('disabled', true);
        }
        else{
            $('#btnUpdate').prop('disabled', false);
        }
    }
}, 0);

$('#assembleditemsTable tbody').on('click', 'tr', function(){
    var table = $('table.assembleditemsTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    var item_id = data.id;
    $('#aim_item_id').val(item_id);
    var item_name = decodeHtml(data.item);
    $('#aim_item_name_details').val(item_name);
    $('#aim_item_name_details_original').val(item_name);
    var prodcode = data.prodcode;
    $('#aim_item_code_details').val(prodcode);
    $('#aim_item_code_details_original').val(prodcode);
    var minimum = data.minimum;
    $('#aim_minimum_details').val(minimum);
    $('#aim_minimum_details_original').val(minimum);
    show_barcode(prodcode, '#show_barcode');

    tblEditParts_isEdited = 'false';
    $("#tbodyEditParts").empty();
    $.ajax({
        url: '/itemDetails',
        async: false,
        data:{
            item_id: item_id
        },
        success: function(data){
            if(data != 'false'){
                var array = $.map(data, function(value, index){
                    return [value];
                });
                array.forEach(value => {
                    html = '<tr class="tblEditParts_data">';
                    html += '<td class="d-none td_1">'+value.item_id+'</td>';
                    html += '<td><i class="fa-solid fa-grip fa-lg mr-2"></i>'+value.item.toUpperCase()+'</td>';
                    html += '<td class="td_2">'+value.quantity+'</td>';
                    html += '<td>'+value.uom+'</td>';
                    html += '<td><button style="zoom: 80%;" class="btn btn-danger bp delete-row-details" id="'+value.id+'"><i class="fa-solid fa-trash-can fa-lg mr-2"></i>REMOVE</button></td>';
                    html += '<td class="d-none td_3"></td>';
                    html += '</tr>';
                    $("#tbodyEditParts").append(html);
                });
            }
        }
    });
    $('#editParts').show();
    $('#tblEditParts').show();
    $('#detailsAssembledItem').modal('show');
});

$(document).ready(function(){
    $('#tblEditParts tbody').sortable({ helper: fixWidthHelper }).disableSelection();

    function fixWidthHelper(e, ui){
        tblEditParts_isEdited = 'true';
        ui.children().each(function(){
            $(this).width($(this).width());
        });
        return ui;
    }
});

$(document).ready(function(){
    $('#tblSaveParts tbody').sortable({ helper: fixWidthHelper }).disableSelection();

    function fixWidthHelper(e, ui){
        tblEditParts_isEdited = 'true';
        ui.children().each(function(){
            $(this).width($(this).width());
        });
        return ui;
    }
});

$(document).ready(function(){
    $('#tblBundle tbody').sortable({ helper: fixWidthHelper }).disableSelection();

    function fixWidthHelper(e, ui){
        tblEditParts_isEdited = 'true';
        ui.children().each(function(){
            $(this).width($(this).width());
        });
        return ui;
    }
});

$('#btnUpdate').on('click', function(){
    var item_id = $('#aim_item_id').val();
    var item_name_original = $('#aim_item_name_details_original').val();
    var item_name = $.trim($('#aim_item_name_details').val());
    var item_code_original = $('#aim_item_code_details_original').val();
    var item_code = $.trim($('#aim_item_code_details').val()).toUpperCase();
    var minimum_original = $('#aim_minimum_details_original').val();
    var minimum = $.trim($('#aim_minimum_details').val());
    Swal.fire({
        title: "UPDATE ASSEMBLED ITEM?",
        html: "You are about to UPDATE this Assembled Item!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#loading').show();
            setTimeout(() => {
                var table = document.getElementById('tblEditParts');
                var count = table.rows.length;
                for(i = 1; i < count; i++){
                    var objCells = table.rows.item(i).cells;
                    objCells.item(5).innerHTML = i;
                }
                $.ajax({
                    type: 'POST',
                    url: '/updateAssemblyItem',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        item_id: item_id,
                        item_name_original: item_name_original,
                        item_name: item_name,
                        item_code_original: item_code_original,
                        item_code: item_code,
                        minimum_original: minimum_original,
                        minimum: minimum,
                        edited_parts: tblEditParts_isEdited
                    },
                    success: function(data){
                        if(data == 'true'){
                            if(tblEditParts_isEdited == 'true'){
                                $('.tblEditParts_data').each(function(){
                                    $.ajax({
                                        type: 'POST',
                                        url: '/saveParts',
                                        async: false,
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            item_id: item_id,
                                            part_id: $(this).children('.td_1').html(),
                                            quantity: $(this).children('.td_2').html(),
                                            priority: $(this).children('.td_3').html()
                                        }
                                    });
                                });
                            }
                            $('#loading').hide();
                            $('#detailsAssembledItem').modal('hide');
                            Swal.fire("UPDATE SUCCESS", "Assembled Item has been updated successfully!", "success");
                            tblAssembled.ajax.reload(null, false);
                        }
                        else if(data == 'duplicate'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE ITEM", "Product Description already exists!", "error");
                            return false;
                        }
                        else if(data == 'dupecode'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE CODE", "Product Code already exists!", "error");
                            return false;
                        }
                        else{
                            $('#loading').hide();
                            $('#detailsAssembledItem').modal('hide');
                            Swal.fire("UPDATE FAILED", "Assembled Item update failed!", "error");
                            tblAssembled.ajax.reload(null, false);
                        }
                    }
                });
            }, 200);
        }
    });
});

setInterval(checkBundle, 0);
function checkBundle(){
    if($('#newBundledItems').is(':visible')){
        var item_description = $.trim($('#bundle').val());
        var item_code = $('#bundle_code').val();
        if(item_description && item_code){
            $('.create_label').hide();
            $('#bundleDetails').show();
        }
        else{
            $('.create_label').show();
            $('#bundleDetails').hide();
        }
    }
    if($('#tblBundle').is(':visible')){
        $('.ordering_label').show();
    }
    else{
        $('.ordering_label').hide();
    }
}

$(".btnNewBundled").on('click', function(){
    $('#bundle').val('');
    $("#categoryBundled").val('');
    $("#itemBundled").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    $("#qtyBundled").val('');
    $('#uomBundled').val('');
    $('#tblBundle tbody').empty();
    if($('#tblBundle tbody').children().length == 0){
        $('#tblBundle').hide();
        $('#divBundle').removeClass();
        $('#btnClose').hide();
        $('#btnSave').hide();
        $('.submit_label').show();
    }

    $('#newBundledItems').modal('show');
});

$('#categoryBundled').on('change', function(){
    var id = $('#categoryBundled').val();
    var descOp = " ";
    if(!$('#categoryBundled').val()){
        $("#itemBundled").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>');
        $("#itemBundled").chosen();
        $("#itemBundled").trigger("chosen:updated");
        $("#itemBundled_chosen").css({"margin-left": "-1px"});
        $('label[for="itemBundled"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
    }
    else{
        $.ajax({
            type: 'GET',
            url: '/itemsAssembly',
            data:{'category_id':id},
            success: function(data)
            {
                var itemcode = $.map(data, function(value, index){
                    return [value];
                });
                descOp+='<option value="" selected disabled>Select Item</option>';
                itemcode.forEach(value => {
                    descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
                });
                $("#itemBundled").find('option').remove().end().append(descOp);
                $("#itemBundled").chosen();
                $("#itemBundled").trigger("chosen:updated");
                $("#itemBundled_chosen").css({"margin-left": "-1px"});
                $('label[for="itemBundled"]').css({"z-index": "1", "margin-top": "-2px", "margin-right": "-20px"});
            }
        });
    }
});

$('#itemBundled').on('change', function(){
    var item_id = $(this).val();
    $.ajax({
        type: 'GET',
        url: '/uomAssembly',
        data:{
            'item_id': item_id,
        },
        success: function(data){
            $('#uomBundled').val(data[0].uom);
        }
    });
});

$("#add-row").on('click', function(){
    var category = $("#categoryBundled option:selected").text();
    var item = $("#itemBundled option:selected").text();
    let item_id = $("#itemBundled").val();
    var uom = $("#uomBundled").val();
    let qty = $("#qtyBundled").val();
    var markup = "<tr class='tblSaveParts_data'><td class='d-none td_1'>" + item_id + "</td><td><i class='fa-solid fa-grip fa-lg mr-2'></i>" + item + "</td><td class='td_2'>" + qty + "</td><td>" + uom + "</td><td><button type='button' style='zoom: 80%;' class='delete-row btn btn-danger bp'><i class='fa-solid fa-trash-can fa-lg mr-2'></i>REMOVE</button></td><td class='d-none td_3'></td></tr>";
    var ctr = 'false';
    if(category == "Select Category" || item == "Select Item" || qty == "" || qty == "0" || uom == ""){
        Swal.fire('REQUIRED','Please select an item!','error');
        return false;
    }
    var table = document.getElementById('tblBundle');
    var count = table.rows.length;
    for(i = 1; i < count; i++){
        var objCells = table.rows.item(i).cells;
        if(item_id == objCells.item(0).innerHTML){
            objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) + parseInt(qty);
            ctr = 'true';
            category = $("#categoryBundled").val('');
            item = $("#itemBundled").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
            uom = $('#uomBundled').val('');
            qty = $("#qtyBundled").val('');
            setTimeout(function(){$('#categoryBundled').change()}, current_timeout);
            $("#categoryBundled").focus();
            return false;
        }
        else{
            ctr = 'false';
        }
    }
    if(ctr == 'false')
    { $("#tblBundle tbody").append(markup); }
    category = $("#categoryBundled").val('');
    item = $("#itemBundled").find('option').remove().end().append('<option value="" selected disabled>Select Item</option>').val();
    uom = $('#uomBundled').val('');
    qty = $("#qtyBundled").val('');
    setTimeout(function(){$('#categoryBundled').change()}, current_timeout);
    $('#tblBundle').show();
    $('#divBundle').toggle();
    $('#submit_footer').show();
    $("#categoryBundled").focus();

    if($('#tblBundle tbody').children().length == 0){
        $('.submit_label').show();
    }
    else{
        $('.submit_label').hide();
    }
});

$("#tblBundle").on('click', '.delete-row', function(){
    $(this).closest("tr").remove();
    if($('#tblBundle tbody').children().length == 0){
        $('#tblBundle').hide();
        $('#divBundle').removeClass();
        $('#submit_footer').hide();
        $('.submit_label').show();
    }
});

$('#btnSaveBundle').on('click', function(){
    var item_code = $.trim($('#bundle_code').val()).toUpperCase();
    var item_description = $.trim($('#bundle').val());
    Swal.fire({
        title: "SAVE BUNDLED ITEMS?",
        html: "You are about to SAVE new Bundled Items!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#loading').show();
            setTimeout(() => {
                var table = document.getElementById('tblBundle');
                var count = table.rows.length;
                for(i = 1; i < count; i++){
                    var objCells = table.rows.item(i).cells;
                    objCells.item(5).innerHTML = i;
                }
                $.ajax({
                    type: 'POST',
                    url: '/saveBundledItems',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        item: item_description,
                        prodcode: item_code,
                        minimum: '1'
                    },
                    success: function(data){
                        if(data.result == 'true'){
                            $('.tblSaveParts_data').each(function(){
                                $.ajax({
                                    type: 'POST',
                                    url: '/saveInclusives',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        item_id: data.id,
                                        part_id: $(this).children('.td_1').html(),
                                        quantity: $(this).children('.td_2').html(),
                                        priority: $(this).children('.td_3').html()
                                    }
                                });
                            });
                            $.ajax({
                                type: 'POST',
                                url: '/logBundle',
                                async: false,
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    item_id: data.id,
                                    item: item_description
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        $('#newBundledItems').modal('hide');
                                        Swal.fire("BUNDLE SUCCESS", "", "success");
                                        tblAssembled.ajax.reload(null, false);
                                    }
                                    else{
                                        $('#loading').hide();
                                        $('#newBundledItems').modal('hide');
                                        Swal.fire("BUNDLE FAILED", "", "error");
                                        tblAssembled.ajax.reload(null, false);
                                    }
                                }
                            });
                        }
                        else if(data.result == 'duplicate'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE BUNDLE", "", "error");
                            return false;
                        }
                        else{
                            $('#loading').hide();
                            $('#newBundledItems').modal('hide');
                            Swal.fire("BUNDLE FAILED", "", "error");
                            tblAssembled.ajax.reload(null, false);
                        }
                    }
                });
            }, 200);
        }
    });
});

$('#bundleditemsTable tbody').on('click', 'tr', function(){
    var table = $('table.bundleditemsTable').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    var item_id = data.id;
    $('#bundle_id').val(item_id);
    var prodcode = decodeHtml(data.prodcode);
        $('#bundle_code_details').val(prodcode);
        $('#bundle_code_details_original').val(prodcode);
    var item_name = decodeHtml(data.item);
        $('#bundle_details').val(item_name);
        $('#bundle_details_original').val(item_name);
    show_barcode(prodcode, '#show_barcode');

    tblEditParts_isEdited = 'false';
    $("#tbodyEditParts").empty();
    $.ajax({
        url: '/itemDetails',
        async: false,
        data:{
            item_id: item_id
        },
        success: function(data){
            if(data != 'false'){
                var array = $.map(data, function(value, index){
                    return [value];
                });
                array.forEach(value => {
                    html = '<tr class="tblEditParts_data">';
                    html += '<td class="d-none td_1">'+value.item_id+'</td>';
                    html += '<td><i class="fa-solid fa-grip fa-lg mr-2"></i>'+value.item.toUpperCase()+'</td>';
                    html += '<td class="td_2">'+value.quantity+'</td>';
                    html += '<td>'+value.uom+'</td>';
                    html += '<td><button style="zoom: 80%;" class="btn btn-danger bp delete-row-details" id="'+value.id+'"><i class="fa-solid fa-trash-can fa-lg mr-2"></i>REMOVE</button></td>';
                    html += '<td class="d-none td_3"></td>';
                    html += '</tr>';
                    $("#tbodyEditParts").append(html);
                });
            }
        }
    });
    $('#editParts').show();
    $('#tblEditParts').show();
    $('#detailsBundledItems').modal('show');
});

$('#btnUpdateBundle').on('click', function(){
    var item_id = $('#bundle_id').val();
    var item_code = $.trim($('#bundle_code_details').val()).toUpperCase();
    var item_code_orig = $.trim($('#bundle_code_details_original').val());
    var item_description = $.trim($('#bundle_details').val());
    var item_description_orig = $.trim($('#bundle_details_original').val());
    Swal.fire({
        title: "UPDATED BUNDLED ITEMS?",
        html: "You are about to UPDATE Bundled Items!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            $('#loading').show();
            setTimeout(() => {
                var table = document.getElementById('tblEditParts');
                var count = table.rows.length;
                for(i = 1; i < count; i++){
                    var objCells = table.rows.item(i).cells;
                    objCells.item(5).innerHTML = i;
                }
                $.ajax({
                    type: 'POST',
                    url: '/updateBundledItems',
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        item_id: item_id,
                        item_name: item_description,
                        item_name_original: item_description_orig,
                        item_code: item_code,
                        item_code_original: item_code_orig,
                        minimum: '1',
                        edited_parts: tblEditParts_isEdited
                    },
                    success: function(data){
                        if(data == 'true'){
                            if(tblEditParts_isEdited == 'true'){
                                $('.tblEditParts_data').each(function(){
                                    $.ajax({
                                        type: 'POST',
                                        url: '/saveInclusives',
                                        async: false,
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            item_id: item_id,
                                            part_id: $(this).children('.td_1').html(),
                                            quantity: $(this).children('.td_2').html(),
                                            priority: $(this).children('.td_3').html()
                                        }
                                    });
                                });
                            }
                            $('#loading').hide();
                            $('#detailsBundledItems').modal('hide');
                            Swal.fire("BUNDLE SUCCESS", "", "success");
                            tblAssembled.ajax.reload(null, false);
                        }
                        else if(data == 'duplicate'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE BUNDLE", "", "error");
                            return false;
                        }
                        else if(data == 'dupecode'){
                            $('#loading').hide();
                            Swal.fire("DUPLICATE CODE", "Product Code already exists!", "error");
                            return false;
                        }
                        else{
                            $('#loading').hide();
                            $('#detailsBundledItems').modal('hide');
                            Swal.fire("BUNDLE FAILED", "", "error");
                            tblAssembled.ajax.reload(null, false);
                        }
                    }
                });
            }, 200);
        }
    });
});

setInterval(() => {
    if($("#itemBundled_chosen").is(':visible')){
        if(!$("#itemBundled").val()){
            $('#itemBundled_chosen').removeClass('defaultInput');
            $('#itemBundled_chosen').addClass('requiredInput');
        }
        else{
            $('#itemBundled_chosen').removeClass('requiredInput');
            $('#itemBundled_chosen').addClass('defaultInput');
        }
    }
    if($("#itemAssembly_chosen").is(':visible')){
        if(!$("#itemAssembly").val()){
            $('#itemAssembly_chosen').removeClass('defaultInput');
            $('#itemAssembly_chosen').addClass('requiredInput');
        }
        else{
            $('#itemAssembly_chosen').removeClass('requiredInput');
            $('#itemAssembly_chosen').addClass('defaultInput');
        }
    }
    if($("#itemAssemblyDetails_chosen").is(':visible')){
        if(!$("#itemAssemblyDetails").val()){
            $('#itemAssemblyDetails_chosen').removeClass('defaultInput');
            $('#itemAssemblyDetails_chosen').addClass('requiredInput');
        }
        else{
            $('#itemAssemblyDetails_chosen').removeClass('requiredInput');
            $('#itemAssemblyDetails_chosen').addClass('defaultInput');
        }
    }
}, 0);

$(document).on('click', '#tblEditParts button.move', function(){
    var row = $(this).closest('tr');
    $(this).hasClass('up') ? row.prev().before(row) : row.next().after(row);
    tblEditParts_isEdited = 'true';
});

setInterval(() => {
    if($('#tblSaveParts tbody').children().length == 0){
        $('.labelSaveParts').hide();
        $('.requiredSaveParts').show();
    }
    else{
        $('.labelSaveParts').show();
        $('.requiredSaveParts').hide();
    }
    if($('#tblEditParts tbody').children().length == 0){
        $('.labelEditParts').hide();
        $('.requiredEditParts').show();
    }
    else{
        $('.labelEditParts').show();
        $('.requiredEditParts').hide();
    }
    if((!$('#bundle_details').val() || !$('#bundle_code_details').val()) || ($('#bundle_code_details').val() == $('#bundle_code_details_original').val() && $('#bundle_details').val() == $('#bundle_details_original').val() && tblEditParts_isEdited == 'false')){
        $('#btnUpdateBundle').prop('disabled', true);
    }
    else{
        $('#btnUpdateBundle').prop('disabled', false);
    }
}, 0);

$(document).ready(function(){
    if(current_location == '/maintenance?import=success_without_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS", "ADD ITEMS via import file is successful without errors.", "success");
    }
    else if(current_location == '/maintenance?import=success_with_errors'){
        $('#loading').hide();
        Swal.fire("IMPORT SUCCESS W/ ERRORS", "ADD ITEMS via import file is successful with some errors.", "warning");
    }
    else if(current_location == '/maintenance?import=failed'){
        $('#loading').hide();
        Swal.fire("IMPORT FAILED", "ADD ITEMS via import file has failed.", "error");
    }
});