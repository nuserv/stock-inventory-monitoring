var minDate, maxDate, newAssembly;
var items = [], items_id = [], item_count = 0, item_total = 0;
$(document).ready(function(){
    $('table.assemblyTable').dataTable().fnDestroy();
    var assemblyTable = $('table.assemblyTable').DataTable({
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language:{
            info: "Showing _START_ to _END_ of _TOTAL_ Assembly Requests",
            lengthMenu: "Show _MENU_ Assembly Requests",
            emptyTable: "No Assembly Requests Data Found!",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/assembly/request_data',
        },
        columnDefs: [
            {
                "targets": [0,1],
                "visible": false,
                "searchable": true
            },
            {
                "targets": [2],
                "render": $.fn.dataTable.render.moment('YYYY-MM-DD', 'MMM. DD, YYYY')
            }
        ],
        columns: [
            { data: 'reqdatetime' },
            { data: 'needdatetime' },
            { data: 'reqdate' },
            {
                data: 'needdate',
                "render": function(data, type, row){
                    if(row.verify == 'Confirmed'){
                        return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                    }
                    var a = new Date(minDate);
                    var b = new Date(row.needdate);
                    var difference = dateDiffInDays(a, b);
                    if(difference >= 0 && difference <= 3){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Blue; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+"<i style='zoom: 150%; color: blue;' class='fa fa-exclamation-triangle ml-2'></i></span>";
                    }
                    else if(difference < 0){
                        return "<span class='d-none'>"+row.needdate+"</span><span style='color: Red; font-weight: bold;'>"+moment(row.needdate).format('MMM. DD, YYYY')+"<i style='zoom: 150%; color: red;' class='fa fa-exclamation-circle ml-2'></i></span>";
                    }
                    else{
                        return "<span class='d-none'>"+row.needdate+"</span>"+moment(row.needdate).format('MMM. DD, YYYY');
                    }
                }
            },
            { data: 'req_num' },
            { data: 'req_type' },
            {
                data: 'item_desc',
                "render": function(data, type, row){
                    if(row.item_id != '0'){
                        return row.item_desc;
                    }
                    else{
                        return '';
                    }
                }
            },
            { data: 'qty' },
            {
                data: 'status',
                "render": function(data, type, row){
                    var d = [3,12,17].includes(row.status_id) == true ? 'd-inline' : 'd-none';
                    if(row.status_id == '1' || row.status_id == '15' || row.status_id == '18' || row.status_id == '21' || row.status_id == '22' || row.status_id == '23' || row.status_id == '25'){
                        if(row.req_type_id == '4' && row.status_id == '1'){ d = 'd-none'; }
                        return "<span style='color: Red; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                    else if(row.status_id == '2' || row.status_id == '5' || row.status_id == '16'){
                        return "<span style='color: Indigo; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                    else if(row.status_id == '3' || row.status_id == '4' || row.status_id == '11' || row.status_id == '13' || row.status_id == '17'){
                        return "<span style='color: Green; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                    else if(row.status_id == '8' || row.status_id == '12' || row.status_id == '20'){
                        return "<span style='color: Blue; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                    else if(row.status_id == '14' || row.status_id == '19' || row.status_id == '26'){
                        return "<span style='color: DarkBlue; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                    else{
                        return "<span style='color: Gray; font-weight: bold;'>"+row.status+"<i style='zoom: 150%;' class='fa fa-exclamation-triangle "+d+" ml-2'></i></span>";
                    }
                }
            }
        ],
        order: [],
        initComplete: function(){
            $('#loading').hide();
        }
    });

    $('.filter-input').on('keyup search', function(){
        assemblyTable.column($(this).data('column')).search($(this).val()).draw();
    });

    $('#assemblyTable tbody').on('click', 'tr', function(){
        var table = $('table.assemblyTable').DataTable();
        if(!table.data().any()){ return false; }
        var value = table.row(this).data();
        window.location.href = '/assembly?request_number='+value.req_num;
    });

    setInterval(function(){
        if(!$('.modal:visible').length && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/assembly/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        assemblyTable.ajax.reload(null, false);
                    }
                }
            });
        }
        if($('#detailsAssembly').is(':visible') && checker == false && $('#loading').is(':hidden') && standby == false){
            $.ajax({
                type: 'GET',
                url: '/checkStatus',
                data:{
                    'type': 'stockrequest',
                    'request_number': $('#request_num_details').val(),
                    'status_id': $('#status_id_details').val()
                },
                success: function(data){
                    if(data.state == 'changed'){
                        checker = true;
                        Swal.fire({
                            title: "REQUEST STATUS CHANGED",
                            html: "Another user has processed "+data.request_type+" Job Order No. "+data.request_number+"; and current request STATUS has been changed from <b>"+data.oldStatus+"</b> to <b>"+data.newStatus+"</b>.",
                            icon: "warning",
                            confirmButtonText: 'REFRESH',
                            allowOutsideClick: false
                        })
                        .then((result) => {
                            if(result.isConfirmed){
                                window.location.href = window.location.href.replace('?request_number='+data.request_number, '')+'?request_number='+data.request_number;
                                return false;
                            }
                        });
                    }
                }
            });
        }
    }, 1000);
});

