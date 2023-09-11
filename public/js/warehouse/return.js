var table;
var send = 1;
var retno;
var rowcount;
var returns;
var items;
var ret_no;
var editserial_id;
var serialnum;
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
            {data: 'date',name: 'date',"width": "15%",
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            var date = new Date(data);
                            var formattedDateTime = date.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' });
                            formattedDateTime = formattedDateTime.replace(' at', '');
                            return '<span class="formatted-date">' + formattedDateTime + '</span>';
                        }
                        return data; // Return the original data for sorting and other purposes
                    }
                },
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
                { data: 'remarks', name:'remarks'},
                { data: null, "render": function ( data, type, row, meta) 
                    {
                        return '<button class="btn-primary editBtn" item="'+data.items_id+'" serial_num="'+data.serial+'" return_id="'+data.id+'" stat="Received">Edit Serial</button><br><br><button class="btn-primary recBtn" return_id="'+data.id+'" stat="Received">Received</button>';
                    }
                }
            ]
        });
});
$(document).on("keyup", "#editserial", function () {
    $(this).val($(this).val().toUpperCase());
    
});
$(document).on("click", "#serial_btn", function() {
    $('#loading').show();
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: editserial_id,
            edit: 'yes',
            new: $('#editserial').val().toUpperCase(),
            old: serialnum,
            type:'put',
            itemid: $('#item').val()
        },
        success: function(data) {
            alert('Serial Number updated');
            location.reload();
        },
        error: function(data) {
            console.log(data);
            // alert(data.responseText);
        }
    });
});
$(document).on('click', '.editBtn', function() {
    editserial_id = $(this).attr('return_id');
    serialnum = $(this).attr('serial_num');
    items_id = $(this).attr('item');
    console.log(items_id);
    var codeOp = " ";
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'get',
        data: {
            id: editserial_id,
            edit: 'yes',
            old: serialnum,
            type:'get'
        },
        success: function(data) {
            console.log(data);
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            codeOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                codeOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#item").find('option').remove().end().append(codeOp);
            $('#item').val(items_id);
            $('#serialModal').modal('show');
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
    $('#editserial').val(serialnum);
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