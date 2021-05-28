var table;
var send = 1;
var retno;
var rowcount;
var pullout;
var items;
var pull_no;
$(document).ready(function()
{
    table =
    $('table.pulloutTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No pullout found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'pullget',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'branch', name:'branch'},
            { data: 'pullout_no', name:'pullout_no'},
            { data: 'status', name:'status'}
        ]
    });
    
});
$(document).on("click", "#pulloutTable tr", function () {
    var data = table.row(this).data();
    $('#head').text(data.branch+' - Pullout Details');
    $('#pulloutModal').modal({backdrop: 'static', keyboard: false});
    pull_no = data.pullout_no;
    Promise.all([pullitems()]).then(() => { 
        if (items == 1) {
            $('#not_rec_Btn').show();
        }else{
            $('#not_rec_Btn').hide();
        }
        pullout =
        $('table.pulloutitems').DataTable({ 
            "dom": 'lrtip',
            processing: true,
            serverSide: false,
            "language": {
                "emptyTable": "No pullout found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "pageLength": 25,
            ajax: {
                url: 'pullitem',
                data: {
                    pullno: data.pullout_no,
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
        $('table.pulloutitems').DataTable().on('select', function () {
            var rowselected = pullout.rows( { selected: true } ).data();
            if(rowselected.length > 0){
                $('#rec_Btn').prop('disabled', false);
                $('#not_rec_Btn').prop('disabled', true);
            }
        });
        $('table.pulloutitems').DataTable().on('deselect', function () {
            var rowselected = pullout.rows( { selected: true } ).data();
            if(rowselected.length == 0){
                $('#rec_Btn').prop('disabled', true);
                $('#not_rec_Btn').prop('disabled', false);
            }
        });
    });
    
    function pullitems() {
        return $.ajax({
            type:'get',
            url: "pullitem",
            data: {
                pullno: data.pullout_no,
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
        url: 'pullnr',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            pull_no : pull_no
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
    $('#pulloutModal').toggle();
    $('#loading').show();
    var datas = pullout.rows( { selected: true } ).data();
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
                    url: 'pullrec',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'put',
                    data: {
                        pull_no : pull_no,
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