setInterval(closeModal, 0);
function closeModal(){
    if($('#newAssembly').is(':visible')){
        newAssembly = 'true';
    }
    if(newAssembly == 'true' && $('#newAssembly').is(':hidden') && checker == false){
        newAssembly = 'false';
        window.location.href = '/assembly';
    }
    if($('#detailsAssembly').is(':visible') && checker == true){
        $('.close').click();
    }
}

$(function(){
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    minDate = year + '-' + month + '-' + day;

    $('#needdate').attr('min', minDate);
    $('#schedOn').attr('min', minDate);
});

var generatedReqNum;
function generateReqNum(){
    var today = new Date();
    var month = today.getMonth()+1;
    if(month <= 9){
        month = '0'+month;
    }
    var day = today.getDate();
    if(day <= 9){
        day = '0'+day;
    }
    var date = today.getFullYear()+'-'+month+day+'-';
    var result = '';
    var characters = '123456789';

    for(var i = 0; i < 3; i++){
        result += characters.charAt(Math.floor(Math.random() * 6));
    }
    var request_number = date+result;

    $.ajax({
        type: 'GET',
        url: '/generateReqNum',
        async: false,
        data:{
            'request_number': request_number
        },
        success: function(data){
            if(data == 'unique'){
                generatedReqNum = request_number;
                $('#request_num').val(request_number);
            }
            else{
                generateReqNum();
            }
        }
    });
}

$(".btnNewAssembly").on('click', function(){
    checker = false;
    $('#newAssembly').modal('show');
    // generateReqNum();
});

setInterval(checkNewAssembly, 0);
function checkNewAssembly(){
    if($('#newAssembly').is(':visible')){
        if($('#assembly').val() && $('#assemblypartsDetails').is(':hidden')){
            $('#btnAssemblyProceed').prop('disabled', false);
            $('.header_label').hide();
        }
        else{
            $('#btnAssemblyProceed').prop('disabled', true);
            $('.header_label').show();
        }
    }
    if($('#assemblypartsDetails').is(':visible')){
        $('.header_label').hide();
    }
}

$('#btnAssemblyProceed').on('click', function(){
    $('#btnAssemblyProceed').prop('disabled', true);
    $("#assembly").prop('disabled', true);
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $('table.tblPartsDetails').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        columnDefs: [
            {
                "targets": [4,5],
                "visible": false,
                "searchable": false
            }
        ],
        language:{
            emptyTable: "No data available in table",
            processing: "Loading...",
        },
        serverSide: true,
        ajax:{
            url: '/partsDetails',
            data:{
                item_id: $("#assembly").val()
            }
        },
        order: [],
        columns: [
            { data: 'prodcode' },
            { data: 'item' },
            { data: 'quantity' },
            { data: 'uom' },
            { data: 'category_id' },
            { data: 'item_id' }
        ],
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function(){
            $('#assemblypartsDetails').show();
        }
    });
    setTimeout(setqty, 1000);
});

function setqty(){
    var table = document.getElementById('tblPartsDetails');
    var count = table.rows.length;
    for(i = 1; i < count; i++){
        var objCells = table.rows.item(i).cells;
        objCells.item(2).innerHTML = parseInt(objCells.item(2).innerHTML) * parseInt($("#qty").val());
    }
    $("#btnAssemblySave").prop('disabled', false);
}

$('#btnAssemblyBack').on('click', function(){
    $('#btnAssemblyProceed').prop('disabled', true);
    $('table.tblPartsDetails').dataTable().fnDestroy();
    $("#assemblypartsDetails").hide();
    $("#btnAssemblySave").prop('disabled', true);
    $("#assembly").prop('disabled', false);
    $("#assembly").val('');
});

