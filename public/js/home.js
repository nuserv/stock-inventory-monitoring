$(document).ready(function()
{
    var table =
    $('table.activityTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
                "emptyTable": "No data found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
            },
        "pageLength": 25,
        "order": [ 0, 'desc' ],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'activity',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        columns: [
            { data: 'id', name:'id'},
            { data: 'date', name:'date',"width": "14%"},
            { data: 'fullname', name:'fullname',"width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'activity', name:'activity'}
        ]
    });
    $('.tbsearch').show();

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});