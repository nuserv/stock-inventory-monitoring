var r = 1;
var y = 1;
var interval = null;
var requestno;
$(document).ready(function()
{
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    var table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 50,
        "language": {
            "emptyTable": "No data found!",
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Stock Request\"",
        },
        "order": [[ 6, 'asc'], [ 0, 'desc']],
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
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
        processing: true,
        serverSide: true,
        ajax: 'requests',
        columns: [
            { data: 'id', name:'id'},
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'status', name:'status', "width": "14%"},
            { data: 'ticket', name:'ticket', "width": "14%"}
        ]
    });
    $('#requestTable tbody').on('click', 'tr', function () {
        var trdata = table.row(this).data();
        var trsched = new Date(trdata.sched);
        requestno = trdata.request_no;
        $('.notes').hide();
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
        $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('#reqbranch').val(trdata.branch_id);
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
        if (trdata.status == 'IN TRANSIT' || trdata.status == 'INCOMPLETE') {
            $('#printBtn').hide();
            $('#reqlabel').remove();
            $('#schedslabel').remove();
            $('table.requestDetails').remove();
            $('table.schedDetails').remove();
            var intransit;
            Promise.all([intrans()]).then(() => {
                if (intransit <= 10) {
                    $('table.schedDetails').dataTable().fnDestroy();
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
                        ]
                    });
                }else if (intransit > 10) {
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
                });
            }
        }else if (trdata.status == 'SCHEDULED' || trdata.status == 'RESCHEDULED') {
            $('#printBtn').show();
            $('#reqlabel').remove();
            $('#intransitlabel').remove();
            $('table.requestDetails').remove();
            $('table.intransitDetails').remove();
            var trsched = new Date(trdata.sched);
            $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
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
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ],
                    });
                }else if (partreq > 10) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
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
                console.log('pasok');
                if (partreq == 0) {
                    $('table.requestDetails').dataTable().fnDestroy();
                    $('table.requestDetails').hide();
                    //$('#reqlabel').remove();
                    //$('#printBtn').show();
                    //$('#prcBtn').hide();
                }else if (partreq <= 10) {
                    console.log('nandito');
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'rt',
                        "language": {
                            "emptyTable": " "
                        },
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ]
                    });
                }else if (partreq > 10) {
                    console.log('dito');
                    $('table.requestDetails').dataTable().fnDestroy();
                    requestdetails = 
                    $('table.requestDetails').DataTable({ 
                        "dom": 'lrtp',
                        "language": {
                            "emptyTable": " "
                        },
                        processing: true,
                        serverSide: true,
                        ajax: "/requests/"+trdata.request_no,
                        columns: [
                            { data: 'item_name', name:'item_name'},
                            { data: 'qty', name:'qty'}
                        ],
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
        }else if (trdata.status == 'PENDING') {
            $('#prcBtn').show();
            $('.sched').hide();
            $('#sched').val('');
            $('#printBtn').hide();
            $('#schedslabel').hide();
            $('#intransitlabel').hide();
            $('#save_Btn').show();
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.intransitDetails').dataTable().fnDestroy();
            $('table.requestDetails').show();
            $('table.schedDetails').hide();
            $('table.intransitDetails').hide();
            $('table.schedDetails').dataTable().fnDestroy();
            $('.sched').hide();
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
        }else if (trdata.status == 'RESOLVED') {
            $('.notes').show();
            $('#notes').text(trdata.remarks);
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.requestDetails').hide();
            $('table.schedDetails').show();
            $('.sched').show();
            $('table.schedDetails').DataTable({ 
                "dom": 'rt',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/send/"+trdata.request_no,
                columnDefs: [
                    {"className": "dt-center", "targets": "_all"}
                ],
                columns: [
                    { data: 'items_id', name:'items_id'},
                    { data: 'item_name', name:'item_name'},
                    { data: 'quantity', name:'quantity'},
                    { data: 'serial', name:'serial'}
                ]
            });
        }else{
            if (trdata.status == 'UNRESOLVED') {
                $('.notes').show();
                $('#notes').text('The five days given to resolve the issue has lapsed [Since '+moment(trdata.updated_at).format("dddd, MMMM D, YYYY")+']. To resolve the issue. A discussion with the warehouse team is recommended and input the remarks in the text field provided for the solution.');
            }
            $('#schedslabel').hide();
            $('#reqlabel').hide();
            $('table.schedDetails').dataTable().fnDestroy();
            $('table.requestDetails').dataTable().fnDestroy();
            $('table.intransitDetails').dataTable().fnDestroy();
            $('table.intransitDetails').show();
            $('table.schedDetails').hide();
            $('table.requestDetails').hide();
            $('.sched').show();
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
                ]
            });
        }
        $('#requestModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});
$(document).on("click", ".intransitDetails tr", function() {
    if ($('#status').val() == 'UNRESOLVED' && $('#level').val() == 'Manager') {
        $('#remarksModal').modal('show');
        $('#requestModal').modal('hide');
    }
});
$(document).on('click', '.close', function(){
    window.location.href = 'request';
});
$('#remarks_btn').prop('disabled', true);
$(document).on('keypress', '#remarkstext', function(){
    if ($(this).val().length >= 9) {
        $('#remarks_btn').prop('disabled', false);
    }else{
        $('#remarks_btn').prop('disabled', true);
    }
});

$(document).on('click', '#remarks_btn', function(){
    $.ajax({
        url: 'resolved',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            requestno: requestno,
            remarks: $('#remarkstext').val()
        },
        success: function (data) {
            window.location.href = 'request';
        },
        error: function (data) {
            alert(data.responseText);
            return false;
        }
    });
});