$('#btnAssemblySave').on('click', function(){
    var needdate = $('#needdate').val();
    var request_type = '5';
    var item_id = $('#assembly').val();
    var item_desc = $("#assembly option:selected").text();
    var qty = $('#qty').val();
    if(needdate < minDate){
        Swal.fire('Minimum Date is today!','Select within date range from today onwards.','error');
        return false;
    }
    else{
        Swal.fire({
            title: "SUBMIT ASSEMBLY REQUEST?",
            html: "Please <b class='text-success'>REVIEW</b> the details of your request. Click <b style='color: #d33;'>CONFIRM</b> button to submit; otherwise, click <b style='color: #3085d6;'>CANCEL</b> button.",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false
        })
        .then((result) => {
            if(result.isConfirmed){
                checker = true;
                $('#loading').show();
                setTimeout(() => {
                    $.ajax({
                        type: 'POST',
                        url: '/assembly/saveReqNum',
                        async: false,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            'request_number': $('#request_num').val(),
                            'needdate': needdate,
                            'request_type': request_type,
                            'item_id': item_id,
                            'qty': qty
                        },
                        success: function(data){
                            if(data == 'true'){
                                var form_data  = $('#tblPartsDetails').DataTable().rows().data();
                                form_data.each(function(value, index){
                                    $.ajax({
                                        type: 'POST',
                                        url: '/assembly/saveRequest',
                                        async: false,
                                        headers:{
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{
                                            'request_number': $('#request_num').val(),
                                            'item': value.item_id,
                                            'quantity': value.quantity,
                                            'qty': qty
                                        }
                                    });
                                });
                                $.ajax({
                                    type: 'POST',
                                    url: '/assembly/logSave',
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num').val(),
                                        'item_desc': item_desc,
                                        'qty': qty
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('#loading').hide();
                                            $('#newAssembly').modal('hide');
                                            Swal.fire("SUBMIT SUCCESS", "ASSEMBLY REQUEST", "success");
                                            setTimeout(function(){location.href="/assembly"}, 2000);
                                        }
                                        else{
                                            $('#loading').hide();
                                            $('#newAssembly').modal('hide');
                                            Swal.fire("SUBMIT FAILED", "ASSEMBLY REQUEST", "error");
                                            setTimeout(function(){location.href="/assembly"}, 2000);
                                        }
                                    }
                                });
                            }
                            else{
                                $('#loading').hide();
                                $('#newAssembly').modal('hide');
                                Swal.fire("SUBMIT FAILED", "ASSEMBLY REQUEST", "error");
                                setTimeout(function(){location.href="/assembly"}, 2000);
                            }
                        }
                    });
                }, 200);
            }
        });
    }
});

