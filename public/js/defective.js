var table;
$(document).ready(function()
{
    table =
    $('table.defectiveTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No data found!"
        },
        processing: true,
        serverSide: true,
        ajax: 'return-table',
        "order": [[ 0, "desc", ]],
        columns: [
            { data: 'date', name:'date'},
            { data: 'branch', name:'branch'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ]
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=5 ; i++ ) {
            $('.fl-'+i).val('').change();
            table
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
        
    });

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});
