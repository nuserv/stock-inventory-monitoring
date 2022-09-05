var table, itemtable, itemtable2;
$(document).ready(function()
{
    $('.requestTable thead tr:eq(0) th').each( function () {
        $(this).html('<input type="text" style="width:100%" class="column_search"/>' );
    });

    table =
    $('table.requestTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No data found!"
        },
        processing: false,
        serverSide: false,
        ajax: 'itemrequestdata',
        "order": [[ 0, "asc", ]],
        columns: [
            { data: 'category', name:'category'},
            { data: 'item_name', name:'item_name'},
            { data: 'request', name:'request'},
            { data: 'stock', name:'stock'}
        ]
    });

    $('.requestTable thead').on( 'keyup', ".column_search",function () {
        table
            .column( $(this).parent().index() )
            .search( this.value )
            .draw();
    });

});

$(document).on('click', '#requestTable tbody tr', function () {
    var trdata = table.row(this).data();
    console.log(trdata.items_id);
    $('#catname').html('<b>CATEGORY: '+trdata.category+'</b>');
    $('#itemname').html('<b>ITEM DESCRIPTION: '+trdata.item_name+'</b>');
    $('#branchitemsdiv').show();
    $('#branchitems2div').hide();
    $('#brname').hide();
    $('#itemrequestModal').modal('show');
    $('table.branchitems').dataTable().fnDestroy();
    itemtable =
        $('table.branchitems').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No data found!"
            },
            processing: false,
            serverSide: false,
            ajax: {
                "url": "/branchitemdata",
                "data": {
                    "items_id": trdata.items_id,
                }
            },
            "order": [[ 0, "asc", ]],
            columns: [
                { data: 'branch', name:'branch'},
                { data: 'pending', name:'pending'}
            ]
        });
});

$(document).on('click', '#branchitems tbody tr', function () {
    var trdata = itemtable.row(this).data();
    $('#branchitemsdiv').hide();
    $('#branchitems2div').show();
    $('#brname').show();
    $('#brname').html('<b>BRANCH: '+trdata.branch+'</b><hr>');
    $('table.branchitems2').dataTable().fnDestroy();
    itemtable2 =
        $('table.branchitems2').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No data found!"
            },
            processing: false,
            serverSide: false,
            ajax: {
                "url": "/branchitemdata2",
                "data": {
                    "items_id": trdata.items_id,
                    'branch_id': trdata.branch_id
                }
            },
            "order": [[ 0, "asc", ]],
            columns: [
                { data: 'pending', name:'pending'},
                { data: 'created_at', name:'created_at'}
            ]
        });
});

$(document).on('click', '#branchitems2 tbody tr', function () {
    var trdata = itemtable2.row(this).data();
    window.location.href = '/request?reqno='+trdata.request_no;
});