if(current_location.includes('request_number') == true){
    var url = new URL(window.location.href);
    var reqnum = url.searchParams.get("request_number");
    $.ajax({
        url: '/reqModal',
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'GET',
        data:{
            request_number: reqnum,
        },
        success: function(data){
            checker = false;
            var reqitem = $.map(data.data, function(value, index){
                return [value];
            });
            reqitem.forEach(value => {
                var requestStatus = value.status_id;
                    $('#status_id_details').val(requestStatus);
                var req_type_id = value.req_type_id;
                    $('#req_type_id_details').val(req_type_id);
                var req_date = value.date;
                    req_date = moment(req_date).format('dddd, MMMM DD, YYYY, h:mm A');
                    $('#reqdate_details').val(req_date);
                var need_date = value.needdate;
                    maxDate = need_date;
                    need_date = moment(need_date).format('dddd, MMMM DD, YYYY');
                    $('#needdate_details').val(need_date);
                var req_num = value.req_num;
                    $('#request_num_details').val(req_num);
                var url_href = window.location.href.replace('?request_number='+req_num, '');
                    $("#btnRefresh").attr("href", url_href+'?request_number='+req_num);
                var asm_req_num = value.assembly_reqnum;
                    $('#asm_request_num_details').val(asm_req_num);
                var req_by = value.req_by;
                    $('#requested_by_details').val(req_by);
                var req_by_id = value.user_id;
                var req_type = value.req_type;
                    $('#request_type_details').val(req_type);
                var item_id = value.item_id;
                    $('#item_id_details').val(item_id);
                var qty = value.qty;
                    $('#qty_details').val(qty);
                var status = value.status;
                    $('#status_details').val(status);
                var sched = value.sched;
                    sched = moment(sched).format('dddd, MMMM DD, YYYY');
                    $('#sched').val(sched);
                    $('#sched1').val(sched);
                    $('#resched').val(sched);
                    $('#resched1').val(sched);

                    if(current_role == 'assembler' && [1, 2, 3, 6].includes(req_type_id) == true){
                        window.location.href = '/assembly';
                    }
                    if(current_role == 'assembler' && current_user != value.user_id){
                        window.location.href = '/assembly';
                    }

                    var ajax_url = '/schedItems';
                    var rcv_url = '/schedItems';
                    var included = 'yes';

                    if(value.item_id){
                        $.ajax({
                            type: 'GET',
                            url: '/getItem',
                            data:{
                                'item_id': value.item_id
                            },
                            success: function(data){
                                $('#item_desc_details').val(decodeHtml(data));
                            }
                        });
                    }
                    if(value.prepared_by){
                        $.ajax({
                            type: 'GET',
                            url: '/getPreparer',
                            data:{
                                'prepared_by': value.prepared_by
                            },
                            success: function(data){
                                $('#prep_by').val(data);
                                $('#prep_by1').val(data);
                                $('#reprep_by').val(data);
                                $('#reprep_by1').val(data);
                            }
                        });
                    }
                    if(value.cancelled_by){
                        $.ajax({
                            type: 'GET',
                            url: '/getCancel',
                            data:{
                                'request_number': $('#request_num_details').val(),
                                'cancelled_by_id': value.cancelled_by
                            },
                            success: function(data){
                                if(data.length > 0){
                                    $(".cancel_field").show();
                                    $("#cancelled_by_details").val(data[0]);
                                    $("#cancel_reason_details").val(data[1]);
                                }
                            }
                        });
                    }
                    if(current_user == req_by_id && requestStatus == 3){
                        $("#btnCancelRequest").show();
                    }
                    if(req_type_id == '4'){
                        $(".rephide").hide();
                        $(".repshow").show();
                        $("#btnCancelRequest").hide();
                    }
                    if(requestStatus == '1' && req_type_id != '4'){
                        $("#btnDelete").show();
                    }
                    if(requestStatus == '2'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'SCHEDULED ITEM DETAILS';
                    }
                    if(requestStatus == '3'){
                        $('#prepItemsModal').show();
                        $('#receive_label').show();
                        $('.btnReceive').show();
                        document.getElementById('modalheader').innerHTML = 'FOR RECEIVING ITEM DETAILS';
                    }
                    if(requestStatus == '11'){
                        var ajax_url = '/retItemsG';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'RETURNED ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '25'){
                        var ajax_url = '/retItemsG';
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE RETURNED ITEM DETAILS';
                        $("#incFooter").hide();
                    }
                    if(requestStatus == '12'){
                        $('#prepItemsModal').show();
                        $('#defective_label').show();
                        $('#btnAssemble').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                    }
                    if(requestStatus == '13'){
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                    }
                    if(requestStatus == '14'){
                        var rcv_url = '/receivedItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'ASSEMBLED ITEM PARTS DETAILS';
                        $('#asmItemsModal').show();
                    }
                    if(requestStatus == '15'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '16'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $(".divResched").show();
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '17'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        $('#increceive_label').show();
                        $(".divResched").show();
                        $(".btnReceive").show();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '18'){
                        var ajax_url = '/dfcItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'DEFECTIVE ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '19'){
                        var ajax_url = '/replacedItems';
                        var rcv_url = ajax_url;
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'REPLACEMENT ITEM DETAILS';
                    }
                    if(requestStatus == '20'){
                        $('#prepItemsModal').show();
                        $('#btnAssemble').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                    }
                    if(requestStatus == '21'){
                        var ajax_url = '/incdfcItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE DEFECTIVE ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }
                    if(requestStatus == '22'){
                        var included = 'no';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $(".pendshow").show();
                    }
                    if(requestStatus == '23'){
                        var ajax_url = '/incItems';
                        $('#prepItemsModal').show();
                        document.getElementById('modalheader').innerHTML = 'FOR ASSEMBLY ITEM DETAILS';
                        $(".prephide").hide();
                        $("#incItemsModal").show();
                        document.getElementById('incmodalheader').innerHTML = 'INCOMPLETE REPLACEMENT ITEM DETAILS';
                        $("#incFooter").hide();
                        $(".divPrint").show();
                    }

                $('#detailsAssembly').modal('show');

                $('table.stockDetails').dataTable().fnDestroy();
                $('table.stockDetails').DataTable({
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        emptyTable: "No data available in table",
                        processing: "Loading...",
                    },
                    serverSide: true,
                    ajax:{
                        url: '/requestDetails',
                        data:{
                            reqnum: req_num,
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'uom' },
                        { data: 'quantity' }
                    ],
                    footerCallback: function(row,data,start,end,display){
                        var api = this.api(), data;
                        var intVal = function(i){
                            return typeof i === 'string'?
                                i.replace(/[\$,]/g,'')*1:
                                typeof i === 'number'?
                                    i:0;
                        };
                        api.columns('.sum', {page:'all'}).every(function(){
                            var sum = this
                            .data()
                            .reduce(function(a,b){
                                return intVal(a) + intVal(b);
                            }, 0);
                            sum = sum.toString();
                            var pattern = /(-?\d+)(\d{3})/;
                            while(pattern.test(sum))
                            sum = sum.replace(pattern,"$1,$2");
                            this.footer().innerHTML = sum;
                        });
                    }
                });

                $('table.prepItems').dataTable().fnDestroy();
                $('table.prepItems').DataTable({
                    columnDefs: [
                        {
                            "targets": [5,6],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    searching: false,
                    paging: false,
                    ordering: false,
                    info: false,
                    language:{
                        processing: "Loading...",
                        emptyTable: "No data available in table"
                    },
                    serverSide: true,
                    ajax:{
                        url: rcv_url,
                        data:{
                            request_number: req_num,
                            included: included
                        }
                    },
                    order: [],
                    columns: [
                        { data: 'prodcode' },
                        { data: 'item' },
                        { data: 'qty' },
                        { data: 'uom' },
                        { data: 'serial' },
                        { data: 'id' },
                        { data: 'id' }
                    ],
                    footerCallback: function(row,data,start,end,display){
                        var api = this.api(), data;
                        var intVal = function(i){
                            return typeof i === 'string'?
                                i.replace(/[\$,]/g,'')*1:
                                typeof i === 'number'?
                                    i:0;
                        };
                        api.columns('.sum', {page:'all'}).every(function(){
                            var sum = this
                            .data()
                            .reduce(function(a,b){
                                return intVal(a) + intVal(b);
                            }, 0);
                            sum = sum.toString();
                            var pattern = /(-?\d+)(\d{3})/;
                            while(pattern.test(sum))
                            sum = sum.replace(pattern,"$1,$2");
                            this.footer().innerHTML = sum;
                        });
                    },
                    initComplete: function(){
                        var requestStatus = $('#status_id_details').val();
                        if(requestStatus == '3'){
                            $('#prepItems tbody tr').each(function(index, tr){
                                var table = $('table.prepItems').DataTable();
                                var data = table.row(index).data();
                                var row_num = index+1;
                                item_total = $('#prepItems_total').text();
                                if(data.serialize == 'YES'){
                                    items.push(data.id);
                                    $(this).toggleClass('selected');
                                    item_count++;
                                }
                                else{
                                    items_id.push({
                                        item_id: data.item_id,
                                        itemQty: data.qty
                                    });
                                    $(this).toggleClass('selected');
                                    item_count = parseInt(item_count)+parseInt(data.qty);
                                    document.getElementById('prepItems').rows[row_num].cells[2].innerHTML = data.qty+' out of '+data.qty;
                                }
                            });
                        }
                    }
                });

                if(ajax_url != '/schedItems'){
                    $('table.incItems').dataTable().fnDestroy();
                    $('table.incItems').DataTable({
                        columnDefs: [
                            {
                                "targets": [5],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language:{
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax:{
                            url: ajax_url,
                            data:{
                                request_number: req_num,
                            }
                        },
                        order: [],
                        columns: [
                            { data: 'prodcode' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'id' }
                        ],
                        footerCallback: function(row,data,start,end,display){
                            var api = this.api(), data;
                            var intVal = function(i){
                                return typeof i === 'string'?
                                    i.replace(/[\$,]/g,'')*1:
                                    typeof i === 'number'?
                                        i:0;
                            };
                            api.columns('.sum', {page:'all'}).every(function(){
                                var sum = this
                                .data()
                                .reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                }, 0);
                                sum = sum.toString();
                                var pattern = /(-?\d+)(\d{3})/;
                                while(pattern.test(sum))
                                sum = sum.replace(pattern,"$1,$2");
                                this.footer().innerHTML = sum;
                            });
                        },
                        initComplete: function(){
                            var requestStatus = $('#status_id_details').val();
                            if(requestStatus == '17'){
                                $('#incItems tbody tr').each(function(index, tr){
                                    var table = $('table.incItems').DataTable();
                                    var data = table.row(index).data();
                                    var row_num = index+1;
                                    item_total = $('#incItems_total').text();
                                    if(data.serialize == 'YES'){
                                        items.push(data.id);
                                        $(this).toggleClass('selected');
                                        item_count++;
                                    }
                                    else{
                                        items_id.push({
                                            item_id: data.item_id,
                                            itemQty: data.qty
                                        });
                                        $(this).toggleClass('selected');
                                        item_count = parseInt(item_count)+parseInt(data.qty);
                                        document.getElementById('incItems').rows[row_num].cells[2].innerHTML = data.qty+' out of '+data.qty;
                                    }
                                });
                            }
                        }
                    });
                }

                if(requestStatus == '14'){
                    $.ajax({
                        type: 'GET',
                        url: '/getReceive',
                        data:{
                            'request_number': $('#request_num_details').val()
                        },
                        success: function(data){
                            document.getElementById("recby").value = data.recby;
                            document.getElementById("recsched").value = moment(data.recsched).format('dddd, MMMM DD, YYYY, h:mm A');
                        }
                    });

                    $('table.asmItems').dataTable().fnDestroy();
                    $('table.asmItems').DataTable({
                        searching: false,
                        paging: false,
                        ordering: false,
                        info: false,
                        language:{
                            processing: "Loading...",
                            emptyTable: "No data available in table"
                        },
                        serverSide: true,
                        ajax:{
                            url: '/asmItems',
                            data:{
                                request_number: req_num,
                            }
                        },
                        order: [],
                        columns: [
                            { data: 'prodcode' },
                            { data: 'item' },
                            { data: 'qty' },
                            { data: 'uom' },
                            { data: 'serial' },
                            { data: 'location' }
                        ],
                        footerCallback: function(row,data,start,end,display){
                            var api = this.api(), data;
                            var intVal = function(i){
                                return typeof i === 'string'?
                                    i.replace(/[\$,]/g,'')*1:
                                    typeof i === 'number'?
                                        i:0;
                            };
                            api.columns('.sum', {page:'all'}).every(function(){
                                var sum = this
                                .data()
                                .reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                }, 0);
                                sum = sum.toString();
                                var pattern = /(-?\d+)(\d{3})/;
                                while(pattern.test(sum))
                                sum = sum.replace(pattern,"$1,$2");
                                this.footer().innerHTML = sum;
                            });
                        }
                    });
                }
            });
        }
    });
}

$('#btnDelete').on('click', function(){
    Swal.fire({
        title: "DELETE ASSEMBLY JOB ORDER?",
        html: "You are about to DELETE your ASSEMBLY JOB ORDER! <br><b style='color: red;'>This will be permanently deleted from the system! CONTINUE?</b>",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            checker = true;
            $('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/deleteRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    $('#loading').hide();
                    if(data == 'true'){
                        $('#detailsAssembly').modal('hide');
                        Swal.fire("DELETE SUCCESS", "ASSEMBLY REQUEST", "success");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                    else{
                        $('#detailsAssembly').modal('hide');
                        Swal.fire("DELETE FAILED", "ASSEMBLY REQUEST", "error");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                }
            });
        }
    });
});

