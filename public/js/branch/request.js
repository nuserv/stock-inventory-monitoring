var y = 1;
var table;
var schedtable;
var dtdata;
var sub = 0;
var add = 0;
var test = 0;
var pendingreq;
$(document).ready(function()
{
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
            "emptyTable": 'No stock request found.',
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
        },
        processing: true,
        serverSide: true,
        ajax: 'requests',
        columns: [
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'ticket', name:'ticket', "width": "14%"},
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"}
        ]
    });
    $('#requestTable tbody').on('click', 'tr', function () { 
        var trdata = table.row(this).data();
        dtdata = table.row(this).data();;
        $('#date').val(trdata.created_at);
        $('#reqno').val(trdata.request_no);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('#requesttypes').val(trdata.type);
        if (trdata.type == "STOCK") {
            $('.ticketno').hide();
            $('#clientrows').hide();
        }else{
            $('.ticketno').show();
            $('#clientrows').show();
            $('#clients').val(trdata.client);
            $('#customers').val(trdata.customer);
            $('#tickets').val(trdata.ticket);
        }
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();
        if (trdata.status == 'PENDING') {
            $('table.schedDetails').hide();
            $('table.requestDetails').show();
            $('.sched').hide();
            $('#del_Btn').show();
            $('#msg').hide();
            $('#rec_Btn').hide();
            $('#del_Btn').attr('reqno', trdata.request_no);
            var penreq;
            Promise.all([pendingrequest()]).then(() => { 
                if (penreq <= 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                }else if (penreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    pendingreq = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                }
            });
            function pendingrequest() {
                return $.ajax({
                    type:'get',
                    url: "/requests/"+trdata.request_no,
                    success:function(data)
                    {
                        penreq = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'SCHEDULED'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var schedul;
            Promise.all([sched()]).then(() => { 
                if (schedul <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (schedul > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function sched() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        schedul = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var incomp;
            Promise.all([incompleteschedtable()]).then(() => { 
                if (incomp <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (incomp > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function incompleteschedtable() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        incomp = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'RESCHEDULED'){
            $('table.requestDetails').hide();
            $('.sched').hide();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var resched;
            Promise.all([rescheduleschedtable()]).then(() => { 
                if (resched <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (resched > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function rescheduleschedtable() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        resched = data.data.length;
                    },
                });
            }
        }else if(trdata.status == 'PARTIAL'){
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            $('.sched').hide();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var partial;
            Promise.all([partialschedtable()]).then(() => {
                if (partial == 0) {
                    $('.sched').hide();
                    $('#msg').hide();
                    $('table.schedDetails').dataTable().fnDestroy();
                    $('table.schedDetails').hide();
                    $('table.requestDetails').show();
                    var requestdet;
                    Promise.all([partialrequesttable()]).then(() => {
                        if (requestdet <= 10) {
                            $('table.requestDetails').dataTable().fnDestroy();
                            requesttable = $('table.requestDetails').DataTable({ 
                                "dom": 'rt',
                                "language": {
                                    "emptyTable": " "
                                },
                                processing: true,
                                serverSide: true,
                                ajax: "/requests/"+trdata.request_no,
                                columns: [
                                    { data: 'items_id', name:'items_id'},
                                    { data: 'item_name', name:'item_name'},
                                    { data: 'qty', name:'qty'}
                                ]
                            });
                        }else if (requestdet > 10){
                            $('table.requestDetails').dataTable().fnDestroy();
                            requesttable = $('table.requestDetails').DataTable({ 
                                "dom": 'lrtip',
                                "language": {
                                    "emptyTable": " "
                                },
                                processing: true,
                                serverSide: true,
                                ajax: "/requests/"+trdata.request_no,
                                columns: [
                                    { data: 'items_id', name:'items_id'},
                                    { data: 'item_name', name:'item_name'},
                                    { data: 'qty', name:'qty'}
                                ]
                            });
                        }
                    });
                    function partialrequesttable() {
                        return $.ajax({
                            type:'get',
                            url: "/requests/"+trdata.request_no,
                            success:function(data)
                            {
                                requestdet = data.data.length;
                            },
                        });
                    }
                    $('#rec_Btn').hide();
                }else if (partial <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (partial > 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
                    schedtable = 
                    $('table.schedDetails').DataTable({ 
                        "dom": 'lrtip',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'items_id', name:'items_id'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }
            });
            function partialschedtable() {
                return $.ajax({
                    type:'get',
                    url: "/send/"+trdata.request_no,
                    success:function(data)
                    {
                        partial = data.data.length;
                    },
                });
            }
        }
        $('#requestModal').modal('show');
    });
    $('table.schedDetails').DataTable().on('select', function () {
        var rowselected = schedtable.rows( { selected: true } ).data();
        if(rowselected.length > 0){
            $('#rec_Btn').prop('disabled', false);
        }
    });
    $('table.schedDetails').DataTable().on('deselect', function () {
        var rowselected = schedtable.rows( { selected: true } ).data();
        if(rowselected.length == 0){
            $('#rec_Btn').prop('disabled', true);
        }
    });
});