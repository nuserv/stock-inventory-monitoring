var table;
var send = 1;
var retno;
var rowcount;
var pullout;
var items;
var pull_no;
var reqDetails;
var reqDate;
var senditems;
var buffersenditems;
var user, dtstat;

$(document).ready(function()
{
    table =
    $('table.bufferTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No request list found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'bufferlist',
            data:{
                list: 'list'
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'request_number', name:'request_number'},
            { data: 'status', name:'status'}
        ]
    });
    
});
$(document).on("click", "#bufferTable tr", function () {
    var data = table.row(this).data();
    user = data.user;
    dtstat = data.status;
    reqDate = data.updated_at;
    $('#head').text('Request no. '+data.request_number);
    $('#bufferModal').modal({backdrop: 'static', keyboard: false});
    buffers_no = data.request_number;
    
    if ($('#level').val() == 'Warehouse Administrator') {
        if (data.status != "For Approval") {
            $('#approvedBtn').hide();
        }
        Promise.all([bufferitems(), buffersend()]).then(() => { 
            if (items > 0) {
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'}
                        ]
                    });
            }else{
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'}
                        ]
                    });
                $('table.bufferitems').dataTable().fnDestroy();
                $('#pending').hide();
            }
            if (senditems > 0) {
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'lrtip',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": 25,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'},
                            { data: 'serial', name:'serial'}
                        ]
                    });
                    //$('#receiving').show();
                    //$('#prcBtn').hide();
            }else{
                $('#receiving').hide();
            }
        });
    }else if ($('#level').val() == 'Warehouse Manager' || $('#userid').val() == '283' || $('#userid').val() == '110') {
        Promise.all([bufferitems(), buffersend()]).then(() => { 
            if (items != 0) {
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'rti',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": items,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'}
                        ]
                    });
            }else{
                buffer =
                    $('table.bufferitems').DataTable({ 
                        "dom": 'rti',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": items,
                        ajax: {
                            url: 'bufferitem',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'pending', name:'pending'}
                        ]
                    });
                $('table.bufferitems').dataTable().fnDestroy();
                $('#pending').hide();
            }
            if (senditems > 0 ) {
                buffersenditems =
                    $('table.buffersend').DataTable({ 
                        "dom": 'rti',
                        processing: true,
                        serverSide: false,
                        "language": {
                            "emptyTable": "No item found!",
                            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        },
                        "order": [ 0, "asc" ],
                        "pageLength": senditems,
                        ajax: {
                            url: 'buffersenditems',
                            data: {
                                buffers_no: data.request_number,
                            },
                            error: function(data) {
                                if(data.status == 401) {
                                    window.location.href = '/login';
                                }
                            }
                        },
                        columns: [
                            { data: 'category', name:'category'},
                            { data: 'item', name:'item'},
                            { data: 'qty', name:'qty'},
                            { data: 'serial', name:'serial'}
                        ],
                        select: {
                            style: 'multi'
                        }
                    });
                $('table.buffersend').DataTable().on('select', function () {
                    var rowselected = buffersenditems.rows( { selected: true } ).data();
                    if(rowselected.length > 0){
                        $('#rec_Btn').prop('disabled', false);
                        $('#not_rec_Btn').prop('disabled', true);
                    }
                });
                $('table.buffersend').DataTable().on('deselect', function () {
                    var rowselected = buffersenditems.rows( { selected: true } ).data();
                    if(rowselected.length == 0){
                        $('#rec_Btn').prop('disabled', true);
                        $('#not_rec_Btn').prop('disabled', false);
                    }
                });
                    $('#receiving').show();
            }else{
                $('#receiving').hide();
            }
        });
    }
  
    function bufferitems() {
        return $.ajax({
            type:'get',
            url: "bufferitem",
            data: {
                buffers_no: data.request_number,
            },
            success:function(data)
            {
                items = data.data.length;
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
    function buffersend() {
        return $.ajax({
            type:'get',
            url: "buffersenditems",
            data: {
                buffers_no: data.request_number,
            },
            success:function(data)
            {
                senditems = data.data.length;
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
});