$('.table.prepItems').DataTable().on('select', function(){});
$('.prepItems tbody').on('click', 'tr', function(){
    var requestStatus = $('#status_id_details').val();
    if(requestStatus == '2'){
        return false;
    }
    if(requestStatus == '13'){
        return false;
    }
    if(requestStatus > 13){
        return false;
    }
    var table = $('table.prepItems').DataTable();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    var row_num = $(this).closest('tr').index()+1;
    item_total = $('#prepItems_total').text();

    if(data.serialize == 'YES'){
        if(items.includes(data.id) == true){
            items = items.filter(item => item !== data.id);
            $(this).toggleClass('selected');
            item_count--;
        }
        else{
            items.push(data.id);
            $(this).toggleClass('selected');
            item_count++;
        }
    }
    else{
        var var_item_id = data.item_id;
        var qtyCell = document.getElementById('prepItems').rows[row_num].cells[2].innerHTML;
        if($(this).hasClass('selected')){
            var index = items_id.findIndex(function(element){
                return element.item_id === var_item_id;
            });
            if(index !== -1){
                items_id.splice(index, 1);
            }
            item_count = parseInt(item_count)-parseInt(qtyCell.split(' ')[0]);
            $(this).toggleClass('selected');
            document.getElementById('prepItems').rows[row_num].cells[2].innerHTML = '0 out of '+data.qty;
        }
        else{
            Swal.fire({
                title: "CONFIRM QUANTITY",
                html: '<input class="w3-input w3-border numbersOnly number_limit" id="itemQty" type="number" min="1" max="'+data.qty+'" value="'+data.qty+'">',
                icon: "warning",
                showCancelButton: true,
                cancelButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false
            })
            .then((result) => {
                if(result.isConfirmed){
                    items_id.push({
                        item_id: var_item_id,
                        itemQty: $('#itemQty').val()
                    });
                    $(this).toggleClass('selected');
                    item_count = parseInt(item_count)+parseInt($('#itemQty').val());
                    document.getElementById('prepItems').rows[row_num].cells[2].innerHTML = $('#itemQty').val()+' out of '+data.qty;
                }
            });
        }
    }
});

