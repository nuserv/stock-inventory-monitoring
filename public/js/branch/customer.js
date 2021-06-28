var branchtable;
branchtable =
$('table.customerbranchTable').DataTable({ 
    "dom": 'lrtip',
    "language": {
            "emptyTable": " "
        },
    processing: true,
    serverSide: false,
    ajax: "/customerbranch/",
    columns: [
        { data: 'customer', name:'customer'},
        { data: 'code', name:'code'},
        { data: 'customer_branch', name:'customer_branch',},
        { data: 'contact', name:'contact'},
        { data: 'status', name:'status',}
    ]
});
$(document).on("keyup", "#searchall", function () {
    if ($(this).val() == '') {
        $('#customerdiv').show();
        $('#searchtable').hide();
    }else{
        $('#customerdiv').hide();
        $('#searchtable').show();
    }
    branchtable.search(this.value).draw();
});