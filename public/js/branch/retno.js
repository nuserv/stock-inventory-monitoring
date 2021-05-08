var table;
$(document).ready(function()
{
    table = 
    $('table.retTable').DataTable({ 
        "dom": 'rtip',
        processing: true,
        serverSide: true,
        destroy: true,
        "language": {
            "emptyTable": "No item/s for return found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        ajax: {
            url: '/retno',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        },
        columns: [
            { data: 'date', name:'date'},
            { data: 'return_no', name:'return_no'}
        ],
    });
});

$(document).on('click', '#retTable tr', function(){
    window.location.href = '/return'+table.row(this).data().return_no;
});