$('.table.incItems').DataTable().on('select', function(){});
$('.incItems tbody').on('click', 'tr', function(){
    var requestStatus = $('#status_id_details').val();
    if(requestStatus == '17'){
        var table = $('table.incItems').DataTable();
        if(!table.data().any()){ return false; }
        var data = table.row(this).data();
        var row_num = $(this).closest('tr').index()+1;
        item_total = $('#incItems_total').text();

        if(data.serialize == 'YES'){
            if(items.includes(data.id) == true){
                items = items.filter(item => item !== data.id);
                $(this).toggleClass('selected');
                item_count--;
            }
            else{
                items.push(data.id);
                $(this).toggleClass('selected');
                item_count++;
            }
        }
        else{
            var var_item_id = data.item_id;
            var qtyCell = document.getElementById('incItems').rows[row_num].cells[2].innerHTML;
            if($(this).hasClass('selected')){
                var index = items_id.findIndex(function(element){
                    return element.item_id === var_item_id;
                });
                if(index !== -1){
                    items_id.splice(index, 1);
                }
                item_count = parseInt(item_count)-parseInt(qtyCell.split(' ')[0]);
                $(this).toggleClass('selected');
                document.getElementById('incItems').rows[row_num].cells[2].innerHTML = '0 out of '+data.qty;
            }
            else{
                Swal.fire({
                    title: "CONFIRM QUANTITY",
                    html: '<input class="w3-input w3-border numbersOnly number_limit" id="itemQty" type="number" min="1" max="'+data.qty+'" value="'+data.qty+'">',
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: '#3085d6',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    allowOutsideClick: false
                })
                .then((result) => {
                    if(result.isConfirmed){
                        items_id.push({
                            item_id: var_item_id,
                            itemQty: $('#itemQty').val()
                        });
                        $(this).toggleClass('selected');
                        item_count = parseInt(item_count)+parseInt($('#itemQty').val());
                        document.getElementById('incItems').rows[row_num].cells[2].innerHTML = $('#itemQty').val()+' out of '+data.qty;
                    }
                });
            }
        }
    }
});

