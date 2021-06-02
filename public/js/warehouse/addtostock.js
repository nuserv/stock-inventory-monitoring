var table;
var send = 1;
var retno;
var rowcount;
var repaired;
var items;
var repaired_no;
$(document).ready(function()
{
    table =
    $('table.repairedTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No repaired item found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'repairedget',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'created_at', name:'created_at'},
            { data: 'repaired_no', name:'repaired_no'},
            { data: 'status', name:'status'}
        ]
    });
    
});
$(document).on("click", "#repairedTable tr", function () {
    var data = table.row(this).data();
    $('#head').text('Repaired Details');
    $('#repairedModal').modal({backdrop: 'static', keyboard: false});
    repaired_no = data.repaired_no;
    Promise.all([repaireditems()]).then(() => { 
        if (items == 1) {
            $('#not_rec_Btn').show();
        }else{
            $('#not_rec_Btn').hide();
        }
        repaired =
        $('table.repaireditems').DataTable({ 
            "dom": 'lrtip',
            processing: true,
            serverSide: false,
            "language": {
                "emptyTable": "No repaired item found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "pageLength": 25,
            ajax: {
                url: 'repaireditem',
                data: {
                    repaired_no: data.repaired_no,
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
                { data: 'serial', name:'serial'},
            ],
            select: {
                style: 'multi'
            }
        });
        $('table.repaireditems').DataTable().on('select', function () {
            var rowselected = repaired.rows( { selected: true } ).data();
            if(rowselected.length > 0){
                $('#rec_Btn').prop('disabled', false);
                $('#not_rec_Btn').prop('disabled', true);
            }
        });
        $('table.repaireditems').DataTable().on('deselect', function () {
            var rowselected = repaired.rows( { selected: true } ).data();
            if(rowselected.length == 0){
                $('#rec_Btn').prop('disabled', true);
                $('#not_rec_Btn').prop('disabled', false);
            }
        });
    });
    
    function repaireditems() {
        return $.ajax({
            type:'get',
            url: "repaireditem",
            data: {
                repaired_no: data.repaired_no,
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
});

$(document).on('click', '#not_rec_Btn', function(){
    $.ajax({
        url: 'repairednr',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            repaired_no : repaired_no
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

$(document).on('click', '#rec_Btn', function(){
    $('#repairedModal').toggle();
    $('#loading').show();
    var datas = repaired.rows( { selected: true } ).data();
    var id = [];
    var eachcount = 0;
    if(datas.length > 0){
        var mydata = $.map(datas, function(value, index) {
            return [value];
        });
        mydata.forEach(value => {
            eachcount++;
            id.push(value.id);
            if (eachcount == datas.length) {
                $.ajax({
                    url: 'repairedrec',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'put',
                    data: {
                        repaired_no : repaired_no,
                        id: id,
                    },
                    success: function(){
                        location.reload();
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
    }
});

$(document).on('click', '.printBtn', function(){
    console.log('click');
    $.ajax({
        type:'get',
        url:'gen',
        async: false,
        success:function(result)
        {
            retno = result;
            $.ajax({
                url: 'pullupdate',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                },
                dataType: 'json',
                type: 'PUT',
                data: {
                    retno: retno,
                    send: send
                },
                success:function(data)
                {
                    location.reload();
                },
                error: function (data) {
                    alert(data.responseText);
                    return false;
                }
            });
        }
    });
});
$(document).on('click', '.cancel', function(){
    $('#loading').show();
    location.reload();
});