var y = 1;
var table;
var intransittable;
var dtdata;
var sub = 0;
var add = 0;
var test = 0;
var pendingreq;
var requestno;
var stockcat;
var servicecat;
var reqno;
var checkrequest = 'wala';
var reqstock;
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
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Stock Request\"",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span>'
        },
        processing: true,
        serverSide: true,
        ajax: 'requests',
        columns: [
            { data: 'created_at', name:'created_at', "width": "14%" },
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"},
            { data: 'ticket', name:'ticket', "width": "14%"}
        ]
    });

    $('#requestTable tbody').on('click', 'tr', function () {
        var trdata = table.row(this).data();
        dtdata = table.row(this).data();
        requestno = trdata.request_no;
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
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
            $('#clients').val(trdata.client.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
            $('#customers').val(trdata.customer.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
            $('#tickets').val(trdata.ticket);
        }
        $('table.requestDetails').dataTable().fnDestroy();
        $('table.schedDetails').dataTable().fnDestroy();
        $('table.intransitDetails').dataTable().fnDestroy();
        if (trdata.status == 'PENDING') {
            $('table.schedDetails').hide();
            $('table.intransitDetails').hide();
            $('table.requestDetails').show();
            $('.sched').hide();
            $('#del_Btn').show();
            if ($('#userlevel').val() != 'Head') {
                $('#del_Btn').hide();
            }
            $('#msg').hide();
            $('#rec_Btn').hide();
            $('#schedslabel').hide();
            $('#intransitlabel').hide();
            $('#intransitrow').hide();
            $('#not_rec_Btn').hide();
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
                            { data: 'cat_name', name:'cat_name'},
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
                            { data: 'cat_name', name:'cat_name'},
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
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'IN TRANSIT' || trdata.status == 'PARTIAL IN TRANSIT'){
            $('table.requestDetails').remove();
            $('table.schedDetails').remove();
            $('table.requestDetails').dataTable().fnDestroy();
            $('.sched').show();
            $('table.intransitDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#intransitlabel').show();
            $('#schedslabel').hide();
            $('#reqlabel').hide();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            $('#intransitrow').show();
            $('#intransitsched').val(trdata.intransit);
            var intransit;
            Promise.all([intransitfunc()]).then(() => { 
                if (intransit == 1) {
                    $('#not_rec_Btn').show();
                }else{
                    $('#not_rec_Btn').hide();
                }
                if (intransit <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (intransit > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        columns: [
                            { data: 'schedule', name:'schedule'},
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
            function intransitfunc() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        intransit = data.data.length;
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'PARTIAL SCHEDULED' && trdata.intransitval == '1'){
            $('table.requestDetails').hide();
            $('.sched').show();
            $('table.schedDetails').hide();
            $('table.intransitDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#reqlabel').hide();
            $('#schedslabel').hide();
            $('#rec_Btn').hide();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            $('#intransitrow').show();
            $('#intransitsched').val(trdata.intransit);
            var intransit;
            Promise.all([intrans()]).then(() => { 
                if (intransit == 1) {
                    $('#not_rec_Btn').show();
                }else{
                    $('#not_rec_Btn').hide();
                }
                if (intransit <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }else if (intransit > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/send/"+trdata.request_no,
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                }
            });
            function intrans() {
                return $.ajax({
                    type:'get',
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        intransit = data.data.length;
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'SCHEDULED' || (trdata.status == 'PARTIAL SCHEDULED' && trdata.intransitval != '1') || trdata.status == 'RESCHEDULED'){
            $('table.requestDetails').hide();
            $('table.intransitDetails').hide();
            console.log('ito');
            $('.sched').show();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').hide();
            $('#reqlabel').hide();
            $('#intransitlabel').hide();
            $('#msg').hide();
            $('#rec_Btn').prop('disabled', true);
            $('#not_rec_Btn').hide();
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
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
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
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ]
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
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'INCOMPLETE'){
            $('table.requestDetails').hide();
            $('table.schedDetails').hide();
            $('.sched').show();
            $('table.intransitDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#intransitrow').show();
            $('#intransitsched').val(trdata.intransit);
            $('#reqlabel').hide();
            $('#schedslabel').hide();
            $('#msg').show();
            $('#not_rec_Btn').hide();
            $('#rec_Btn').prop('disabled', true);
            var incomp;
            Promise.all([incompleteschedtable()]).then(() => { 
                if (incomp <= 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
                            { data: 'item_name', name:'item_name'},
                            { data: 'quantity', name:'quantity'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                }else if (incomp > 10) {
                    $('table.intransitDetails').dataTable().fnDestroy();
                    intransittable = 
                    $('table.intransitDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/intransit/"+trdata.request_no,
                        
                        columns: [
                            { data: 'schedule', name:'schedule'},
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
                    url: "/intransit/"+trdata.request_no,
                    success:function(data)
                    {
                        incomp = data.data.length;
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'IN TRANSIT'){
            $('table.requestDetails').hide();
            $('.sched').show();
            $('table.schedDetails').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var resched;
            Promise.all([rescheduleschedtable()]).then(() => { 
                if (resched == 1) {
                    $('#not_rec_Btn').show();
                }else{
                    $('#not_rec_Btn').hide();
                }
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
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }else if(trdata.status == 'PARTIAL'){
            $('table.requestDetails').show();
            $('table.schedDetails').hide();
            $('table.intransitDetails').hide();
            $('.sched').show();
            $('#sched').val(trdata.sched);
            $('#del_Btn').hide();
            $('#rec_Btn').show();
            $('#msg').show();
            $('#rec_Btn').prop('disabled', true);
            var partial;
            Promise.all([partialschedtable()]).then(() => {
                if (partial == 1) {
                    $('#not_rec_Btn').show();
                }else{
                    $('#not_rec_Btn').hide();
                }
                if (partial == 0) {
                    $('.sched').hide();
                    $('#msg').hide();
                    $('table.schedDetails').dataTable().fnDestroy();
                    $('table.schedDetails').hide();
                    $('table.requestDetails').show();
                    $('#intransitlabel').hide();
                    $('#schedslabel').hide();
                    $('#reqlabel').show();
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
                            error: function (data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                                alert(data.responseText);
                            }
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
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        }
        $('#requestModal').modal('show');
    });
    $('#reqBtn').prop('disabled', true);
    $('#loading').show();

    setTimeout(function() { 
        $('#reqBtn').prop('disabled', false);
        $('#loading').hide();
    }, 5000);
    
    $('table.intransitDetails').DataTable().on('select', function () {
        var rowselected = intransittable.rows( { selected: true } ).data();
        if(rowselected.length > 0){
            $('#rec_Btn').prop('disabled', false);
            $('#not_rec_Btn').prop('disabled', true);
        }
    });
    $('table.intransitDetails').DataTable().on('deselect', function () {
        var rowselected = intransittable.rows( { selected: true } ).data();
        if(rowselected.length == 0){
            $('#rec_Btn').prop('disabled', true);
            $('#not_rec_Btn').prop('disabled', false);
        }
    });

    $.ajax({
        type:'get',
        url:'checkService',
        success:function(data)
        {   
            if (data.length != "0") {
                var category = $.map(data, function(value, index) {
                    return [value];
                });
                servicecat = category;
            }
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
    $.ajax({
        type:'get',
        url:'checkStock',
        success:function(data)
        {
            var category = $.map(data, function(value, index) {
                return [value];
            });
            stockcat = category;
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});

$(document).on('keyup', '#ticket', function () {
    $(this).val($(this).val().toUpperCase());
    if ($(this).val().length >= 3 && !/[a-zA-Z]/i.test($(this).val())) {
        alert('Incorrect Ticket number Format!');
        $(this).val('');
    }
});

$(document).on('click', '#not_rec_Btn', function(){
    $.ajax({
        url: 'notrec',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            reqno : requestno
        },
        success:function()
        {
            location.reload();
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