setInterval(btnReceive, 0);
function btnReceive(){
    if($('#detailsAssembly').is(':visible')){
        var requestStatus = $('#status_id_details').val();
        if(requestStatus == '3' || requestStatus == '17'){
            if(item_count == 0){
                $('.btnReceive').prop('disabled', true);
            }
            else{
                $('.btnReceive').prop('disabled', false);
            }
        }
        if(requestStatus == '12'){
            if(item_count == 0){
                $('#btnAssemble').show();
                $('#btnDefective').hide();
            }
            else{
                $('#btnAssemble').hide();
                $('#btnDefective').show();
            }
        }
    }
}

$('.btnReceive').on('click', function(){
    var inc = 'false';
    var inctype = 'COMPLETE';
    var incnote = '';
    if(item_count < item_total){
        inc = 'true';
        inctype = 'INCOMPLETE';
        incnote = ' <br><b style="color: red;">WARNING: Items for assembly are INCOMPLETE! CONTINUE?</b>';
    }
    Swal.fire({
        title: "RECEIVE "+inctype+" ASSEMBLY PARTS?",
        html: "You are about to RECEIVE these ASSEMBLY PARTS!"+incnote,
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            checker = true;
            $('#loading').show();
            setTimeout(() => {
                $.ajax({
                    type: 'POST',
                    url: '/assembly/receiveRequest',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'assembly_reqnum': $('#asm_request_num_details').val(),
                        'request_type': $('#req_type_id_details').val(),
                        'inc': inc
                    },
                    success: function(data){
                        if(data == 'true'){
                            for(var i=0; i < items.length; i++){
                                $.ajax({
                                    type: 'POST',
                                    url: '/assembly/receiveItems',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num_details').val(),
                                        'request_type': $('#req_type_id_details').val(),
                                        'status': $('#status_id_details').val(),
                                        'id': items[i],
                                        'type': 'single'
                                    }
                                });
                            }
                            for(var i=0; i < items_id.length; i++){
                                $.ajax({
                                    type: 'POST',
                                    url: '/assembly/receiveItems',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num_details').val(),
                                        'request_type': $('#req_type_id_details').val(),
                                        'status': $('#status_id_details').val(),
                                        'items_id': items_id[i],
                                        'type': 'bulk'
                                    }
                                });
                            }
                            $('#detailsAssembly').modal('hide');
                            $.ajax({
                                type: 'POST',
                                url: '/assembly/logReceive',
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'request_type': $('#req_type_id_details').val(),
                                    'status': $('#status_id_details').val(),
                                    'inc': inc
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("RECEIVED "+inctype, "ASSEMBLY REQUEST", "success");
                                        setTimeout(function(){location.href="/assembly"}, 2000);
                                    }
                                    else{
                                        $('#loading').hide();
                                        Swal.fire("RECEIVE FAILED", "ASSEMBLY REQUEST", "error");
                                        setTimeout(function(){location.href="/assembly"}, 2000);
                                    }
                                }
                            });
                        }
                        else{
                            $('#loading').hide();
                            $('#detailsAssembly').modal('hide');
                            Swal.fire("RECEIVE FAILED", "ASSEMBLY REQUEST", "error");
                            setTimeout(function(){location.href="/assembly"}, 2000);
                        }
                    }
                });
            }, 200);
        }
    });
});

