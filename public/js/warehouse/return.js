var table;
var send = 1;
var retno;
var rowcount;
var returns;
var items;
var ret_no;
$(document).ready(function()
{
    table =
    $('table.returnTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No return found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'returnget',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'branch', name:'branch'},
            { data: 'return_no', name:'return_no'},
            { data: 'status', name:'status'}
        ]
    });
    
});

$(document).on("click", "#returnTable tr", function () {
    var data = table.row(this).data();
    $('#head').text(data.branch+' - Return Details');
    $('#returnModal').modal({backdrop: 'static', keyboard: false});
    ret_no = data.return_no;
    returns =
        $('table.returnitems').DataTable({ 
            "dom": 'lrtip',
            processing: true,
            serverSide: false,
            "language": {
                "emptyTable": "No return data found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "pageLength": 25,
            ajax: {
                url: 'returnitem',
                data: {
                    retno: ret_no,
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
                { data: null, "render": function ( data, type, row, meta) 
                    {
                        return '<button class="btn-primary recBtn" return_id="'+data.id+'" stat="Received">Received</button>';
                    }
                }
            ]
        });
});
$(document).on('click', '.recBtn', function() {
    var returnid = $(this).attr('return_id');
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'Received'
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
    returns.row($(this).parents('tr')).remove().draw( false );
    var count = returns.data().count();
    if (count == 0) {
        $('#returnModal').toggle();
        $('#loading').show();
        location.reload();
    }
});
$(document).on('click', '.cancel', function(){
    $('#loading').show();
    location.reload();
});