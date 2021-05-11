var r = 1;
var y = 1;
var c = 1;
var w = 0;
var uomcount = 0;
var uomarray = new Array();
var bID;
var sub = 0;
var save = 0;
var pcount = 0;
var requestdetails;
var intransitdets;
var prepared_id;
var stat = "notok";
var pending = 0;
var check = false;
var requestgo;
var reqnumber;
var valpartial;
var valid = 'yes';
var table;
var serialnum;

$(document).ready(function()
{
    $("#datesched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
    $("#resched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 25,
        "language": {
            "emptyTable": "No stock request found!",
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Stock Request\"",
        },
        "fnRowCallback": function(nRow, aData) {
        //"createdRow": function ( nRow, aData ) {
            if (aData.schedule && (aData.status == "SCHEDULED" || aData.status == "RESCHEDULED")) {
                var scheddate = aData.schedule
                var datesplited = scheddate.split("/");;
                var setsched = datesplited[2]+datesplited[0]+datesplited[1];
                var today = new Date().toISOString().slice(0,10).split('-');
                var syncdate = today[0]+today[1]+today[2];
                if (setsched <= syncdate) {
                    $('td', nRow).eq(4).css('color', 'darkmagenta');
                    $('td', nRow).eq(4).css('font-weight', 'bold');
                }
            }
            if (!aData.schedule && aData.status == "PENDING" && aData.type == "SERVICE") {
                var created = aData.leftcreatedmin;
                if (created <= 0) {
                    $('td', nRow).css('background-color', 'lightgray');
                    $('td', nRow).css('font-weight', 'bold');
                }
            }
            if (aData.schedule && (aData.status == "PARTIAL SCHEDULED")) {
                var scheddate = aData.schedule
                var datesplited = scheddate.split("/");;
                var setsched = datesplited[2]+datesplited[0]+datesplited[1];
                var today = new Date().toISOString().slice(0,10).split('-');
                var syncdate = today[0]+today[1]+today[2];
                if (setsched <= syncdate) {
                    $('td', nRow).eq(4).css('color', 'darkmagenta');
                    $('td', nRow).eq(4).css('font-weight', 'bold');
                }
            }
            if (aData.schedule && (aData.status == "PARTIAL IN TRANSIT" && aData.intransitval == '1')) {
                var scheddate = aData.schedule
                var datesplited = scheddate.split("/");;
                var setsched = datesplited[2]+datesplited[0]+datesplited[1];
                var today = new Date().toISOString().slice(0,10).split('-');
                var syncdate = today[0]+today[1]+today[2];
                if (setsched <= syncdate) {
                    $('td', nRow).eq(4).css('color', 'darkmagenta');
                    $('td', nRow).eq(4).css('font-weight', 'bold');
                }
            }
            
            if ( aData.status == "UNRESOLVED" || aData.status == "INCOMPLETE") {        
                $('td', nRow).eq(4).css('color', '#F1423A');
                $('td', nRow).eq(4).css('font-weight', 'bold');
            }
            if (aData.type == "SERVICE" && aData.status == 'PENDING') {
                $('td', nRow).eq(4).css('color', 'blue');
                $('td', nRow).eq(4).css('font-weight', 'bold');
            }
            if (aData.type == "STOCK" && aData.status == 'PENDING') {
                $('td', nRow).eq(4).css('color', 'GREEN');
                $('td', nRow).eq(4).css('font-weight', 'bold');
            }
        },
        "order": [ 0, 'asc'],
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'requests',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'id', name:'id'},
            { data: 'created_at', name:'created_at', "width": "14%" },
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"},
            { data: 'ticket', name:'ticket', "width": "14%"}
        ]
    });

    $('#requestTable tbody').on('click', 'tr', function () { 
        console.log('ito');
        var trdata = table.row(this).data();
        bID = trdata.branch_id
        reqnumber = trdata.request_no;
        valpartial = trdata.intransitval;
        $('.notes').hide();
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
        $('#requesttypes').val(trdata.type);
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();
        $('table.intransitDetails').dataTable().fnDestroy();
        if (trdata.type == "STOCK") {
            $('.ticketno').hide();
            $('#clientrows').hide();
        }else{
            $('.ticketno').show();
            $('#clientrows').show();
            $('#clients').val(trdata.client.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&'));
            $('#customers').val(trdata.customer.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&'));
            $('#tickets').val(trdata.ticket);
        }
        $('#schedbyrow').hide();
        if (trdata.status != 'PENDING') {
            if (trdata.schedby) {
                $('#schedby').val(trdata.schedby);
                $('#schedbyrow').show();
            }
        }
        if (trdata.status == 'SCHEDULED') {
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            $('#intransitrow').hide();
            $('#intransitBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if (trdata.status == 'RESCHEDULED') {
            $('#prcBtn').hide();
            $('#schedlabel').text('Reschedule on:')
            $('#intransitBtn').show();
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if (trdata.status == 'IN TRANSIT') {
            $('#prcBtn').hide();
            $('#intransitrow').show();
            $('#intransitBtn').hide();
            $('#intransitsched').val(trdata.intransit);
            $('.sched').show();
            $('#printBtn').show();
            $('#save_Btn').hide();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if(trdata.status == 'PENDING'){
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#save_Btn').show();
        }else if(trdata.status == 'UNRESOLVED'){
            $('#printBtn').show();
            $('#printBtn').val('PRINT');
            $('.sched').hide();
            $('.notes').show();
            $('#notes').text('Please be informed that the current status is now UNRESOLVED after the five days given to resolve the issue. Kindly contact the manager to resolve the issue.');
            $('#intransitlabel').remove();
            $('#schedslabel').remove();
            $('table.requestDetails').hide();
            $('table.intransitDetails').show();
            $('table.schedDetails').remove();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.intransitDetails').DataTable({ 
                "dom": 't',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/intransit/"+trdata.request_no,
                columns: [
                    { data: 'item_name', name:'item_name'},
                    { data: 'quantity', name:'quantity'},
                    { data: 'serial', name:'serial'},
                    { data: null}
                ],
                columnDefs: [
                    {
                        "targets": [ 3 ],
                        "visible": false
                    }
                ],
            });
            //$('#unresolveBtn').hide();
            $('#prcBtn').hide();
        }else if(trdata.status == 'PARTIAL SCHEDULED'){
            $('#prcBtn').show();
            $('.sched').show();
            $('#printBtn').hide();
            $('#intransitBtn').show();
            $('#save_Btn').show();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }else if(trdata.status == 'PARTIAL IN TRANSIT'){
            $('#prcBtn').show();
            $('.sched').show();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#schedslabel').remove();
            $('table.intransitDetails').show();
            $('table.schedDetails').remove();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
            if (valpartial == '1') {
                $('#intransitBtn').show();
            }else{
                $('#intransitBtn').hide();
            }
            
            $('#save_Btn').show();
        }else if(trdata.status == 'INCOMPLETE'){
            $('#prcBtn').hide();
            $('.sched').show();
            $('#printBtn').show();
            var trsched = new Date(trdata.sched);
            $('.notes').show();
            if (trdata.left != 0) {
                trdata.left++;
                if (trdata.left > 1) {
                    var withs = 'days';
                }else if(trdata.left == 1){
                    trdata.hour = trdata.hour-24;
                    var withs = 'day and '+toWords(trdata.hour)+'('+trdata.hour+') hours';
                    if (trdata.hour == 1) {
                        var withs = 'day and '+toWords(trdata.hour)+'('+trdata.hour+') hour';
                    }
                }
                $('#notes').text('Please be informed that you only have '+toWords(trdata.left)+'('+trdata.left+') '+withs+' to resolve this issue. Once the '+toWords(trdata.left)+'('+trdata.left+') '+withs+' given has elapsed, the status of this issue will be automatically converted to UNRESOLVE.');
            }else{
                if (trdata.left == 0) {
                    if (trdata.hour != 0) {
                        var withs = 'hours';
                        trdata.left = trdata.hour;
                        if (trdata.hour == 1) {
                            trdata.minute = trdata.minute-60;
                            var withs = 'hour and '+toWords(trdata.minute)+'('+trdata.minute+') minutes';
                            if (trdata.minute == 1) {
                                var withs = 'hour and '+toWords(trdata.minute)+'('+trdata.minute+') minute';
                            }
                        }
                    }else{
                        var withs = 'minutes';
                        trdata.left = trdata.minute;
                    }
                }
                $('#notes').text('Please be informed that you only have '+toWords(trdata.left)+'('+trdata.left+') '+withs+' to resolve this issue. Once the '+toWords(trdata.left)+'('+trdata.left+') '+withs+' given has elapsed, the status of this issue will be automatically converted to UNRESOLVE.');
            }
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        }
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();

        if (trdata.status == 'PENDING') {
            $('#printBtn').remove();
            $('table.schedDetails').remove();
            //$('#unresolveBtn').hide();
            $('table.requestDetails').show();
            $('#schedslabel').remove();
            $('#intransitlabel').remove();
            $('table.intransitDetails').remove();
            var pendreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (pendreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'cat_name', name:'cat_name'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (pendreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'cat_name', name:'cat_name'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        pendreq = data.data.length;
                    },
                });
            }
        }else if (trdata.status == 'PARTIAL SCHEDULED' && (trdata.intransitval == '0' || !trdata.intransitval)) {
            $('#printBtn').hide();
            console.log('test');
            //$('#unresolveBtn').hide();
            $('#intransitlabel').remove();
            $('table.intransitDetails').remove();
            $('table.requestDetails').show();
            $('table.schedDetails').show();
            var partreq;
            Promise.all([partialrequest()]).then(() => { 
                if (partreq == 0) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    $('table.requestDetails').hide();
                    $('#prcBtn').hide();
                    $('#reqlabel').remove();
                    $('#printBtn').show();

                }else if (partreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'cat_name', name:'cat_name'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (partreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'cat_name', name:'cat_name'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        partreq = data.data.length;
                        console.log(data);
                    },
                });
            }
            var schedreq;
            Promise.all([schedrequest()]).then(() => {
                if (schedreq <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (schedreq > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });

            function schedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedreq = data.data.length;
                    },
                });
            }
        }else if (trdata.status == 'PARTIAL SCHEDULED' && trdata.intransitval == '1') {
            $('#printBtn').hide();
            console.log('test2');
            //$('#unresolveBtn').hide();
            $('table.requestDetails').show();
            $('table.schedDetails').show();
            $('table.intransitDetails').hide();
            $('#intransitlabel').remove();
            var partreq;
            Promise.all([partialrequest()]).then(() => { 
                if (partreq == 0) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    $('table.requestDetails').hide();
                    $('#reqlabel').remove();
                    $('#printBtn').show();
                    $('#prcBtn').hide();
                }else if (partreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'cat_name', name:'cat_name'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (partreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        partreq = data.data.length;
                    },
                });
            }
            var schedreq;
            Promise.all([schedrequest()]).then(() => {
                if (schedreq <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (schedreq > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function schedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedreq = data.data.length;
                    },
                });
            }

            var intransitreq;
            Promise.all([intransitrequest()]).then(() => {
                if (intransitreq <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdetails =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (intransitreq > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdetails =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function intransitrequest() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        intransitreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('#printBtn').show();
            $('#reqlabel').remove();
            $('#intransitlabel').remove();
            $('table.requestDetails').remove();
            $('table.intransitDetails').remove();
            //$('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var schedreq;
            Promise.all([schedrequest()]).then(() => {
                if (schedreq <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (schedreq > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function schedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'IN TRANSIT'){
            $('#printBtn').hide();
            $('table.requestDetails').remove();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.schedDetails').remove();
            $('#schedslabel').remove();
            $('#reqlabel').remove();
            $('#intransitlabel').show();
            $('table.intransitDetails').show();
            //$('#unresolveBtn').hide();
            var intransitreq;
            Promise.all([intransitrequest()]).then(() => {
                if (intransitreq <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdets =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (intransitreq > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdets =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                    console.log(data);
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function intransitrequest() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        intransitreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'PARTIAL IN TRANSIT'){
            $('#printBtn').hide();
            $('#prcBtn').hide();
            $('table.schedDetails').remove();
            $('#schedslabel').remove();
            $('#intransitlabel').show();
            $('table.intransitDetails').show();
            //$('#unresolveBtn').hide();
            var intransitreq;
            Promise.all([intransitrequest()]).then(() => {
                if (intransitreq <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdets =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (intransitreq > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdets =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function intransitrequest() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        intransitreq = data.data.length;
                    },
                });
            }
            var pendreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (pendreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (pendreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        "fnRowCallback": function(nRow, aData) {
                            if (aData.validation == 'no') {
                                valid = 'no';
                            }
                        },
                        processing: true,
                        serverSide: false,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'},
                            { data: 'stockuom', name:'stockuom'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        pendreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('#printBtn').show();
            $('#reqlabel').remove();
            $('#intransitlabel').remove();
            $('table.requestDetails').remove();
            $('table.intransitDetails').remove();
            //$('#unresolveBtn').hide();
            $('table.schedDetails').show();
            var resched;
            Promise.all([reschedrequest()]).then(() => {
                if (resched <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'},
                            { data: null}
                        ],
                        columnDefs: [
                            {
                                "targets": [ 3 ],
                                "visible": false
                            }
                        ],
                    });
                }else if (resched > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    scheddetails =
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'},
                            { data: null}
                        ],
                        columnDefs: [
                            {
                                "targets": [ 3 ],
                                "visible": false
                            }
                        ],
                    });
                }
            });

            function reschedrequest() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        resched = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('#printBtn').show();
            $('#intransitrow').show();
            $('#intransitsched').val(trdata.intransit);
            $('#printBtn').val("RESCHEDULE");
            //$('#unresolveBtn').show();
            $('#intransitlabel').remove();
            $('#schedslabel').remove();
            $('table.requestDetails').remove();
            $('table.intransitDetails').show();
            $('table.schedDetails').remove();
            var incomp;
            Promise.all([incompleterequest()]).then(() => {
                if (incomp <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdetails =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }else if (incomp > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransitdetails =
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        "columnDefs": [
                            {   
                                "render": function ( data, type, row, meta ) {
                                        return '<button id="editBtn" class="btn-primary editBtn" serial_num="'+data.serial+'" prepared_id="'+data.items_id+'">Edit Serial</button>';
                                },
                                "defaultContent": '',
                                "data": null,
                                "targets": [3]
                            }
                        ]
                    });
                }
            });

            function incompleterequest() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        incomp = data.data.length;
                    },
                });
            }
        }
        
        $('#requestModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    searchtable =
    $('table.searchtable').DataTable({ 
        "dom": 't',
        "language": {
            "emptyTable": " "
        },
        "pageLength": 25,
        "order": [[ 1, "asc" ]],
        processing: true,
        serverSide: true,
        ajax: {
            "url": 'searchserial',
            error: function (data) {
                alert(data.responseText);
            }
        },
        columns: [
            { data: 'created_at', name:'date'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'branch', name:'branch'},
            { data: 'user', name:'user'}
        ]
    });
});

$(document).on("click", ".editBtn", function() {
    prepared_id = $(this).attr('prepared_id');
    serialnum = $(this).attr('serial_num');
    $('#serialModal').modal('show');
    $('#editserial').val(serialnum);
    $('#serial_btn').prop('disabled', true);
});

$(document).on("click", "#serial_btn", function() {
    $.ajax({
        url: 'update_serial',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            old: serialnum,
            new: $('#editserial').val()
        },
        success: function(data) {
            console.log(data);
            alert('Serial Number updated');
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);

        }
    });
});
$(document).on("keyup", "#searchall", function () {
    table.search(this.value).draw();
});
$(document).on("keyup", "#editserial", function () {
    if ($(this).val() == serialnum) {
        $('#serial_btn').prop('disabled', true);
    }else{
        $('#serial_btn').prop('disabled', false);
    }
    if ($(this).val().toLowerCase() ==  "n/a" || $(this).val().toLowerCase() ==  "faded" || $(this).val().toLowerCase() ==  "none") {
        $.ajax({
            url: 'checkserial',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'get',
            async: false,
            data: {
                item: prepared_id
            },
            success: function (data) {
                if (data != "allowed") {
                    $('#editserial').val(serialnum);
                    $('#serial_btn').prop('disabled', true);
                    alert('n/a not allowed!')
                }
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }
});

var th = ['','thousand','million', 'billion','trillion'];
// uncomment this line for English Number System
// var th = ['','thousand','million', 'milliard','billion'];

var dg = ['zero','one','two','three','four', 'five','six','seven','eight','nine'];
var tn = ['ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen', 'seventeen','eighteen','nineteen']; 
var tw = ['twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety']; 
function toWords(s){
    s = s.toString(); 
    s = s.replace(/[\, ]/g,''); 
    if (s != parseFloat(s)) return 'not a number'; 
    var x = s.indexOf('.'); 
    if (x == -1) x = s.length; 
    if (x > 15) return 'too big'; 
    var n = s.split(''); 
    var str = ''; 
    var sk = 0; 
    for (var i=0; i < x; i++) {
        if ((x-i)%3==2) {
            if (n[i] == '1') {
                str += tn[Number(n[i+1])] + ' '; 
                i++; 
                sk=1;
            } else if (n[i]!=0) {
                str += tw[n[i]-2] + ' ';
                sk=1;
            }
        } else if (n[i]!=0) {
            str += dg[n[i]] +' '; 
            if ((x-i)%3==0) str += 'hundred ';
            sk=1;
        } if ((x-i)%3==1) {
            if (sk) str += th[(x-i-1)/3] + ' ';
            sk=0;
        }
    } 
    if (x != s.length) {
        var y = s.length; 
        str += 'point '; 
        for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';
    } 
    return str.replace(/\s+/g,' ');
}