$('#btnDefective').on('click', function(){
    generateReqNum();
    Swal.fire({
        title: "REQUEST REPLACEMENTS?",
        html: "You are about to REQUEST REPLACEMENTS for these DEFECTIVE ASSEMBLY PARTS!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            checker = true;
            $('#loading').show();
            setTimeout(() => {
                $.ajax({
                    type: 'POST',
                    url: '/assembly/defectiveRequest',
                    async: false,
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        'request_number': $('#request_num_details').val(),
                        'generatedReqNum': generatedReqNum
                    },
                    success: function(data){
                        if(data == 'true'){
                            for(var i=0; i < items.length; i++){
                                $.ajax({
                                    type: 'POST',
                                    url: '/assembly/defectiveItems',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num_details').val(),
                                        'generatedReqNum': generatedReqNum,
                                        'id': items[i],
                                        'type': 'single'
                                    }
                                });
                            }
                            for(var i=0; i < items_id.length; i++){
                                $.ajax({
                                    type: 'POST',
                                    url: '/assembly/defectiveItems',
                                    async: false,
                                    headers:{
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{
                                        'request_number': $('#request_num_details').val(),
                                        'generatedReqNum': generatedReqNum,
                                        'items_id': items_id[i],
                                        'type': 'bulk'
                                    }
                                });
                            }
                            $('#detailsAssembly').modal('hide');
                            $.ajax({
                                type: 'POST',
                                url: '/assembly/logDefective',
                                headers:{
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data:{
                                    'request_number': $('#request_num_details').val(),
                                    'generatedReqNum': generatedReqNum
                                },
                                success: function(data){
                                    if(data == 'true'){
                                        $('#loading').hide();
                                        Swal.fire("REQUEST SUCCESS", "REPLACEMENT REQUEST", "success");
                                        setTimeout(function(){location.href="/assembly"}, 2000);
                                    }
                                    else{
                                        $('#loading').hide();
                                        Swal.fire("REQUEST FAILED", "REPLACEMENT REQUEST", "error");
                                        setTimeout(function(){location.href="/assembly"}, 2000);
                                    }
                                }
                            });
                        }
                        else{
                            $('#loading').hide();
                            $('#detailsAssembly').modal('hide');
                            Swal.fire("REQUEST FAILED", "REPLACEMENT REQUEST", "error");
                            setTimeout(function(){location.href="/assembly"}, 2000);
                        }
                    }
                });
            }, 200);
        }
    });
});

$('#btnAssemble').on('click', function(){
    var item_desc_details = decodeHtml($('#item_desc_details').val());
    Swal.fire({
        title: "ASSEMBLE: "+item_desc_details+"?",
        html: "You are about to ASSEMBLE this Assembly Job Order!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false
    })
    .then((result) => {
        if(result.isConfirmed){
            checker = true;
            $('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/assembly/assembleRequest',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    'request_number': $('#request_num_details').val()
                },
                success: function(data){
                    $('#loading').hide();
                    if(data == 'true'){
                        $('#detailsAssembly').modal('hide');
                        Swal.fire("ASSEMBLE SUCCESS", "ASSEMBLY REQUEST", "success");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                    else{
                        $('#detailsAssembly').modal('hide');
                        Swal.fire("ASSEMBLE FAILED", "ASSEMBLY REQUEST", "error");
                        setTimeout(function(){location.href="/assembly"}, 2000);
                    }
                }
            });
        }
    });
});

$('#btnPending').on('click', function(){
    $.ajax({
        type: 'GET',
        url: '/getLink',
        data:{
            'request_number': $('#request_num_details').val()
        },
        success: function(data){
            window.location.href = '/assembly?request_number='+data;
        }
    });
});